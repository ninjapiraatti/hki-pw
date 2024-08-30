<template>
	<router-link :to="characterLink" custom v-slot="{ navigate }">
		<img src="../assets/logo.png" alt="HKI2050 logo" />
		<button @click="navigate" @keypress.enter="navigate" role="link">Create Character</button>
	</router-link>
</template>

<script setup lang="ts">
import { computed, onMounted } from "vue"
import { v4 as uuidv4 } from "uuid"

const characterLink = computed(() => {
	const characterId = uuidv4()
	return `/characters/${characterId}`
})

onMounted(() => {
	console.log(typeof bootstrap !== "undefined")
})
</script>
