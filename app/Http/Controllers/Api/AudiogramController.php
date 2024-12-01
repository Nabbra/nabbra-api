<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AudiogramRequest;
use App\Http\Resources\AudiogramResource;
use App\Models\Audiogram;

class AudiogramController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(AudiogramRequest $request)
    {
        auth()->user()->audiograms()->create($request->validated());

        return response()->json([
            'message' => 'Audiogram saved successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Audiogram $audiogram)
    {
        return new AudiogramResource($audiogram);
    }
}
