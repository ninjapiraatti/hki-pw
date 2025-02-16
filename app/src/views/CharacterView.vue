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
		if (response.ok) {
			const data = await response.json()
			character.value = data as Character
		} else if (response.status === 404) {
			console.warn("Character not found, assigning null.")
			character.value = null
		} else {
			throw new Error(`Error: ${response.status} ${response.statusText}`)
		}
	} catch (error) {
		console.error("Error:", error)
		character.value = null
	}
}

const postCharacter = async (newCharacter: Character) => {
	if (loading.value === true) return
	loading.value = true
	const formData = new FormData()

	formData.append("name", newCharacter.name)
	formData.append("id", String(characterId.value))
	formData.append("bio", newCharacter.bio)
	if (newCharacter.image) {
		formData.append("image", newCharacter.image)
	}
	formData.append("strength", String(newCharacter.strength))
	formData.append("perception", String(newCharacter.perception))
	formData.append("endurance", String(newCharacter.endurance))
	formData.append("charisma", String(newCharacter.charisma))
	formData.append("intelligence", String(newCharacter.intelligence))
	formData.append("agility", String(newCharacter.agility))
	formData.append("luck", String(newCharacter.luck))

	try {
		let url = ""
		if (character.value) {
			url = `http://localhost:8888/hki-pw/characters/${characterId.value}/`
		} else {
			url = `http://localhost:8888/hki-pw/characters/`
		}
		const response = await fetch(url, {
			method: "POST",
			body: formData,
		})
		if (!response.ok) {
			throw new Error("Network response was not ok")
		}
		const data = await response.json()
		character.value = data as Character
	} catch (error) {
		console.error("Error:", error)
	}
	loading.value = false
}

onMounted(() => {
	/*
	if (typeof characterId.value === 'string') {
		getCharacter(characterId.value)
	}
		*/
})
</script>
