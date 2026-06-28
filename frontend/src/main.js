import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import echo from './echo'

const app = createApp(App)

app.provide('echo', echo)
app.use(createPinia())
app.use(router)
app.mount('#app')