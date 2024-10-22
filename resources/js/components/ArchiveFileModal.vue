<script setup>
	import axios from "axios";
	import { ref, watch } from "vue";

	const props = defineProps({
		isActive: Boolean,
		selectedFile: Object,
	});

	const emit = defineEmits(['closeArchiveFileModal']);

	watch(
		() => props.isActive,
		(value) => showDialog.value = value
	)

	const loading = ref(false);
	const form = ref();
	const error = ref('');

	function startArchiving() {
		loading.value = true;
		error.value = '';
		
		axios.post(
			route('uploaded-files.archive', props.selectedFile.id),
			new FormData(form.value),
			{ responseType: 'blob' }
		)
		.then(resp => {
			let blob = resp.data
			const link = document.createElement('a');
			link.href = URL.createObjectURL(blob);
			link.setAttribute('download', props.selectedFile.label + '.zip');
			link.click();
			URL.revokeObjectURL(link.href);
			
			emit('closeArchiveFileModal')
		})
		.catch(err => {
			if (!err.response.data) {
				error.value = err.message
				return;
			}
			
			err.response.data.text().then((errorText) => {
				const errorJson = JSON.parse(errorText);
				error.value = errorJson.message
			});
		})
		.finally(() => loading.value = false)

		
	}

	const showDialog = ref(props.isActive);

</script>

<template>
	<v-dialog
		v-model="showDialog"
		width="1200"
		persistent
	>
		<v-card
			max-width="1200"
			prepend-icon="mdi-file-download-outline"
			:title="selectedFile.label"
		>
			<template v-slot:text>
				<form ref="form">
					<div class="d-flex ga-2">
						<v-textarea rows="5" name="text1" label="Приветствие" clearable no-resize class="w-100" hint="Разные варианты можно писать с новой строки"></v-textarea>
						<v-textarea rows="5" name="text2" label="Текст2" clearable no-resize class="w-100" hint="Разные варианты можно писать с новой строки"></v-textarea>
						<v-textarea rows="5" name="text3" label="Текст3" clearable no-resize class="w-100" hint="Разные варианты можно писать с новой строки"></v-textarea>
					</div>
					<div class="justify-center d-flex">
						<v-text-field name="number" max-width="500" label="Количество номеров в файле" class="mt-4"></v-text-field>
					</div>
					<div class="text-center text-danger">{{ error }}</div>
				</form>
			</template>
			<template v-slot:actions>
				<v-btn
					class="ms-auto"
					text="Отмена"
					@click="$emit('closeArchiveFileModal')"
				></v-btn>
				<v-btn
					color="info"
					variant="tonal"
					text="Начать"
					:disabled="loading"
					:loading="loading"
					@click="startArchiving"
				></v-btn>
			</template>
		</v-card>
	</v-dialog>
</template>
