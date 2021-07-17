<?php

return [
    "rss" => [
        "index" => env('RSS_INDEX_URL', 'https://arabi21.com/rss'),
        "url" => env('RSS_BASE_URL', "https://arabi21.com/Rss/SectionNewsRSS?id="),
    ],
    "post_base_url" => env('POST_BASE_URL', "https://arabi21.com/story/"),
];
