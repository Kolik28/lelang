import { defineStore } from 'pinia'
import { ref } from 'vue'
import { auctions, bids } from '../services/api'

export const useAuctionStore = defineStore('auction', () => {
  // State
  const auctionsList = ref([])
  const myAuctionsList = ref([])
  const currentAuction = ref(null)
  const currentAuctionBids = ref([])
  const loading = ref(false)
  const error = ref(null)

  // Fetch all auctions
  const fetchAuctions = async () => {
    loading.value = true
    try {
      const response = await auctions.list()
      auctionsList.value = response.data.data || response.data
      console.log('✅ Fetched auctions:', auctionsList.value.length)
    } catch (err) {
      error.value = err.message
      console.error('❌ Error fetching auctions:', err)
    } finally {
      loading.value = false
    }
  }

  // Fetch my auctions
  const fetchMyAuctions = async () => {
    loading.value = true
    try {
      const response = await auctions.myAuctions()
      myAuctionsList.value = response.data.data || response.data
      auctionsList.value = myAuctionsList.value
    } catch (err) {
      error.value = err.message
      console.error('❌ Error fetching my auctions:', err)
    } finally {
      loading.value = false
    }
  }

  // Fetch single auction
  const fetchAuction = async (id) => {
    loading.value = true
    try {
      const response = await auctions.show(id)
      currentAuction.value = response.data.data || response.data
      
      const bidsResponse = await auctions.getBids(id)
      currentAuctionBids.value = bidsResponse.data.data || bidsResponse.data
    } catch (err) {
      error.value = err.message
      console.error('❌ Error fetching auction:', err)
    } finally {
      loading.value = false
    }
  }

  // Create auction
  const createAuction = async (data) => {
    try {
      const response = await auctions.create(data)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || err.message
      throw err
    }
  }

  // Delete auction
  const deleteAuction = async (id) => {
    try {
      await auctions.delete(id)
      auctionsList.value = auctionsList.value.filter(a => a.id !== id)
      myAuctionsList.value = myAuctionsList.value.filter(a => a.id !== id)
    } catch (err) {
      error.value = err.message
      throw err
    }
  }

  // Place bid
  const placeBid = async (auctionId, amount) => {
    try {
      const response = await bids.create({
        auction_id: auctionId,
        amount,
      })
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || err.message
      throw err
    }
  }

  // Update auction data from WebSocket event (BidPlaced)
  const updateAuctionFromEvent = (eventData) => {
    if (currentAuction.value?.id === eventData.auction_id) {
      currentAuction.value.highest_bid = eventData.highest_bid
      currentAuction.value.bids_count = eventData.bids_count
      console.log(`✅ Auction ${eventData.auction_id} updated from event`)
    }
  }

  // Add bid to list from WebSocket event
  const addBidToList = (bidData) => {
    currentAuctionBids.value.unshift(bidData)
    console.log(`✅ Bid added to list`)
  }

  // Update auction status from WebSocket event (AuctionStatusChanged)
  const updateAuctionStatus = (auctionId, newStatus) => {
    const auction = auctionsList.value.find(a => a.id === auctionId)
    if (auction) {
      auction.status = newStatus
      console.log(`✅ Auction ${auctionId} status updated to ${newStatus}`)
    }

    // Update current auction jika sedang dilihat
    if (currentAuction.value && currentAuction.value.id === auctionId) {
      currentAuction.value.status = newStatus
    }

    // Force reactivity dengan membuat array baru
    auctionsList.value = [...auctionsList.value]
  }

  // Update auction winner dari WebSocket event (AuctionEnded)
  const updateAuctionWinner = (auctionId, winnerId, finalPrice) => {
    const auction = auctionsList.value.find(a => a.id === auctionId)
    if (auction) {
      auction.status = 'ended'
      auction.winner_id = winnerId
      auction.highest_bid = finalPrice
      console.log(`✅ Auction ${auctionId} ended with winner ${winnerId}`)
    }

    // Force reactivity
    auctionsList.value = [...auctionsList.value]
  }

  // Add new auction dari WebSocket event (AuctionCreated)
  const addAuction = (auction) => {
    auctionsList.value.unshift(auction)
    auctionsList.value = [...auctionsList.value]
    console.log(`✅ New auction added: ${auction.id}`)
  }

  return {
    auctionsList,
    myAuctionsList,
    currentAuction,
    currentAuctionBids,
    loading,
    error,
    fetchAuctions,
    fetchMyAuctions,
    fetchAuction,
    createAuction,
    deleteAuction,
    placeBid,
    updateAuctionFromEvent,
    addBidToList,
    updateAuctionStatus,
    updateAuctionWinner,
    addAuction,
  }
})