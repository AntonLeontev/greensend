<script setup>
import { ref, reactive } from "vue"
import TreeNode from "./TreeNode.vue";

const data = reactive([
	{
		id: 1,
		text: 'Hello to everyone!',
		answers: [
			{id: 2, label: 'yes', text: 'Do you wanna buy our goods?\n\n1-yes\n2-no', parent_id: 1, answers: [
				{id: 4, label: 'yes', text: 'Here is paylink:', parent_id: 2, answers: [
					{id: 6, label: 'no', text: 'Have a good day!', parent_id: 4, answers: []},
					{id: 7, label: 'yes', text: 'Have a nice day!', parent_id: 4, answers: []},
				]},
				{id: 5, label: 'no', text: 'Good bye!', parent_id: 2, answers: []},
			]},
			{id: 3, label: 'no', text: 'Good bye!!!', parent_id: 1, answers: []},
		]
	},
])
const index = ref(8);
const editNode = ref(false);
const deleteNode = ref(false);
const createNode = ref(false);
const createMode = ref('root');
const selectedNode = reactive({});


function openCreateDialog(data) {
	Object.assign(selectedNode, findNode(data.id))
	createNode.value = true
	createMode.value = data.label
}
function openEditDialog(id) {
	Object.assign(selectedNode, findNode(id))
	editNode.value = true
}
function openDeleteDialog(id) {
	Object.assign(selectedNode, findNode(id))
	deleteNode.value = true
}

function updateNode(e) {
	let node = findNode(selectedNode.id)
	node.text = new FormData(e.target).get('text')
	editNode.value = false
}
function storeNode(e) {
	let node = findNode(selectedNode.id)
	let data = new FormData(e.target)
	let child = {
		id: index.value++,
		label: data.get('label'),
		text: data.get('text'),
		parent_id: node.id,
		answers: [],
	}
	node.answers.push(child)
	createNode.value = false
}
function destroyNode(e) {
	let parent = findNode(selectedNode.parent_id)
	parent.answers = parent.answers.filter(el => el.id !== selectedNode.id)
	deleteNode.value = false
}

function findNode(id) {
	function recursiveSearch(array) {
		for (const el of array) {
			
			if (el.id === id) {
				return el;
			}
			
			if (el.answers.length > 0) {
				let result = recursiveSearch(el.answers)

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
	<div class="ms-n12">
		<TreeNode 
			v-for="item in data" 
			:node="item" 
			@create-node="openCreateDialog"
			@edit-node="openEditDialog"
			@delete-node="openDeleteDialog"
		/>

	</div>

	<v-dialog v-model="editNode" max-width="500">
		<v-card label="Редактирование сообщения">
			<template v-slot:text>
				<form @submit.prevent="updateNode">
					<textarea rows="6" :value="selectedNode.text" class="mb-2 w-100 border-b-md" name="text"></textarea>
					<v-btn type="submit" text="Сохранить" color="info" variant="tonal" class="w-100"></v-btn>
				</form>
			</template>

			<template v-slot:actions>
				<v-btn
					class="ms-auto"
					text="Отмена"
					@click="editNode = false"
				></v-btn>
			</template>
		</v-card>
	</v-dialog>

	<v-dialog v-model="createNode" max-width="500">
		<v-card label="Редактирование сообщения">
			<template v-slot:text>
				<form @submit.prevent="storeNode">
					<input type="hidden" name="label" :value="createMode">
					<textarea rows="6" class="mb-2 w-100 border-b-md" name="text"></textarea>
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
		<v-card label="Удаление сообщения">
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
</template>
