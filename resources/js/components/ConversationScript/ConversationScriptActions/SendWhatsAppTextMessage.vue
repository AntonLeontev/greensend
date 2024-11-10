<script setup>
	import { ref } from "vue";
import NodeActionButtons from "../NodeActionButtons.vue";

	const props = defineProps({
		node: Object,
	});
	const emit = defineEmits(['deleteNode', 'createNode', 'nodeDataUpdated'])

	const menuIsOpen = ref(false);
	const editDialogIsOpen = ref(false);

	function formatText() {
		return props.node.action.data.text.replaceAll('\n', '<br>');
	} 
	function updateNode(e) {
		let data = props.node.action.data;
		data.text = new FormData(e.target).get('text')
		editDialogIsOpen.value = false

		emit('nodeDataUpdated', {id: props.node.id, data: data})
	}
</script>

<template>
	<div v-html="formatText()" class="text"></div>

	<div class="d-flex" v-click-outside="() => menuIsOpen = false">
		<v-btn
			density="compact"
			class="d-flex"
			icon="mdi-dots-vertical"
			variant="plain"
			@click="menuIsOpen = !menuIsOpen"
		></v-btn>

		<Transition>
			<div class="d-flex ga-1" v-if="menuIsOpen">
				
				<v-btn icon="mdi-pencil" density="compact" v-tooltip:top="'Изменить текст'"
					variant="flat"
					class="me-1"
					@click="editDialogIsOpen = true"
				></v-btn>

				<NodeActionButtons 
					:node="props.node" 
					@create-node="(data) => $emit('createNode', data)"
					@delete-node="(id) => $emit('deleteNode', id)"
				/>
			</div>
		</Transition>
	</div>

	<v-dialog v-model="editDialogIsOpen" max-width="500">
		<v-card title="Редактирование сообщения">
			<template v-slot:text>
				<form @submit.prevent="updateNode">
					<textarea rows="6" :value="props.node.action.data.text" class="mb-2 w-100 border-md" name="text"  ref="updateTextarea"></textarea>
					<v-btn type="submit" text="Сохранить" color="info" variant="tonal" class="w-100"></v-btn>
				</form>
			</template>

			<template v-slot:actions>
				<v-btn
					class="ms-auto"
					text="Отмена"
					@click="editDialogIsOpen = false"
				></v-btn>
			</template>
		</v-card>
	</v-dialog>
</template>
