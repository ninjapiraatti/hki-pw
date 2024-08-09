<template>
	<div class="character-form">
		<h3>{{ character ? "Edit Character" : "Create Character" }}</h3>
		<form @submit.prevent="submitForm">
			<div class="mb-3">
				<label for="name" class="form-label">Name</label>
				<input type="text" class="form-control" id="name" v-model="form.name" required />
			</div>

			<div class="mb-3" v-for="(stat, index) in stats" :key="index">
				<label :for="stat" class="form-label">{{ stat }}</label>
				<input
					type="number"
					class="form-control"
					:id="stat"
					v-model.number="form[stat.toLowerCase()]"
					min="1"
					max="10"
					required
				/>
			</div>

			<button type="submit" class="btn btn-primary">
				{{ character ? "Update Character" : "Create Character" }}
			</button>
		</form>
	</div>
</template>

<script setup lang="ts">
import { ref, watch, defineProps } from "vue"
import { Character } from "@/types"

const props = defineProps<{
	character?: Character
	onSubmit: (character: Character) => void
}>()

const stats = ["Strength", "Perception", "Endurance", "Charisma", "Intelligence", "Agility", "Luck"]

const form = ref<Character>({
	name: "",
	strength: 1,
	perception: 1,
	endurance: 1,
	charisma: 1,
	intelligence: 1,
	agility: 1,
	luck: 1,
})

// Watch for changes in the character prop to populate the form for editing
watch(
	() => props.character,
	(newCharacter) => {
		if (newCharacter) {
			form.value = { ...newCharacter }
		}
	}
)

// Submit the form
const submitForm = () => {
	props.onSubmit(form.value)
}
</script>

<style scoped>
.character-form {
	max-width: 400px;
	margin: auto;
}
</style>
