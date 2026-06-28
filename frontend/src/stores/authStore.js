import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { auth } from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token'))

  const isAuthenticated = computed(() => !!token.value)

  const register = async (name, email, password) => {
    const response = await auth.register({
      name,
      email,
      password,
      password_confirmation: password,
    })
    token.value = response.data.token
    user.value = response.data.user
    localStorage.setItem('auth_token', token.value)
    return response.data
  }

  const login = async (email, password) => {
    const response = await auth.login({ email, password })
    token.value = response.data.token
    user.value = response.data.user
    localStorage.setItem('auth_token', token.value)
    return response.data
  }

  const logout = async () => {
    await auth.logout()
    token.value = null
    user.value = null
    localStorage.removeItem('auth_token')
  }

  const fetchUser = async () => {
    if (!token.value) return
    const response = await auth.user()
    user.value = response.data
  }

  return {
    user,
    token,
    isAuthenticated,
    register,
    login,
    logout,
    fetchUser,
  }
})