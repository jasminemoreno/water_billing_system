<template>
  <div class="login-page">
    <div class="login-container">
      <aside class="brand">
        <div class="logo-wrap">
          <img src="@/assets/img/alipao.png" alt="Official Seal">
        </div>
        <h2>Barangay Alipao<br>Water Billing System</h2>
      </aside>

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
  background: linear-gradient(270deg, #a18cd1, #fbc2eb, #d9afd9, #cda2f7);
  background-size:600% 600%;
  animation: gradientMove 12s ease infinite;
  font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

@keyframes gradientMove{
  0%{background-position:0% 50%}
  50%{background-position:100% 50%}
  100%{background-position:0% 50%}
}


/* =========================
   CONTAINER
========================= */
.login-container{
  display:flex;
  width:900px;
  max-width:95%;
  border-radius:16px;
  overflow:hidden;
  backdrop-filter: blur(15px);
  box-shadow:0 20px 60px rgba(0,0,0,0.25);
}


/* =========================
   BRAND SIDE
========================= */
.brand{
  flex:1;
  background:linear-gradient(180deg,#0d6efd,#0a58ca);
  color:white;
  padding:3rem 2rem;
  display:flex;
  flex-direction:column;
  justify-content:center;
  align-items:center;
  text-align:center;
}

.logo-wrap img{
  width:110px;
  margin-bottom:20px;
  border-radius:50%;
  border:3px solid rgba(255,255,255,0.6);
  box-shadow:0 6px 20px rgba(0,0,0,0.3);
}

.brand h2{
  font-weight:600;
  line-height:1.4;
  font-size:1.6rem;
}


/* =========================
   LOGIN CARD
========================= */
.card{
  flex:1;
  background:white;
  padding:3rem 2.5rem;
  display:flex;
  flex-direction:column;
  justify-content:center;
}

.card h1{
  text-align:center;
  color:#0d6efd;
  margin-bottom:2rem;
  font-size:1.9rem;
  font-weight:700;
}


/* =========================
   FORM
========================= */
.form-row{
  margin-bottom:1.6rem;
}

.form-row label{
  font-weight:600;
  color:#444;
  display:block;
  margin-bottom:6px;
}


/* INPUT STYLE */

.form-row input{
  width:100%;
  padding:12px 14px;
  border-radius:10px;
  border:1px solid #d0d7de;
  font-size:15px;
  transition:all .25s ease;
}

.form-row input:focus{
  outline:none;
  border-color:#0d6efd;
  box-shadow:0 0 0 3px rgba(13,110,253,0.15);
  transform:translateY(-1px);
}


/* =========================
   LINKS
========================= */

.helper-line{
  text-align:right;
  margin-bottom:1.4rem;
}

.helper-line a{
  font-size:14px;
  color:#0d6efd;
  text-decoration:none;
}

.helper-line a:hover{
  text-decoration:underline;
}


/* =========================
   ERROR MESSAGE
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
  background:linear-gradient(135deg,#0d6efd,#0056d2);
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

.btn-primary:active{
  transform:scale(.97);
}

.btn-primary:disabled{
  background:#6c757d;
  cursor:not-allowed;
  box-shadow:none;
}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:768px){

.login-container{
  flex-direction:column;
}

.brand{
  padding:2rem;
}

.card{
  padding:2rem;
}

}

</style>