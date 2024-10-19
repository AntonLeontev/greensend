import AboutView from "@/components/AboutView.vue";
import HomeView from "@/components/HomeView.vue";
import ForgotPassword from "@/pages/ForgotPassword.vue";
import Login from "@/pages/Login.vue";

export default [
    { path: "/", component: HomeView, name: "home", meta: { auth: true } },
    {
        path: "/about",
        component: AboutView,
        name: "about",
        meta: { auth: true },
    },
    { path: "/login", component: Login, name: "login" },
    {
        path: "/forgot-password",
        component: ForgotPassword,
        name: "forgot-password",
    },
];
