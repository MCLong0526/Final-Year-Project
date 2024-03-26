import App from '@/App.vue';
import { registerPlugins } from '@core/utils/plugins';
import { createApp } from 'vue';

// Styles
import '@core-scss/template/index.scss';
import '@layouts/styles/index.scss';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

// Create vue app

const app = createApp(App)


// Register plugins
registerPlugins(app)

// Mount vue app
app.use(VueDatePicker);
app.mount('#app')
