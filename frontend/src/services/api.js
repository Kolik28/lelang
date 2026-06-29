import axios from 'axios'

const API_BASE = 'http://localhost:8000/api'

const api = axios.create({
  baseURL: API_BASE,
  headers: {
    'Accept': 'application/json',
  },
  withCredentials: true,
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  
  if (config.data instanceof FormData) {
    delete config.headers['Content-Type']
  } else {
    config.headers['Content-Type'] = 'application/json'
  }
  
  return config
})

// Error handling
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

// Auth endpoints
export const auth = {
  register: (data) => api.post('/register', data),
  login: (data) => api.post('/login', data),
  logout: () => api.post('/logout'),
  user: () => api.get('/user'),
}

// Auction endpoints
export const auctions = {
  list: () => api.get('/auctions'),
  show: (id) => api.get(`/auctions/${id}`),
  
  create: (data, config = {}) => api.post('/auctions', data, config),
  
  delete: (id) => api.delete(`/auctions/${id}`),
  myAuctions: () => api.get('/my-auctions'),
  getBids: (id) => api.get(`/auctions/${id}/bids`),
}

// Bid endpoints
export const bids = {
  create: (data) => api.post('/bids', data),
  myBids: () => api.get('/my-bids'),
}

export default api