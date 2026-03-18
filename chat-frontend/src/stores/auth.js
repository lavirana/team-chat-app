import { defineStore } from 'pinia'
import { ref }         from 'vue'
import api             from '../composables/useApi'

export const useAuthStore = defineStore('auth', () => {

    const user    = ref(null)
    const token   = ref(localStorage.getItem('token'))
    const errors  = ref({})
    const loading = ref(false)

    async function login(credentials) {
        loading.value = true
        errors.value  = {}
        try {
            const data  = await api('/login', {
                method: 'POST',
                body:   credentials,
            })
            token.value = data.token
            user.value  = data.user
            localStorage.setItem('token', data.token)
            return true
        } catch (err) {
            errors.value = err.data?.errors || {}
            return false
        } finally {
            loading.value = false
        }
    }

    async function register(userData) {
        loading.value = true
        errors.value  = {}
        try {
            const data  = await api('/register', {
                method: 'POST',
                body:   userData,
            })
            token.value = data.token
            user.value  = data.user
            localStorage.setItem('token', data.token)
            return true
        } catch (err) {
            errors.value = err.data?.errors || {}
            return false
        } finally {
            loading.value = false
        }
    }

    async function logout() {
        try {
            await api('/logout', { method: 'POST' })
        } finally {
            token.value = null
            user.value  = null
            localStorage.removeItem('token')
        }
    }

    async function fetchMe() {
        if (!token.value) return
        try {
            user.value = await api('/me')
        } catch {
            token.value = null
            localStorage.removeItem('token')
        }
    }

    const isLoggedIn = () => !!token.value

    return {
        user, token, errors, loading,
        login, register, logout, fetchMe, isLoggedIn
    }
})