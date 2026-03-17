// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
    {
        path: '/',
        redirect: '/login'         
    },
    {
        path: '/',
        name: 'Home',
        component: () => import('../views/Home.vue')
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/Auth/Login.vue'),
        meta: {guest: true}
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('../views/Auth/Register.vue'),
        meta: { guest: true }
    },
    {
        path: '/workspaces',
        name: 'Workspaces',
        component: () => import('../views/Workspace/Index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/workspace/:workspaceId',
        component: () => import('../views/Chat/Layout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: 'channel/:channelId',
                name: 'Channel',
                component: () => import('../views/Chat/Channel.vue'),
            },
            {
                path: 'dm/:userId',
                name: 'DirectMessage',
                component: () => import('../views/Chat/DirectMessage.vue'),
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    const auth = useAuthStore()
    const token = localStorage.getItem('token')

    if(to.meta.requiresAuth && !token) {
        next('/login')
    }else if(to.meta.guest && token) {
        next('/workspaces')
    }else{
        next()
    }
})

export default router