<?php

namespace App\Modules\Analytics\Exports;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Writer;

class BaseExport
{
    public $writerType = 'csv';

    public $uuid;

    public $fileName;

    public $requester;

    public $request;

    public $take;

    public $total;

    public $skip;

    public function __construct($uuid, $fileName, $requester, $extraData)
    {
        $this->uuid = $uuid;
        $this->fileName = $fileName;
        $this->requester = $requester;
        $this->request = $extraData['request'];
        $this->take = $extraData['take'] ?? 0;
        $this->total = $extraData['total'] ?? 0;
        $this->skip = $extraData['skip'] ?? 0;
    }

    public function count()
    {
        return $this->data();
    }

    public function generate(bool $all = false, bool $allBasedOnCount = false): void
    {
        $data = $this->data($this->take, $this->skip);

        $count = $data->count();

        $csv = Writer::createFromString('');

        for ($index = 0; $index < $count; $index++) {
            $item = $this->map($data[$index]);
            if ($all) {
                $csv->insertAll($item);
            } else {
                $csv->insertOne($item);
            }
        }

        $writeType = '.'.(Str::lower($this->writerType ?? 'csv'));

        $writerFileName = $this->uuid.$writeType;

        $this->storeFile($writerFileName, $csv);
    }

    public function storeFile($filename, $content = null)
    {
        if (Storage::disk('public')->exists('/downloads/tmp/'.$filename)) {
            Storage::disk('public')->append('/downloads/tmp/'.$filename, (string) $content);
        } else {
            $cleanedString = str_replace("\n", '', $content); // Remove the newline character
            Storage::disk('public')->put('/downloads/tmp/'.$filename, (string) $cleanedString);
        }
    }
}
