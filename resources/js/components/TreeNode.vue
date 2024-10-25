<script setup>
import { ref } from "vue";

const props = defineProps({
	node: Object,
});
const emit = defineEmits(['editNode', 'deleteNode', 'createNode'])

const isOpen = ref(true);
const menuIsOpen = ref(false);

function formatText() {
	return props.node.text.replaceAll('\n', '<br>');
} 
function hasYes() {
	return !!props.node.answers.find(el => el.label === 'yes')
}
function hasNo() {
	return !!props.node.answers.find(el => el.label === 'no')
}
</script>

<template>
	<ul class="ms-12">
		<li class="mb-2">
			<div class="d-flex ga-2">

				<v-btn density="compact" 
					:disabled="!props.node.answers?.length > 0"
					:class="{'invisible': !props.node.answers?.length > 0}"
					:icon="isOpen ? 'mdi-menu-down' : 'mdi-menu-right'" 
					@click="isOpen = !isOpen"
					variant="text"
				></v-btn>

				<v-badge 
					v-if="props.node.label" 
					:content="props.node.label" 
					inline 
					:color="props.node.label === 'yes' ? 'success' : 'error'"
					class="mt-1"
				></v-badge>

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
							<v-btn icon="$success" density="compact" v-tooltip:top="'Добавить ответ ДА'" v-if="!hasYes()" 
								@click="$emit('createNode', {id: props.node.id, label: 'yes'})"
								variant="flat"
							></v-btn>
							<v-btn icon="mdi-close-circle" density="compact" v-tooltip:top="'Добавить ответ НЕТ'" v-if="!hasNo()"
								@click="$emit('createNode', {id: props.node.id, label: 'no'})"
								variant="flat"
							></v-btn>
							<v-btn icon="mdi-pencil" density="compact" v-tooltip:top="'Изменить текст'"
								@click="$emit('editNode', props.node.id)"
								variant="flat"
							></v-btn>
							<v-btn icon="mdi-trash-can-outline" density="compact" v-tooltip:top="'Удалить'"
								@click="$emit('deleteNode', props.node.id)"
								variant="flat"
							></v-btn>
						</div>
					</Transition>
				</div>

			</div>
			<div class="children" :class="{'children_opened': isOpen}">
				<div class="children__inner">
					<TreeNode 
						v-for="answer in props.node.answers" 
						:node="answer" 
						@create-node="(data) => $emit('createNode', data)"
						@edit-node="(id) => $emit('editNode', id)"
						@delete-node="(id) => $emit('deleteNode', id)"
					/>
				</div>
			</div>
		</li>
	</ul>
</template>

<style scoped>
	.text {
		max-width: 350px;
	}
	.children {
		display: grid;
		grid-template-rows: 0fr;
		transition: grid-template-rows 0.3s ease;
		overflow: hidden;
	}

	.children_opened {
		grid-template-rows: 1fr;
	}

	.children__inner {
		min-height: 0px;
	}

	.invisible {
		visibility: hidden;
	}

	.v-enter-active,
	.v-leave-active {
		transition: opacity 0.3s ease;
	}

	.v-enter-from,
	.v-leave-to {
		opacity: 0;
	}
</style>
