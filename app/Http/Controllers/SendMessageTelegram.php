<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SendMessageTelegram extends Controller
{
    public function sendMessageApi(Request $request)
    {
        $message = $request->input('message');
        $chat_id = $request->input('chat_id');

        if (!$message || !$chat_id) {
            return response()->json(['error' => 'Pesan dan Chat ID diperlukan'], 400);
        }

        $response = $this->sendMessage($message, $chat_id);

        if (!$response->successful() || !$response->json('ok')) {
            // kalau gagal, Telegram biasanya kasih error_description
            return response()->json([
                'success' => false,
                'error' => $response->json('description') ?? 'Gagal mengirim pesan, chat_id tidak valid.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim ke Telegram'
        ]);
    }

    public function sendPhotoApi(Request $request)
    {
        $filePath = $request->input('file_path');
        $chat_id = $request->input('chat_id');
        $caption = $request->input('caption');

        if (!$filePath || !$chat_id) {
            return response()->json(['error' => 'File Path dan Chat ID diperlukan'], 400);
        }

        $this->sendPhoto($filePath, $chat_id, $caption);

        return response()->json(['success' => true, 'message' => 'Pesan Foto berhasil dikirim']);
    }

    public function sendMessage($message, $chat_id)
    {
        $token = settings('token_bot');
        
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        
        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];

        $response = Http::post($url, $data);
        return $response;
    }

    public function sendPhoto($filePath, $chat_id, $caption = null)
    {
        $token = settings('token_bot');

        $url = "https://api.telegram.org/bot{$token}/sendPhoto";

        $data = [
            'chat_id' => $chat_id,
            'caption' => $caption ?? '',
            'parse_mode' => 'HTML',
            'photo' => new \CURLFile(realpath($filePath)), // Pastikan path benar
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: multipart/form-data"
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode($response, true);
    }
}
