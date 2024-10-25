<script setup>
	import AppLayout from '../layouts/AppLayout.vue';
	import {reactive} from "vue"

	const files = reactive([]);
</script>

<template>
	<AppLayout>
		<form action="">
			<v-select label="Выберите файл для рассылки" :items="files"></v-select>
		</form>
	</AppLayout>
</template>
