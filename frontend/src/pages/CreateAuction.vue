<template>
  <div class="min-h-screen py-8 sm:py-12">
    <!-- Header -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
      <div class="text-center">
        <h1 class="text-4xl sm:text-5xl font-black text-slate-900 mb-3">Buat Lelang Baru</h1>
        <p class="text-lg text-slate-600">Jual barang Anda dengan sistem lelang yang transparan dan aman</p>
      </div>
    </div>

    <!-- Form Container -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <form @submit.prevent="handleSubmit" class="bg-white rounded-2xl shadow-lg border border-slate-200 p-8 sm:p-10 space-y-8">
        
        <!-- Title Field -->
        <div class="space-y-3">
          <label class="block text-sm font-bold text-slate-900">
            Judul Lelang
            <span class="text-red-500 ml-1">*</span>
          </label>
          <input 
            v-model="form.title" 
            type="text" 
            class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all duration-200"
            maxlength="255"
            placeholder="Contoh: iPhone 13 Pro Max 256GB Silver"
            required
          />
          <p class="text-xs text-slate-500">Berikan judul yang jelas dan menarik untuk barang Anda</p>
        </div>

        <!-- Description Field -->
        <div class="space-y-3">
          <label class="block text-sm font-bold text-slate-900">
            Deskripsi
            <span class="text-red-500 ml-1">*</span>
          </label>
          <textarea 
            v-model="form.description"
            class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all duration-200 resize-none"
            rows="5"
            minlength="10"
            placeholder="Jelaskan kondisi barang secara detail, termasuk kekurangan atau kerusakan jika ada..."
            required
          />
          <p class="text-xs text-slate-500">Minimal 10 karakter. Semakin detail, semakin banyak pembeli yang tertarik</p>
        </div>

        <!-- Image URL Field -->
        <div class="space-y-3">
          <label class="block text-sm font-bold text-slate-900">
            URL Gambar
            <span class="text-slate-500 text-xs ml-1">(Opsional)</span>
          </label>
          <input 
            v-model="form.image_url" 
            type="url" 
            class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all duration-200"
            placeholder="https://example.com/image.jpg"
          />
          <p class="text-xs text-slate-500">Gunakan URL gambar yang valid. Gambar akan ditampilkan di kartu lelang</p>
        </div>

        <!-- Price Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-3">
            <label class="block text-sm font-bold text-slate-900">
              Harga Awal
              <span class="text-red-500 ml-1">*</span>
            </label>
            <div class="relative">
              <span class="absolute left-4 top-3 text-slate-600 font-semibold">Rp</span>
              <input 
                v-model.number="form.starting_price" 
                type="number" 
                class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all duration-200"
                min="0.01"
                step="1000"
                placeholder="0"
                required
              />
            </div>
            <p class="text-xs text-slate-500">Harga awal lelang Anda</p>
          </div>

          <div class="space-y-3">
            <label class="block text-sm font-bold text-slate-900">
              Kenaikan Tawaran Minimum
              <span class="text-red-500 ml-1">*</span>
            </label>
            <div class="relative">
              <span class="absolute left-4 top-3 text-slate-600 font-semibold">Rp</span>
              <input 
                v-model.number="form.bid_increment" 
                type="number" 
                class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all duration-200"
                min="0.01"
                step="1000"
                placeholder="0"
                required
              />
            </div>
            <p class="text-xs text-slate-500">Penawaran harus naik minimal jumlah ini</p>
          </div>
        </div>

        <!-- DateTime Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-3">
            <label class="block text-sm font-bold text-slate-900">
              Tanggal & Waktu Mulai
              <span class="text-red-500 ml-1">*</span>
            </label>
            <input 
              v-model="form.starts_at" 
              type="datetime-local" 
              class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all duration-200"
              :min="minDateTime"
              required
            />
            <p class="text-xs text-slate-500">Lelang akan dimulai pada waktu ini</p>
          </div>

          <div class="space-y-3">
            <label class="block text-sm font-bold text-slate-900">
              Tanggal & Waktu Berakhir
              <span class="text-red-500 ml-1">*</span>
            </label>
            <input 
              v-model="form.ends_at" 
              type="datetime-local" 
              class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all duration-200"
              :min="form.starts_at || minDateTime"
              required
            />
            <p class="text-xs text-slate-500">Lelang akan berakhir pada waktu ini</p>
          </div>
        </div>

        <!-- Info Box -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-indigo-200 rounded-lg p-4 space-y-2">
          <div class="flex gap-3">
            <span class="text-xl flex-shrink-0">ℹ️</span>
            <div class="text-sm text-slate-700">
              <p class="font-semibold mb-1">Tips Membuat Lelang yang Sukses:</p>
              <ul class="space-y-1 text-xs">
                <li>• Gunakan foto berkualitas tinggi untuk barang Anda</li>
                <li>• Jelaskan kondisi barang dengan jujur dan detail</li>
                <li>• Tetapkan harga awal yang kompetitif</li>
                <li>• Berikan waktu lelang yang cukup (minimal 24 jam)</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <button 
          type="submit" 
          class="w-full py-4 px-6 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 disabled:from-slate-300 disabled:to-slate-300 disabled:text-slate-500 disabled:cursor-not-allowed disabled:shadow-none flex items-center justify-center gap-2 text-lg"
          :disabled="loading"
        >
          <span v-if="!loading">✨</span>
          <span v-else>⏳</span>
          {{ loading ? 'Memproses...' : 'Buat Lelang' }}
        </button>
      </form>

      <!-- Error Message -->
      <div v-if="error" class="mt-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-lg p-4 shadow-md">
        <div class="flex gap-3">
          <span class="text-2xl flex-shrink-0">❌</span>
          <div>
            <p class="font-semibold text-red-900 mb-1">Gagal Membuat Lelang</p>
            <p class="text-sm text-red-700">{{ error }}</p>
          </div>
        </div>
      </div>

      <!-- Success Message (Optional) -->
      <div v-if="success" class="mt-6 bg-gradient-to-r from-emerald-50 to-green-50 border-l-4 border-emerald-500 rounded-lg p-4 shadow-md">
        <div class="flex gap-3">
          <span class="text-2xl flex-shrink-0">✅</span>
          <div>
            <p class="font-semibold text-emerald-900 mb-1">Lelang Berhasil Dibuat!</p>
            <p class="text-sm text-emerald-700">Anda akan dialihkan ke halaman lelang saya...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuctionStore } from '../stores/auctionStore'
