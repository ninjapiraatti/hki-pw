<template>
	<div class="character-view">
		<h2 v-if="character">{{ character.name }}</h2>
		<h2 v-else>Create Character</h2>
		<CharacterForm :character="character" @onSubmit="postCharacter" />
	</div>
</template>

<script setup lang="ts">
import { onMounted, ref, computed } from "vue"
import CharacterForm from "@/components/CharacterForm.vue"
import { Character } from "@/types"
import { useRoute } from "vue-router"

const route = useRoute()
const character = ref<Character | null>(null)
const loading = ref(false)

const characterId = computed(() => {
	return route.params.id
})

const getCharacter = async (characterId: string) => {
	try {
		const response = await fetch(`http://localhost:8888/hki-pw/characters/${characterId}/`, {
			method: "GET",
			headers: {
				"Content-Type": "application/json",
				"X-Requested-With": "XMLHttpRequest",
			},
		})
		if (!response.ok) {
			throw new Error("Network response was not ok")
		}
		const data = await response.json()
		character.value = data as Character
	} catch (error) {
		console.error("Error:", error)
	}
}

const postCharacter = async (character: Character) => {
	if (loading.value === true) return
	loading.value = true
	const formData = new FormData()

	formData.append("name", character.name)
	formData.append("id", String(characterId.value))
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
		character.value = response
	} catch (error) {
		console.error("Error:", error)
	}
	loading.value = false
}

onMounted(() => {
	getCharacter(characterId?.value)
})
</script>
