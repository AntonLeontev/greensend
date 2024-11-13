<script setup>
	import axios from 'axios';
	import {ref} from "vue";
import { route } from 'ziggy-js';


	const emit = defineEmits(['channelCreated']);
	const isActive = ref(false);
	const loading = ref(false);
	const error = ref('')
	const form = ref()

	function submit() {
		loading.value = true;
		error.value = '';

		axios.post(route('channels.store'), new FormData(form.value))
			.then(response => {
				emit('channelCreated', response.data)
				isActive.value = false
			})
			.catch(err => error.value = err.response?.data?.message ?? err.message)
			.finally(() => loading.value = false)
	}
	function copyWebhook() {
		navigator.clipboard.writeText(route('webhooks.wamm'))
	}
</script>

<template>
    <v-dialog max-width="600" v-model="isActive">
        <template v-slot:activator="{ props: activatorProps }">
            <v-btn v-bind="activatorProps" prepend-icon="mdi-plus" color="info" variant="tonal">
                Добавить номер
            </v-btn>
        </template>

        <template v-slot:default="{ isActive }">
            <v-card prepend-icon="mdi-numeric" title="Добавление номера WhatsApp">
                <template v-slot:text>
					<div class="mb-6">
						<v-btn href="/add_channel_instruction.pdf" target="_blank" variant="text" prepend-icon="mdi-information" density="comfortable">Инструкция</v-btn>
					</div>
                    <form ref="form" @submit.prevent="submit">
                        <v-text-field clearable label="Номер" name="number"></v-text-field>
                        <v-text-field clearable label="Токен API" name="token"></v-text-field>
                        <v-text-field clearable label="Название" name="label"></v-text-field>
                    </form>
					<div class="text-center text-danger">{{ error }}</div>

					<div class="mt-2">
						Не забудьте указать в настройках Wamm для этого номера вебхук: 
					</div>
					<div class="d-flex ga-2 align-center">
						{{ route('webhooks.wamm') }}
						<v-hover>
							<template v-slot:default="{ isHovering, props }">
								<v-btn variant="text" icon="mdi-content-copy" density="comfortable" @click="copyWebhook" :color="isHovering ? 'white' : 'grey'" v-bind="props"></v-btn>
							</template>
						</v-hover>
					</div>
                </template>
                <template v-slot:actions>
                    <v-btn class="ml-auto" text="Закрыть" variant="plain" @click="isActive.value = false"></v-btn>
                    <v-btn color="info" text="Сохранить" variant="tonal" :loading="loading" :disabled="loading" @click="submit"></v-btn>
                </template>
            </v-card>
        </template>
    </v-dialog>

</template>
