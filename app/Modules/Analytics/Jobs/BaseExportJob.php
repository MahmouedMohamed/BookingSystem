<?php

namespace App\Modules\Analytics\Jobs;

use App\Modules\Analytics\Notifications\SendExportCompletionEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class BaseExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $uuid;

    private $name;

    private $exportFile;

    private $requester;

    private $extraData;

    private $isLast;

    /**
     * Create a new job instance.
     */
    public function __construct($uuid, $name, $exportFile, $requester, $extraData = [], $isLast = false)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->exportFile = $exportFile;
        $this->requester = $requester;
        $this->extraData = $extraData;
        $this->isLast = $isLast;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $exportFile = app()->make($this->exportFile, ['uuid' => $this->uuid, 'fileName' => $this->name, 'requester' => $this->requester, 'extraData' => $this->extraData]);

        $exportFile->generate();

        $writerFileName = $this->uuid;

        if ($this->isLast) {
            $writeType = '.'.(Str::lower($exportFile->writerType ?? 'csv'));

            $writerFileName = $this->uuid;

            $url = env('APP_URL').'/storage/downloads/tmp/'.$writerFileName.$writeType;

            $this->requester->notify(new SendExportCompletionEmail($this->name, $url));
        }
    }
}
