<template>
  <div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b-2 border-gray-100">
      <div>
        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
          Lelang Saya
        </h1>
        <p class="text-gray-500 mt-1 text-sm">
          Kelola semua lelang yang Anda buat
        </p>
      </div>
      <router-link 
        to="/create-auction" 
        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-bold text-white shadow-lg transition-all duration-200 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Buat Lelang Baru
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="auctionStore.loading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="bg-white rounded-2xl shadow-md p-5 animate-pulse">
        <div class="flex flex-col md:flex-row gap-5">
          <div class="w-full md:w-48 h-48 bg-gray-200 rounded-xl"></div>
          <div class="flex-1 space-y-3">
            <div class="h-6 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 rounded w-full"></div>
            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
            <div class="flex gap-3 pt-2">
              <div class="h-10 bg-gray-200 rounded w-24"></div>
              <div class="h-10 bg-gray-200 rounded w-24"></div>
              <div class="h-10 bg-gray-200 rounded w-24"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="auctionStore.auctionsList.length === 0" class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <div class="p-12 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full mb-5">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Lelang</h3>
        <p class="text-gray-500 mb-6 max-w-md mx-auto">
          Anda belum membuat lelang apapun. Mulai jual barang Anda dengan membuat lelang pertama!
        </p>
        <router-link 
          to="/create-auction" 
          class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-white shadow-lg transition-all duration-200 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl hover:-translate-y-0.5"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Buat Lelang Pertama
        </router-link>
      </div>
    </div>

    <!-- Auctions List -->
    <div v-else class="space-y-4">
      <div 
        v-for="auction in auctionStore.auctionsList" 
        :key="auction.id" 
        class="group bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
      >
        <div class="flex flex-col md:flex-row">
          <!-- Image -->
          <div class="relative md:w-56 md:flex-shrink-0">
            <img 
              v-if="auction.image_url"
              :src="auction.image_url" 
              :alt="auction.title"
              class="w-full h-48 md:h-full object-cover"
            />
            <div v-else class="w-full h-48 md:h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>

            <!-- Status Badge (Overlay) -->
            <div 
              class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold shadow-md backdrop-blur-sm"
              :class="statusClass(auction.status)"
            >
              <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass(auction.status)"></span>
              {{ formatStatus(auction.status) }}
            </div>
          </div>

          <!-- Content -->
          <div class="flex-1 p-5 flex flex-col">
            <div class="flex-1">
              <div class="flex items-start justify-between gap-3 mb-2">
                <h3 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-1">
                  {{ auction.title }}
                </h3>
              </div>

              <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                {{ auction.description }}
              </p>

              <!-- Stats -->
              <div class="grid grid-cols-3 gap-3 mb-4">
                <!-- Starting Price -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-3 border border-blue-100">
                  <div class="flex items-center gap-1.5 text-xs text-blue-600 font-semibold mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Harga Awal
                  </div>
                  <p class="text-sm font-bold text-gray-900">
                    Rp {{ formatPrice(auction.starting_price) }}
                  </p>
                </div>

                <!-- Bids Count -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-3 border border-purple-100">
                  <div class="flex items-center gap-1.5 text-xs text-purple-600 font-semibold mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Penawaran
                  </div>
                  <p class="text-sm font-bold text-gray-900">
                    {{ auction.bids_count || 0 }} 
                    <span class="text-xs font-normal text-gray-500">bid</span>
                  </p>
                </div>

                <!-- Highest Bid -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-3 border border-amber-100">
                  <div class="flex items-center gap-1.5 text-xs text-amber-600 font-semibold mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Tertinggi
                  </div>
                  <p class="text-sm font-bold text-gray-900">
                    Rp {{ formatPrice(auction.highest_bid) }}
                  </p>
                </div>
              </div>

              <!-- Countdown Mini -->
              <div v-if="auction.status === 'active'" class="flex items-center gap-2 text-sm">
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 rounded-lg border border-red-100">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span class="font-semibold">Berakhir:</span>
                  <span class="font-mono">{{ formatEndDate(auction.ends_at) }}</span>
                </div>
              </div>
              <div v-else-if="auction.status === 'scheduled'" class="flex items-center gap-2 text-sm">
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg border border-amber-100">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span class="font-semibold">Dimulai:</span>
                  <span class="font-mono">{{ formatEndDate(auction.ends_at) }}</span>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap items-center gap-2 pt-4 mt-4 border-t border-gray-100">
              <router-link 
                :to="`/auctions/${auction.id}`" 
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-all duration-200 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 hover:shadow-md"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Lihat Detail
              </router-link>

              <button 
                v-if="auction.status === 'scheduled'"
                @click="handleDelete(auction.id)"
                :disabled="deleting === auction.id"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-all duration-200 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 hover:shadow-md disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:shadow-none"
              >
                <svg v-if="deleting === auction.id" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                {{ deleting === auction.id ? 'Menghapus...' : 'Hapus' }}
              </button>

              <!-- Quick Stats Badge -->
              <div class="ml-auto hidden sm:flex items-center gap-1.5 text-xs text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span class="font-mono">#{{ auction.id }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuctionStore } from '../stores/auctionStore'
import { useAuthStore } from '../stores/authStore'
import dayjs from 'dayjs'

const auctionStore = useAuctionStore()
const authStore = useAuthStore()
const deleting = ref(null)

const formatPrice = (price) => {
  if (!price || isNaN(price)) return '0'
  return new Intl.NumberFormat('id-ID').format(price)
}

const formatStatus = (status) => {
  const statuses = {
    scheduled: 'Terjadwal',
    active: 'Aktif',
    ended: 'Selesai',
  }
  return statuses[status] || status
}

const statusClass = (status) => {
  const classes = {
    scheduled: 'bg-amber-400/90 text-amber-900',
    active: 'bg-emerald-500/90 text-white',
    ended: 'bg-gray-500/90 text-white',
  }
  return classes[status] || 'bg-gray-500/90 text-white'
}

const statusDotClass = (status) => {
  const classes = {
    scheduled: 'bg-amber-900',
    active: 'bg-white animate-pulse',
    ended: 'bg-white',
  }
  return classes[status] || 'bg-white'
}

const formatEndDate = (date) => {
  if (!date) return '-'
  return dayjs(date).format('DD MMM, HH:mm')
}

const handleDelete = async (auctionId) => {
  if (!confirm('Hapus lelang ini?')) return

  deleting.value = auctionId
  try {
    await auctionStore.deleteAuction(auctionId)
    await auctionStore.fetchMyAuctions()
  } catch (err) {
    alert('Gagal menghapus lelang: ' + (err.response?.data?.message || err.message))
  } finally {
    deleting.value = null
  }
}

onMounted(async () => {
  await authStore.fetchUser()
  
  try {
    await auctionStore.fetchMyAuctions()
  } catch (err) {
    console.error('Error loading my auctions:', err)
  }
})
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>