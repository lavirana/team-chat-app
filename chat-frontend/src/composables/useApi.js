import { ofetch } from 'ofetch';

const api = ofetch.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',

    onRequest({ options }) {
        const token = localStorage.getItem('token')

        options.headers = {
            ...options.headers,
            'Accept': 'application/json',
            ...(token && { Authorization: `Bearer ${token}` }),
        }
    },

    onResponseError({ response }) {
        if (response.status === 401) {
            localStorage.removeItem('token')
            window.location.href = '/login'
        }
    }
})

export default api