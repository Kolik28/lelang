<template>
  <div class="group relative bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-slate-200">
    <!-- Image Section -->
    <div class="relative h-48 overflow-hidden bg-slate-200">
      <img 
        v-if="auction.image_url"
        :src="auction.image_url" 
        :alt="auction.title"
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
      />
      <div v-else class="w-full h-full flex items-center justify-center text-slate-400">
        <svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>

      <!-- Status Badge -->
      <div class="absolute top-3 left-3">
        <span 
          class="px-3 py-1 rounded-full text-xs font-bold text-white shadow-md"
          :class="{
            'bg-gradient-to-r from-emerald-500 to-green-600': auction.status === 'active',
            'bg-gradient-to-r from-amber-500 to-orange-600': auction.status === 'scheduled',
            'bg-gradient-to-r from-slate-500 to-gray-600': auction.status === 'ended'
          }"
        >
          {{ formatStatus(auction.status) }}
        </span>
      </div>

      <!-- Winner Badge (untuk auction yang sudah ended) -->
      <div v-if="showWinner && auction.status === 'ended'" class="absolute top-3 right-3">
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-amber-400 to-yellow-500 text-white shadow-md">
          🏆 {{ auction.highest_bidder || 'Tidak ada pemenang' }}
        </span>
      </div>
    </div>

    <!-- Content Section -->
    <div class="p-5">
      <!-- Title -->
      <h3 class="font-bold text-slate-900 text-lg mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">
        {{ auction.title }}
      </h3>

      <!-- Description -->
      <p class="text-sm text-slate-600 mb-4 line-clamp-2">
        {{ auction.description }}
      </p>

      <!-- Price Info -->
      <div class="mb-4">
        <p class="text-xs text-slate-500 font-semibold mb-1">Harga Saat Ini</p>
        <p class="text-2xl font-bold text-indigo-600">
          Rp {{ formatPrice(auction.highest_bid || auction.starting_price) }}
        </p>
        <p class="text-xs text-slate-500 mt-1">
          {{ auction.bids_count || 0 }} penawaran
        </p>
      </div>

      <!-- Timer atau Waktu Mulai -->
      <div v-if="auction.status === 'active'" class="mb-4">
        <div class="flex items-center gap-2 text-red-600 font-semibold text-sm">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Berakhir dalam: {{ countdown }}</span>
        </div>
      </div>

      <div v-else-if="auction.status === 'scheduled' && showStartsAt" class="mb-4">
        <div class="flex items-center gap-2 text-amber-600 font-semibold text-sm">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Dimulai: {{ formatDateTime(auction.starts_at) }}</span>
        </div>
      </div>

      <!-- Action Button -->
      <router-link
        :to="`/auctions/${auction.id}`"
        class="block w-full py-2.5 px-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-lg text-center transition-all duration-200 shadow-md hover:shadow-lg"
      >
        {{ auction.status === 'active' ? 'Tawar Sekarang' : 'Lihat Detail' }}
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  auction: {
    type: Object,
    required: true,
  },
  isEnded: {
    type: Boolean,
    default: false,
  },
  showWinner: {
    type: Boolean,
    default: false,
  },
  isMyWin: {
    type: Boolean,
    default: false,
  },
  showStartsAt: {
    type: Boolean,
    default: false,
  },
})

const countdown = ref('')
let timer = null

// Format status auction
const formatStatus = (status) => {
  const statuses = {
    active: 'Aktif',
    scheduled: 'Terjadwal',
    ended: 'Selesai',
  }
  return statuses[status] || status
}

// Format harga
const formatPrice = (price) => {
  if (!price || isNaN(price)) return '0'
  return new Intl.NumberFormat('id-ID').format(price)
}

// Format tanggal dan waktu
const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A'
  
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

// Update countdown timer
const updateCountdown = () => {
  if (!props.auction) return

  const now = new Date().getTime()
  
  if (props.auction.status === 'active') {
    const end = new Date(props.auction.ends_at).getTime()
    const distance = end - now

    if (distance < 0) {
      countdown.value = 'Berakhir'
      clearInterval(timer)
    } else {
      const hours = Math.floor(distance / (1000 * 60 * 60))
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
      const seconds = Math.floor((distance % (1000 * 60)) / 1000)
      countdown.value = `${hours}j ${minutes}m ${seconds}d`
    }
  } else if (props.auction.status === 'scheduled') {
    const start = new Date(props.auction.starts_at).getTime()
    const distance = start - now

    if (distance < 0) {
      countdown.value = 'Sedang dimulai...'
    } else {
      const hours = Math.floor(distance / (1000 * 60 * 60))
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
      const seconds = Math.floor((distance % (1000 * 60)) / 1000)
      countdown.value = `${hours}j ${minutes}m ${seconds}d`
    }
  }
}

onMounted(() => {
  updateCountdown()
  timer = setInterval(updateCountdown, 1000)
})

onUnmounted(() => {
  if (timer) {
    clearInterval(timer)
  }
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>