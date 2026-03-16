import { ofetch } from 'ofetch';

const api = ofetch.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
     
     onRequest( { options }) {
        options.headers  = {
            ...options.headers,
            Authorization: `Bearer ${localStorage.getItem('token')}`,
            'Accept': 'application/json',
        }
     },
     onResponseError({ response }) {
        //Handle 401 globally — redirect to login
        if(response.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
     }
});
export default api