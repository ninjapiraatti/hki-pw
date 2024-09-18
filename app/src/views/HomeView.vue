<template>
	<div class="container-fluid text-center">
		<div class="w-100 my-4">
			<img class="logo" src="../assets/logo.png" alt="HKI2050 logo" />
		</div>
		<div v-html="mainContent" class="angled-corner text-start p-4 mb-4"></div>
		<router-link :to="characterLink" custom v-slot="{ navigate }">
			<button type="button" class="btn--glitch btn" @click="navigate" @keypress.enter="navigate" role="link">
				<span class="btn__content">Create Character</span>
				<span class="btn__effect"></span>
				<span class="btn__label">
					<PlusCircleIcon class="btn__label__icon" />
				</span>
			</button>
		</router-link>
	</div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from "vue"
import { v4 as uuidv4 } from "uuid"
import { PlusCircleIcon } from "@heroicons/vue/24/outline"

const mainContent = ref("")

const characterLink = computed(() => {
	const characterId = uuidv4()
	return `/characters/${characterId}`
})

const getIntro = async () => {
	try {
		const response = await fetch(`http://localhost:8888/hki-pw/`, {
			method: "GET",
			headers: {
				"Content-Type": "application/json",
				"X-Requested-With": "XMLHttpRequest",
			},
		})
		if (response.ok) {
			const data = await response.json()
			mainContent.value = data.body
		} else if (response.status === 404) {
			console.warn("No content")
		} else {
			throw new Error(`Error: ${response.status} ${response.statusText}`)
		}
	} catch (error) {
		console.error("Error:", error)
	}
}

onMounted(() => {
	getIntro()
})
</script>
