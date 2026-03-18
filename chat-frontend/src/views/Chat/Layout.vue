<template>
  <div class="chat-layout">

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Workspace Name -->
      <div class="workspace-header">
        <h2>{{ currentWorkspace?.name || 'Loading...' }}</h2>
        <button @click="goToWorkspaces" class="btn-switch" title="Switch Workspace">
          ⇄
        </button>
      </div>

      <!-- User Info -->
      <div class="user-info">
        <div class="avatar">{{ auth.user?.name?.charAt(0).toUpperCase() }}</div>
        <div class="user-details">
          <span class="user-name">{{ auth.user?.name }}</span>
          <span class="user-status online">● Online</span>
        </div>
        <button @click="handleLogout" class="btn-logout" title="Logout">⏻</button>
      </div>

      <!-- Channels -->
      <div class="sidebar-section">
        <div class="section-header">
          <span>Channels</span>
          <button @click="showCreateChannel = true" class="btn-add">+</button>
        </div>

        <div
          v-for="channel in channels"
          :key="channel.id"
          class="channel-item"
          :class="{ active: currentChannelId == channel.id }"
          @click="goToChannel(channel.id)"
        >
          # {{ channel.name }}
        </div>

        <div v-if="channels.length === 0" class="empty-channels">
          No channels yet
        </div>
      </div>

    </div>

    <!-- Main Chat Area -->
    <div class="chat-area">
      <RouterView />
    </div>

  </div>

  <!-- Create Channel Modal -->
  <div v-if="showCreateChannel" class="modal-overlay" @click.self="showCreateChannel = false">
    <div class="modal">
      <h3>Create Channel</h3>

      <div class="form-group">
        <label>Channel Name</label>
        <input
          v-model="channelForm.name"
          type="text"
          placeholder="e.g. design, marketing"
        />
      </div>

      <div class="form-group">
        <label>Description (Optional)</label>
        <input
          v-model="channelForm.description"
          type="text"
          placeholder="What is this channel for?"
        />
      </div>

      <div class="modal-actions">
        <button @click="showCreateChannel = false" class="btn-cancel">Cancel</button>
        <button @click="handleCreateChannel" :disabled="creating" class="btn-primary">
          {{ creating ? 'Creating...' : 'Create Channel' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter }             from 'vue-router'
import { useAuthStore }                    from '../../stores/auth'
import { useWorkspaceStore }               from '../../stores/workspace'
import api                                 from '../../composables/useApi'

const route          = useRoute()
const router         = useRouter()
const auth           = useAuthStore()
const workspaceStore = useWorkspaceStore()

const channels           = ref([])
const showCreateChannel  = ref(false)
const creating           = ref(false)
const currentWorkspace   = ref(null)

const channelForm = ref({
    name:        '',
    description: '',
})

const currentChannelId = computed(() => route.params.channelId)
const workspaceId      = computed(() => route.params.workspaceId)

onMounted(async () => {
    await fetchWorkspace()
    await fetchChannels()
})

// Reload when workspace changes
watch(() => route.params.workspaceId, async () => {
    await fetchWorkspace()
    await fetchChannels()
})

async function fetchWorkspace() {
    try {
        currentWorkspace.value = await api(`/workspaces/${workspaceId.value}`)
    } catch {
        console.error('Failed to load workspace')
    }
}

async function fetchChannels() {
    try {
        channels.value = await api(`/workspaces/${workspaceId.value}/channels`)
    } catch {
        console.error('Failed to load channels')
    }
}

async function handleCreateChannel() {
    if (!channelForm.value.name.trim()) return
    creating.value = true

    try {
        const channel = await api(`/workspaces/${workspaceId.value}/channels`, {
            method: 'POST',
            body:   channelForm.value,
        })
        channels.value.push(channel)
        showCreateChannel.value = false
        channelForm.value       = { name: '', description: '' }
        goToChannel(channel.id)
    } catch {
        console.error('Failed to create channel')
    } finally {
        creating.value = false
    }
}

function goToChannel(channelId) {
    router.push(`/workspace/${workspaceId.value}/channel/${channelId}`)
}

function goToWorkspaces() {
    router.push('/workspaces')
}

async function handleLogout() {
    await auth.logout()
    router.push('/login')
}
</script>

<style scoped>
.chat-layout {
    display: flex;
    height: 100vh;
    overflow: hidden;
    background: #1a1d21;
}

/* Sidebar */
.sidebar {
    width: 260px;
    min-width: 260px;
    background: #19171d;
    display: flex;
    flex-direction: column;
    border-right: 1px solid #2d2d2d;
    overflow-y: auto;
}

.workspace-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    border-bottom: 1px solid #2d2d2d;
}

.workspace-header h2 {
    color: white;
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btn-switch {
    background: none;
    border: none;
    color: #aaa;
    font-size: 18px;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
}
.btn-switch:hover { background: #2d2d2d; color: white; }

.user-info {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    border-bottom: 1px solid #2d2d2d;
}

.avatar {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #5865f2;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    flex-shrink: 0;
}

.user-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.user-name {
    color: white;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-status {
    font-size: 11px;
    color: #aaa;
}
.user-status.online { color: #23a559; }

.btn-logout {
    background: none;
    border: none;
    color: #aaa;
    font-size: 16px;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
}
.btn-logout:hover { color: #e53e3e; }

.sidebar-section {
    padding: 16px 8px;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 8px;
    margin-bottom: 4px;
    color: #aaa;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-add {
    background: none;
    border: none;
    color: #aaa;
    font-size: 18px;
    cursor: pointer;
    line-height: 1;
    padding: 0 4px;
    border-radius: 4px;
}
.btn-add:hover { background: #2d2d2d; color: white; }

.channel-item {
    padding: 6px 12px;
    color: #aaa;
    font-size: 14px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s;
    margin-bottom: 2px;
}
.channel-item:hover { background: #2d2d2d; color: white; }
.channel-item.active {
    background: #5865f2;
    color: white;
}

.empty-channels {
    padding: 8px 12px;
    color: #666;
    font-size: 13px;
}

/* Chat Area */
.chat-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #313338;
}

/* Modal */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
}
.modal {
    background: #2b2d31;
    padding: 28px;
    border-radius: 12px;
    width: 100%;
    max-width: 420px;
}
.modal h3 {
    color: white;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
}
.form-group { margin-bottom: 16px; }
label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #aaa;
    margin-bottom: 6px;
    text-transform: uppercase;
}
input {
    width: 100%;
    padding: 10px 12px;
    background: #1e1f22;
    border: 1px solid #444;
    border-radius: 6px;
    color: white;
    font-size: 14px;
    outline: none;
    box-sizing: border-box;
}
input:focus { border-color: #5865f2; }
.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 20px;
}
.btn-primary {
    padding: 10px 20px;
    background: #5865f2;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}
.btn-primary:hover:not(:disabled) { background: #4752c4; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-cancel {
    padding: 10px 20px;
    background: transparent;
    color: #aaa;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
}
.btn-cancel:hover { color: white; }
</style>