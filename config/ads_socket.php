<?php

return [
    'enabled' => env('ADS_SOCKET_ENABLED', false),
    'server_url' => env('ADS_SOCKET_SERVER_URL', 'https://nodeapp.bookkhata.com/'),
    'publish_token' => env('ADS_SOCKET_PUBLISH_TOKEN', ''),
    'request_timeout' => env('ADS_SOCKET_TIMEOUT', 3),
];
