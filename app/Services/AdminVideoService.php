<?php

namespace App\Services;

use App\Models\BirthdayUser;
use App\Models\BirthdayVideoRecord;
use App\Models\TemplateVideo;
use App\Models\WishText;
use App\Services\Api\VideoService;
use Carbon\Carbon;

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
        $wishText = WishText::find(1);
        $userIds = $data['user_ids'];

        $text1 = $data['wish_text_a'];
        $text2 = $data['wish_text_b'];
        $text3 = $data['wish_text_c'];

        foreach ($userIds as $userId) {
            $log = new BirthdayVideoRecord();
            
            $user = BirthdayUser::find($userId);

            // Reset video generation status
            $user->update([
                'video_url' => null,
                'is_video_generated' => false,
            ]);

            $name = $user->last_name . '' . $user->first_name;
            $department = $user->department;

            $joinDate = Carbon::parse($user->join_date);
            $now = Carbon::now();
            $years = $now->diffInYears($joinDate);
            $months = $now->diffInMonths($joinDate) % 12;
            $totalWorkingDuration = "{$years}年{$months}ヶ月";

            $wishes = [
                str_replace(['{name}', '{department}', '{duration}'], [$name, $department, $totalWorkingDuration], $text1),
                str_replace(['{name}', '{department}', '{duration}'], [$name, $department, $totalWorkingDuration], $text2),
                str_replace(['{name}', '{department}', '{duration}'], [$name, $department, $totalWorkingDuration], $text3),
            ];
            
            // $wishes = [
            //     str_replace(['{name}', '{department}', '{duration}'], [$name, $department, $totalWorkingDuration], $wishText->{"wish_1_text_" . rand(1, 1)}),
            //     str_replace(['{name}', '{department}', '{duration}'], [$name, $department, $totalWorkingDuration], $wishText->{"wish_2_text_" . rand(1, 1)}),
            //     str_replace(['{name}', '{department}', '{duration}'], [$name, $department, $totalWorkingDuration], $wishText->{"wish_3_text_" . rand(1, 1)})
            // ];

            // dd($wishes);

            $font = public_path('NOTOSANS_SEMI.ttf');

            $this->videoService->setOutputPath($user->employee_id);

            $this->videoService->addTextToVideo(
                $wishes[0],
                0.5,
                10,
                [520, 250],
                $font,
                40,
                'black',
                0.03,
            );

            $this->videoService->addTextToVideo(
                $wishes[1],
                10,
                19,
                [900, 250],
                $font,
                40,
                'black',
                0.03,
                public_path("videos/{$user->employee_id}.mp4")
            );

            $this->videoService->addTextToVideo(
                $wishes[2],
                20,
                30,
                [1000, 250],
                $font,
                40,
                'black',
                0.03,
                public_path("videos/{$user->employee_id}.mp4"),
                true
            );

            $log->create([
                'birthday_user_id' => $userId,
                'wish_text_1' => $wishes[0],
                'wish_text_2' => $wishes[1],
                'wish_text_3' => $wishes[2],
            ]);
        }

        return [
            'success' => true,
            'message' => 'Video generation started for ' . count($userIds) . ' users.',
        ];
    }

    public function sendVideo($data)
    {
        $thumbnailUrl = url('bd.png');

        foreach ($data as $user) {
            $videoUrl = url("videos/{$user->employee_id}.mp4");

            $this->lineWorkService->sendVideo($user->email, $videoUrl, $thumbnailUrl);

            $user->update([
                'is_wish_sent' => true,
            ]);
        }
    }
}
