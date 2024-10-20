<script setup>
	import axios from 'axios';
	import AppLayout from '../layouts/AppLayout.vue';
	import { ref } from "vue";
	import { useToastsStore } from "@/stores/toasts";

	const toastsStore = useToastsStore();

	const loading = ref(false)
	const result = ref(false)
	const showResult = ref(false)
	const phone = ref('')

	function submit(e) {
		loading.value = true
		showResult.value = false

		axios.post('', {phone: phone.value.replaceAll(/\D/g, '')})
			.then(response => {
				result.value = response.data.result
				showResult.value = true
			})
			.catch(error => toastsStore.addError(error.response?.data?.message ?? error.message))
			.finally(() => loading.value = false)
	}
</script>

<template>
	<AppLayout>
		<v-form class="w-25" @submit.prevent="submit">
			<v-text-field name="phone" placeholder="79120000000" label="Введите номер для проверки" clearable v-model="phone"></v-text-field>
			<v-btn type="submit" :loading="loading" :disabled="loading">Проверить</v-btn>
			<v-icon
				class="ms-3"
				v-show="showResult"
				:icon="result ? 'mdi-check-circle' : 'mdi-close-circle'"
				:color="result ? 'success' : 'danger'"
			></v-icon>
		</v-form>
	</AppLayout>
</template>
