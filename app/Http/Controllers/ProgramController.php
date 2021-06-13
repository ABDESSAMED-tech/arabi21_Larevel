<?php

namespace App\Http\Controllers;

use \App\Http\Resources\Program as ProgramResource;
use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Video;

;

use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        return ProgramResource::collection(
            Program::whereActive(1)
                ->whereProcessed(1)
                ->has("videos")
                ->orderBy('order', 'desc')
                ->orderByDesc(Video::select('published_on')
                    ->whereColumn('videos.program_id', 'programs.id')
                    ->latest()
                    ->take(1)
                )
                ->get()
        );
    }

    public function show(Program $program)
    {
        return new ProgramResource($program);
    }
}
