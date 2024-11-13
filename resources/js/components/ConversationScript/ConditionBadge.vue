<script setup>
	const props = defineProps({
		condition: String,
	});

	function color() {
		if (props.condition === 'yes') {
			return 'success';
		}

		if (props.condition === 'no') {
			return 'error';
		}
		
		return '';
	}
</script>

<template>
 	<v-badge 
		:content="props.condition" 
		inline 
		:color="color()"
		class="mt-1"
	></v-badge>
</template>
