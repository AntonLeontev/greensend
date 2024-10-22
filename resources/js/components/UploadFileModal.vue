<script setup>
	import axios from 'axios';
	import {ref} from "vue";


	const emit = defineEmits(['fileUploaded']);
	const isActive = ref(false);
	const loading = ref(false);
	const uploadFileForm = ref()
	const label = ref('')
	const error = ref('')

	function getLabel(e) {
		const files = e.target.files;
		const fileName = files[0].name;
		let name = fileName.split('.');
		name.pop();
		const newLabel = name.join('.');

		if (label.value === null || label.value === '') {
			label.value = newLabel;
		}
	}

	function submit() {
		loading.value = true;
		error.value = '';

		axios.post(route('uploaded-files.store'), new FormData(uploadFileForm.value))
			.then(response => {
				emit('fileUploaded', {file: response.data})
				isActive.value = false
				label.value = ''
			})
			.catch(err => error.value = err.response?.data?.message ?? err.message)
			.finally(() => loading.value = false)
	}
</script>

<template>
    <v-dialog max-width="600" v-model="isActive">
        <template v-slot:activator="{ props: activatorProps }">
            <v-btn v-bind="activatorProps" prepend-icon="mdi-file-plus-outline" color="info" variant="tonal">
                Загрузить файл
            </v-btn>
        </template>

        <template v-slot:default="{ isActive }">
            <v-card prepend-icon="mdi-package" title="Загрузка файла">
                <template v-slot:text>
                    <form enctype="multipart/form-data" ref="uploadFileForm" @submit.prevent="submit">
                        <v-file-input clearable label="Файл" accept=".csv" name="file" @change="getLabel"></v-file-input>
                        <v-text-field clearable label="Название" prepend-icon="mdi-label" name="label" v-model="label"></v-text-field>
                    </form>
					<div class="text-center text-danger">{{ error }}</div>
                </template>
                <template v-slot:actions>
                    <v-btn class="ml-auto" text="Закрыть" variant="plain" @click="isActive.value = false"></v-btn>
                    <v-btn color="info" text="Загрузить" variant="tonal" :loading="loading" :disabled="loading" @click="submit"></v-btn>
                </template>
            </v-card>
        </template>
    </v-dialog>

</template>
