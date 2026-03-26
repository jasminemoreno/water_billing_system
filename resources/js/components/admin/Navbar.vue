<template>
  <div class="navbar">
    <!-- Profile Picture + Name -->
    <div class="profile-area" @click="togglePopup" ref="toggle">
      <img :src="profileIcon" class="profile-pic" />
      <span>{{ adminName }}</span>
    </div>

    <!-- Dropdown / Popup -->
    <div class="profile-popup" :class="{ show: showPopup }" ref="popup">
      <ul>
        <li>
          <router-link to="/admin/profile" class="popup-link">
            <img :src="profileIcon" />
            Profile
          </router-link>
        </li>
        <li>
          <router-link to="/admin/about" class="popup-link">
            <img :src="aboutIcon" />
            About
          </router-link>
        </li>
        <li>
          <router-link to="/admin/setting" class="popup-link">
            <img :src="settingsIcon" />
            Setting
          </router-link>
        </li>
        <li>
          <!-- Logout button -->
          <button @click="logout" class="popup-link">
            <img :src="logoutIcon" />
            Logout
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import profileIcon from "@/assets/icons/profile.png";
import aboutIcon from "@/assets/icons/about.png";
import settingsIcon from "@/assets/icons/setting.png";
import logoutIcon from "@/assets/icons/logout.png";

export default {
  name: "AdminNavbar",
  data() {
    return {
      showPopup: false,
      adminName: "Admin",
      profileIcon,
      aboutIcon,
      settingsIcon,
      logoutIcon,
    };
  },
  mounted() {
    document.addEventListener("click", this.handleOutsideClick);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleOutsideClick);
  },
  methods: {
    togglePopup() {
      this.showPopup = !this.showPopup;
    },
    handleOutsideClick(event) {
      if (
        this.$refs.toggle &&
        !this.$refs.toggle.contains(event.target) &&
        this.$refs.popup &&
        !this.$refs.popup.contains(event.target)
      ) {
        this.showPopup = false;
      }
    },
    logout() {
      // Clear sessionStorage
      sessionStorage.clear();

      // Navigate to login page and replace history
      this.$router.replace({ path: "/admin/login" });
    },
  },
};
</script>

<style scoped>
/* Keep your existing styles as-is */
.navbar {
  height: 65px;
  background: white;
  padding: 0 25px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  border-bottom: 1px solid #e3e3e3;
  position: fixed;
  top: 0;
  left: 250px;
  right: 0;
  z-index: 999;
}

.profile-area {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  position: relative;
}

.profile-pic {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #007bff;
}

.profile-area span {
  font-size: 15px;
  font-weight: 600;
  color: #333;
}

/* Dropdown / Popup */
.profile-popup {
  position: absolute;
  top: 70px;
  right: 0;
  background: white;
  border-radius: 12px;
  width: 180px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.18);
  overflow: hidden;
  opacity: 0;
  transform: translateY(-10px);
  pointer-events: none;
  transition: opacity 0.25s ease, transform 0.25s ease;
  z-index: 9999;
}

.profile-popup.show {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

.profile-popup ul {
  list-style: none;
  padding: 8px 0;
  margin: 0;
}

.profile-popup ul li {
  margin: 0;
}

.popup-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 15px;
  font-size: 14.5px;
  color: #2c2c2c;
  text-decoration: none;
  width: 100%;
  background: none;
  border: none;
  cursor: pointer;
}

.popup-link:hover {
  background: #007bff;
  color: white;
}

.popup-link img {
  width: 20px;
}

.popup-link:hover img {
  filter: brightness(0) invert(1);
}
</style>