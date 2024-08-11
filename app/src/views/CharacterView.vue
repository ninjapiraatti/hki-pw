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
		<CharacterForm :character="character" @onSubmit="handleSubmit" />
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

// Function to handle the submission of the form
const handleSubmit = (character: Character) => {
	// Handle the character submission (e.g., save to a database or state)
	console.log("Character submitted:", character)
}

onMounted(() => {
	console.log(props)
})
</script>
