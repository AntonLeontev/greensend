<script setup>
import Toasts from '@/components/Toasts.vue';
import { useUserStore } from '@/stores/user';
import { useRouter, useRoute } from 'vue-router';


const userStore = useUserStore();
const router = useRouter();
const route = useRoute();

function logout() {
	userStore.logout()

	router.push({name: 'login'});
}
</script>

<template>
    <v-app>
        <v-app-bar class="px-2" image="https://cdn.vuetifyjs.com/images/backgrounds/vbanner.jpg">
			<span class="text-h4">Greensend</span>
			<v-spacer></v-spacer>
            <v-btn prepend-icon="mdi-logout" text="Выйти" @click="logout"></v-btn>
        </v-app-bar>

        <v-navigation-drawer>
            <v-list>
                <v-list-item>
                    <RouterLink :to="{ name: 'home' }" class="d-flex ga-1" :class="route.name === 'home' ? 'text-info' : ''">
						<v-icon icon="mdi-file-send-outline"></v-icon>
						Загрузка файлов
					</RouterLink>
                </v-list-item>
                <v-list-item>
                    <RouterLink :to="{ name: 'whats-app-check' }" class="d-flex ga-1" :class="route.name === 'whats-app-check' ? 'text-info' : ''">
						<v-icon icon="mdi-whatsapp"></v-icon>
						Проверить номер в WhatsApp
					</RouterLink>
                </v-list-item>
                <v-list-item>
                    <RouterLink :to="{ name: 'new-distribution' }" class="d-flex ga-1" :class="route.name === 'new-distribution' ? 'text-info' : ''">
						<v-icon icon="mdi-message-plus"></v-icon>
						Создать рассылку
					</RouterLink>
                </v-list-item>
                <v-list-item>
                    <RouterLink :to="{ name: 'distributions' }" class="d-flex ga-1" :class="route.name === 'distributions' ? 'text-info' : ''">
						<v-icon icon="mdi-message-reply-text"></v-icon>
						Список рассылок
					</RouterLink>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-main>
			<v-container class="justify-center d-flex">
				<slot></slot>
			</v-container>
        </v-main>
    </v-app>

	<Toasts />
</template>
