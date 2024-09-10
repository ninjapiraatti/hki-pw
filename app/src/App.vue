<template>
	<TheHeader />
	<RouterView />
</template>

<script setup lang="ts">
import { onMounted } from "vue"
import { RouterView } from "vue-router"
import TheHeader from "./components/TheHeader.vue"

function applyRandomClassToRandomElement(className: string, tagNames: string[]): void {
	const elements = Array.from(tagNames.flatMap((tag) => document.getElementsByTagName(tag)))
	if (elements.length === 0) return
	elements.forEach((element) => element.classList.remove(className))

	const randomElement = elements[Math.floor(Math.random() * elements.length)]
	randomElement.classList.add(className)
	console.log(`Applied class "${className}" to element:`, randomElement)
}

function startRandomClassTimer(className: string, tagNames: string[], interval: number, probability: number): void {
	setInterval(() => {
		if (Math.random() < probability) {
			applyRandomClassToRandomElement(className, tagNames)
		} else {
			console.log(`No class applied this time. Chance: ${Math.random()}`)
		}
	}, interval)
}

onMounted(() => {
	startRandomClassTimer("cyberpunk", ["button", "h1"], 1000, 0.5)
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
