<template>
  <div class="channel-container">

    <!-- Channel Header -->
    <div class="channel-header">
      <div class="channel-info">
        <span class="channel-hash">#</span>
        <h3>{{ channelName }}</h3>
      </div>
    </div>

    <!-- Messages Area -->
    <div ref="messagesEl" class="messages-area">

      <!-- Loading -->
      <div v-if="messagesStore.isLoading" class="status-msg">
        Loading messages...
      </div>

      <!-- No messages -->
      <div v-else-if="messagesStore.messages.length === 0" class="status-msg">
        <p>🎉 Yeh {{ channelName }} channel ka start hai!</p>
        <p style="font-size:13px; color: #666; margin-top: 8px;">
          Pehla message bhejo!
        </p>
      </div>

      <!-- Messages List -->
      <div v-else class="messages-list">
        <div
          v-for="message in messagesStore.messages"
          :key="message.id"
          class="message-item"
          :class="{ 'own-message': message.user_id === auth.user?.id }"
        >
          <!-- Avatar -->
          <div class="msg-avatar">
            {{ message.user?.name?.charAt(0).toUpperCase() }}
          </div>

          <!-- Content -->
          <div class="msg-content">
            <div class="msg-header">
              <span class="msg-author">{{ message.user?.name }}</span>
              <span class="msg-time">{{ formatTime(message.created_at) }}</span>
              <!-- Delete button — sirf apne message pe -->
              <button
                v-if="message.user_id === auth.user?.id"
                @click="deleteMessage(message.id)"
                class="btn-delete"
                title="Delete"
              >
                🗑
              </button>
            </div>

            <!-- Text message -->
            <p v-if="message.type === 'text'" class="msg-text">
              {{ message.content }}
            </p>

            <!-- Image message -->
            <img
              v-else-if="message.type === 'image' && message.attachments?.length"
              :src="`http://localhost:8000/storage/${message.attachments[0].file_path}`"
              class="msg-image"
              alt="image"
            />

            <!-- File message -->
            <div v-else-if="message.type === 'file' && message.attachments?.length" class="msg-file">
              📎 {{ message.attachments[0].file_name }}
            </div>

            <!-- Edited badge -->
            <span v-if="message.is_edited" class="edited-badge">(edited)</span>
          </div>
        </div>
      </div>

    </div>

    <!-- Message Input -->
    <div class="message-input-area">

      <!-- File preview -->
      <div v-if="selectedFile" class="file-preview">
        📎 {{ selectedFile.name }}
        <button @click="selectedFile = null">✕</button>
      </div>

      <div class="input-row">
        <!-- File attach button -->
        <button @click="fileInput.click()" class="btn-attach" title="Attach file">
          📎
        </button>
        <input
          ref="fileInput"
          type="file"
          class="hidden"
          @change="handleFileSelect"
        />

        <!-- Text input -->
        <textarea
          v-model="messageContent"
          @keydown="handleKeydown"
          :placeholder="`Message #${channelName}`"
          class="msg-input"
          rows="1"
        />

        <!-- Send button -->
        <button
          @click="handleSend"
          :disabled="sending || (!messageContent.trim() && !selectedFile)"
          class="btn-send"
        >
          {{ sending ? '...' : '➤' }}
        </button>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { useRoute }            from 'vue-router'
import { useAuthStore }        from '../../stores/auth'
import { useMessagesStore }    from '../../stores/messages'
import { useWebSocket }        from '../../composables/useWebSocket'

const route         = useRoute()
const auth          = useAuthStore()
const messagesStore = useMessagesStore()
const { joinChannel, leaveChannel } = useWebSocket()

const messagesEl     = ref(null)
const messageContent = ref('')
const selectedFile   = ref(null)
const fileInput      = ref(null)
const sending        = ref(false)

const channelId   = computed(() => route.params.channelId)
const channelName = computed(() => route.params.channelId)

onMounted(async () => {
    await loadMessages()
    joinChannel(channelId.value, (message) => {
        messagesStore.addMessage(message)
        scrollToBottom()
    })
})

onUnmounted(() => {
    leaveChannel(channelId.value)
    messagesStore.clearMessages()
})

// Channel change hone pe reload
watch(channelId, async (newId, oldId) => {
    leaveChannel(oldId)
    messagesStore.clearMessages()
    await loadMessages()
    joinChannel(newId, (message) => {
        messagesStore.addMessage(message)
        scrollToBottom()
    })
})

// Naya message aane pe scroll
watch(() => messagesStore.messages.length, async () => {
    await nextTick()
    scrollToBottom()
})

async function loadMessages() {
    await messagesStore.fetchMessages(channelId.value)
    await nextTick()
    scrollToBottom()
}

