<?php

namespace App\Console\Commands;

use App\Models\UploadedFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteOldFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UploadedFile::query()
            ->where('created_at', '<', now()->subDays(14)->startOfDay())
            ->get(['id', 'path', 'result_path'])
            ->each(function ($file) {
                Storage::delete($file->path);
                Storage::delete($file->result_path);
                $file->delete();
            });
    }
}
