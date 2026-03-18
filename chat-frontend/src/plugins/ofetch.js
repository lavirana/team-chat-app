import { ofetch } from 'ofetch'

export const api = ofetch.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Accept': 'application/json',
    },

    onRequest({ options }) {
        const token = localStorage.getItem('token')
        if(token){
            options.headers = {
                ...options.headers,
                Authorization: `Bearer ${token}`,
            }
        }
    },
    onResponseError({ response }) {
            if(response.status === 401) {
                localStorage.removeItem('token')
                window.location.href = '/login'
            }
    }

});