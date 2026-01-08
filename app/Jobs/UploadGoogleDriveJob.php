<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;


class UploadGoogleDriveJob implements ShouldQueue
{
    use Queueable;

    
    private $request;

    /**
     * Create a new job instance.
     */
    public function __construct($request)
    {
        $this->request = $request;    
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $googleDriveService = app(GoogleDriveService::class);
        $uploadResult = $googleDriveService->uploadFile(
            $this->request->file('certificate_file'),
            'certificates'
        );
    }
}
