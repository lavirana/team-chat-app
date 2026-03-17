
import { defineStore }      from 'pinia'
import { ref }              from 'vue'
import api                  from '../composables/useApi'
import { useWebSocket }     from '../composables/useWebSocket'

export const useMessagesStore = defineStore('messages', () => {
    const messages       = ref([])
    const isLoading      = ref(false)
    const currentChannel = ref(null)

    async function fetchMessages(channelId) {
        isLoading.value = true
        try {
            const data      = await api(`/channels/${channelId}/messages`)
            // Reverse because API returns latest first
            messages.value  = data.data.reverse()
            currentChannel.value = channelId
        } finally {
            isLoading.value = false
        }
    }

    async function sendMessage(channelId, content, file = null) {
        let data

        if (file) {
            // File upload — use FormData
            const formData = new FormData()
            if (content) formData.append('content', content)
            formData.append('file', file)

            data = await api(`/channels/${channelId}/messages`, {
                method: 'POST',
                body:   formData,
                // Note: do NOT set Content-Type for FormData
                // ofetch handles it automatically
            })
        } else {
            // Normal text message
            data = await api(`/channels/${channelId}/messages`, {
                method: 'POST',
                body:   { content },
            })
        }

        // Add to local state immediately (optimistic update)
        messages.value.push(data)
        return data
    }

    // Called when WebSocket receives new message from others
    function addMessage(message) {
        const exists = messages.value.find(m => m.id === message.id)
        if (!exists) messages.value.push(message)
    }

    async function deleteMessage(messageId) {
        await api(`/messages/${messageId}`, { method: 'DELETE' })
        messages.value = messages.value.filter(m => m.id !== messageId)
    }

    async function reactToMessage(messageId, emoji) {
        await api(`/messages/${messageId}/react`, {
            method: 'POST',
            body:   { emoji },
        })
    }

    return {
        messages, isLoading, currentChannel,
        fetchMessages, sendMessage, addMessage,
        deleteMessage, reactToMessage
    }
})