<script setup>
    import AuthLayout from '../layouts/AuthLayout.vue';

    import { ref } from 'vue';

    const loading = ref(false);

    function onSubmit() {
        loading.value = true;

        setTimeout(() => loading.value = false, 2000)
    }
</script>

<template>
    <AuthLayout>
        <v-responsive class="pa-4">
            <v-card class="px-6 py-8 mx-auto" max-width="344">
                <v-form @submit.prevent="onSubmit">
                    <v-text-field class="mb-2" label="Email" name="email" type="email"
                        prepend-inner-icon="mdi-at"></v-text-field>

					<div class="mt-n3 text-end">
						<router-link :to="{name: 'login'}" class="text-caption hover-blue">Вход на сайт</router-link>
					</div>

                    <br>

                    <v-btn :disabled="loading" :loading="loading" color="primary" size="large" type="submit"
                        variant="elevated" block>
                        Напомнить
                    </v-btn>
                </v-form>
            </v-card>
        </v-responsive>
    </AuthLayout>
</template>

<style scoped>
.hover-blue {
	transition: color .3s ease;
}
.hover-blue:hover {
	color: aquamarine;
}
</style>
