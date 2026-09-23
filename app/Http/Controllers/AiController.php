<?php

namespace App\Http\Controllers;

use App\Services\SupportAiService;
use App\Services\SupportService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function index(Request $request, SupportService $support)
    {
        $conversation = $request->user()
            ? $support->conversationForUser($request->user()->id)
            : null;

        $messages = $conversation ? $support->messages((int)$conversation->id, 120) : collect();
        $channels = $support->channels();

        return view('storefront.ai-assistant', compact('conversation','messages','channels'));
    }

    public function ask(Request $request, SupportAiService $ai)
    {
        $data = $request->validate([
            'message' => 'required|string|max:2000',
            'conversation_id' => 'nullable|integer',
        ]);

        if (!$request->user()) {
            return response()->json([
                'ok' => false,
                'message' => 'Please sign in so the AI can securely access your orders, account and support history.',
                'login_url' => route('auth.login'),
            ], 401);
        }

        try {
            $result = $ai->reply(
                $request->user(),
                $data['message'],
                $data['conversation_id'] ?? null
            );

            return response()->json(['ok'=>true] + $result);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'ok'=>false,
                'message'=>'The AI could not safely complete this request. Please contact a support agent.',
                'escalate'=>true,
            ], 500);
        }
    }
}
