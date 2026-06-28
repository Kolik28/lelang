<template>
  <div class="space-y-12">
    <!-- Hero Header -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-16 sm:px-12 sm:py-24 shadow-xl">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-1/2 -right-1/4 h-96 w-96 rounded-full bg-white opacity-10 blur-3xl"></div>
        <div class="absolute -bottom-1/3 -left-1/4 h-80 w-80 rounded-full bg-white opacity-5 blur-3xl"></div>
      </div>
      <div class="relative z-10 text-center">
        <h1 class="text-4xl sm:text-5xl font-black text-white mb-4 tracking-tight">Platform Lelang Daring</h1>
        <p class="text-lg sm:text-xl text-indigo-100 font-light">Temukan barang impian Anda atau jual dengan harga terbaik</p>
      </div>
    </div>

    <!-- Auth Prompt -->
    <div v-if="!authStore.isAuthenticated" class="bg-white rounded-2xl shadow-lg border border-slate-200 p-8 sm:p-12 text-center">
      <h2 class="text-3xl font-bold text-slate-900 mb-3">Bergabunglah dengan Komunitas Kami</h2>
      <p class="text-lg text-slate-600 mb-8">Login atau daftar untuk mulai menawar atau membuat lelang</p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <router-link 
          to="/login" 
          class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
        >
          <span>→</span> Login
        </router-link>
        <router-link 
          to="/register" 
          class="px-8 py-3 bg-slate-100 hover:bg-slate-200 text-indigo-600 font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-2 border border-slate-300"
        >
          <span>+</span> Daftar
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="auctionStore.loading" class="flex flex-col items-center justify-center py-20">
      <div class="w-12 h-12 border-4 border-slate-200 border-t-indigo-600 rounded-full animate-spin mb-4"></div>
      <p class="text-slate-600 text-lg">Memuat lelang...</p>
    </div>

    <!-- Auctions by Status -->
    <div v-else class="space-y-16">
      <!-- My Wins Section -->
      <div v-if="authStore.isAuthenticated && myWins.length > 0" class="space-y-6">
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border-l-4 border-amber-400 p-6 sm:p-8">
          <div class="flex items-center gap-3 mb-2">
            <h2 class="text-3xl font-bold text-slate-900">Kemenangan Saya</h2>
          </div>
          <p class="text-slate-600">{{ myWins.length }} lelang yang Anda menangkan</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <auction-card 
            v-for="auction in myWins" 
            :key="auction.id" 
            :auction="auction" 
            :is-ended="true"
            :show-winner="true"
            :is-my-win="true"
          />
        </div>
      </div>

      <!-- Active Auctions -->
      <div v-if="activeAuctions.length > 0" class="space-y-6">
        <div class="flex items-center gap-3 mb-2">
          <h2 class="text-3xl font-bold text-slate-900">Lelang Aktif</h2>
        </div>
        <p class="text-slate-600">{{ activeAuctions.length }} lelang sedang berlangsung</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <auction-card 
            v-for="auction in activeAuctions" 
            :key="auction.id" 
            :auction="auction" 
          />
        </div>
      </div>

      <!-- Scheduled Auctions -->
      <div v-if="scheduledAuctions.length > 0" class="space-y-6">
        <div class="flex items-center gap-3 mb-2">
          <h2 class="text-3xl font-bold text-slate-900">Lelang Akan Datang</h2>
        </div>
        <p class="text-slate-600">{{ scheduledAuctions.length }} lelang akan segera dimulai</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <auction-card 
            v-for="auction in scheduledAuctions" 
            :key="auction.id" 
            :auction="auction"
            :show-starts-at="true" 
          />
        </div>
      </div>

      <!-- Ended Auctions -->
      <div v-if="endedAuctions.length > 0" class="space-y-6">
        <div class="flex items-center gap-3 mb-2">
          <h2 class="text-3xl font-bold text-slate-900">Lelang Selesai</h2>
        </div>
        <p class="text-slate-600">{{ endedAuctions.length }} lelang telah berakhir</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <auction-card 
            v-for="auction in endedAuctions" 
            :key="auction.id" 
            :auction="auction" 
            :is-ended="true"
            :show-winner="true"
          />
        </div>
      </div>

      <!-- No Auctions Message -->
      <div v-if="auctionStore.auctionsList.length === 0" class="bg-white rounded-2xl shadow-lg border border-slate-200 p-12 text-center">
        <div class="text-6xl mb-4">📭</div>
        <h3 class="text-2xl font-bold text-slate-900 mb-2">Tidak Ada Lelang Saat Ini</h3>
        <p class="text-slate-600 mb-6">Cek kembali nanti atau buat lelang baru Anda sendiri!</p>
        <router-link 
          v-if="authStore.isAuthenticated" 
          to="/create" 
          class="inline-block px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg"
        >
          Buat Lelang Baru
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, inject } from 'vue'
import { useAuctionStore } from '../stores/auctionStore'
import { useAuthStore } from '../stores/authStore'
import AuctionCard from '../components/AuctionCard.vue'

const auctionStore = useAuctionStore()
const authStore = useAuthStore()
const echo = inject('echo')

// Filter auctions by status
const activeAuctions = computed(() =>
  auctionStore.auctionsList.filter(a => a.status === 'active')
)

const scheduledAuctions = computed(() =>
  auctionStore.auctionsList.filter(a => a.status === 'scheduled')
)

const endedAuctions = computed(() =>
  auctionStore.auctionsList.filter(a => a.status === 'ended')
)

const myWins = computed(() => {
  if (!authStore.isAuthenticated || !authStore.user) return []
  return auctionStore.auctionsList.filter(a => 
    a.status === 'ended' && a.winner_id === authStore.user.id
  )
})

const setupWebSocket = () => {
  if (!echo) {
    console.error('❌ Echo not available!')
    return
  }

  const channel = echo.channel('auctions')
  channel.listen('.AuctionStatusChanged', (event) => {
    handleStatusChange(event)
  })
  
  channel.listen('.AuctionCreated', (event) => {
    auctionStore.addAuction(event.auction)
  })
  
  channel.listen('.AuctionEnded', (event) => {
    handleAuctionEnded(event)
  })

  // Error handling
  channel.error((error) => {
    console.error('❌ Channel error:', error)
  })
}

const handleStatusChange = (event) => {
  
  // Update di auctions list
  const auctionIndex = auctionStore.auctionsList.findIndex(a => a.id === event.auction_id)
  
  if (auctionIndex !== -1) {
    auctionStore.auctionsList[auctionIndex].status = event.new_status
    auctionStore.auctionsList = [...auctionStore.auctionsList]
  } else {
    console.warn(`Auction ${event.auction_id} not found in list`)
  }

  if (auctionStore.currentAuction && auctionStore.currentAuction.id === event.auction_id) {
    auctionStore.currentAuction.status = event.new_status
  }
}

const handleAuctionEnded = (event) => {
  
  const auction = auctionStore.auctionsList.find(a => a.id === event.auction_id)
  if (auction) {
    auction.status = 'ended'
    auction.winner_id = event.winner_id
    auction.highest_bid = event.final_price || event.highest_bid
    
    auctionStore.auctionsList = [...auctionStore.auctionsList]
  }
}

onMounted(async () => {
  try {
    await authStore.fetchUser()
    await auctionStore.fetchAuctions()
    setupWebSocket()
  } catch (error) {
    console.error('❌ Error in onMounted:', error)
  }
})

onUnmounted(() => {
  if (echo) {
    echo.leave('auctions')
  }
})
</script>