async function handleSend() {
    if (!messageContent.value.trim() && !selectedFile.value) return
    sending.value = true
    try {
        await messagesStore.sendMessage(
            channelId.value,
            messageContent.value,
            selectedFile.value
        )
        messageContent.value = ''
        selectedFile.value   = null
        if (fileInput.value) fileInput.value.value = ''
    } catch (err) {
        console.error('Send failed:', err)
    } finally {
        sending.value = false
    }
}

function handleKeydown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault()
        handleSend()
    }
}

function handleFileSelect(e) {
    selectedFile.value = e.target.files[0]
}

async function deleteMessage(id) {
    if (confirm('Delete this message?')) {
        await messagesStore.deleteMessage(id)
    }
}

function scrollToBottom() {
    if (messagesEl.value) {
        messagesEl.value.scrollTop = messagesEl.value.scrollHeight
    }
}

function formatTime(dateStr) {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    return date.toLocaleTimeString('en-US', {
        hour:   '2-digit',
        minute: '2-digit',
        hour12: true
    })
}
</script>

<style scoped>
.channel-container {
    display: flex;
    flex-direction: column;
    height: 100vh;
    background: #313338;
}

/* Header */
.channel-header {
    padding: 12px 20px;
    border-bottom: 1px solid #2d2d2d;
    background: #313338;
    flex-shrink: 0;
}
.channel-info {
    display: flex;
    align-items: center;
    gap: 6px;
}
.channel-hash {
    color: #aaa;
    font-size: 20px;
    font-weight: 300;
}
.channel-header h3 {
    color: white;
    font-size: 16px;
    font-weight: 700;
    margin: 0;
}

/* Messages Area */
.messages-area {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
}
.messages-area::-webkit-scrollbar { width: 6px; }
.messages-area::-webkit-scrollbar-track { background: transparent; }
.messages-area::-webkit-scrollbar-thumb { background: #444; border-radius: 3px; }

.status-msg {
    text-align: center;
    padding: 60px 20px;
    color: #aaa;
    font-size: 15px;
}

/* Message Item */
.messages-list { display: flex; flex-direction: column; gap: 2px; }

.message-item {
    display: flex;
    gap: 12px;
    padding: 4px 8px;
    border-radius: 6px;
    transition: background 0.1s;
}
.message-item:hover {
    background: #2d2d2d;
}
.message-item:hover .btn-delete { opacity: 1; }

.msg-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #5865f2;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    flex-shrink: 0;
    margin-top: 2px;
}

.msg-content { flex: 1; min-width: 0; }

.msg-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 2px;
}
.msg-author {
    color: white;
    font-size: 15px;
    font-weight: 600;
}
.msg-time {
    color: #666;
    font-size: 11px;
}
.btn-delete {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 13px;
    opacity: 0;
    transition: opacity 0.2s;
    padding: 0 4px;
}

.msg-text {
    color: #dcddde;
    font-size: 15px;
    line-height: 1.5;
    margin: 0;
    white-space: pre-wrap;
    word-break: break-word;
}

.msg-image {
    max-width: 300px;
    max-height: 300px;
    border-radius: 8px;
    margin-top: 4px;
}

.msg-file {
    background: #2b2d31;
    padding: 8px 12px;
    border-radius: 6px;
    color: #aaa;
    font-size: 13px;
    display: inline-block;
    margin-top: 4px;
}

.edited-badge {
    color: #666;
    font-size: 11px;
    margin-left: 4px;
}

/* Message Input */
.message-input-area {
    padding: 16px 20px;
    background: #313338;
    flex-shrink: 0;
}

.file-preview {
    background: #2b2d31;
    padding: 6px 12px;
    border-radius: 6px;
    color: #aaa;
    font-size: 13px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.file-preview button {
    background: none;
    border: none;
    color: #aaa;
    cursor: pointer;
    font-size: 14px;
}

.input-row {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    background: #383a40;
    border-radius: 10px;
    padding: 8px 12px;
}

.btn-attach {
    background: none;
    border: none;
    color: #aaa;
    font-size: 18px;
    cursor: pointer;
    padding: 4px;
    flex-shrink: 0;
}
.btn-attach:hover { color: white; }

.msg-input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    color: #dcddde;
    font-size: 15px;
    resize: none;
    max-height: 120px;
    line-height: 1.5;
    padding: 4px 0;
}
.msg-input::placeholder { color: #666; }

.btn-send {
    background: #5865f2;
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    flex-shrink: 0;
    transition: background 0.2s;
}
.btn-send:hover:not(:disabled) { background: #4752c4; }
.btn-send:disabled { opacity: 0.4; cursor: not-allowed; }

.hidden { display: none; }
</style>