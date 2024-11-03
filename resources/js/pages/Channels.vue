<script setup>
	import CrudPage from '@/components/CrudPage.vue';
	import CreateChannel from '@/components/CreateChannel.vue';
	import AppLayout from '@/layouts/AppLayout.vue';


	import axios from 'axios';
	import { route } from 'ziggy-js';
	import { reactive } from "vue";
	import { useToastsStore } from '@/stores/toasts.js';
	import { useDate } from 'vuetify'

	const toastsStore = useToastsStore();
	const date = useDate()
	const channels = reactive([]);


	getChannels();


	function getChannels() {
		axios.get(route('channels.index'))
			.then(response => {
				channels.push(...response.data);
			})
			.catch(error => toastsStore.addError(error.response?.data?.message ?? error.message))
	}
	function remove(channel) {
		axios.delete(route('channels.destroy', channel.id))
			.then(response => {
				channels.splice(channels.indexOf(channel), 1);
			})
			.catch(error => toastsStore.addError(error.response?.data?.message ?? error.message))
	}
</script>

<template>
	<AppLayout>
		<CrudPage>
			<template v-slot:header>
				<CreateChannel @channel-created="(channel) => channels.unshift(channel)" />
			</template>
			<template v-slot:content>
				<v-table v-if="channels.length > 0">
					<thead>
						<tr>
							<th class="text-left">
								Номер
							</th>
							<th class="text-left">
								Название
							</th>
							<th class="text-left">
								Дата создания
							</th>
							<th class="text-left">
								Действия
							</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="channel in channels">
							<th>{{ channel.number }}</th>
							<th>{{ channel.label }}</th>
							<th>{{ date.format(channel.created_at, 'keyboardDateTime') }}</th>
							<th>
								<div class="d-flex ga-2">
									<v-hover>
										<template v-slot:default="{ isHovering, props }">
											<v-btn density="compact" icon="mdi-trash-can-outline" :color="isHovering ? 'danger' : undefined" v-bind="props" class="ms-4" title="Удалить" @click="remove(channel)" />
										</template>
									</v-hover>
								</div>
							</th>
						</tr>
					</tbody>
				</v-table>
				<div class="text-center pa-2" v-else>Нет исходящих номеров</div>
			</template>
		</CrudPage>
	</AppLayout>
</template>
