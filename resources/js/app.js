import './bootstrap';

import.meta.glob(["../images/**", "../fonts/**"]);

import { createApp } from "vue";
import App from "./components/App.vue";

const app = createApp(App);

app.mount("#app");
