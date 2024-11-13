<script setup>
	import { useAppStore } from '@/stores/app';

	const props = defineProps({
		node: Object,
	})
	const emit = defineEmits(['deleteNode', 'createNode'])

	const appStore = useAppStore();

	function canAddAnswer() {
		return appStore.conditions.length > props.node.children.length
	}
</script>

<template>
	<v-btn icon="mdi-plus-box" density="compact" v-tooltip:top="'Добавить ответ'" v-if="canAddAnswer()" 
		@click="$emit('createNode', {id: props.node.id})"
		variant="flat"
	></v-btn>
	<v-btn icon="mdi-trash-can-outline" density="compact" v-tooltip:top="'Удалить'"
		v-if="props.node.parentId"
		@click="$emit('deleteNode', props.node.id)"
		variant="flat"
	></v-btn>
</template>
