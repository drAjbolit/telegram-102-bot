<?php


$TOKEN = getenv('TOKEN');

function tg($method, $data = [])
{
    global $TOKEN;

    $ch = curl_init(
        "https://api.telegram.org/bot{$TOKEN}/{$method}"
    );

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CONNECTTIMEOUT => 10
    ]);

    $result = curl_exec($ch);

    curl_close($ch);

    return $result;
}

$update = json_decode(
    file_get_contents('php://input'),
    true
);

if (!$update) {
    http_response_code(200);
    exit;
}

$chat_id = $update['message']['chat']['id'] ?? null;
$text    = trim($update['message']['text'] ?? '');

if (!$chat_id) {
    http_response_code(200);
    exit;
}

if ($text === '/start') {

    $inline = [
        'inline_keyboard' => [
            [
                [
                    'text' => '🛍 Перейти к витрине',
                    'url'  => 'https://102procenta.ru/businka/?tg=1'
                ]
            ]
        ]
    ];

    tg('sendPhoto', [
        'chat_id' => $chat_id,

        // Загрузи сюда баннер
        'photo' => 'https://102procenta.ru/Upload_Bot/start.webp',

        'caption' =>
            "Авторские темлячные бусины ручной работы",

        'reply_markup' => json_encode(
            $inline,
            JSON_UNESCAPED_UNICODE
        )
    ]);

    http_response_code(200);
    exit;
}

// Для любых других сообщений
tg('sendMessage', [
    'chat_id' => $chat_id,
    'text' => 'Нажмите /start'
]);

http_response_code(200);
exit;
