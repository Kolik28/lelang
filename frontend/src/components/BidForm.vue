<template>
  <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
      <div class="flex items-center gap-2 text-white">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
        </svg>
        <h3 class="font-bold text-lg">Tempat Penawaran</h3>
      </div>
    </div>

    <form @submit.prevent="submitBid" class="p-6 space-y-5">
      <!-- Info Tawaran Minimum -->
      <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 mb-1">Tawaran Minimum</p>
            <p class="text-2xl font-bold text-gray-900">
              Rp {{ formatPrice(minimumBid) }}
            </p>
          </div>
          <div class="bg-white p-3 rounded-full shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Input dengan prefix Rp -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Masukkan Tawaran Anda
        </label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <span class="text-gray-500 font-semibold">Rp</span>
          </div>
          <input 
            v-model.number="bidAmount" 
            type="number" 
            :min="minimumBid"
            step="0.01"
            class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border-2 border-gray-200 rounded-xl text-lg font-semibold text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all"
            :placeholder="`Minimal ${formatPrice(minimumBid)}`"
            required
          />
        </div>
        <p class="mt-2 text-xs text-gray-500">
          Kenaikan minimum: Rp {{ formatPrice(props.bidIncrement) }}
        </p>
      </div>

      <!-- Button Submit -->
      <button 
        type="submit"
        class="w-full relative py-3.5 px-6 rounded-xl font-bold text-white shadow-lg transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:shadow-none bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0"
        :disabled="loading || bidAmount < minimumBid"
      >
        <!-- Loading Spinner -->
        <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        
        <!-- Icon Hammer -->
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
        
        <span>{{ loading ? 'Memproses...' : 'Tawar Sekarang' }}</span>
      </button>

      <!-- Alert Error -->
      <div 
        v-if="error" 
        class="flex items-start gap-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg animate-fadeIn"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <p class="text-sm text-red-700 font-medium">{{ error }}</p>
      </div>

      <!-- Alert Success -->
      <div 
        v-if="success" 
        class="flex items-start gap-3 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg animate-fadeIn"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
          <p class="text-sm text-green-700 font-semibold">Penawaran Berhasil!</p>
          <p class="text-xs text-green-600 mt-0.5">Tawaran Anda telah tercatat dalam sistem.</p>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuctionStore } from '../stores/auctionStore'
import { useAuthStore } from '../stores/authStore'

const props = defineProps({
  auctionId: Number,
  highestBid: Number,
  bidIncrement: Number,
  sellerId: Number,
  isActive: Boolean,
})

const emit = defineEmits(['bid-placed'])

const auctionStore = useAuctionStore()
const authStore = useAuthStore()

const bidAmount = ref(null)
const loading = ref(false)
const error = ref(null)
const success = ref(false)

const minimumBid = computed(() => {
  return props.highestBid + props.bidIncrement
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const submitBid = async () => {
  if (!authStore.isAuthenticated || !authStore.user) {
    error.value = 'Anda harus login terlebih dahulu'
    return
  }

  if (authStore.user?.id === props.sellerId) {
    error.value = 'Anda tidak bisa menawar lelang milik Anda sendiri'
    return
  }

  if (!bidAmount.value || bidAmount.value < minimumBid.value) {
    error.value = `Tawaran minimum adalah Rp ${formatPrice(minimumBid.value)}`
    return
  }

  loading.value = true
  error.value = null
  success.value = false

  try {
    await auctionStore.placeBid(props.auctionId, bidAmount.value)
    success.value = true
    bidAmount.value = null

    setTimeout(() => {
      success.value = false
    }, 3000)

    emit('bid-placed')
  } catch (err) {
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else if (err.message) {
      error.value = err.message
    } else {
      error.value = 'Gagal menempatkan penawaran'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}
</style>