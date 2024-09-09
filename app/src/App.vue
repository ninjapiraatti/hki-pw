<template>
	<TheHeader />
	<RouterView />
</template>

<script setup lang="ts">
import { onMounted } from "vue"
import { RouterView } from "vue-router"
import TheHeader from "./components/TheHeader.vue"

function applyRandomClassToRandomElement(className: string, tagName: string): void {
	// Select all elements of the specified tag name
	const elements = document.getElementsByTagName(tagName)

	// Check if there are any elements found
	if (elements.length === 0) {
		console.warn(`No elements found with tag name: ${tagName}`)
		return
	}

	// Remove the specified class from all elements
	for (let i = 0; i < elements.length; i++) {
		elements[i].classList.remove(className)
	}

	// Generate a random index
	const randomIndex = Math.floor(Math.random() * elements.length)

	// Select the random element
	const randomElement = elements[randomIndex]

	// Apply the specified class to the random element
	randomElement.classList.add(className)

	console.log(`Applied class "${className}" to element:`, randomElement)
}

function startRandomClassTimer(className: string, tagName: string, interval: number, probability: number): void {
	setInterval(() => {
		// Generate a random number between 0 and 1
		const randomChance = Math.random()

		// Check if the random chance is less than the specified probability
		if (randomChance < probability) {
			applyRandomClassToRandomElement(className, tagName)
		} else {
			console.log(`No class applied this time. Chance: ${randomChance}`)
		}
	}, interval)
}

onMounted(() => {
	startRandomClassTimer("cyberpunk", "button", 10000, 0.5)
})
</script>

<style scoped>
.logo {
	height: 6em;
	padding: 1.5em;
	will-change: filter;
	transition: filter 300ms;
}
.logo:hover {
	filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
	filter: drop-shadow(0 0 2em #42b883aa);
}
</style>
