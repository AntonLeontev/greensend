import './bootstrap';

import.meta.glob(["../images/**", "../fonts/**"]);

import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "@/router/index";
import vuetify from "@/vuetify";

import App from "@/components/App.vue";

const app = createApp(App);

app.use(createPinia()).use(router).use(vuetify).mount("#app");