import { useAuthStore } from '../stores/authStore'

const router = useRouter()
const auctionStore = useAuctionStore()
const authStore = useAuthStore()

const form = ref({
  title: '',
  description: '',
  image_url: '',
  starting_price: 0,
  bid_increment: 0,
  starts_at: '',
  ends_at: '',
})

const loading = ref(false)
const error = ref(null)
const success = ref(false)

const minDateTime = computed(() => {
  const now = new Date()
  now.setHours(now.getHours() + 1)
  return now.toISOString().slice(0, 16)
})

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }
  form.value.starts_at = minDateTime.value
})

const handleSubmit = async () => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }

  if (!form.value.starts_at || !form.value.ends_at) {
    error.value = 'Tanggal mulai dan berakhir harus diisi'
    return
  }

  if (new Date(form.value.ends_at) <= new Date(form.value.starts_at)) {
    error.value = 'Tanggal berakhir harus lebih besar dari tanggal mulai'
    return
  }

  loading.value = true
  error.value = null
  success.value = false

  try {
    const payload = {
      title: form.value.title,
      description: form.value.description,
      image_url: form.value.image_url || null,
      starting_price: form.value.starting_price,
      bid_increment: form.value.bid_increment,
      starts_at: form.value.starts_at,  
      ends_at: form.value.ends_at,      
    }
    
    await auctionStore.createAuction(payload)
    success.value = true
    
    setTimeout(() => {
      router.push('/my-auctions')
    }, 2000)
  } catch (err) {
    if (err.response?.data?.errors) {
      error.value = Object.values(err.response.data.errors).flat().join(', ')
    } else {
      error.value = err.response?.data?.message || 'Gagal membuat lelang. Silakan coba lagi.'
    }
  } finally {
    loading.value = false
  }
}
</script>
