import ArticlesView from "../views/ArticlesView.vue"
import CharacterView from "../views/CharacterView.vue"
import HomeView from "../views/HomeView.vue"
import NotFoundView from "../views/NotFoundView.vue"

import { createRouter, createWebHistory } from "vue-router"

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			path: "/",
			name: "home",
			component: HomeView,
		},
		{
			path: "/articles",
			name: "Articles",
			component: ArticlesView,
		},
		{
			path: "/characters/:id",
			name: "Character",
			component: CharacterView,
		},
    /*
		{
			path: "/projects/:id",
			component: ArticleView,
		},
		{
			path: "/profile",
			name: "Profile",
			component: ProfileView,
			meta: {
				requiresAuth: true,
			},
		},
		{
			path: "/slides",
			name: "slides",
			meta: { hideHeader: true },
			// route level code-splitting
			// this generates a separate chunk (About.[hash].js) for this route
			// which is lazy-loaded when the route is visited.
			component: () => import("../views/SlideDeckView.vue"),
		},
    */
		{
			path: "/:catchAll(.*)",
			component: NotFoundView,
		},
	],
})

export default router