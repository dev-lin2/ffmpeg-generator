<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\VideoService;
use Illuminate\Support\Facades\Log;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employeeId;
    protected $text;
    protected $start;
    protected $end;
    protected $position;
    protected $fontPath;
    protected $size;
    protected $color;
    protected $charDelay;
    protected $videoPath;
    protected $endOfProcess;

    public function __construct($employeeId, $text, $start, $end, $position, $fontPath, $size, $color, $charDelay = 0.1, $videoPath = null, $endOfProcess = false)
    {
        $this->employeeId = $employeeId;
        $this->text = $text;
        $this->start = $start;
        $this->end = $end;
        $this->position = $position;
        $this->fontPath = $fontPath;
        $this->size = $size;
        $this->color = $color;
        $this->charDelay = $charDelay;
        $this->videoPath = $videoPath;
        $this->endOfProcess = $endOfProcess;
    }

    public function handle(VideoService $videoService)
    {
        $videoService->setOutputPath($this->employeeId);
        $result = $videoService->processVideo(
            $this->text,
            $this->start,
            $this->end,
            $this->position,
            $this->fontPath,
            $this->size,
            $this->color,
            $this->charDelay,
            $this->videoPath,
            $this->endOfProcess
        );

        if ($result['status'] !== 200) {
            Log::error("ProcessVideo job failed", [
                'employeeId' => $this->employeeId,
                'result' => $result
            ]);
            throw new \Exception($result['message'] . ': ' . ($result['error'] ?? 'Unknown error'));
        }
    }
}
