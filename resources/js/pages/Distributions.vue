<script setup>
	import AppLayout from '@/layouts/AppLayout.vue';
	import CrudPage from '@/components/CrudPage.vue';

	import axios from 'axios';
	import { route } from 'ziggy-js';
	import { reactive } from "vue";
	import { useToastsStore } from '@/stores/toasts.js';
	import { useDate } from 'vuetify'

	const toastsStore = useToastsStore();
	const date = useDate()
	const distributions = reactive([]);


	getDistributions();


	function getDistributions() {
		axios.get(route('distributions.index'))
			.then(response => {
				distributions.push(...response.data.data);
			})
			.catch(error => toastsStore.addError(error.response?.data?.message ?? error.message))
	}
	function remove(distribution) {
		axios.delete(route('distributions.destroy', distribution.id))
			.then(response => {
				distributions.splice(distributions.indexOf(distribution), 1);
			})
			.catch(error => toastsStore.addError(error.response?.data?.message ?? error.message))
	}
</script>

<template>
	<AppLayout>
		<CrudPage>
			<template v-slot:header></template>
			<template v-slot:content>
				<v-table v-if="distributions.length > 0">
					<thead>
						<tr>
							<th class="text-left">
								Название
							</th>
							<th class="text-left">
								Дата создания
							</th>
							<th class="text-left">
								Дата старта
							</th>
							<th class="text-left">
								Статус
							</th>
							<th class="text-left">
								Действия
							</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="distribution in distributions">
							<td>{{ distribution.name }}</td>
							<td>{{ date.format(distribution.created_at, 'keyboardDateTime') }}</td>
							<td>{{ date.format(distribution.starts_at, 'keyboardDateTime') }}</td>
							<td>{{ distribution.status }}</td>
							<td>
								<div class="d-flex ga-2">
									<v-hover>
										<template v-slot:default="{ isHovering, props }">
											<v-btn density="compact" icon="mdi-trash-can-outline" :color="isHovering ? 'danger' : undefined" v-bind="props" class="ms-4" title="Удалить" @click="remove(distribution)" />
										</template>
									</v-hover>
								</div>
							</td>
						</tr>
					</tbody>
				</v-table>
				<div class="text-center pa-2" v-else>Нет рассылок</div>
			</template>
		</CrudPage>
	</AppLayout>
</template>
