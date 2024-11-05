<?php

namespace App\Services\Api;

use App\Jobs\ProcessOverlayVideo;
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

    public function setVideoPath($videoPath)
    {
        $this->videoPath = $videoPath;
    }

    public function getVideoPath()
    {
        return $this->videoPath;
    }

    public function setOutputPath($employeeId)
    {
        $this->employeeId = $employeeId;
        $this->outputPath = public_path("videos/{$employeeId}.mp4");
    }

    public function addTextToVideo($text, $start, $end, $position, $fontPath, $size, $color, $charDelay = 0.1, $videoPath = null)
    {
        // Dispatch the job to the queue
        ProcessVideo::dispatch($this->employeeId, $text, $start, $end, $position, $fontPath, $size, $color, $charDelay, $videoPath);

        return ['status' => 202, 'message' => 'Video processing job has been queued'];
    }

    public function processVideo($text, $start, $end, $position, $fontPath, $size, $color, $charDelay = 0.1, $videoPath = null)
    {
        Log::info("Starting video processing", [
            'employeeId' => $this->employeeId,
            'text' => $text,
            'start' => $start,
            'end' => $end,
            'position' => $position,
            'fontPath' => $fontPath,
            'size' => $size,
            'color' => $color,
            'charDelay' => $charDelay,
            'videoPath' => $videoPath
        ]);

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

        Log::info("Processing text lines", [
            'employeeId' => $this->employeeId,
            'lines' => $lines
        ]);

        foreach ($lines as $index => $line) {
            $line = $this->escapeString($line);
            // Reset x position for each line
            $x = $position[0];
            $textLength = mb_strlen($line);
            $fontSize = $size;
            $drawtext = "";

            $outputPath = str_replace('.mp4', "_{$index}.mp4", $this->outputPath);
            $tempFiles[] = $outputPath;

            for ($i = 0; $i < $textLength; $i++) {
                $char = mb_substr($line, $i, 1);
                $x += $fontSize * 0.8; // Adjust this factor to change character spacing
                $startTime += $charDelay;
                $drawtext .= "drawtext=text='$char':fontfile={$fontPath}:fontsize={$fontSize}:fontcolor={$color}:x=$x:y=$y:enable='between(t,$startTime,$endTime)'";
                if ($i != $textLength - 1) {
                    $drawtext .= ", ";
                }
            }

            $command = "ffmpeg -y -i {$inputPath} -vf \"$drawtext\" -codec:a copy {$outputPath}";

            Log::info("Executing FFmpeg command", [
                'employeeId' => $this->employeeId,
                'command' => $command
            ]);
            exec($command . " 2>&1", $output, $status);

            Log::info("FFmpeg command output", [
                'employeeId' => $this->employeeId,
                'output' => $output
            ]);

            if ($status !== 0) {
                $this->cleanupTempFiles($tempFiles);
                Log::error("Failed to add text to video", [
                    'employeeId' => $this->employeeId,
                    'status' => $status,
                    'output' => $output,
                    'command' => $command
                ]);
                return ['status' => 500, 'message' => 'Failed to add text to video', 'error' => implode("\n", $output)];
            }

            if (!file_exists($outputPath)) {
                Log::error("Output file was not created", [
                    'employeeId' => $this->employeeId,
                    'outputPath' => $outputPath
                ]);
                return ['status' => 500, 'message' => 'Output file was not created', 'outputPath' => $outputPath];
            }

            $inputPath = $outputPath;
            $y += $fontSize + 10;
            $startTime += 0.5;
        }

        if (!rename(end($tempFiles), $this->outputPath)) {
            Log::error("Failed to rename final output file", [
                'employeeId' => $this->employeeId,
                'from' => end($tempFiles),
                'to' => $this->outputPath
            ]);
            return ['status' => 500, 'message' => 'Failed to rename final output file'];
        }

        $this->cleanupTempFiles($tempFiles);

        Log::info("Video processing completed successfully", [
            'employeeId' => $this->employeeId,
            'outputPath' => $this->outputPath
        ]);

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
                if (!unlink($file)) {
                    Log::warning("Failed to delete temporary file", [
                        'employeeId' => $this->employeeId,
                        'file' => $file
                    ]);
                }
            }
        }
    }

    public function addVideoToVideo($videoPath, $start, $end, $position, $overlayVideoPath)
    {
        // Dispatch the job to the queue
        ProcessOverlayVideo::dispatch($this->employeeId, $videoPath, $start, $end, $position, $overlayVideoPath);

        return ['status' => 202, 'message' => 'Video overlay processing job has been queued'];
    }

    public function processOverlayVideo($videoPath, $start, $end, $position, $overlayVideoPath)
    {
        Log::info("Starting video overlay processing", [
            'employeeId' => $this->employeeId,
            'videoPath' => $videoPath,
            'start' => $start,
            'end' => $end,
            'position' => $position,
            'overlayVideoPath' => $overlayVideoPath
        ]);

        // Wait for the video file to be available
        $maxAttempts = 10;
        $attemptDelay = 5;
        $attempts = 0;

        while (!file_exists($videoPath) && $attempts < $maxAttempts) {
            sleep($attemptDelay);
            $attempts++;
        }

        if (!file_exists($videoPath)) {
            Log::error("Input video file not found", [
                'employeeId' => $this->employeeId,
                'videoPath' => $videoPath
            ]);
            return ['status' => 404, 'message' => 'Input video file not found'];
        }

        $x = $position[0];
        $y = $position[1];
        $width = 300;
        $height = 300;

        $inputPath = $videoPath;
        $tempOutputPath = str_replace('.mp4', '_overlay_temp.mp4', $videoPath);

        // Calculate the duration of the overlay
        $duration = $end - $start;

        $command = "ffmpeg -y -i {$inputPath} -i {$overlayVideoPath} -filter_complex \"[1:v]trim=0:{$duration},setpts=PTS-STARTPTS[overlay];[0:v][overlay] overlay={$x}:{$y}:enable='between(t,{$start},{$end})'\" -codec:a copy {$tempOutputPath}";

        Log::info("Executing FFmpeg command", [
            'employeeId' => $this->employeeId,
            'command' => $command
        ]);
        exec($command . " 2>&1", $output, $status);

        Log::info("FFmpeg command output", [
            'employeeId' => $this->employeeId,
            'output' => $output
        ]);

        if ($status !== 0) {
            Log::error("Failed to overlay video", [
                'employeeId' => $this->employeeId,
                'status' => $status,
                'output' => $output,
                'command' => $command
            ]);
            return ['status' => 500, 'message' => 'Failed to overlay video', 'error' => implode("\n", $output)];
        }

        if (!file_exists($tempOutputPath)) {
            Log::error("Temporary output file was not created", [
                'employeeId' => $this->employeeId,
                'outputPath' => $tempOutputPath
            ]);
            return ['status' => 500, 'message' => 'Temporary output file was not created', 'outputPath' => $tempOutputPath];
        }

        $finalOutputPath = str_replace('_overlay_temp.mp4', '.mp4', $tempOutputPath);

        if (!rename($tempOutputPath, $finalOutputPath)) {
            Log::error("Failed to rename temporary output file", [
                'employeeId' => $this->employeeId,
                'from' => $tempOutputPath,
                'to' => $finalOutputPath
            ]);
            return ['status' => 500, 'message' => 'Failed to rename temporary output file'];
        }

        Log::info("Video overlay processing completed successfully", [
            'employeeId' => $this->employeeId,
            'outputPath' => $finalOutputPath
        ]);

        return ['status' => 200, 'message' => 'Video overlaid successfully', 'output_path' => $finalOutputPath];
    }
}
