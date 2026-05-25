import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ── Laravel Echo — Real-time via Reverb ──────────────────────────────────────
declare global {
    interface Window {
        axios: typeof axios;
        Echo: Echo<'reverb'>;
        Pusher: typeof Pusher;
    }
}

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key:         import.meta.env.VITE_REVERB_APP_KEY as string,
    wsHost:      import.meta.env.VITE_REVERB_HOST    as string ?? window.location.hostname,
    wsPort:      import.meta.env.VITE_REVERB_PORT    as number ?? 8080,
    wssPort:     import.meta.env.VITE_REVERB_PORT    as number ?? 8080,
    forceTLS:    (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});

