<?php

namespace App\Services;

use App\Models\BirthdayUser;
use App\Models\TemplateVideo;
use App\Services\Api\VideoService;

class AdminVideoService
{
    public $videoService;
    public $lineWorkService;

    public function __construct(VideoService $videoService, LineWorkService $lineWorkService)
    {
        $this->videoService = $videoService;
        $this->lineWorkService = $lineWorkService;
    }

    public function generateVideo($data)
    {
        $userIds = $data['user_ids'];
        $templateId = $data['template_id'];

        $video = TemplateVideo::find($templateId);

        // Here you can process the video generation for each user
        foreach ($userIds as $userId) {
            $user = BirthdayUser::find($userId);
            $name = "Dear " . $user->name . " さん";
            $wish1 = $video->wish_text_1;
            $wish2 = $video->wish_text_2;
            $font = public_path('SAWARIBI.ttf');

            // Set the output path for the video
            $this->videoService->setOutputPath($user->employee_id);

            // Add text to the video
            $this->videoService->addTextToVideo(
                "{$name}",
                0.5,
                3.5,
                [720, 100],
                $font,
                50,
                'white',
                0.1
            );

            $this->videoService->addTextToVideo(
                "{$wish1}",
                4,
                13,
                [1000, 200],
                $font,
                50,
                'white',
                0.05,
                public_path("videos/{$user->employee_id}.mp4")
            );

            $this->videoService->addTextToVideo(
                "{$wish2}",
                8,
                13,
                [1000, 400],
                $font,
                50,
                'white',
                0.05,
                public_path("videos/{$user->employee_id}.mp4")
            );

            // I want to update the user's video_url and is_video_generated after the video is generated
            $user->update([
                'video_url' => url("videos/{$user->employee_id}.mp4"),
                'is_video_generated' => true,
                'template_video_id' => $templateId,
            ]);
        }

        // Return some response or process result
        return [
            'success' => true,
            'message' => 'Video generation started for ' . count($userIds) . ' users.',
        ];
    }

    public function sendVideo($data)
    {
        $thumbnailUrl = url('bd.png');

        // Here you can send the video to each user
        foreach ($data as $user) {
            // The video is /public/videos/{$user->employee_id}.mp4
            $videoUrl = url("videos/{$user->employee_id}.mp4");

            $this->lineWorkService->sendVideo($user->email, $videoUrl, $thumbnailUrl);

            // Update the user's is_video_sent after the video is sent
            $user->update([
                'is_wish_sent' => true,
            ]);
        }
    }
}
