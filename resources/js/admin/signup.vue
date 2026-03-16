<template>
    <div class="signup-page">
      <div class="container">
        <div class="signup-box">
          <h2>Sign Up</h2>
  
          <!-- Full Name -->
          <label>Full Name:</label>
          <input
            type="text"
            v-model="form.fullname"
            placeholder="Enter your full name"
            required
          />
          <small v-if="errors.fullname" class="error">{{ errors.fullname[0] }}</small>
  
          <!-- Email -->
          <label>Email:</label>
          <input
            type="email"
            v-model="form.email"
            placeholder="Enter your email"
            required
          />
          <small v-if="errors.email" class="error">{{ errors.email[0] }}</small>
  
          <!-- Password -->
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
  
          <!-- Confirm Password -->
          <label>Confirm Password:</label>
          <div class="password-container">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="form.password_confirmation"
              placeholder="Confirm your password"
              required
            />
            <i
              class="fa"
              :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"
              @click="togglePassword"
            ></i>
          </div>
          <small v-if="errors.password_confirmation" class="error">{{ errors.password_confirmation[0] }}</small>
  
          <!-- Show/Hide Password Checkbox (optional) -->
          <!-- Show Password Section -->
                <div class="show-password">
                <input type="checkbox" id="showPassword" v-model="showPassword" />
                <label for="showPassword">Show Password</label>
                </div>

          <!-- Signup Button -->
          <button class="login-btn" @click.prevent="signup" :disabled="loading">
            {{ loading ? "Signing Up..." : "Sign Up" }}
          </button>
  
          <!-- Link to Login -->
          <div class="login-link">
            Already have an account?
            <a @click.prevent="$router.push({ name: 'admin.login' })">Login here</a>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    name: "SignUp",
    data() {
      return {
        showPassword: false, // toggles both password fields
        loading: false,      // shows "Signing Up..." when request is processing
        form: {
          fullname: "",
          email: "",
          password: "",
          password_confirmation: "",
        },
        errors: {},
      };
    },
    methods: {
      togglePassword() {
        this.showPassword = !this.showPassword;
      },
      async signup() {
        this.errors = {};
        this.loading = true;
  
        try {
          // ✅ Get Sanctum CSRF cookie
          await axios.get("/sanctum/csrf-cookie");
  
          // ✅ Call signup API
          await axios.post("/api/signup", this.form, { withCredentials: true });
  
          // Success message & redirect to login
          alert("Account created successfully!");
          this.$router.push({ name: "admin.login" });
  
        } catch (error) {
          if (error.response?.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            alert("Something went wrong. Please try again.");
          }
        } finally {
          this.loading = false;
        }
      },
    },
  };
  </script>
  
  <style scoped>
  /* SIGN UP CSS */
  .signup-page * {
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
  }
  
  .signup-page {
    margin: 0;
    padding: 0;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(to bottom right, #00343a, #008c9e);
  }
  
  .signup-page .container {
    width: 1000px;
    display: flex;
    justify-content: center;
  }
  
  .signup-page .signup-box {
    background-color: #b0d0d3;
    padding: 40px 50px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    width: 400px;
    text-align: left;
  }
  
  .signup-page .signup-box h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #111;
  }
  
  .signup-page label {
    display: block;
    font-weight: 500;
    margin-top: 10px;
    margin-bottom: 5px;
    color: #222;
  }
  
  .signup-page input {
    width: 100%;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #444;
    outline: none;
    margin-bottom: 10px;
    font-size: 14px;
  }
  
  .signup-page button.login-btn {
    width: 100%;
    padding: 10px 0;
    border-radius: 15px;
    background-color: #1a045a;
    color: white;
    font-size: 16px;
    font-weight: bold;
    border: none;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
  }
  
  .signup-page button.login-btn:hover {
    background-color: #2e067e;
    transform: translateY(-2px);
  }
  
  .signup-page .login-link {
    text-align: center;
    margin-top: 15px;
    font-size: 12px;
    color: #333;
  }
  
  .signup-page .login-link a {
    color: #0a2463;
    text-decoration: none;
  }
  
  .signup-page .login-link a:hover {
    text-decoration: underline;
  }
  
  .signup-page .password-container {
    position: relative;
  }
  
  .signup-page .password-container i {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #333;
    font-size: 16px;
  }
  
  .signup-page .password-container i:hover {
    color: #000;
  }
  
/* Updated show-password styling */
.signup-page .show-password {
  display: flex;           /* Align items horizontally */
  align-items: center;     /* Vertically center checkbox and label */
  gap: 10px;               /* Space between checkbox and text */
  margin-bottom: 15px;
  font-size: 14px;
  color: #222;
  
}


/* Make checkbox bigger */
.signup-page .show-password input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}
  
  .signup-page .error {
    color: red;
    font-size: 12px;
    display: block;
    margin-top: -10px;
    margin-bottom: 10px;
  }
  </style>