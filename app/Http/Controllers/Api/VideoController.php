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

        $video = $this->videoService->addTextToVideo(
            "{$name}",
            0.5,
            3.5,
            [720, 100],
            $font,
            50,
            'white'
        );

        if ($video['status'] !== 200) {
            return response()->json($video, 400);
        }

        $video = $this->videoService->addTextToVideo(
            "{$wish1}",
            3.5,
            7.5,
            [1100, 200],
            $font,
            50,
            'white',
            $video['output_path']
        );

        if ($video['status'] !== 200) {
            return response()->json($video, 400);
        }

        $video = $this->videoService->addTextToVideo(
            "{$wish2}",
            8,
            12,
            [1100, 200],
            $font,
            50,
            'white',
            $video['output_path']
        );

        return response()->json(
            [
                'status' => 200,
                'message' => 'Video generated successfully',
                'video_path' => url('public/videos/' . $request->employee_id . '.mp4')
            ],
            200
        );
    }
}
