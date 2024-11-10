<script setup>
import { ref, reactive, nextTick } from "vue"
import TreeNode from "./TreeNode.vue";

const emit = defineEmits(['changed']);
const data = reactive([
	{
		id: 1,
		action: {
			type: 'SendWhatsAppTextMessage',
			class: 'Src\\ScriptNodes\\SendWhatsAppTextMessage',
			data: {
				text: 'Здравствуйте!'
			},
		},
		children: [
			{
				id: 2,
				action: {
					type: 'SendWhatsAppTextMessage',
					class: 'Src\\ScriptNodes\\SendWhatsAppTextMessage',
					data: {
						text: 'ОТвет на да!'
					},
				},
				children: [
					{
						id: 4,
						action: {
							type: 'SendWhatsAppTextMessage',
							class: 'Src\\ScriptNodes\\SendWhatsAppTextMessage',
							data: {
								text: 'любой ответ!'
							},
						},
						children: [],
						condition: 'default',
						parentId: 2,
					},
				],
				condition: 'yes',
				parentId: 1,
			},
			{
				id: 3,
				action: {
					type: 'SendWhatsAppTextMessage',
					class: 'Src\\ScriptNodes\\SendWhatsAppTextMessage',
					data: {
						text: 'Ответ на нет!'
					},
				},
				children: [],
				condition: 'no',
				parentId: 1,
			},
		]
	},
])
const index = ref(8);
const deleteNode = ref(false);
const createNode = ref(false);
const createMode = ref('root');
const selectedNode = reactive({});

const storeTextarea = ref(null);
const updateTextarea = ref(null);

emit('changed', data)


function openCreateDialog(data) {
	Object.assign(selectedNode, findNode(data.id))
	createNode.value = true
	createMode.value = data.label
	
	nextTick(() => {
		setTimeout(() => storeTextarea.value?.focus(), 100)
	})
}
function openDeleteDialog(id) {
	Object.assign(selectedNode, findNode(id))
	deleteNode.value = true
}

function updateNode(eventData) {
	let node = findNode(eventData.id)
	node.action.data = eventData.data

	emit('changed', data)
}
function storeNode(e) {
	let node = findNode(selectedNode.id)
	let formData = new FormData(e.target)
	let child = {
		id: index.value++,
		label: formData.get('label'),
		text: formData.get('text'),
		parent_id: node.id,
		children: [],
	}
	node.children.push(child)
	createNode.value = false

	emit('changed', data)
}
function destroyNode(e) {
	let parent = findNode(selectedNode.parentId)
	parent.children = parent.children.filter(el => el.id !== selectedNode.id)
	deleteNode.value = false

	emit('changed', data)
}

function findNode(id) {
	function recursiveSearch(array) {
		for (const el of array) {
			
			if (el.id === id) {
				return el;
			}
			
			if (el.children.length > 0) {
				let result = recursiveSearch(el.children)

				if (result) {
					return result;
				}
			}
		}
	}

	return recursiveSearch(data)
}
</script>

<template>
	<div class="p-2 border-thin">
		<div class="ms-n12">
			<TreeNode 
				v-for="item in data" 
				:node="item" 
				@create-node="openCreateDialog"
				@node-data-updated="updateNode"
				@delete-node="openDeleteDialog"
			/>

		</div>

		<v-dialog v-model="createNode" max-width="500">
			<v-card title="Добавление сообщения">
				<template v-slot:text>
					<form @submit.prevent="storeNode">
						<input type="hidden" name="label" :value="createMode">
						<textarea rows="6" class="mb-2 w-100 border-md" name="text" id="store-textarea" ref="storeTextarea"></textarea>
						<v-btn type="submit" text="Сохранить" color="info" variant="tonal" class="w-100"></v-btn>
					</form>
				</template>

				<template v-slot:actions>
					<v-btn
						class="ms-auto"
						text="Отмена"
						@click="createNode = false"
					></v-btn>
				</template>
			</v-card>
		</v-dialog>

		<v-dialog v-model="deleteNode" max-width="500">
			<v-card title="Удаление сообщения">
				<template v-slot:text>
					<div class="mb-2">Уверены что хотите удалить ветку сообщений?</div>
					<form @submit.prevent="destroyNode">
						<v-btn type="submit" text="Удалить" color="info" variant="tonal" class="w-100"></v-btn>
					</form>
				</template>

				<template v-slot:actions>
					<v-btn
						class="ms-auto"
						text="Отмена"
						@click="deleteNode = false"
					></v-btn>
				</template>
			</v-card>
		</v-dialog>
	</div>
</template>
