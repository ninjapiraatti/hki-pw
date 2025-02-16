<template>
	<div class="container">
		<form @submit.prevent="submitForm">
			<div class="mb-3 w-100 row">
				<label for="name" class="col-sm-2 col-form-label">Name</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="name" v-model="form.name" required />
				</div>
			</div>

			<div class="mb-3 w-100 row">
				<label for="bio" class="col-sm-2 col-form-label">Bio</label>
				<div class="col-sm-10">
					<textarea class="form-control" id="bio" name="bio" v-model="form.bio" required />
				</div>
			</div>

			<div v-if="imageUrl.length">
				<img :src="imageUrl" class="mb-3 w-100" />
			</div>

			<div class="mb-3 w-100 row">
				<label for="image" class="form-label">Picture</label>
				<input class="form-control" type="file" id="image" @change="onImageChange" />
			</div>

			<div class="mb-3 w-100 row" v-for="(stat, index) in stats" :key="index">
				<label :for="stat" class="col-sm-2 col-form-label">{{ stat }}</label>
				<div class="col-sm-10">
					<input
						type="number"
						class="form-control"
						:id="stat"
						v-model.number="form[stat]"
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
import { ref, watch, defineProps, computed } from "vue"
import { Character } from "@/types"

const emit = defineEmits(["onSubmit"])
const props = defineProps<{
	character?: Character | null
}>()

const stats = ["strength", "perception", "endurance", "charisma", "intelligence", "agility", "luck"] as const
type Stat = typeof stats[number]

const onImageChange = (event: Event) => {
	const target = event.target as HTMLInputElement
	if (target.files && target.files.length > 0) {
		form.value.image = target.files[0]
	}
}

const imageUrl = computed(() => {
	const baseUrl = "http://localhost:8888"
	return props.character?.image ? `${baseUrl}${props.character.image}` : ""
})

const form = ref<Character>({
	name: "",
	bio: "",
	image: null,
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
	emit("onSubmit", form.value)
}
</script>
