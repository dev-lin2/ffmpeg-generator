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
        Log::info("Starting video processing", [
            'text' => $text,
            'start' => $start,
            'end' => $end,
            'position' => $position,
            'fontPath' => $fontPath,
            'size' => $size,
            'color' => $color,
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

        foreach ($lines as $index => $line) {
            $line = $this->escapeString($line);
            $size = mb_strlen($line);
            $drawtext = "";

            $outputPath = str_replace('.mp4', "_{$index}.mp4", $this->outputPath);
            $tempFiles[] = $outputPath;

            $fadeDuration = 1; // Duration of fade-in effect in seconds

            for ($i = 0; $i < $size; $i++) {
                $char = mb_substr($line, $i, 1);
                $x += 50;
                $startTime += 0.03;
                $drawtext .= "drawtext=text='$char':fontfile='D\\:/Site/Practice/birthday-video/storage/app/public/fonts/FUTENE.ttf':fontsize={$size}:fontcolor={$color}:x=$x:y=$y:enable='between(t,$startTime,$endTime)'";
                if ($i != $size - 1) {
                    $drawtext .= ", ";
                }
            }

            // $command = "ffmpeg -i {$inputPath} -vf \"drawtext=fontfile={$fontPath}:text='{$line}':fontsize={$size}:fontcolor={$color}:x={$x}:y={$y}:alpha='if(lt(t,{$startTime}),0,if(lt(t,{$startTime}+{$fadeDuration}),(t-{$startTime})/{$fadeDuration},1))*between(t,{$startTime},{$endTime})'\" -c:a copy {$outputPath} -y";

            $command = "ffmpeg -y -i {$inputPath} -vf \"$drawtext\" -codec:a copy {$outputPath}";
            // exec($command, $output, $return_var);

            Log::info("Executing FFmpeg command", ['command' => $command]);
            exec($command . " 2>&1", $output, $status);
            
            Log::info("FFmpeg command output", ['output' => $output]);

            if ($status !== 0) {
                $this->cleanupTempFiles($tempFiles);
                Log::error("Failed to add text to video", [
                    'status' => $status,
                    'output' => $output,
                    'command' => $command
                ]);
                return ['status' => 500, 'message' => 'Failed to add text to video', 'error' => implode("\n", $output)];
            }

            // Check if the output file was created
            if (!file_exists($outputPath)) {
                Log::error("Output file was not created", ['outputPath' => $outputPath]);
                return ['status' => 500, 'message' => 'Output file was not created', 'outputPath' => $outputPath];
            }

            // Update input path for next iteration
            $inputPath = $outputPath;

            // Add y offset by font size
            $y += $size + 10;

            // Add time offset
            $startTime += 1;
        }

        // Rename the last temp file to the final output file
        if (!rename(end($tempFiles), $this->outputPath)) {
            Log::error("Failed to rename final output file", [
                'from' => end($tempFiles),
                'to' => $this->outputPath
            ]);
            return ['status' => 500, 'message' => 'Failed to rename final output file'];
        }

        // Clean up temporary files
        $this->cleanupTempFiles($tempFiles);

        Log::info("Video processing completed successfully", ['outputPath' => $this->outputPath]);

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
                    Log::warning("Failed to delete temporary file", ['file' => $file]);
                }
            }
        }
    }
}
