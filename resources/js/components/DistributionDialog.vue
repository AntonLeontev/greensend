<script setup>
	import axios from "axios";
	import { ref, watch, reactive } from "vue";
	import DelayedLaunch from "./DelayedLaunch.vue";
	import ConversationTree from "./ConversationTree.vue";
	import ChannelSelect from "./ChannelSelect.vue";
	import { useToastsStore } from "@/stores/toasts" 

	const props = defineProps({
		isActive: Boolean,
		selectedFile: Object,
	});

	const emit = defineEmits(['closeDistributionDialog']);
	const toastsStore = useToastsStore();

	watch(
		() => props.isActive,
		(value) => showDialog.value = value
	)

	const loading = ref(false);
	const error = ref('');
	const tab = ref();
	const channels = reactive([]);
	const conversationTree = ref('');

	const showDialog = ref(props.isActive);


	getChannels();
	

	function createDistribution(e) {
		loading.value = true;
		error.value = '';
		
		axios.post(
			route('distributions.store'),
			new FormData(e.target),
		)
		.then(resp => {
			emit('closeDistributionDialog')

			toastsStore.addSuccess('Рассылка поставлена в очередь');
		})
		.catch(err => {
			error.value = err.response?.data?.message ?? err.message
		})
		.finally(() => loading.value = false)

		
	}
	function getChannels() {
		if (sessionStorage.getItem('channels')) {
			channels.push(...JSON.parse(sessionStorage.getItem('channels')));
			return;
		}

		axios.get(route('channels.index'))
			.then(response => {
				channels.push(...response.data);

				sessionStorage.setItem('channels', JSON.stringify(response.data));
			})
			.catch(error => toastsStore.addError(error.response?.data?.message ?? error.message))
	}
</script>

<template>
	<v-dialog
		v-model="showDialog"
		persistent
		scrollable
		max-height="80vh"
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
						<form @submit.prevent="createDistribution">
							<input type="hidden" name="uploaded_file_id" :value="selectedFile.id">
							<input type="hidden" name="type" value="script">
							<input type="hidden" name="conversation" :value="conversationTree">

							<div class="justify-between d-flex ga-4">
								<DelayedLaunch />
								<div class="w-33">
									<ChannelSelect :channels="channels" />
								</div>
							</div>

							<div class="mb-2">Сценарий переписки для рассылки:</div>
							<ConversationTree @changed="data => conversationTree = JSON.stringify(data)" />

							<div class="my-2 text-center text-danger">{{ error }}</div>

							<v-btn type="submit" text="Создать рассылку" color="info" variant="tonal" class="w-100" :loading="loading" :disabled="loading"></v-btn>
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
