<?php

namespace App\Modules\Analytics\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use League\Csv\Reader;
use League\Csv\Writer;

class BaseExportShutterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $uuid;

    private $name;

    private $exportFile;

    private $requester;

    private $extraData;

    public $timeout = 1800; // Set to 6 mins

    /**
     * Create a new job instance.
     */
    public function __construct($uuid, $name, $exportFile, $requester, $extraData = [])
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->exportFile = $exportFile;
        $this->requester = $requester;
        $this->extraData = $extraData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $exportFile = app()->make($this->exportFile, ['uuid' => $this->uuid, 'fileName' => $this->name, 'requester' => $this->requester, 'extraData' => $this->extraData]);

        $count = $exportFile->count();
        $numberOfJobs = ceil($count / env('EXCEL_EXPORT_LIMIT', 100));

        // Initialize Writer instance and add UTF-8 BOM
        $csv = Writer::createFromString("\xEF\xBB\xBF"); // Adding BOM for UTF-8

        // Apply UTF-8 encoding
        $csv->setOutputBOM(Reader::BOM_UTF8);

        // Add Headers
        $csv->insertOne($exportFile->headings());

        $writeType = '.'.(Str::lower($exportFile->writerType ?? 'csv'));

        $writerFileName = $this->uuid.$writeType;

        $exportFile->storeFile($writerFileName, $csv);

        if ($numberOfJobs == 0) {
            BaseExportJob::dispatch($this->uuid, $this->name, $this->exportFile, $this->requester,
                array_merge($this->extraData, [
                    'total' => $count,
                    'take' => env('EXCEL_EXPORT_LIMIT', 100),
                    'skip' => 0,
                ]), true, true
            );
        }

        for ($index = 0; $index < $numberOfJobs; $index++) {
            (new BaseExportJob($this->uuid, $this->name, $this->exportFile, $this->requester,
                array_merge($this->extraData, [
                    'total' => $count,
                    'take' => env('EXCEL_EXPORT_LIMIT', 100),
                    'skip' => env('EXCEL_EXPORT_LIMIT', 100) * $index,
                ]), $index == 0, $numberOfJobs == $index + 1
            ))->dispatch($this->uuid, $this->name, $this->exportFile, $this->requester,
                array_merge($this->extraData, [
                    'total' => $count,
                    'take' => env('EXCEL_EXPORT_LIMIT', 100),
                    'skip' => env('EXCEL_EXPORT_LIMIT', 100) * $index,
                ]), $index == 0, $numberOfJobs == $index + 1);
        }
    }
}
