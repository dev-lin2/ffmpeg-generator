<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateVideoRequest;
use App\Services\Api\VideoService;

class VideoController extends Controller
{
    public $videoService;
    public $url = "http://testlab.cfd/video-generator";

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
        $overlayVideo = public_path('overlay.mp4');

        $this->videoService->setOutputPath($request->employee_id);

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
            7.5,
            [1100, 200],
            $font,
            72,
            'white',
            0.1,
            public_path("videos/{$request->employee_id}.mp4")
        );

        $this->videoService->addTextToVideo(
            "{$wish2}",
            8,
            12,
            [1100, 200],
            $font,
            72,
            'white',
            0.1,
            public_path("videos/{$request->employee_id}.mp4")
        );

        $this->videoService->addVideoToVideo(
            public_path("videos/{$request->employee_id}.mp4"),
            4,
            11,
            [1100, 700],
            $overlayVideo
        );

        return response()->json(
            [
                'status' => 202,
                'message' => 'Video generation jobs have been queued',
                'expected_video_path' => "{$this->url}/videos/{$request->employee_id}.mp4"
            ],
            202
        );
    }
}
