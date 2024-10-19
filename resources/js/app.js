import './bootstrap';

import.meta.glob(["../images/**", "../fonts/**"]);

import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "./router/index";

import App from "./components/App.vue";

// Vuetify
import "vuetify/styles";
import { createVuetify } from "vuetify";
import { aliases, mdi } from "vuetify/iconsets/mdi";
import colors from "vuetify/util/colors";
import "@mdi/font/css/materialdesignicons.css";

const vuetify = createVuetify({
    theme: {
        defaultTheme: "dark",
        themes: {
            dark: {
                dark: true,
                colors: {
                    primary: colors.teal.darken4,
                    danger: colors.red.base,
                },
            },
        },
    },
    icons: {
        defaultSet: "mdi",
        aliases,
        sets: {
            mdi,
        },
    },
});

const app = createApp(App);

app.use(createPinia()).use(router).use(vuetify).mount("#app");
