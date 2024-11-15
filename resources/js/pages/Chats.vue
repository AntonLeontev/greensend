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
	const chats = reactive([]);


	getChats();


	function getChats() {
		axios.get(route('chats.index'))
			.then(response => {
				chats.push(...response.data.data);
			})
			.catch(error => toastsStore.addError(error.response?.data?.message ?? error.message))
	}
</script>

<template>
	<AppLayout>
		 <v-navigation-drawer class="w-25">
			<v-list>
				<v-hover v-for="chat in chats">
					<template v-slot:default="{ isHovering, props }">
						<v-list-item
							class="cursor-pointer border-b-thin border-t-thin"
							:class="isHovering ? 'bg-light-blue-darken-4' : ''"
							:title="chat.name"
							v-bind="props"
							style="transition: all 0.3s ease;"
						>
							<template v-slot:subtitle>
								<span v-if="!chat.last_message.is_incoming">Вы:</span>
								{{ chat.last_message?.text }}
							</template>

							<div class="text-end">
								<span class="text-caption font-weight-thin font-italic">{{ date.format(chat.last_message?.created_at, 'shortDate') + ' ' + date.format(chat.last_message?.created_at, 'hours24h') + ':' + date.format(chat.last_message?.created_at, 'minutes') }}</span>
							</div>
						</v-list-item>
					</template>
				</v-hover>
			</v-list>
		 </v-navigation-drawer>

		<div class="">
			
		</div>

	</AppLayout>
</template>
