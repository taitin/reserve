<?php

return [
    'channel_id' => env('LINE_CHANNEL_ID'),
    'secret' => env('LINE_SECRET'),
    'bot_channel_id' => env('LINE_BOT_CHANNEL_ID'),
    'bot_secret' => env('LINE_BOT_SECRET'),
    'authorize_base_url' => 'https://access.line.me/oauth2/v2.1/authorize',
    'get_access_token_url' => 'https://api.line.me/v2/oauth/accessToken',
    'get_token_url' => 'https://api.line.me/oauth2/v2.1/token',
    'get_user_profile_url' => 'https://api.line.me/v2/profile',
    'bot_data_api_url' => 'https://api-data.line.me/v2/bot',
    'bot_api_url' => 'https://api.line.me/v2/bot',
];
