<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Log;
use App\Models\BirthdayUser;

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

    public function processVideo($text, $start, $end, $position, $fontPath, $size, $color, $charDelay = 0.1, $videoPath = null, $endOfProcess = false)
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

        if ($videoPath) {
            $this->videoPath = $videoPath;
        }

        $inputPath = $this->videoPath;
        $outputPath = $this->outputPath;

        // Create a temporary output path
        $tempOutputPath = str_replace('.mp4', '_temp.mp4', $outputPath);

        $x = $position[0];
        $y = $position[1];
        $startTime = $start;
        $endTime = $end;

        // Escape the entire text for use in FFmpeg command
        $escapedText = $this->escapeString($text);

        // Calculate fade durations
        $fadeInDuration = 0.5; // Duration of fade-in effect in seconds
        $fadeOutDuration = 0.5; // Duration of fade-out effect in seconds
        $fadeOutStart = $endTime - $fadeOutDuration;

        // Construct the drawtext filter
        $drawtext = "drawtext=text='$escapedText':fontfile=$fontPath:fontsize=$size:fontcolor=$color:x=$x:y=$y:";
        $drawtext .= "enable='between(t,$startTime,$endTime)':";
        $drawtext .= "alpha='if(lt(t,$startTime),0,if(lt(t,$startTime+$fadeInDuration),(t-$startTime)/$fadeInDuration,";
        $drawtext .= "if(gt(t,$fadeOutStart),($endTime-t)/$fadeOutDuration,1)))'";

        // Construct the FFmpeg command using the temporary output path
        $command = "ffmpeg -y -i $inputPath -vf \"$drawtext\" -codec:a copy $tempOutputPath";

        Log::info("Executing FFmpeg command", [
            'employeeId' => $this->employeeId,
            'command' => $command
        ]);

        exec($command . " 2>&1", $output, $status);

        Log::info("FFmpeg command output", [
            'employeeId' => $this->employeeId,
            'output' => $output,
            'status' => $status
        ]);

        if ($status !== 0) {
            Log::error("Failed to add text to video", [
                'employeeId' => $this->employeeId,
                'status' => $status,
                'output' => $output,
                'command' => $command,
                'inputPath' => $inputPath,
                'outputPath' => $tempOutputPath
            ]);
            return ['status' => 500, 'message' => 'Failed to add text to video', 'error' => implode("\n", $output)];
        }

        if (!file_exists($tempOutputPath)) {
            Log::error("Temporary output file was not created", [
                'employeeId' => $this->employeeId,
                'outputPath' => $tempOutputPath
            ]);
            return ['status' => 500, 'message' => 'Temporary output file was not created', 'outputPath' => $tempOutputPath];
        }

        // Move the temporary file to the final output path
        if (!rename($tempOutputPath, $outputPath)) {
            Log::error("Failed to rename temporary output file", [
                'employeeId' => $this->employeeId,
                'from' => $tempOutputPath,
                'to' => $outputPath
            ]);
            return ['status' => 500, 'message' => 'Failed to rename temporary output file'];
        }

        // Update the video path for the next operation
        $this->videoPath = $outputPath;

        Log::info("Video processing completed successfully", [
            'employeeId' => $this->employeeId,
            'outputPath' => $outputPath
        ]);

        // If end of process is true, then set the user's video path and is_video_generated
        if ($endOfProcess) {
            Log::info("Setting user's video path and is_video_generated flag", [
                'employeeId' => $this->employeeId
            ]);
            $user = BirthdayUser::where('employee_id', $this->employeeId)->first();
            $user->video_url = "https://testlab.cfd/videos/{$user->employee_id}.mp4";
            $user->is_video_generated = true;
            $user->save();
        }

        return ['status' => 200, 'message' => 'Text added to video successfully', 'output_path' => $outputPath];
    }

    protected function escapeString($string)
    {
        $string = str_replace('\\', '\\\\', $string);
        $string = str_replace(':', '\\:', $string);
        $string = str_replace('\'', '\\\'', $string);
        return trim($string);
    }
}
