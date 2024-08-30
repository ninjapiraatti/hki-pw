<template>
	<nav class="navbar navbar-expand-lg bg-body-tertiary">
		<div class="container-fluid">
			<a class="navbar-brand" href="#">Navbar</a>
			<button
				class="navbar-toggler"
				type="button"
				data-bs-toggle="collapse"
				data-bs-target="#navbarSupportedContent"
				aria-controls="navbarSupportedContent"
				aria-expanded="false"
				aria-label="Toggle navigation"
			>
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="#">Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Link</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Dropdown
						</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="#">Action</a></li>
							<li><a class="dropdown-item" href="#">Another action</a></li>
							<li><hr class="dropdown-divider" /></li>
							<li><a class="dropdown-item" href="#">Something else here</a></li>
						</ul>
					</li>
					<li class="nav-item">
						<a class="nav-link disabled" aria-disabled="true">Disabled</a>
					</li>
				</ul>
				<form class="d-flex" role="search">
					<input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" />
					<button class="btn btn-outline-success" type="submit">Search</button>
				</form>
			</div>
		</div>
	</nav>
	<!--<header class="bg-gray-800 text-white">
		<nav class="mx-auto px-4 py-2 flex items-center justify-between">
			<div>
				<router-link to="/" class="block w-8">
					<UserIcon class="h-6 w-6" />
				</router-link>
			</div>
			<div class="hidden md:flex items-center space-x-4 z-10">
				<div class="relative">
					<button disabled @click.stop="toggleDropdown" class="focus:outline-none">
						<UserIcon class="h-6 w-6" />
					</button>
					<div v-if="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white text-black shadow-md rounded-md py-2">
						<router-link to="/login" class="block px-4 py-2 hover:bg-gray-200">Log in</router-link>
					</div>
				</div>
			</div>
			<button disabled @click.stop="toggleMenu" class="md:hidden focus:outline-none">
				<template v-if="!menuOpen">
					<Bars3Icon class="h-6 w-6" />
				</template>
				<template v-else>
					<XMarkIcon class="h-6 w-6" />
				</template>
			</button>
		</nav>
		<div class="container mx-auto px-4" v-if="menuOpen">
			<div class="md:hidden flex flex-col space-y-2 mt-4">
				<router-link to="/about" class="text-white hover:text-gray-300">About</router-link>
			</div>
		</div>
	</header>-->
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue"
import { Bars3Icon, XMarkIcon, UserIcon } from "@heroicons/vue/24/outline"

const menuOpen = ref(false)
const dropdownOpen = ref(false)

const toggleDropdown = () => {
	dropdownOpen.value = !dropdownOpen.value
}

const toggleMenu = () => {
	menuOpen.value = !menuOpen.value
}

const closeMenus = () => {
	dropdownOpen.value = false
	menuOpen.value = false
}

const handleClickOutside = () => {
	closeMenus()
}

onMounted(async () => {
	document.addEventListener("click", handleClickOutside)
})

onUnmounted(() => {
	document.removeEventListener("click", handleClickOutside)
})
</script>
