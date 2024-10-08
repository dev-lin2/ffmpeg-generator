<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\VideoService;

class ProcessOverlayVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employeeId;
    protected $videoPath;
    protected $start;
    protected $end;
    protected $position;
    protected $overlayVideoPath;

    public function __construct($employeeId, $videoPath, $start, $end, $position, $overlayVideoPath)
    {
        $this->employeeId = $employeeId;
        $this->videoPath = $videoPath;
        $this->start = $start;
        $this->end = $end;
        $this->position = $position;
        $this->overlayVideoPath = $overlayVideoPath;
    }

    public function handle()
    {
        $videoService = new VideoService();
        $videoService->setOutputPath($this->employeeId);
        $videoService->processOverlayVideo(
            $this->videoPath,
            $this->start,
            $this->end,
            $this->position,
            $this->overlayVideoPath
        );
    }
}
