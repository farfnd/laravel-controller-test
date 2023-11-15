<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoiceStoreRequest;
use App\Models\Voice;
use App\Services\VoiceService;
use Exception;

class VoiceController extends Controller
{
    public function __construct(
        private readonly VoiceService $voiceService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return [
            'status' => 200,
            'message' => 'successfully get all questions',
            'data' => $this->voiceService->getAll()
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VoiceStoreRequest $request)
    {
        try {
            $input = $request->validated();
            $result = $this->voiceService->createVote($input);

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'error' => $e->getMessage()
            ]);
        }
    }
}
