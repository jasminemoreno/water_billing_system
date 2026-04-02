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
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import profileIconDefault from "@/assets/icons/profile.png";
import aboutIcon from "@/assets/icons/about.png";
import settingsIcon from "@/assets/icons/setting.png";
import logoutIcon from "@/assets/icons/logout.png";

export default {
  name: "AdminNavbar",
  setup() {
    const showPopup = ref(false);
    const adminName = ref("Admin");
    const profileIcon = ref(profileIconDefault);

    const toggle = ref(null);
    const popup = ref(null);

    const togglePopup = () => {
      showPopup.value = !showPopup.value;
    };

    const handleOutsideClick = (event) => {
      if (
        toggle.value &&
        !toggle.value.contains(event.target) &&
        popup.value &&
        !popup.value.contains(event.target)
      ) {
        showPopup.value = false;
      }
    };

    // Fetch admin profile
    const fetchAdminProfile = async () => {
  try {
    const response = await axios.get("/admin/profile");
    const admin = response.data;

    adminName.value = admin.fullname || "Admin";

    // Use full URL directly
    profileIcon.value = admin.profile_photo || profileIconDefault;
  } catch (error) {
    console.error("Failed to fetch admin profile:", error);
  }
};

    // Update profile when edited
    const handleProfileUpdated = (e) => {
      const updatedAdmin = e.detail;
      adminName.value = updatedAdmin.fullname || "Admin";
      profileIcon.value = updatedAdmin.profile_photo || profileIconDefault;
    };

    const logout = () => {
      sessionStorage.clear();
      window.location.href = "/admin/login";
    };

    onMounted(() => {
      document.addEventListener("click", handleOutsideClick);
      window.addEventListener("adminProfileUpdated", handleProfileUpdated);
      fetchAdminProfile();
    });

    onBeforeUnmount(() => {
      document.removeEventListener("click", handleOutsideClick);
      window.removeEventListener("adminProfileUpdated", handleProfileUpdated);
    });

    return {
      showPopup,
      adminName,
      profileIcon,
      aboutIcon,
      settingsIcon,
      logoutIcon,
      toggle,
      popup,
      togglePopup,
      logout,
    };
  },
};
</script>

<style scoped>
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