<template>
  <div class="workspace-container">

    <!-- Header -->
    <div class="header">
      <h1>Your Workspaces</h1>
      <button @click="showCreate = true" class="btn-primary">
        + New Workspace
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading">
      Loading workspaces...
    </div>

    <!-- No workspaces -->
    <div v-else-if="workspaces.length === 0" class="empty">
      <h2>No workspaces yet</h2>
      <p>Create your first workspace to get started</p>
      <button @click="showCreate = true" class="btn-primary">
        Create Workspace
      </button>
    </div>

    <!-- Workspace list -->
    <div v-else class="workspace-grid">
      <div
        v-for="workspace in workspaces"
        :key="workspace.id"
        class="workspace-card"
        @click="goToWorkspace(workspace.id)"
      >
        <div class="workspace-icon">
          {{ workspace.name.charAt(0).toUpperCase() }}
        </div>
        <div class="workspace-info">
          <h3>{{ workspace.name }}</h3>
          <p>{{ workspace.description || 'No description' }}</p>
        </div>
      </div>
    </div>

    <!-- Create Workspace Modal -->
    <div v-if="showCreate" class="modal-overlay" @click.self="showCreate = false">
      <div class="modal">
        <h2>Create Workspace</h2>

        <div class="form-group">
          <label>Workspace Name</label>
          <input
            v-model="form.name"
            type="text"
            placeholder="e.g. My Team"
            required
          />
        </div>

        <div class="form-group">
          <label>Description (Optional)</label>
          <input
            v-model="form.description"
            type="text"
            placeholder="What is this workspace for?"
          />
        </div>

        <div v-if="errorMsg" class="error-box">{{ errorMsg }}</div>

        <div class="modal-actions">
          <button @click="showCreate = false" class="btn-cancel">Cancel</button>
          <button @click="handleCreate" :disabled="creating" class="btn-primary">
            {{ creating ? 'Creating...' : 'Create Workspace' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted }      from 'vue'
import { useRouter }           from 'vue-router'
import { useWorkspaceStore }   from '../../stores/workspace'

const router         = useRouter()
const workspaceStore = useWorkspaceStore()

const workspaces = ref([])
const loading    = ref(false)
const showCreate = ref(false)
const creating   = ref(false)
const errorMsg   = ref('')

const form = ref({
    name:        '',
    description: '',
})

onMounted(async () => {
    loading.value    = true
    workspaces.value = await workspaceStore.fetchWorkspaces()
    loading.value    = false
})

async function handleCreate() {
    if (!form.value.name.trim()) return
    creating.value = true
    errorMsg.value = ''

    const workspace = await workspaceStore.createWorkspace(form.value)
    if (workspace) {
        showCreate.value = false
        form.value       = { name: '', description: '' }
        router.push(`/workspace/${workspace.id}/channel/general`)
    } else {
        errorMsg.value = 'Failed to create workspace.'
    }
    creating.value = false
}

function goToWorkspace(id) {
    router.push(`/workspace/${id}`)
}
</script>

<style scoped>
.workspace-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 40px 20px;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}
h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
}
.workspace-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 16px;
}
.workspace-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}
.workspace-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}
.workspace-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #5865f2;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    flex-shrink: 0;
}
.workspace-info h3 {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 4px;
}
.workspace-info p {
    font-size: 13px;
    color: #888;
}
.empty {
    text-align: center;
    padding: 80px 20px;
    color: #888;
}
.empty h2 {
    font-size: 22px;
    color: #444;
    margin-bottom: 8px;
}
.loading {
    text-align: center;
    padding: 60px;
    color: #888;
}
.btn-primary {
    padding: 10px 20px;
    background: #5865f2;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-primary:hover:not(:disabled) { background: #4752c4; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-cancel {
    padding: 10px 20px;
    background: #f0f0f0;
    color: #444;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
}
.modal {
    background: white;
    padding: 32px;
    border-radius: 16px;
    width: 100%;
    max-width: 440px;
}
.modal h2 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 24px;
}
.form-group {
    margin-bottom: 16px;
}
label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #444;
    margin-bottom: 6px;
}
input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    box-sizing: border-box;
}
input:focus { border-color: #5865f2; }
.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
}
.error-box {
    background: #fff5f5;
    border: 1px solid #fed7d7;
    color: #e53e3e;
    padding: 10px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 12px;
}
</style>