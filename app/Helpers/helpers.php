<?php
// Daftarkan helper di composer.json
// dan jalankan menggunakan composer dump-autoload
if (!function_exists('formatRupiah')) {
    function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

function sendTelegramMessage($message, $chat_id)
{
    $token = settings('token_bot');
    
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    Http::post($url, $data);
}

