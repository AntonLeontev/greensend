<script setup>
import Toasts from '@/components/Toasts.vue';
import { useUserStore } from '@/stores/user';
import { useRouter } from 'vue-router';


const userStore = useUserStore();
const router = useRouter();

function logout() {
	userStore.logout()

	router.push({name: 'login'});
}
</script>

<template>
    <v-app>
        <v-app-bar class="px-2">
			<span class="text-h4">Greensend</span>
			<v-spacer></v-spacer>
            <v-btn prepend-icon="mdi-logout" text="Выйти" @click="logout"></v-btn>
        </v-app-bar>

        <v-navigation-drawer>
            <v-list>
                <v-list-item>
                    <RouterLink :to="{ name: 'home' }">Home</RouterLink>
                </v-list-item>
                <v-list-item>
                    <RouterLink :to="{ name: 'about' }">About</RouterLink>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-main class="justify-center d-flex align-center" style="min-height: 300px;">
            <slot></slot>
        </v-main>
    </v-app>

	<Toasts />
</template>
