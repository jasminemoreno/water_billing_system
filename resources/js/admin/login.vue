<template>
  <div class="login-page">
    <div class="container">
      <!-- Left Side -->
      <div class="left-panel">
        <img :src="logo" alt="Barangay Alipao Logo" class="logo" />
        <h2>Barangay Alipao<br />Water Billing<br />System</h2>
      </div>

      <!-- Right Side -->
      <div class="right-panel">
        <form class="login-form" @submit.prevent="login">
          <h2>Log In Form</h2>

          <!-- Email Field -->
          <label>Email:</label>
          <input
            type="email"
            v-model="form.email"
            placeholder="Enter your email"
            required
          />
          <small v-if="errors.email" class="error">{{ errors.email[0] }}</small>

          <!-- Password Field -->
          <label>Password:</label>
          <div class="password-container">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="form.password"
              placeholder="Enter your password"
              required
            />
            <i
              class="fa"
              :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"
              @click="togglePassword"
            ></i>
          </div>
          <small v-if="errors.password" class="error">{{ errors.password[0] }}</small>

          <!-- Forgot Password Link (left side, smaller, blue) -->
          <div class="forgot-link">
            <router-link to="/admin/ForgotPassword">Forgot password?</router-link>
          </div>

          <!-- Buttons -->
          <div class="buttons">
            <button type="submit">{{ loading ? "Logging in..." : "Log In" }}</button>
            <button type="button" @click="goToSignup">Sign Up</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api, { setToken } from "@/api.js";
import logoImg from "../assets/img/alipao.png";

export default {
  data() {
    return {
      logo: logoImg,
      showPassword: false,
      loading: false,
      form: { email: "", password: "" },
      errors: {},
    };
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword;
    },
    async login() {
      this.loading = true;
      this.errors = {};
      try {
        const response = await api.post("/login", this.form);
        const token = response.data.token;

        sessionStorage.setItem("authToken", token);
        setToken(token);

        this.$router.replace({ name: "admin.dashboard" });
      } catch (err) {
        if (err.response?.status === 422) {
          this.errors = err.response.data.errors;
        } else if (err.response?.status === 401) {
          this.errors = { email: ["Invalid credentials."] };
        } else {
          console.error("Login error", err);
        }
      } finally {
        this.loading = false;
      }
    },
    goToSignup() {
      this.$router.push({ name: "admin.signup" });
    },
    goToForgotPassword() {
      this.$router.push({ name: "admin.forgot-password" });
    },
  },
};
</script>

<style scoped>
.login-page * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Georgia", serif;
}

.login-page {
  height: 100vh;
  background: linear-gradient(to bottom right, #00343a, #008c9e);
  display: flex;
  justify-content: center;
  align-items: center;
}

.container {
  width: 90%;
  max-width: 900px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.left-panel {
  color: white;
  text-align: center;
  flex: 1;
}

.logo {
  width: 270px;
  margin-bottom: 20px;
  transform: translate(-100px, -50px);
}

.left-panel h2 {
  font-size: 25px;
  line-height: 1.5;
  transform: translate(-100px, -50px);
}

.right-panel {
  flex: 1.3;
  background: linear-gradient(to bottom, #cfd8dc, #b0bec5);
  border-radius: 15px;
  padding: 40px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
}

.login-form {
  text-align: center;
}

.login-form h2 {
  margin-bottom: 20px;
  color: #000;
}

label {
  display: block;
  text-align: left;
  margin: 10px 0 5px 10px;
  color: #000;
  font-size: 14px;
}

input[type="email"],
input[type="password"],
input[type="text"] {
  width: 100%;
  padding: 10px 35px 10px 10px;
  border: 1.5px solid #000;
  border-radius: 10px;
  outline: none;
  margin-bottom: 15px;
  font-size: 14px;
}

.password-container {
  position: relative;
}

.password-container i {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #333;
  font-size: 16px;
}

.password-container i:hover {
  color: #000;
}

/* Forgot password link - left aligned, smaller, blue */
.forgot-link {
  text-align:right;
  margin-bottom:1.4rem;
  
}

.forgot-link a {
  font-size:14px;
  color:#0d6efd;
  text-decoration:none;
}

.forgot-link a:hover {
  text-decoration:underline;
}

.buttons {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  gap: 20px;
}

.buttons button {
  padding: 10px 35px;
  border-radius: 15px;
  background-color: #0a558c;
  color: white;
  border: none;
  font-size: 14px;
  font-weight: bold;
  transition: all 0.3s ease;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.25);
  cursor: pointer;
}

.buttons button:hover {
  background-color: #084b7d;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.35);
}

.error {
  color: red;
  font-size: 12px;
  display: block;
  text-align: left;
  margin-top: -10px;
  margin-bottom: 10px;
}
</style>