<script setup>

	const props = defineProps({
		channels: Array
	})
	
	function itemProps(item) {
        return {
			title: item.number,
			subtitle: item.label,
			value: item.id,
        }
	}
</script>

<template>
	<v-select
		label="Выберите номер отправки сообщений"
		:items="channels"
		:item-props="itemProps"
		name="channel_id"
	></v-select>
</template>
