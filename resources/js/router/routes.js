import ForgotPassword from "@/pages/auth/ForgotPassword.vue";
import ResetPassword from "@/pages/auth/ResetPassword.vue";
import Login from "@/pages/auth/Login.vue";

export default [
    {
        path: "/",
        component: () => import("@/pages/UploadedFiles.vue"),
        name: "home",
        meta: { auth: true },
    },
    {
        path: "/whats-app-check",
        component: () => import("@/pages/WhatsAppCheck.vue"),
        name: "whats-app-check",
        meta: { auth: true },
    },
    {
        path: "/distributions",
        component: () => import("@/pages/Distributions.vue"),
        name: "distributions",
        meta: { auth: true },
    },
    {
        path: "/new-distribution",
        component: () => import("@/pages/NewDistribution.vue"),
        name: "new-distribution",
        meta: { auth: true },
    },
    {
        path: "/channels",
        component: () => import("@/pages/Channels.vue"),
        name: "channels",
        meta: { auth: true },
    },
    {
        path: "/chats",
        component: () => import("@/pages/Chats.vue"),
        name: "chats",
        meta: { auth: true },
    },
    { path: "/login", component: Login, name: "login" },
    {
        path: "/forgot-password",
        component: ForgotPassword,
        name: "forgot-password",
    },
    {
        path: "/reset-password",
        component: ResetPassword,
        name: "reset-password",
    },
];
