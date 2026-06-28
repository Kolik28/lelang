import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

const echo = new Echo({
  broadcaster: 'reverb',
  key: 'bvrmk5qvcfa67ckkx4bg',
  wsHost: 'localhost',
  wsPort: 8080,
  wssPort: 443,
  forceTLS: false,
  encrypted: false,
})

export default echo