<template>
  <div class="auth-container">
    <div class="auth-box">

      <h1>Welcome back! 👋</h1>
      <p class="subtitle">Login to your ChatApp account</p>

      <form @submit.prevent="handleLogin">

        <!-- Email -->
        <div class="form-group">
          <label>Email</label>
          <input
            v-model="form.email"
            type="email"
            placeholder="ashish@example.com"
            required
          />
          <span v-if="auth.errors.email" class="error">
            {{ auth.errors.email[0] }}
          </span>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label>Password</label>
          <input
            v-model="form.password"
            type="password"
            placeholder="Your password"
            required
          />
          <span v-if="auth.errors.password" class="error">
            {{ auth.errors.password[0] }}
          </span>
        </div>

        <!-- Error message -->
        <div v-if="errorMsg" class="error-box">
          {{ errorMsg }}
        </div>

        <!-- Submit -->
        <button type="submit" :disabled="auth.loading">
          {{ auth.loading ? 'Logging in...' : 'Login' }}
        </button>

      </form>

      <p class="switch">
        Don't have an account?
        <RouterLink to="/register">Register here</RouterLink>
      </p>

    </div>
  </div>
</template>

<script setup>
import { ref }          from 'vue'
import { useRouter }    from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router   = useRouter()
const auth     = useAuthStore()
const errorMsg = ref('')

const form = ref({
    email:    '',
    password: '',
})

async function handleLogin() {
    errorMsg.value = ''
    const success  = await auth.login(form.value)
    if (success) {
        router.push('/workspaces')
    } else {
        errorMsg.value = 'Invalid email or password.'
    }
}
</script>

<style scoped>
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f2f5;
}
.auth-box {
    background: white;
    padding: 40px;
    border-radius: 12px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.1);
}
h1 {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 4px;
}
.subtitle {
    color: #888;
    font-size: 14px;
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
    transition: border 0.2s;
}
input:focus {
    border-color: #5865f2;
}
.error {
    color: #e53e3e;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}
.error-box {
    background: #fff5f5;
    border: 1px solid #fed7d7;
    color: #e53e3e;
    padding: 10px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 16px;
}
button {
    width: 100%;
    padding: 12px;
    background: #5865f2;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
button:hover:not(:disabled) {
    background: #4752c4;
}
button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.switch {
    text-align: center;
    margin-top: 20px;
    font-size: 13px;
    color: #666;
}
.switch a {
    color: #5865f2;
    font-weight: 600;
    text-decoration: none;
}
</style>