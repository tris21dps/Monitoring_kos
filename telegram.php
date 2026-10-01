<?php

function kirimTelegram($botToken, $chatId, $pesan)
{
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";

    $data = [
        'chat_id' => $chatId,
        'text' => $pesan
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);

    if ($response === false) {
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    return true;
}
?>