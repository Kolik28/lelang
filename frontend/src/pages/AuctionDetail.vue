<template>
  <div v-if="!loading" class="min-h-screen py-8 sm:py-12">
    <!-- Back Button -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
      <router-link 
        to="/" 
        class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-semibold transition-colors duration-200"
      >
        <span>←</span> Kembali ke Beranda
      </router-link>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Auction Header -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6 sm:p-10">
          <!-- Image Section -->
          <div class="flex flex-col gap-4">
            <div class="relative w-full h-96 rounded-xl overflow-hidden bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center">
              <img 
                v-if="auction.image_url"
                :src="auction.image_url" 
                :alt="auction.title"
                class="w-full h-full object-cover hover:scale-110 transition-transform duration-300"
              />
              <div v-else class="flex flex-col items-center justify-center text-slate-400">
                <svg class="w-16 h-16 mb-2" fill="none" viewBox="0 0 48 48">
                  <path d="M6 8C6 6.89543 6.89543 6 8 6H40C41.1046 6 42 6.89543 42 8V40C42 41.1046 41.1046 42 40 42H8C6.89543 42 6 41.1046 6 40V8Z" stroke="currentColor" stroke-width="2"/>
                  <circle cx="16" cy="16" r="3" fill="currentColor"/>
                  <path d="M6 32L16 22L28 32L42 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm font-medium">Tidak Ada Gambar</span>
              </div>
            </div>
            <!-- Status Badge -->
            <div 
              class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-bold text-white text-sm w-full"
              :class="{
                'bg-gradient-to-r from-emerald-500 to-green-600': auction.status === 'active',
                'bg-gradient-to-r from-amber-500 to-orange-600': auction.status === 'scheduled',
                'bg-gradient-to-r from-red-500 to-rose-600': auction.status === 'ended'
              }"
            >
              <span v-if="auction.status === 'active'" class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
              <span v-else-if="auction.status === 'scheduled'">⏰</span>
              <span v-else>✓</span>
              {{ formatStatus(auction.status) }}
            </div>
          </div>

          <!-- Info Section -->
          <div class="flex flex-col gap-6">
            <!-- Title -->
            <div>
              <h1 class="text-3xl sm:text-4xl font-black text-slate-900 mb-2">{{ auction.title }}</h1>
              <p class="text-slate-600">ID Lelang: #{{ auction.id }}</p>
            </div>

            <!-- Seller Info -->
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-l-4 border-indigo-500 rounded-lg p-4">
              <p class="text-sm text-slate-600 mb-1">Penjual</p>
              <p class="text-lg font-bold text-slate-900">{{ auction.seller.name }}</p>
            </div>

            <!-- Timing Info -->
            <div class="space-y-3">
              <div v-if="auction.status === 'scheduled'" class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <p class="text-sm font-semibold text-amber-900 mb-1">⏰ Lelang Dimulai</p>
                <p class="text-base font-bold text-amber-900">{{ formatDateTime(auction.starts_at) }}</p>
              </div>
              
              <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <p class="text-sm font-semibold text-slate-700 mb-1">🏁 Lelang Berakhir</p>
                <p class="text-base font-bold text-slate-900">{{ formatDateTime(auction.ends_at) }}</p>
              </div>
            </div>

            <!-- Price Section -->
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-xl p-6 text-white">
              <p class="text-sm font-semibold opacity-90 mb-2">Harga Tertinggi Saat Ini</p>
              <p class="text-4xl font-black mb-6">Rp {{ formatPrice(auction.highest_bid) }}</p>
              
              <!-- Countdown Timer -->
              <countdown-timer 
                :endsAt="auction.ends_at"
                :status="auction.status"
              />
            </div>

            <!-- Bid Stats -->
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-center">
                <p class="text-sm text-slate-600 font-semibold mb-1">Total Penawaran</p>
                <p class="text-3xl font-black text-indigo-600">{{ auction.bids_count }}</p>
              </div>
              <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-center">
                <p class="text-sm text-slate-600 font-semibold mb-1">Kenaikan Min.</p>
                <p class="text-lg font-bold text-slate-900">Rp {{ formatPrice(auction.bid_increment) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bid Form or Status Alert -->
      <div class="mb-8">
        <!-- Active Auction - Bid Form -->
        <div v-if="auction.status === 'active' && authStore.isAuthenticated">
          <bid-form 
            :auctionId="auction.id"
            :highestBid="auction.highest_bid"
            :bidIncrement="auction.bid_increment"
            :sellerId="auction.seller.id"
            :isActive="auction.status === 'active'"
            @bid-placed="onBidPlaced"
          />
        </div>

        <!-- Not Authenticated -->
        <div v-else-if="!authStore.isAuthenticated" class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-500 rounded-lg p-6">
          <div class="flex items-start gap-4">
            <span class="text-3xl flex-shrink-0">🔐</span>
            <div>
              <h3 class="font-bold text-slate-900 mb-2">Ingin Membuat Penawaran?</h3>
              <p class="text-slate-700 mb-4">Silakan login terlebih dahulu untuk berpartisipasi dalam lelang ini.</p>
              <router-link 
                to="/login" 
                class="inline-block px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200"
              >
                Login Sekarang
              </router-link>
            </div>
          </div>
        </div>

        <!-- Scheduled Auction -->
        <div v-else-if="auction.status === 'scheduled'" class="bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-500 rounded-lg p-6">
          <div class="flex items-start gap-4">
            <span class="text-3xl flex-shrink-0">⏳</span>
            <div>
              <h3 class="font-bold text-slate-900 mb-1">Lelang Belum Dimulai</h3>
              <p class="text-slate-700">Lelang akan dimulai pada {{ formatDateTime(auction.starts_at) }}. Tunggu waktu yang ditentukan untuk membuat penawaran.</p>
            </div>
          </div>
        </div>

        <!-- Ended Auction -->
        <div v-else-if="auction.status === 'ended'" class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-lg p-6">
          <div class="flex items-start gap-4">
            <span class="text-3xl flex-shrink-0">🏁</span>
            <div>
              <h3 class="font-bold text-slate-900 mb-2">Lelang Telah Selesai</h3>
              <div class="space-y-1">
                <p class="text-slate-700"><strong>Pemenang:</strong> {{ auction.highest_bidder || 'Tidak ada penawaran' }}</p>
                <p class="text-slate-700"><strong>Harga Akhir:</strong> Rp {{ formatPrice(auction.highest_bid) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Description Section -->
      <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-10 mb-8">
        <h2 class="text-2xl font-bold text-slate-900 mb-4">📝 Deskripsi Produk</h2>
        <div class="prose prose-sm max-w-none">
          <p class="text-slate-700 leading-relaxed whitespace-pre-wrap">{{ auction.description }}</p>
        </div>
      </div>

      <!-- Bid History Section -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <bid-list 
          :bids="currentAuctionBids"
        />
      </div>
    </div>
  </div>

  <!-- Loading State -->
  <div v-else class="flex items-center justify-center min-h-screen">
    <div class="text-center">
      <div class="w-16 h-16 border-4 border-slate-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-4"></div>
      <p class="text-slate-600 text-lg font-semibold">Memuat detail lelang...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, inject } from 'vue'
import { useRoute } from 'vue-router'
import { useAuctionStore } from '../stores/auctionStore'
import { useAuthStore } from '../stores/authStore'
import CountdownTimer from '../components/CountdownTimer.vue'
import BidForm from '../components/BidForm.vue'
import BidList from '../components/BidList.vue'

const route = useRoute()
const auctionStore = useAuctionStore()
const authStore = useAuthStore()
const echo = inject('echo')

const loading = ref(true)
const auction = ref(null)
const currentAuctionBids = ref([])
let channel = null

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

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A'
  
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const setupWebSocket = () => {
  if (!echo) {
    console.error('❌ Echo not available!')
    return
  }

  const auctionId = route.params.id
  
  if (!auctionId) {
    console.error('❌ Auction ID is undefined!')
    return
  }

  try {
    if (channel) {
      echo.leave(`auction.${auctionId}`)
    }
    
    channel = echo.channel(`auction.${auctionId}`)
      .listen('.BidPlaced', (event) => {
        handleNewBid(event)
      })
      .listen('.AuctionEnded', (event) => {
        handleAuctionEnded(event)
      })
      
  } catch (err) {
    console.error('❌ Error setting up WebSocket:', err)
  }
}

const handleNewBid = (event) => {
  
  try {
    // Update auction data
    if (auction.value) {
      auction.value.highest_bid = event.highest_bid
      auction.value.bids_count = event.bids_count
    }
    
    // Create new bid object
    const newBid = {
      id: event.id,
      bidder_name: event.bidder_name || event.bidder?.name || 'Unknown',
      amount: event.amount,
      created_at: event.created_at,
    }
    
    // Update bid list
    currentAuctionBids.value = [newBid, ...currentAuctionBids.value]
  } catch (err) {
    console.error('❌ Error in handleNewBid:', err)
    console.error('Event was:', event)
  }
}

const handleAuctionEnded = (event) => {
  if (auction.value) {
    auction.value.status = 'ended'
    auction.value.highest_bid = event.highest_bid
    auction.value.highest_bidder = event.highest_bidder
  }
}

const onBidPlaced = async () => {
  try {
    await auctionStore.fetchAuction(route.params.id)
    auction.value = auctionStore.currentAuction
    currentAuctionBids.value = auctionStore.currentAuctionBids || []
  } catch (err) {
    console.error('❌ Error refreshing auction:', err)
  }
}

onMounted(async () => {
  try {
    if (!route.params.id) {
      console.error('❌ Auction ID not found in route params!')
      loading.value = false
      return
    }
    
    await auctionStore.fetchAuction(route.params.id)
    auction.value = auctionStore.currentAuction
    currentAuctionBids.value = auctionStore.currentAuctionBids || []
    setupWebSocket()
    
    loading.value = false
  } catch (err) {
    console.error('❌ Error loading auction:', err)
    loading.value = false
  }
})

onUnmounted(() => {
  const auctionId = route.params.id
  if (echo) {
    echo.leave(`auction.${auctionId}`)
    if (authStore.user?.id) {
      echo.leave(`user.${authStore.user.id}`)
    }
  }
})
</script>
