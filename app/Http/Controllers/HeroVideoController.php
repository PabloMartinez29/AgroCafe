<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HeroVideoController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $path = storage_path('app/videos/gemini_generated_video_079d102d.mp4');

        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => 'video/mp4',
            'Content-Disposition' => 'inline',
            'Accept-Ranges' => 'bytes',
        ]);
    }
}
