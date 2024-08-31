<template>
	<div class="container-fluid text-center">
		<div class="w-100">
			<img src="../assets/logo.png" alt="HKI2050 logo" />
		</div>
		<router-link :to="characterLink" custom v-slot="{ navigate }">
			<button type="button" class="btn--cyberpunk btn" @click="navigate" @keypress.enter="navigate" role="link">
				<span class="btn__content">Create Character</span>
				<span class="btn__glitch"></span>
				<span class="btn__label">r25</span>
			</button>
		</router-link>
	</div>
</template>

<script setup lang="ts">
import { computed } from "vue"
import { v4 as uuidv4 } from "uuid"

const characterLink = computed(() => {
	const characterId = uuidv4()
	return `/characters/${characterId}`
})
</script>
