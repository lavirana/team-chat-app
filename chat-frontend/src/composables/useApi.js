import { ofetch } from 'ofetch'

const api = ofetch.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',

    onRequest({ options }) {
        const token = localStorage.getItem('token')

        // Debug — console me dekho
        console.log('Token being sent:', token)

        options.headers = {
            ...options.headers,
            'Accept':       'application/json',
            'Content-Type': 'application/json',
            ...(token && { 'Authorization': `Bearer ${token}` }),
        }

        // Debug — headers dekho
        console.log('Headers:', options.headers)
    },

    onResponseError({ response }) {
        console.log('Response error:', response.status)
        if (response.status === 401) {
            localStorage.removeItem('token')
            window.location.href = '/login'
        }
    }
})

export default api