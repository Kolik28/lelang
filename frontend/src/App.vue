<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-indigo-600 shadow-md">
      <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
          <!-- Brand Logo -->
          <router-link to="/" class="text-white text-xl font-bold">
            Lelang Online
          </router-link>

          <!-- Desktop Menu -->
          <div class="hidden md:flex items-center gap-6">
            <router-link 
              to="/" 
              class="text-white font-medium hover:opacity-80 transition-opacity"
            >
              Home
            </router-link>
            
            <router-link 
              v-if="authStore.isAuthenticated"
              to="/my-auctions" 
              class="text-white font-medium hover:opacity-80 transition-opacity"
            >
              Lelang Saya
            </router-link>

            <router-link 
              to="/about" 
              class="text-white font-medium hover:opacity-80 transition-opacity"
            >
              About
            </router-link>

            <!-- Auth Section -->
            <div v-if="authStore.isAuthenticated" class="flex items-center gap-3 ml-4 pl-4 border-l border-indigo-400">
              <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-sm">
                {{ getUserInitials() }}
              </div>
              <span class="text-white text-sm font-medium">{{ authStore.user?.name }}</span>
              <button 
                @click="handleLogout" 
                class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition-colors"
              >
                Logout
              </button>
            </div>

            <div v-else class="flex items-center gap-2 ml-4 pl-4 border-l border-indigo-400">
              <router-link 
                to="/login" 
                class="px-4 py-1.5 bg-white text-indigo-600 font-medium text-sm rounded-lg hover:bg-opacity-90 transition-colors"
              >
                Login
              </router-link>
              <router-link 
                to="/register" 
                class="px-4 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-medium text-sm rounded-lg transition-colors"
              >
                Daftar
              </router-link>
            </div>
          </div>

          <!-- Mobile Menu Toggle -->
          <button 
            class="md:hidden p-2 text-white hover:bg-indigo-500 rounded-lg transition-colors"
            @click="mobileMenuOpen = !mobileMenuOpen"
          >
            <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="md:hidden py-4 border-t border-indigo-500">
          <div class="flex flex-col gap-2">
            <router-link 
              to="/" 
              class="px-4 py-2 text-white font-medium hover:bg-indigo-500 rounded-lg transition-colors"
              @click="mobileMenuOpen = false"
            >
              Home
            </router-link>
            
            <router-link 
              v-if="authStore.isAuthenticated"
              to="/my-auctions" 
              class="px-4 py-2 text-white font-medium hover:bg-indigo-500 rounded-lg transition-colors"
              @click="mobileMenuOpen = false"
            >
              Lelang Saya
            </router-link>

            <div class="border-t border-indigo-500 pt-2 mt-2">
              <div v-if="authStore.isAuthenticated" class="px-4 py-2">
                <div class="flex items-center gap-3 mb-3">
                  <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-sm">
                    {{ getUserInitials() }}
                  </div>
                  <span class="text-white font-medium">{{ authStore.user?.name }}</span>
                </div>
                <button 
                  @click="handleLogout" 
                  class="w-full px-4 py-2 bg-white/20 hover:bg-white/30 text-white font-medium rounded-lg transition-colors"
                >
                  Logout
                </button>
              </div>

              <div v-else class="flex flex-col gap-2 px-4">
                <router-link 
                  to="/login" 
                  class="px-4 py-2 bg-white text-indigo-600 font-medium text-center rounded-lg hover:bg-opacity-90 transition-colors"
                  @click="mobileMenuOpen = false"
                >
                  Login
                </router-link>
                <router-link 
                  to="/register" 
                  class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium text-center rounded-lg transition-colors"
                  @click="mobileMenuOpen = false"
                >
                  Daftar
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 py-8">
      <div class="max-w-6xl mx-auto px-4">
        <router-view />
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6">
      <div class="max-w-6xl mx-auto px-4 text-center text-slate-600 text-sm">
        <p>&copy; 2026 Platform Lelang Online. Prindapan Team</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from './stores/authStore'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()
const mobileMenuOpen = ref(false)

const handleLogout = async () => {
  try {
    await authStore.logout()
    mobileMenuOpen.value = false
    router.push('/login')
  } catch (err) {
    console.error('Logout error:', err)
  }
}

const getUserInitials = () => {
  const name = authStore.user?.name || 'User'
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}
</script>