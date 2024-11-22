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

        // Normal Text effect
        $drawtext = "drawtext=text='$escapedText':fontfile=$fontPath:fontsize=$size:fontcolor=$color:x=$x:y=$y:";
        $drawtext .= "enable='between(t,$startTime,$endTime)':";
        $drawtext .= "alpha='if(lt(t,$startTime),0,if(lt(t,$startTime+$fadeInDuration),(t-$startTime)/$fadeInDuration,";
        $drawtext .= "if(gt(t,$fadeOutStart),($endTime-t)/$fadeOutDuration,1)))'";

        // Bouncing text effect
        // Bouncing effect parameters
        // $bounceHeight = 20; // Maximum pixels to move up and down
        // $bouncePeriod = 2; // Time for one complete bounce cycle in seconds

        // $drawtext = "drawtext=text='$escapedText':fontfile=$fontPath:fontsize=$size:fontcolor=$color:";
        // $drawtext .= "x=$x:";
        // $drawtext .= "y='$y+$bounceHeight*sin((t-$startTime)*2*PI/$bouncePeriod)':";
        // $drawtext .= "enable='between(t,$startTime,$endTime)':";
        // $drawtext .= "alpha='if(lt(t,$startTime),0,if(lt(t,$startTime+$fadeInDuration),(t-$startTime)/$fadeInDuration,";
        // $drawtext .= "if(gt(t,$fadeOutStart),($endTime-t)/$fadeOutDuration,1)))'";

        // Wave effect
        // Wave effect parameters
        // $waveHeight = 10; // Maximum pixels to move up and down
        // $wavePeriod = 2; // Time for one complete wave cycle in seconds
        // $waveCharOffset = 0.3; // Offset between characters in the wave (reduced for smoother wave)
        // $charSpacing = 1.0; // Adjust this value to change horizontal spacing between characters

        // // Line spacing
        // $lineHeight = $size * 1.5; // Adjust this value to change line spacing

        // // Split text into lines
        // $lines = explode("\n", $text);

        // // Construct the drawtext filter with wave effect for each line
        // $drawtext = "";
        // foreach ($lines as $lineIndex => $line) {
        //     $lineY = $y + $lineIndex * $lineHeight;
        //     $lineX = $x; // Reset X position for each new line

        //     for ($i = 0; $i < mb_strlen($line); $i++) {
        //         $char = mb_substr($line, $i, 1);
        //         $escapedChar = $this->escapeString($char);

        //         // Calculate X position with increased spacing
        //         $charX = "$lineX+{$i}*{$size}*$charSpacing";

        //         $drawtext .= "drawtext=text='$escapedChar':fontfile=$fontPath:fontsize=$size:fontcolor=$color:";
        //         $drawtext .= "x=$charX:";
        //         $drawtext .= "y='$lineY+$waveHeight*sin((t-$startTime)*2*PI/$wavePeriod+$i*$waveCharOffset)':";
        //         $drawtext .= "enable='between(t,$startTime,$endTime)':";
        //         $drawtext .= "alpha='if(lt(t,$startTime),0,if(lt(t,$startTime+$fadeInDuration),(t-$startTime)/$fadeInDuration,";
        //         $drawtext .= "if(gt(t,$fadeOutStart),($endTime-t)/$fadeOutDuration,1)))',";
        //     }
        // }
        // $drawtext = rtrim($drawtext, ',');

        // Slide effect
        // Effect parameters
        // $slideDuration = 0.5; // Duration of sliding effect in seconds
        // $lineHeight = $size * 1.5; // Adjust this value to change line spacing

        // // Split text into lines
        // $lines = explode("\n", $text);

        // // Construct the complex filter
        // $complex_filter = "";
        // foreach ($lines as $lineIndex => $line) {
        //     $lineY = $y + $lineIndex * $lineHeight;
        //     $lineStart = $start + $lineIndex * 0.1; // Stagger start time for each line
        //     $slideEnd = $lineStart + $slideDuration;

        //     $escapedLine = $this->escapeString($line);

        //     // Sliding effect
        //     $complex_filter .= "drawtext=fontfile=$fontPath:fontsize=$size:fontcolor=$color:";
        //     $complex_filter .= "text='$escapedLine':";
        //     $complex_filter .= "x='if(gte(t,$lineStart),max($x,w-tw-((t-$lineStart)/$slideDuration)*(w-$x)),w)':";
        //     $complex_filter .= "y=$lineY:";
        //     $complex_filter .= "enable='between(t,$lineStart,$end)',";
        // }
        // $complex_filter = rtrim($complex_filter, ',');
        // $drawtext = $complex_filter;

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
            $user = BirthdayUser::where('uniqid', $this->employeeId)->first();
            $user->video_url = "https://testlab.cfd/videos/{$user->uniqid}.mp4";
            $user->is_video_generated = 2;
            $user->save();
        }

        return ['status' => 200, 'message' => 'Text added to video successfully', 'output_path' => $outputPath];
    }

    public function convertVideoToGIF($videoPath, $user)
    {
        Log::info("Starting video to GIF conversion", [
            'employeeId' => $user->uniqid,
            'videoPath' => $videoPath
        ]);

        $inputPath = $videoPath;
        $outputPath = public_path("videos/{$user->uniqid}.gif");

        // Construct the FFmpeg command
        $command = "ffmpeg -y -i $inputPath -vf \"fps=10,scale=320:-1:flags=lanczos,split[s0][s1];[s0]palettegen[p];[s1][p]paletteuse=dither=floyd_steinberg\" -loop 1 $outputPath";

        Log::info("Executing FFmpeg command", [
            'employeeId' => $user->uniqid,
            'command' => $command
        ]);

        exec($command . " 2>&1", $output, $status);

        Log::info("FFmpeg command output", [
            'employeeId' => $user->uniqid,
            'output' => $output,
            'status' => $status
        ]);

        if ($status !== 0) {
            Log::error("Failed to convert video to GIF", [
                'employeeId' => $user->uniqid,
                'status' => $status,
                'output' => $output,
                'command' => $command,
                'inputPath' => $inputPath,
                'outputPath' => $outputPath
            ]);
            return ['status' => 500, 'message' => 'Failed to convert video to GIF', 'error' => implode("\n", $output)];
        }

        if (!file_exists($outputPath)) {
            Log::error("GIF file was not created", [
                'employeeId' => $user->uniqid,
                'outputPath' => $outputPath
            ]);
            return ['status' => 500, 'message' => 'GIF file was not created', 'outputPath' => $outputPath];
        }

        Log::info("Video to GIF conversion completed successfully", [
            'employeeId' => $user->uniqid,
            'outputPath' => $outputPath
        ]);

        // Update user's video URL
        $user->video_url = "https://testlab.cfd/videos/{$user->uniqid}.gif";
        $user->save();

        return ['status' => 200, 'message' => 'Video converted to GIF successfully', 'output_path' => $outputPath];
    }

    protected function escapeString($string)
    {
        $string = str_replace('\\', '\\\\', $string);
        $string = str_replace(':', '\\:', $string);
        $string = str_replace('\'', '\\\'', $string);
        return trim($string);
    }
}
