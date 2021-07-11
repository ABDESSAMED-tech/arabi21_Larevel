<?php

return [

    'channel_id' => env('YT_CHANNEL_ID'),
    'key1' => env('YT_API_KEY1'),
    'key2' => env('YT_API_KEY2'),
    'key3' => env('YT_API_KEY3'),
    'local' => env('APP_LOCAL', "en"),
    'yt_search_api' => env('YT_SEARCH_API', 'https://youtube.googleapis.com/youtube/v3/search'),
    'yt_playlist_api' => env('YT_PLAYLIST_API', 'https://youtube.googleapis.com/youtube/v3/playlistItems'),
    'rss' => env('RSS_BASE_URL', "https://arabi21.com/Rss/SectionNewsRSS?id="),
];
