<template>
	<div class="character-view">
		<h2>Character Details</h2>
		<div v-if="character">
			<h3>{{ character.name }}</h3>
			<ul>
				<li v-for="(stat, index) in stats" :key="index">{{ stat }}: {{ character[stat.toLowerCase()] }}</li>
			</ul>
			<button class="btn btn-secondary">Edit Character</button>
		</div>
		<CharacterForm :character="character" @onSubmit="postCharacter" />
	</div>
</template>

<script setup lang="ts">
import { onMounted } from "vue"
import CharacterForm from "@/components/CharacterForm.vue"
import { Character } from "@/types"

const props = defineProps<{
	character?: Character
}>()

const stats = ["Strength", "Perception", "Endurance", "Charisma", "Intelligence", "Agility", "Luck"]

const postCharacter = async (character: Character) => {
	try {
		console.log("Toon:", character)
		const response = await fetch("http://localhost:8888/hki-pw/characters/", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"X-Requested-With": "XMLHttpRequest",
			},
			body: JSON.stringify(character),
		})

		if (!response.ok) {
			throw new Error("Network response was not ok")
		}

		const data = await response.json()
		console.log(data)
	} catch (error) {
		console.error("Error:", error)
	}
}

onMounted(() => {
	console.log(props)
})
</script>
