<template>
	<div class="character-view">
		<h2>Character Details</h2>
		<div v-if="character">
			<h3>{{ character.name }}</h3>
			<ul>
				<li v-for="(stat, index) in stats" :key="index">{{ stat }}: {{ character[stat.toLowerCase()] }}</li>
			</ul>
			<button class="btn btn-secondary" @click="editCharacter">Edit Character</button>
		</div>
		<CharacterForm v-if="isEditing" :character="character" :onSubmit="handleSubmit" />
	</div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue"
import CharacterForm from "@/components/CharacterForm.vue"
import { Character } from "@/types"

const props = defineProps<{
	character?: Character
}>()

const isEditing = ref(false)
const stats = ["Strength", "Perception", "Endurance", "Charisma", "Intelligence", "Agility", "Luck"]

// Function to handle the submission of the form
const handleSubmit = (character: Character) => {
	// Handle the character submission (e.g., save to a database or state)
	console.log("Character submitted:", character)
	isEditing.value = false // Exit editing mode
}

// Function to enter editing mode
const editCharacter = () => {
	isEditing.value = true
}

onMounted(() => {
	console.log(props)
})
</script>

<style scoped>
.character-view {
	max-width: 600px;
	margin: auto;
}
</style>
