import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from './stores/authStore'

// Pages
import Home from './pages/Home.vue'
import Login from './pages/Login.vue'
import Register from './pages/Register.vue'
import AuctionDetail from './pages/AuctionDetail.vue'
import MyAuctions from './pages/MyAuctions.vue'
import CreateAuction from './pages/CreateAuction.vue'
import About from './pages/About.vue'

const routes = [
  { path: '/', component: Home, name: 'home' },
  { path: '/login', component: Login, name: 'login' },
  { path: '/register', component: Register, name: 'register' },
  { 
    path: '/auctions/:id', 
    component: AuctionDetail,
    name: 'auctionDetail' 
  },
  { 
    path: '/my-auctions', 
    component: MyAuctions, 
    name: 'myAuctions',
    meta: { requiresAuth: true }
  },
  { 
    path: '/create-auction', 
    component: CreateAuction, 
    name: 'createAuction',
    meta: { requiresAuth: true }
  },
  {
    path: '/about',
    component: About,
    name: 'about'
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Guard untuk protected routes
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.token) {
    next({ name: 'login', query: { redirect: to.fullPath } })
  } else {
    next()
  }
})

export default router