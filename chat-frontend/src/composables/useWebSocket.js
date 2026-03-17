
import Echo                 from 'laravel-echo'
import Pusher               from 'pusher-js'
import { useMessagesStore } from '../stores/messages'
import { useAuthStore }     from '../stores/auth'

let echo = null

export function useWebSocket() {

    function connect() {
        if (echo) return // Already connected

        window.Pusher = Pusher

        // Laravel Reverb config — uses env variables from .env
        echo = new Echo({
            broadcaster:       'reverb',
            key:               import.meta.env.VITE_REVERB_APP_KEY,
            wsHost:            import.meta.env.VITE_REVERB_HOST,
            wsPort:            import.meta.env.VITE_REVERB_PORT ?? 8080,
            wssPort:           import.meta.env.VITE_REVERB_PORT ?? 8080,
            scheme:            import.meta.env.VITE_REVERB_SCHEME ?? 'http',
            forceTLS:          (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
            enabledTransports: ['ws', 'wss'],
            disableStats:      true,
            authEndpoint:      'http://localhost:8000/broadcasting/auth',
            auth: {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`,
                }
            }
        })

        console.log('✅ Reverb WebSocket connected')
    }

    function joinChannel(channelId) {
        if (!echo) connect()

        const messagesStore = useMessagesStore()

        echo.join(`channel.${channelId}`)
            // When a new message is sent by others
            .listen('.message.sent', (data) => {
                messagesStore.addMessage(data.message)
            })
            // Who is currently in this channel
            .here((users) => {
                console.log('Users online in channel:', users)
            })
            // Someone joined
            .joining((user) => {
                console.log(user.name, 'joined the channel')
            })
            // Someone left
            .leaving((user) => {
                console.log(user.name, 'left the channel')
            })
            .error((error) => {
                console.error('Channel error:', error)
            })
    }

    function leaveChannel(channelId) {
        if (echo) echo.leave(`channel.${channelId}`)
    }

    function listenForDMs(userId) {
        if (!echo) connect()

        echo.private(`dm.${userId}`)
            .listen('.dm.sent', (data) => {
                console.log('New DM received:', data)
            })
    }

    function listenForOnlineUsers() {
        if (!echo) connect()

        echo.channel('online-users')
            .listen('.user.online', (data) => {
                console.log('User status changed:', data)
            })
    }

    function disconnect() {
        if (echo) {
            echo.disconnect()
            echo = null
            console.log('WebSocket disconnected')
        }
    }

    return {
        connect,
        joinChannel,
        leaveChannel,
        listenForDMs,
        listenForOnlineUsers,
        disconnect
    }
}