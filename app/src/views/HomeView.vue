<template>
	<div class="container-fluid flex justify-content-center text-center">
		<div class="w-100">
			<img src="../assets/logo.png" alt="HKI2050 logo" />
		</div>
		<router-link :to="characterLink" custom v-slot="{ navigate }">
			<button type="button" class="btn btn-primary" @click="navigate" @keypress.enter="navigate" role="link">
				Create Character
			</button>
		</router-link>
	</div>
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
