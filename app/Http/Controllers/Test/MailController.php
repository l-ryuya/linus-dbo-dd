<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class MailController extends Controller
{
    /**
     * メールを送信するアクション
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMail(Request $request)
    {
        try {
            // メール送信処理
            Mail::raw('テストメールの本文です', function (Message $message) use (
                $request
            ) {
                $message->to($request->input('email'))
                        ->subject('テストメール');
            });

            return response()->json([
                'success' => true,
                'message' => 'メールが正常に送信されました',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'メール送信に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
