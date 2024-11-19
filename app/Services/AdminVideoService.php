<?php

namespace App\Services;

use App\Jobs\ProcessConvertVideo;
use App\Models\BirthdayUser;
use App\Models\BirthdayVideoRecord;
use App\Models\WishText;
use App\Services\Api\VideoService;
use Carbon\Carbon;
use App\Jobs\ProcessVideo;
use Illuminate\Support\Facades\Bus;

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

            // Delete old video and gif, check video and gif exist first
            $video = public_path("videos/{$user->uniqid}.mp4");
            $gif = public_path("videos/{$user->uniqid}.gif");

            if (file_exists($video)) {
                unlink($video);
            }

            if (file_exists($gif)) {
                unlink($gif);
            }            

            // Generate random string with 50 characters using employee_id
            $uniqid = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 50)), 0, 50);

            // Mix uniqid with employee_id and generate a new employee_id
            $employeeId = $uniqid;

            // Save the new employee_id to uniqid
            $user->update([
                'uniqid' => $employeeId,
            ]);

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

            $font = public_path('NOTOSANS_SEMI.ttf');

            $this->videoService->setOutputPath($user->uniqid);

            $baseVideoPath = public_path("videos/{$user->uniqid}.mp4");

            $jobs = [
                new ProcessVideo($user->uniqid, $wishes[0], 2, 10, [650, 250], $font, 40, 'black', 0.03, null, false),
                new ProcessVideo($user->uniqid, $wishes[1], 11, 19, [900, 250], $font, 40, 'black', 0.03, $baseVideoPath, false),
                new ProcessVideo($user->uniqid, $wishes[2], 21, 30, [1000, 250], $font, 40, 'black', 0.03, $baseVideoPath, true),
                new ProcessConvertVideo($user, $baseVideoPath),
            ];

            Bus::chain($jobs)->dispatch();

            $log->create([
                'birthday_user_id' => $userId,
                'wish_text_1' => $wishes[0],
                'wish_text_2' => $wishes[1],
                'wish_text_3' => $wishes[2],
            ]);

            $user->is_video_generated = 1;
            $user->save();
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
            $videoUrl = url("videos/{$user->uniqid}.mp4");

            $this->lineWorkService->sendVideo($user->email, $videoUrl, $thumbnailUrl);

            $user->update([
                'is_wish_sent' => true,
            ]);
        }
    }
}
