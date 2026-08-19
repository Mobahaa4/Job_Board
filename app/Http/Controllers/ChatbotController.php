<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    public function __construct(private readonly ChatbotService $chatbot) {}

    public function index(): View
    {
        $history = auth()->user()?->chatMessages()->latest()->take(50)->get()->reverse();

        return view('chatbot.index', compact('history'));
    }

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $response = $this->chatbot->respond($data['message'], auth()->user());

        if (auth()->check()) {
            ChatMessage::create([
                'user_id' => auth()->id(),
                'message' => $data['message'],
                'response' => $response['text'],
            ]);
        }

        return response()->json([
            'reply' => $response['text'],
            'type' => $response['type'],
            'job_ids' => $response['job_ids'] ?? [],
        ]);
    }
}
