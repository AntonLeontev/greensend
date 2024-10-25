<script setup>
	import axios from "axios";
	import { ref, watch } from "vue";
import DelayedLaunch from "./DelayedLaunch.vue";
import ConversationTree from "./ConversationTree.vue";

	const props = defineProps({
		isActive: Boolean,
		selectedFile: Object,
	});

	const emit = defineEmits(['closeDistributionDialog']);

	watch(
		() => props.isActive,
		(value) => showDialog.value = value
	)

	const loading = ref(false);
	const form = ref();
	const error = ref('');
	const tab = ref();


	const showDialog = ref(props.isActive);
	

	function createDistribution() {
		loading.value = true;
		error.value = '';
		
		// axios.post(
		// 	route('uploaded-files.archive', props.selectedFile.id),
		// 	new FormData(form.value),
		// )
		// .then(resp => {
			
		// 	emit('closeDistributionDialog')
		// })
		// .catch(err => {
		// 	error.value = err.response?.data?.message ?? err.message
		// })
		// .finally(() => loading.value = false)

		
	}
</script>

<template>
	<v-dialog
		v-model="showDialog"
		persistent
	>
		<v-card
			prepend-icon="mdi-multicast"
			:title="'Создание рассылки из файла ' + selectedFile.label"
		>
			<template v-slot:text>
				<v-tabs fixed-tabs v-model="tab" color="info">
					<v-tab text="Рассылка по сценарию" value="script"></v-tab>

					<v-tab text="Общение с AI" value="ai"></v-tab>
				</v-tabs>

				<v-tabs-window v-model="tab">
					<v-tabs-window-item value="script">
						<form ref="form">
							<input type="hidden" name="uploaded_file_id" :value="selectedFile.id">
							<input type="hidden" name="type" value="script">

							<DelayedLaunch />

							<ConversationTree />

							<div class="text-center text-danger">{{ error }}</div>
						</form>
					</v-tabs-window-item>

					<v-tabs-window-item value="ai">
						<form ref="form">
							<input type="hidden" name="uploaded_file_id" :value="selectedFile.id">
							<input type="hidden" name="type" value="ai">
							
							<DelayedLaunch />

							<div class="text-center text-danger">{{ error }}</div>
						</form>
					</v-tabs-window-item>
				</v-tabs-window>
				
			</template>
			<template v-slot:actions>
				<v-btn
					class="ms-auto"
					text="Отмена"
					@click="$emit('closeDistributionDialog')"
				></v-btn>
			</template>
		</v-card>
	</v-dialog>
</template>
