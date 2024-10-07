<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateVideoRequest;
use App\Services\Api\VideoService;

class VideoController extends Controller
{
    public $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function generateVideo(GenerateVideoRequest $request)
    {
        $name = "Dear " . $request->name . " さん";
        $wish1 = $request->wish1;
        $wish2 = $request->wish2;

        $font = public_path('FUTENE.ttf');

        $this->videoService->setOutputPath($request->employee_id);

        $this->videoService->addTextToVideo(
            "{$name}",
            0.5,
            3.5,
            [720, 100],
            $font,
            50,
            'white'
        );

        $this->videoService->addTextToVideo(
            "{$wish1}",
            3.8,
            7.5,
            [1100, 200],
            $font,
            50,
            'white',
            public_path("videos/{$request->employee_id}.mp4")
        );

        $this->videoService->addTextToVideo(
            "{$wish2}",
            8,
            12,
            [1100, 200],
            $font,
            50,
            'white',
            public_path("videos/{$request->employee_id}.mp4")
        );

        return response()->json(
            [
                'status' => 202,
                'message' => 'Video generation jobs have been queued',
                'expected_video_path' => url('public/videos/' . $request->employee_id . '.mp4')
            ],
            202
        );
    }
}
