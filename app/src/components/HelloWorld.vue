<template>
	<h1>Testing PW POST</h1>

	<div class="card">
		<button type="button" @click="getProcessWireData">Get shit</button>
		<p>{{ responseMessage }}</p>
	</div>

	<p>
		Check out
		<a href="https://vuejs.org/guide/quick-start.html#local" target="_blank">create-vue</a>, the official Vue + Vite
		starter
	</p>
	<p>
		Learn more about IDE Support for Vue in the
		<a href="https://vuejs.org/guide/scaling-up/tooling.html#ide-support" target="_blank">Vue Docs Scaling up Guide</a>.
	</p>
	<p class="read-the-docs">Click on the Vite and Vue logos to learn more</p>
</template>

<script setup lang="ts">
import { ref } from "vue"

const responseMessage = ref("")

const getProcessWireData = async () => {
	const payload = {
		foo: "bar",
	}

	try {
		const response = await fetch("http://localhost:8888/hki-pw/this-is-htmx-test/", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"X-Requested-With": "XMLHttpRequest",
			},
			body: JSON.stringify(payload),
		})

		if (!response.ok) {
			throw new Error("Network response was not ok")
		}

		const data = await response.json()
		responseMessage.value = data // Assuming your server returns a message
	} catch (error) {
		console.error("Error:", error)
	}
}
</script>

<style scoped>
.read-the-docs {
	color: #888;
}
</style>
