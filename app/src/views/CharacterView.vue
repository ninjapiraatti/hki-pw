<template>
	<div class="character-view">
		<h2 v-if="character">{{ character.name }}</h2>
		<h2 v-else>Create Character</h2>
		<div v-if="character">
			<ul>
				<li v-for="(stat, index) in stats" :key="index">{{ stat }}: {{ character[stat.toLowerCase()] }}</li>
			</ul>
			<button class="btn btn-secondary">Edit Character</button>
		</div>
		<CharacterForm :character="character" @onSubmit="postCharacter" />
	</div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue"
import CharacterForm from "@/components/CharacterForm.vue"
import { Character } from "@/types"

const props = defineProps<{
	character?: Character
}>()
const loading = ref(false)

const stats = ["Strength", "Perception", "Endurance", "Charisma", "Intelligence", "Agility", "Luck"]

const postCharacter = async (character: Character) => {
	if (loading.value === true) return
	loading.value = true
	const formData = new FormData()

	formData.append("name", character.name)
	formData.append("bio", character.bio)
	formData.append("image", character.image)
	formData.append("strength", character.strength)
	formData.append("perception", character.perception)
	formData.append("endurance", character.endurance)
	formData.append("charisma", character.charisma)
	formData.append("intelligence", character.intelligence)
	formData.append("agility", character.agility)
	formData.append("luck", character.luck)

	try {
		const response = await fetch("http://localhost:8888/hki-pw/characters/", {
			method: "POST",
			body: formData,
		})
		if (!response.ok) {
			throw new Error("Network response was not ok")
		}
	} catch (error) {
		console.error("Error:", error)
	}
	/*
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
		*/
	loading.value = false
}

onMounted(() => {
	console.log(props)
})
</script>
