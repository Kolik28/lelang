<template>
  <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-white">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="font-bold text-lg">Riwayat Penawaran</h3>
        </div>
        <span class="bg-white/20 backdrop-blur-sm text-white text-sm font-semibold px-3 py-1 rounded-full">
          {{ bids.length }} {{ bids.length === 1 ? 'Tawaran' : 'Tawaran' }}
        </span>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="bids.length === 0" class="p-12 text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
      </div>
      <p class="text-gray-600 font-semibold mb-1">Belum Ada Penawaran</p>
      <p class="text-sm text-gray-400">Jadilah yang pertama menawar produk ini!</p>
    </div>

    <!-- Bid List -->
    <div v-else class="p-4">
      <div class="space-y-2 max-h-[450px] overflow-y-auto pr-2 custom-scrollbar">
        <div 
          v-for="(bid, index) in bids" 
          :key="bid.id" 
          class="group relative flex items-center justify-between p-4 rounded-xl transition-all duration-200 hover:shadow-md"
          :class="index === 0 
            ? 'bg-gradient-to-r from-amber-50 to-yellow-50 border-2 border-amber-200 hover:border-amber-300' 
            : 'bg-gray-50 border border-gray-100 hover:bg-white hover:border-gray-200'"
        >
          <!-- Left: Avatar + Info -->
          <div class="flex items-center gap-3 flex-1 min-w-0">
            <!-- Avatar dengan Initial -->
            <div 
              class="flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center font-bold text-white shadow-sm"
              :class="index === 0 
                ? 'bg-gradient-to-br from-amber-400 to-orange-500' 
                : 'bg-gradient-to-br from-blue-500 to-indigo-600'"
            >
              {{ getInitial(bid.bidder_name) }}
            </div>

            <!-- Info -->
            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-2 mb-0.5">
                <p class="font-semibold text-gray-900 truncate">
                  {{ bid.bidder_name }}
                </p>
                <!-- Badge Tertinggi -->
                <span 
                  v-if="index === 0"
                  class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-400 text-white text-xs font-bold rounded-full shadow-sm"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 16.8 5.8 21.3l2.4-7.4L2 9.4h7.6z"/>
                  </svg>
                  Tertinggi
                </span>
              </div>
              <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ formatTime(bid.created_at) }}</span>
              </div>
            </div>
          </div>

          <!-- Right: Amount -->
          <div class="flex-shrink-0 text-right ml-3">
            <p 
              class="font-bold text-lg"
              :class="index === 0 ? 'text-amber-600' : 'text-blue-600'"
            >
              Rp {{ formatPrice(bid.amount) }}
            </p>
            <p 
              v-if="index === 0 && bids.length > 1"
              class="text-xs text-amber-500 font-medium"
            >
              +Rp {{ formatPrice(bid.amount - bids[1].amount) }} dari #2
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/id'

dayjs.extend(relativeTime)
dayjs.locale('id')

defineProps({
  bids: {
    type: Array,
    required: true,
  },
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const formatTime = (timestamp) => {
  return dayjs(timestamp).fromNow()
}

const getInitial = (name) => {
  if (!name) return '?'
  return name.charAt(0).toUpperCase()
}
</script>

<style scoped>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #3b82f6, #6366f1);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #2563eb, #4f46e5);
}

/* Firefox */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #3b82f6 #f1f5f9;
}
</style>