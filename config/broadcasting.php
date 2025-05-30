    <?php

    return [

        /*
        |--------------------------------------------------------------------------
        | Default Broadcaster
        |--------------------------------------------------------------------------
        |
        | This option controls the default broadcaster that will be used by the
        | framework when an event needs to be broadcast. You may set this to
        | any of the connections defined in the "connections" array below.
        |
        | Supported: "pusher", "ably", "redis", "log", "null"
        |
        */

        'default' => env('BROADCAST_DRIVER', 'null'),

        /*
        |--------------------------------------------------------------------------
        | Broadcast Connections
        |--------------------------------------------------------------------------
        |
        | Here you may define all of the broadcast connections that will be used
        | to broadcast events to other systems or over websockets. Samples of
        | each available type of connection are provided inside this array.
        |
        */

        'connections' => [

            'pusher' => [
                'driver' => 'pusher',
                'key' => 'bfdcd4030f09a5a101b7',
                'secret' => 'c57a78669b3f5a541990',
                'app_id' => '1787685',
                'options' => [
                    'cluster' => 'ap1',
                ],
                'client_options' => [
                    'cluster' => 'ap1',
                    'useTLS' => true,
                ],
            ],

            'ably' => [
                'driver' => 'ably',
                'key' => env('ABLY_KEY'),
            ],

            'redis' => [
                'driver' => 'redis',
                'connection' => 'default',
            ],

            'log' => [
                'driver' => 'log',
            ],

            'null' => [
                'driver' => 'null',
            ],

        ],

    ];
