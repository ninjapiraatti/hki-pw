<template>
	<div class="container-fluid">
		<div class="angled-corner p-4 mb-4 mt-4">
			<h1>Articles</h1>
			<ul>
				<li v-for="article in articles" :key="article.id">
					<router-link :to="`/articles/${article.id}`">{{ article.data.title }}</router-link>
				</li>
			</ul>
			<div class="card">
				<button type="button" @click="getProcessWireData">Post an article</button>
				<p>{{ responseMessage }}</p>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { Article } from "@/types"
import { ref, onMounted } from "vue"
const responseMessage = ref("")
const articles = ref()

const getProcessWireData = async () => {
	try {
		const response = await fetch("http://localhost:8888/hki-pw/articles/", {
			method: "GET",
			headers: {
				"Content-Type": "application/json",
				"X-Requested-With": "XMLHttpRequest",
			},
		})

		if (response.ok) {
			const data = await response.json()
			console.log(data)
			articles.value = data.pages as Article[]
		} else if (response.status === 404) {
			console.warn("Character not found, assigning null.")
			articles.value = null
		} else {
			throw new Error(`Error: ${response.status} ${response.statusText}`)
		}
	} catch (error) {
		console.error("Error:", error)
	}
}

onMounted(() => {
	getProcessWireData()
})
</script>
