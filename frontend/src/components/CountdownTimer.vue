<template>
  <div class="bg-white rounded-xl shadow-md overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-white">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="font-semibold text-sm">Waktu Tersisa</span>
        </div>
        <span 
          class="text-white text-xs font-semibold px-2 py-1 rounded-full bg-white/20"
          :class="isEnded ? 'bg-gray-500/50' : ''"
        >
          {{ isEnded ? 'Berakhir' : 'Aktif' }}
        </span>
      </div>
    </div>

    <!-- Content -->
    <div class="p-4">
      <!-- Ended State -->
      <div v-if="isEnded" class="text-center py-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <p class="text-lg font-bold text-gray-900">Lelang Berakhir</p>
      </div>

      <!-- Active Timer -->
      <div v-else>
        <!-- Timer Display -->
        <div class="flex items-center justify-center gap-2">
          <!-- Hours -->
          <div class="flex-1 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg p-3 text-center text-white shadow-md">
            <div class="text-2xl sm:text-3xl font-bold font-mono leading-none">
              {{ timeParts.hours }}
            </div>
            <div class="text-[10px] font-semibold mt-1 opacity-90">JAM</div>
          </div>

          <span class="text-2xl font-bold text-gray-400">:</span>

          <!-- Minutes -->
          <div class="flex-1 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg p-3 text-center text-white shadow-md">
            <div class="text-2xl sm:text-3xl font-bold font-mono leading-none">
              {{ timeParts.minutes }}
            </div>
            <div class="text-[10px] font-semibold mt-1 opacity-90">MENIT</div>
          </div>

          <span class="text-2xl font-bold text-gray-400">:</span>

          <!-- Seconds -->
          <div class="flex-1 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg p-3 text-center text-white shadow-md">
            <div class="text-2xl sm:text-3xl font-bold font-mono leading-none">
              {{ timeParts.seconds }}
            </div>
            <div class="text-[10px] font-semibold mt-1 opacity-90">DETIK</div>
          </div>
        </div>

        <!-- End Date Info -->
        <div v-if="endsAt" class="mt-4 text-center">
          <div class="inline-flex items-center gap-1.5 text-xs text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Berakhir: {{ formatEndDate }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import dayjs from 'dayjs'

const props = defineProps({
  endsAt: String,
  status: String,
})

const remaining = ref(0)
let interval

const calculateRemaining = () => {
  if (props.status !== 'active') {
    remaining.value = 0
    return
  }

  const endTime = new Date(props.endsAt).getTime()
  const now = new Date().getTime()
  remaining.value = Math.max(0, Math.floor((endTime - now) / 1000))
}

const isEnded = computed(() => {
  return props.status !== 'active' || remaining.value <= 0
})

const timeParts = computed(() => {
  const hours = Math.floor(remaining.value / 3600)
  const minutes = Math.floor((remaining.value % 3600) / 60)
  const seconds = remaining.value % 60

  return {
    hours: String(hours).padStart(2, '0'),
    minutes: String(minutes).padStart(2, '0'),
    seconds: String(seconds).padStart(2, '0'),
  }
})

const formatEndDate = computed(() => {
  if (!props.endsAt) return '-'
  return dayjs(props.endsAt).format('DD MMM YYYY, HH:mm')
})

onMounted(() => {
  calculateRemaining()
  interval = setInterval(calculateRemaining, 1000)
})

onUnmounted(() => {
  clearInterval(interval)
})

watch(() => props.endsAt, calculateRemaining)
watch(() => props.status, calculateRemaining)
</script>