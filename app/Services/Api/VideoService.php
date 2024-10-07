<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessVideo;

class VideoService
{
    protected $outputPath;
    protected $videoPath;
    protected $employeeId;

    public function __construct()
    {
        $this->videoPath = public_path('main.mp4');
    }

    public function setOutputPath($employeeId)
    {
        $this->employeeId = $employeeId;
        $this->outputPath = public_path("videos/{$employeeId}.mp4");
    }

    public function addTextToVideo($text, $start, $end, $position, $fontPath, $size, $color, $videoPath = null)
    {
        // Dispatch the job to the queue
        ProcessVideo::dispatch($this->employeeId, $text, $start, $end, $position, $fontPath, $size, $color, $videoPath);

        return ['status' => 202, 'message' => 'Video processing job has been queued'];
    }

    public function processVideo($text, $start, $end, $position, $fontPath, $size, $color, $videoPath = null)
    {
        $x = $position[0];
        $y = $position[1];
        $startTime = $start;
        $endTime = $end;

        if ($videoPath) {
            $this->videoPath = $videoPath;
        }

        $lines = explode("\n", $text);
        $inputPath = $this->videoPath;
        $tempFiles = [];

        foreach ($lines as $index => $line) {
            $line = $this->escapeString($line);

            $outputPath = str_replace('.mp4', "_{$index}.mp4", $this->outputPath);
            $tempFiles[] = $outputPath;

            $fadeDuration = 1; // Duration of fade-in effect in seconds
            $command = "ffmpeg -i {$inputPath} -vf \"drawtext=fontfile={$fontPath}:text='{$line}':fontsize={$size}:fontcolor={$color}:x={$x}:y={$y}:alpha='if(lt(t,{$startTime}),0,if(lt(t,{$startTime}+{$fadeDuration}),(t-{$startTime})/{$fadeDuration},1))*between(t,{$startTime},{$endTime})'\" -c:a copy {$outputPath} -y";

            Log::info("Executing command: {$command}");

            exec($command, $output, $status);
            if ($status !== 0) {
                $this->cleanupTempFiles($tempFiles);
                Log::error("Failed to add text to video: " . implode("\n", $output));
                return ['status' => 500, 'message' => 'Failed to add text to video'];
            }

            // Update input path for next iteration
            $inputPath = $outputPath;

            // Add y offset by font size
            $y += $size + 10;

            // Add time offset
            $startTime += 1;
        }

        // Rename the last temp file to the final output file
        rename(end($tempFiles), $this->outputPath);

        // Clean up temporary files
        $this->cleanupTempFiles($tempFiles);

        return ['status' => 200, 'message' => 'Text added to video successfully', 'output_path' => $this->outputPath];
    }

    protected function escapeString($string)
    {
        $string = str_replace('\\', '\\\\', $string);
        $string = str_replace(':', '\\:', $string);
        $string = str_replace('\'', '\\\'', $string);
        return trim($string);
    }

    protected function cleanupTempFiles($files)
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
