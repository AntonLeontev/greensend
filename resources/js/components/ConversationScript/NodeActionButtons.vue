<script setup>
	const props = defineProps({
		node: Object,
	})
	const emit = defineEmits(['editNode', 'deleteNode', 'createNode'])

	function shouldHaveYes() {
		return !props.node.children.find(el => el.condition === 'yes')
	}
	function shouldHaveNo() {
		return !props.node.children.find(el => el.condition === 'no')
	}
	function shouldHaveDefault() {
		return !props.node.children.find(el => el.condition === 'default')
	}
</script>

<template>
	<v-btn icon="$success" density="compact" v-tooltip:top="'Добавить ответ ДА'" v-if="shouldHaveYes()" 
		@click="$emit('createNode', {id: props.node.id, condition: 'yes'})"
		variant="flat"
	></v-btn>
	<v-btn icon="mdi-close-circle" density="compact" v-tooltip:top="'Добавить ответ НЕТ'" v-if="shouldHaveNo()"
		@click="$emit('createNode', {id: props.node.id, condition: 'no'})"
		variant="flat"
	></v-btn>
	<v-btn icon="mdi-asterisk-circle-outline" density="compact" v-tooltip:top="'Добавить ответ по умолчанию'" v-if="shouldHaveDefault()"
		@click="$emit('createNode', {id: props.node.id, condition: 'default'})"
		variant="flat"
	></v-btn>
	<v-btn icon="mdi-trash-can-outline" density="compact" v-tooltip:top="'Удалить'"
		v-if="props.node.parentId"
		@click="$emit('deleteNode', props.node.id)"
		variant="flat"
	></v-btn>
</template>
