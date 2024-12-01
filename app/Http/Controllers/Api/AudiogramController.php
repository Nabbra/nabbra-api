<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AudiogramRequest;
use App\Http\Resources\AudiogramResource;
use App\Models\Audiogram;
use Illuminate\Validation\ValidationException;

class AudiogramController extends Controller
{
    /**
     * List user audiograms.
     */
    public function index()
    {
        $audiograms = auth()->user()->audiograms()->get();

        return AudiogramResource::collection($audiograms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AudiogramRequest $request)
    {
        if (auth()->user()->audiograms()->where('type', $request->input('type'))->exists()) {
            throw ValidationException::withMessages([
                'freqs' => ['The frequency already exists.'],
            ]);
        }

        $audiogram = auth()->user()->audiograms()->create($request->validated());

        return new AudiogramResource($audiogram);
    }

    /**
     * Display the specified resource.
     */
    public function show(Audiogram $audiogram)
    {
        return new AudiogramResource($audiogram);
    }
}
