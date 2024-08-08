import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from "./router"

const app = createApp(App)

app.use(router)

/*
router.beforeEach((to, from, next) => {
	const requiresAuth = to.meta.requiresAuth
	const userStore = useUsersStore()
	const isLoggedIn = userStore.isLoggedIn
	console.log("NAV GUARD. isLoggedin:", isLoggedIn)

	if (requiresAuth && !isLoggedIn) {
		next({ name: "Login" })
	} else {
		next()
	}
})
*/

app.mount("#app")
