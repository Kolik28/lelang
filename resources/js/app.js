//

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
import './bootstrap';

import { createApp } from 'vue';
import App from './App.vue';

createApp(App).mount('#app');