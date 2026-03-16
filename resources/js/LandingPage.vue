<template>
  <div class="landing-card">
    <img :src="logo" alt="Logo" class="landing-logo" />
    <h1>Water Billing System</h1>
    <p>Select your login type to continue</p>

    <button @click="goToAdminLogin" class="btn btn-admin btn-login">Admin Login</button>
    <button @click="goToCustomerLogin" class="btn btn-customer btn-login">Customer Login</button>
  </div>
</template>

<script>
import logoImg from "./assets/img/alipao.png";

export default {
  name: "LandingPage",
  data() {
    return {
      logo: logoImg,
    };
  },
  methods: {
    goToAdminLogin() {
      this.$router.push({ name: "admin.login" });
    },
    goToCustomerLogin() {
      this.$router.push({ name: "customer.login" });
    },
  },
};
</script>

<style scoped>
body, html { height: 100vh; margin: 0; font-family: 'Roboto', sans-serif; } .landing-card { height: auto; display: flex; flex-direction: column; justify-content: center; align-items: center; background: #ffffff; padding: 3rem 2rem; border-radius: 20px; text-align: center; width: 380px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); transition: transform 0.3s ease, box-shadow 0.3s ease; margin: auto; position: relative; top: 50%; transform: translateY(50%); } .landing-card:hover { transform: translateY(48%); box-shadow: 0 20px 45px rgba(0,0,0,0.25); } .landing-logo { width: 130px; height: auto; margin-bottom: 1.5rem; } h1 { color: #333; font-size: 1.9rem; font-weight: 700; margin-bottom: 0.5rem; } p { color: #666; margin-bottom: 2rem; font-size: 1.05rem; } .btn-login { width: 100%; padding: 0.8rem; font-size: 1rem; font-weight: 500; border-radius: 8px; margin-bottom: 1rem; transition: all 0.3s ease; border: none; cursor: pointer; } .btn-admin { background: linear-gradient(90deg, #007bff, #0056b3); color: #fff; } .btn-admin:hover { background: linear-gradient(90deg, #0056b3, #003d80); } .btn-customer { background: linear-gradient(90deg, #28a745, #1c7c31); color: #fff; } .btn-customer:hover { background: linear-gradient(90deg, #1c7c31, #14521f); } @media (max-width: 420px) { .landing-card { width: 90%; padding: 2.5rem 1.5rem; } h1 { font-size: 1.6rem; } p { font-size: 1rem; } }
</style>