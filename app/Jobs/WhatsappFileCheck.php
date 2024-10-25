<?php

namespace App\Jobs;

use App\Enums\UploadedFileStatus;
use App\Models\UploadedFile;
use App\Services\Wamm\WammService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class WhatsappFileCheck implements ShouldQueue
{
    use Queueable;

    public $timeout = 1200;

    /**
     * Create a new job instance.
     */
    public function __construct(public UploadedFile $file) {}

    /**
     * Execute the job.
     */
    public function handle(WammService $wamm): void
    {
        $content = Storage::get($this->file->result_path);
        $phones = collect();

        $oldPhones = str($content)->explode("\n");
        $count = $oldPhones->count();

        foreach ($oldPhones as $key => $phone) {
            if ($wamm->checkPhone($phone)) {
                $phones->add($phone);
            }

            if ($key % 10 === 0) {
                $this->file->update(['whatsapp_check_percent' => ($key + 1) / $count * 100]);
            }
        }

        $this->file->update([
            'whatsapp_phones_number' => $phones->count(),
            'status' => UploadedFileStatus::CLEANED_WHATSAPP,
        ]);
        Storage::write($this->file->result_path, $phones->join("\n"));
    }
}