<?php
function Tele($user_id, $text) {
    $token = "7657734664:AAGpbkasmOSmXcldRWOeg5xpJSkkwl-cTWU";
    $url = "https://api.telegram.org/bot$token/sendMessage";

        $name = $user->name;

        // Construct the message with proper emoji
        $message = "Hi *$name* 👋\n\n$text\n-------------\n*This broadcast message is sent automatically by the bot, please don't reply to it!*";

        // Create inline keyboard
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Accept', 'callback_data' => 'accept_action'],
                    ['text' => '❌ Decline', 'callback_data' => 'decline_action']
                ],
                [
                    ['text' => '📋 View Details', 'callback_data' => 'view_details'],
                    ['text' => '🔗 Open Link', 'url' => 'https://example.com']
                ]
            ]
        ];

        // Prepare data to send to Telegram API
        $data = [
            "chat_id" => $user_id,
            "text" => $message,
            "parse_mode" => "Markdown",
            "reply_markup" => json_encode($keyboard)
        ];

        // Send the request to Telegram API using curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
   
}


Tele(890657047, "Hello, this is a test message!");

?>