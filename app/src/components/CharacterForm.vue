<template>
	<div class="character-form">
		<h3>{{ character ? "Edit Character" : "Create Character" }}</h3>
		<form @submit.prevent="submitForm">
			<div class="mb-3 row">
				<label for="name" class="col-sm-2 col-form-label">Name</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="name" v-model="form.name" required />
				</div>
			</div>

			<div class="mb-3 row" v-for="(stat, index) in stats" :key="index">
				<label :for="stat" class="col-sm-2 col-form-label">{{ stat }}</label>
				<div class="col-sm-10">
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
			</div>

			<div class="text-center">
				<button type="submit" @click="submitForm" class="btn btn-primary">
					{{ character ? "Update Character" : "Create Character" }}
				</button>
			</div>
		</form>
	</div>
</template>

<script setup lang="ts">
import { ref, watch, defineProps } from "vue"
import { Character } from "@/types"

const emit = defineEmits(["onSubmit"])
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

const submitForm = () => {
	console.log("submittin")
	emit("onSubmit", form.value) // Emit the created/updated character
}
</script>
