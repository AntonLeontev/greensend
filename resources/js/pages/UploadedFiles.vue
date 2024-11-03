<script setup>
    import AppLayout from '@/layouts/AppLayout.vue';
	import CrudPage from '@/components/CrudPage.vue';
	import UploadFileModal from '@/components/UploadFileModal.vue';
	import { reactive, ref } from "vue";
	import {useToastsStore} from "@/stores/toasts.js"
	import { useDate } from 'vuetify'
	import ArchiveFileModal from '@/components/ArchiveFileModal.vue';
	import DistributionDialog from '@/components/DistributionDialog.vue';

	const toastsStore = useToastsStore();
	const date = useDate()

	const files = reactive([]);
	const archiveDialog = ref(false);
	const distributionDialog = ref(false);
	const activeFile = reactive({});

	
	getFiles();


	function getFiles() {
		axios.get(route('uploaded-files.index'))
			.then(response => files.push(...response.data.data))
			.catch(error => toastsStore.addError(error.response?.data?.message ?? error.message))
	}

	function removeFile(file) {
		axios.delete(route('uploaded-files.destroy', file.id))
			.then(response => {
				let collection = files.filter(el => el.id !== file.id)
				files.length = 0
				files.push(...collection)
			})
			.catch(error => toastsStore.addError(error.response?.data?.message ?? error.message))
	}

	function openArchiveDialog(file) {
		archiveDialog.value = true;
		Object.assign(activeFile, file)
	}
	function closeArchiveDialog() {
		archiveDialog.value = false;
	}

	function openDistributionDialog(file) {
		distributionDialog.value = true;
		Object.assign(activeFile, file)
	}
	function closeDistributionDialog() {
		distributionDialog.value = false;
	}

	function whatsappCheck(file) {
		axios.post(route('whatsapp.check-file', file.id))
			.then(response => {
				file.status = response.data.status
				file.whatsapp_check_percent = response.data.whatsapp_check_percent
			})
			.catch(error => toastsStore.addError(error.response?.data?.message ?? error.message))
	}
</script>

<template>
    <AppLayout>
		<CrudPage>
			<template v-slot:header>
				<UploadFileModal @file-uploaded="(data) => files.unshift(data.file)" />
			</template>
			<template v-slot:content>
				<v-table v-if="files.length > 0">
					<thead>
						<tr>
							<th class="text-left">
								Название
							</th>
							<th class="text-left">
								Статус
							</th>
							<th class="text-left">
								Загружен
							</th>
							<th class="text-left">
								Количество номеров
							</th>
							<th class="text-left">
								Действия
							</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="file in files">
							<th>{{ file.label }}</th>
							<th>{{ file.status === 'cleaning whatsapp' ? file.whatsapp_check_percent+'%' : file.status }}</th>
							<th>{{ date.format(file.created_at, 'keyboardDateTime') }}</th>
							<th>
								<div class="d-flex ga-2">
									<span title="Количество телефонных номеров в исходнике">{{ file.initial_phones_number }}</span>
									|
									<span title="Количество мобильных телефонных номеров">{{ file.clean_phones_number }}</span>
									|
									<span title="Количество телефонных номеров существующих в WhatsApp">{{ file.whatsapp_phones_number ?? '?' }}</span>
								</div>
							</th>
							<th>
								<div class="d-flex ga-2">
									<v-hover v-if="file.status === 'cleaned'">
										<template v-slot:default="{ isHovering, props }">
											<v-btn density="compact" icon="mdi-whatsapp" :color="isHovering ? 'green' : undefined" v-bind="props" title="Проверить наличие номеров в WhatsApp" @click="whatsappCheck(file)" />
										</template>
									</v-hover>

									<v-hover>
										<template v-slot:default="{ isHovering, props }">
											<v-btn density="compact" icon="mdi-file-download-outline" :color="isHovering ? 'green' : undefined" v-bind="props" title="Скачать подготовленный архив" @click="openArchiveDialog(file)" />
										</template>
									</v-hover>

									<v-hover>
										<template v-slot:default="{ isHovering, props }">
											<v-btn density="compact" icon="mdi-multicast" :color="isHovering ? 'green' : undefined" v-bind="props" title="Создать рассылку" @click="openDistributionDialog(file)" />
										</template>
									</v-hover>

									<v-hover>
										<template v-slot:default="{ isHovering, props }">
											<v-btn density="compact" icon="mdi-trash-can-outline" :color="isHovering ? 'danger' : undefined" v-bind="props" class="ms-4" title="Удалить файл" @click="removeFile(file)" />
										</template>
									</v-hover>
								</div>
							</th>
						</tr>
					</tbody>
				</v-table>
				<div class="text-center pa-2" v-else>Нет загруженных файлов</div>
			</template>
		</CrudPage>

		<ArchiveFileModal :isActive="archiveDialog" :selectedFile="activeFile" @close-archive-file-modal="closeArchiveDialog" />

		<DistributionDialog :isActive="distributionDialog" :selectedFile="activeFile" @close-distribution-dialog="closeDistributionDialog" />
    </AppLayout>
</template>
