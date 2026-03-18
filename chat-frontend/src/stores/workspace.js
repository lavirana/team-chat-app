import { defineStore } from 'pinia'
import { ref }         from 'vue'
import api             from '../composables/useApi'

export const useWorkspaceStore = defineStore('workspace', () => {

    const workspaces       = ref([])
    const currentWorkspace = ref(null)

    async function fetchWorkspaces() {
        try {
            const data   = await api('/workspaces')
            workspaces.value = data
            return data
        } catch {
            return []
        }
    }

    async function createWorkspace(form) {
        try {
            const data = await api('/workspaces', {
                method: 'POST',
                body:   form,
            })
            workspaces.value.push(data)
            return data
        } catch {
            return null
        }
    }

    return {
        workspaces, currentWorkspace,
        fetchWorkspaces, createWorkspace
    }
})