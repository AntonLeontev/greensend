<script setup>
	import { ref } from "vue";

	const enabled = ref(false);
</script>

<template>
	<div class="mb-2 d-flex align-center ga-4">
		<v-checkbox label="Отложенный запуск" v-model="enabled"></v-checkbox>
		<div class="d-flex ga-2" v-if="enabled">
			<v-input prepend-icon="mdi-calendar" variant="outlined">
				<input type="date" name="start_date">
			</v-input>
			<v-input prepend-icon="mdi-clock-outline" class="ms-4">
				<input type="time" name="start_time"></input>
			</v-input>
		</div>
	</div>
</template>
