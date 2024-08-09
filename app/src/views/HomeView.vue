<template>
	<RouterLink :to="characterLink" tag="button">Make a character</RouterLink>
</template>

<script setup lang="ts">
import { computed } from "vue"
import { v4 as uuidv4 } from "uuid"

const characterId = uuidv4()
const characterLink = computed(() => {
	return `/characters/${characterId}`
})
</script>
