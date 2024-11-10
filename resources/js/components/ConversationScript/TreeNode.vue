<script setup>

import { ref } from "vue";
import ConditionBadge from "./ConditionBadge.vue";
import SendWhatsAppTextMessage from "./ConversationScriptActions/SendWhatsAppTextMessage.vue";

const props = defineProps({
	node: Object,
});
const emit = defineEmits(['nodeDataUpdated', 'deleteNode', 'createNode'])

const isOpen = ref(true);
const components = {
	SendWhatsAppTextMessage,
};

</script>

<template>
	<ul class="ms-12">
		<li class="mb-2">
			<div class="d-flex ga-2">

				<v-btn density="compact" 
					:disabled="!props.node.children?.length > 0"
					:class="{'invisible': !props.node.children?.length > 0}"
					:icon="isOpen ? 'mdi-menu-down' : 'mdi-menu-right'" 
					@click="isOpen = !isOpen"
					variant="text"
				></v-btn>

				<ConditionBadge :condition="props.node.condition" v-if="props.node.condition"  />
				
				<component 
					:is="components[props.node.action.type]" 
					v-if="props.node.action?.type" 
					:node="props.node" 
					@create-node="(data) => $emit('createNode', data)"
					@delete-node="(id) => $emit('deleteNode', id)"
					@node-data-updated="(data) => $emit('nodeDataUpdated', data)"
				/>
				

			</div>
			<div class="children" :class="{'children_opened': isOpen}">
				<div class="children__inner">
					<TreeNode 
						v-for="child in props.node.children" 
						:node="child" 
						@create-node="(data) => $emit('createNode', data)"
						@node-data-updated="(data) => $emit('nodeDataUpdated', data)"
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
