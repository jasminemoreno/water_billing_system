<template>
  <div class="login-page">
    <div class="login-container">

      <!-- LOGO -->
      <div class="logo-wrap">
        <img src="@/assets/img/alipao.png" alt="Official Seal">
      </div>

      <!-- TITLE -->
      <h2 class="system-title">
        Barangay Alipao <br> Water Billing System
      </h2>

      <!-- LOGIN CARD -->
      <section class="card">
        <h1>Customer Login</h1>

        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>

        <form @submit.prevent="login">
          <div class="form-row">
            <label>Email:</label>
            <input v-model="form.email" type="email" placeholder="Enter email" required autofocus>
          </div>

          <div class="form-row">
            <label>Password:</label>
            <input v-model="form.password" type="password" placeholder="Enter password" required minlength="6">
          </div>

          <div class="helper-line">
            <router-link to="/customer/ForgotPassword">Forgot password?</router-link>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="loading">
            <span v-if="loading">Logging in...</span>
            <span v-else>Log In</span>
          </button>
        </form>
      </section>

    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import api, { setToken } from '@/customerApi.js'

const router = useRouter()

const form = reactive({ email: '', password: '' })
const errorMessage = ref('')
const loading = ref(false)

const login = async () => {
  errorMessage.value = ''
  loading.value = true

  try {
    const response = await api.post('/customer/login', form)
    
    if (response.data.success) {
      sessionStorage.setItem('customerToken', response.data.token)
      setToken(response.data.token)
      router.push('/customer/dashboard')
    } else {
      errorMessage.value = response.data.message || 'Login failed'
    }
  } catch (error) {
    errorMessage.value = (error.response?.data?.message) || 'Server error. Try again later.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>

/* =========================
   PAGE BACKGROUND
========================= */
.login-page {
  display:flex;
  justify-content:center;
  align-items:center;
  min-height:100vh;

  background:
    
    url('@/assets/img/water2.png');

    background-size: 100%;
  background-position: center;
  background-repeat: no-repeat;

  font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}


/* =========================
   CONTAINER (VERTICAL)
========================= */
.login-container{
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  width:100%;
  max-width:500px;
  padding:20px;
}


/* =========================
   LOGO
========================= */
.logo-wrap img{
  width:200px;
  margin-bottom:15px;
  border-radius:50%;
  border:3px solid rgba(255,255,255,0.6);
  box-shadow:0 6px 20px rgba(0,0,0,0.3);
}


/* =========================
   TITLE
========================= */
.system-title{
  text-align:center;
  color:white;
  margin-bottom:25px;
  font-size:1.5rem;
  font-weight:600;
}


/* =========================
   CARD
========================= */
.card{
  width:100%;
  background: rgba(203, 221, 233, 0.75); /* 75% opacity background ONLY */
  padding:2rem;
  border-radius:16px;
  box-shadow:0 15px 40px rgba(0,0,0,0.25);
}

.card h1{
  text-align:center;
  color:#2872A1;
  margin-bottom:1.5rem;
  font-size:1.7rem;
  font-weight:700;
}


/* =========================
   FORM
========================= */
.form-row{
  margin-bottom:1.4rem;
}

.form-row label{
  font-weight:600;
  color:#444;
  display:block;
  margin-bottom:6px;
}

.form-row input{
  width:100%;
  padding:12px 14px;
  border-radius:10px;
  border:1px solid #d0d7de;
  font-size:16px;
  transition:all .25s ease;
}

.form-row input:focus{
  outline:none;
  border-color:#0d6efd;
  box-shadow:0 0 0 3px rgba(13,110,253,0.15);
}


/* =========================
   LINKS
========================= */
.helper-line{
  text-align:right;
  margin-bottom:1.2rem;
}

.helper-line a{
  font-size:16px;
  color:#2872A1;
  text-decoration:none;
}

.helper-line a:hover{
  text-decoration:underline;
}


/* =========================
   ERROR
========================= */
.error-message{
  background:#fdeaea;
  border:1px solid #f5c6cb;
  padding:10px;
  border-radius:8px;
  text-align:center;
  margin-bottom:15px;
  color:#c0392b;
  font-weight:500;
}


/* =========================
   BUTTON
========================= */
.btn-primary{
  width:100%;
  padding:12px;
  border:none;
  border-radius:10px;
  background:#2872A1;
  color:white;
  font-size:16px;
  font-weight:600;
  cursor:pointer;
  transition:all .25s ease;
}

.btn-primary:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 20px rgba(13,110,253,0.35);
}

.btn-primary:disabled{
  background:#6c757d;
  cursor:not-allowed;
}

</style>