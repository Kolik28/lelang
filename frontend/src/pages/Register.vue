<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 px-4 py-8">
    <div class="w-full max-w-md">
      <!-- Card -->
      <div class="bg-white rounded-xl shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <h2 class="text-2xl font-bold text-gray-900">Daftar Akun</h2>
          <p class="text-sm text-gray-500 mt-2">Buat akun baru Anda</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleRegister" class="space-y-5">
          <!-- Nama -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Nama
            </label>
            <input 
              v-model="name" 
              type="text" 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
              placeholder="Nama lengkap"
              required
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Email
            </label>
            <input 
              v-model="email" 
              type="email" 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
              placeholder="nama@email.com"
              required
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Password
            </label>
            <input 
              v-model="password" 
              type="password" 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
              placeholder="Minimal 8 karakter"
              minlength="8"
              required
            />
          </div>

          <!-- Konfirmasi Password -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Konfirmasi Password
            </label>
            <input 
              v-model="passwordConfirm" 
              type="password" 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
              placeholder="Ulangi password"
              required
            />
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            :disabled="loading"
            class="w-full py-3 px-4 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:from-emerald-500 disabled:hover:to-green-600"
          >
            {{ loading ? 'Loading...' : 'Daftar' }}
          </button>
        </form>

        <!-- Error Alert -->
        <div 
          v-if="error" 
          class="mt-5 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg"
        >
          <p class="text-sm text-red-700">{{ error }}</p>
        </div>

        <!-- Login Link -->
        <p class="text-center text-sm text-gray-600 mt-6">
          Sudah punya akun? 
          <router-link 
            to="/login" 
            class="text-emerald-600 font-semibold hover:text-emerald-700 transition-colors"
          >
            Login di sini
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirm = ref('')
const loading = ref(false)
const error = ref(null)

const handleRegister = async () => {
  if (password.value !== passwordConfirm.value) {
    error.value = 'Password tidak cocok'
    return
  }

  loading.value = true
  error.value = null

  try {
    await authStore.register(name.value, email.value, password.value)
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.message || 'Registrasi gagal'
  } finally {
    loading.value = false
  }
}
</script>