<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionStoreRequest;
use App\Services\QuestionService;
use Exception;

class QuestionController extends Controller
{
    public function __construct(
        private readonly QuestionService $questionService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = $this->questionService->getAll();

        return response()->json([
            'status' => 200,
            'message' => 'successfully get all questions',
            'data' => $questions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuestionStoreRequest $request)
    {
        $input = $request->validated();

        try {
            $result = $this->questionService->create($input);
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($result);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $question = $this->questionService->get($id);

        return response()->json([
            'status' => 200,
            'message' => 'successfully get question',
            'data' => $question
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $result = $this->questionService->delete($id);
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($result);
    }
}
