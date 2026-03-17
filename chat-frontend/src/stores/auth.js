import { defineStore } from 'pinia'
import { ref }         from 'vue'
import api             from '../composables/useApi'

export const useAuthStore = defineStore('auth', () => {
    const user  = ref(null)
    const token = ref(localStorage.getItem('token'))

    async function login(credentials) {
        const data  = await api('/login', {
            method: 'POST',
            body:   credentials,
        })
        token.value = data.token
        user.value  = data.user
        localStorage.setItem('token', data.token)
    }

    async function register(userData) {
        const data  = await api('/register', {
            method: 'POST',
            body:   userData,
        })
        token.value = data.token
        user.value  = data.user
        localStorage.setItem('token', data.token)
    }

    async function logout() {
        await api('/logout', { method: 'POST' })
        token.value = null
        user.value  = null
        localStorage.removeItem('token')
    }

    async function fetchMe() {
        if (!token.value) return
        user.value = await api('/me')
    }

    const isLoggedIn = () => !!token.value

    return { user, token, login, register, logout, fetchMe, isLoggedIn }
})