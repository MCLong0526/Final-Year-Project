<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>UNIMAS Life Enhancement App</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('loader.css') }}" />
    @vite(['resources/js/main.js'])
</head>

<body>
    <div id="app">
        <div id="loading-bg">
            <div class="loading-logo">
                <!-- Use the LOGO1.png image -->
                <img src="/images/logos/LOGO1.png" alt="UNIMAS Logo" style="width: 100px; height: auto;">
            </div>
            <div class="loading">
                <div class="effect-1 effects"></div>
                <div class="effect-2 effects"></div>
                <div class="effect-3 effects"></div>
            </div>
        </div>
    </div>

    <script>
        const loaderColor = localStorage.getItem('materio-initial-loader-bg') || '#FFFFFF'
        const primaryColor = localStorage.getItem('materio-initial-loader-color') || '#9155FD'

        if (loaderColor)
            document.documentElement.style.setProperty('--initial-loader-bg', loaderColor)

        if (primaryColor)
            document.documentElement.style.setProperty('--initial-loader-color', primaryColor)
    </script>
    <!-- Include Pusher script -->
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <!-- Include Echo script -->
    <script>
        import Echo from 'laravel-echo';

        window.Pusher = require('pusher-js');

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: 'bfdcd4030f09a5a101b7',
            cluster: 'ap1',
            forceTLS: true
        });
    </script>
</body>

</html>
