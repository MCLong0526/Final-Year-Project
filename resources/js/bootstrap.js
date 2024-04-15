import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'bfdcd4030f09a5a101b7',
    cluster: 'ap1',
    encrypted: true
});
