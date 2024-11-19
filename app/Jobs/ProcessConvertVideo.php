<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\VideoService;
use Illuminate\Support\Facades\Log;

class ProcessConvertVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $videoPath;

    public function __construct($user, $videoPath)
    {
        $this->user = $user;
        $this->videoPath = $videoPath;
    }

    public function handle(VideoService $videoService)
    {
        $videoService->setOutputPath($this->user->uniqid);
        $result = $videoService->convertVideoToGIF($this->videoPath, $this->user);

        if ($result['status'] !== 200) {
            Log::error('Failed to convert video for user: ' . $this->user->id);
        }
    }
}
