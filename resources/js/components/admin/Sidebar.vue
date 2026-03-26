<template>
  <aside class="sidebar">
    <!-- Logo Section -->
    <div class="logo-section">
      <img :src="logo" alt="Barangay Logo" class="logo" />
      <h2>Water Billing System</h2>
    </div>

    <ul>
      <!-- Dashboard Link -->
      <li>
        <router-link to="/admin/dashboard" class="sidebar-link" active-class="active">
          <span>
            <img :src="dashboardIcon" />
            Dashboard
          </span>
        </router-link>
      </li>

      <!-- Customer Link -->
      <li>
        <router-link to="/admin/customer" class="sidebar-link" active-class="active">
          <span>
            <img :src="customerIcon" />
            Customer Management
          </span>
        </router-link>
      </li>

      <!-- Static / Non-clickable links -->
      <li>
        <router-link to="/admin/bill" class="sidebar-link" active-class="active">
        <span>
          <img :src="billIcon" />
          Bill Management
        </span>
      </router-link>
      </li>

      <li>
        <router-link to="/admin/payment" class="sidebar-link" active-class="active">
        <span>
          <img :src="paymentIcon" />
          Payment Management
        </span>
      </router-link>
      </li>

      <li>
        <router-link to="/admin/report" class="sidebar-link" active-class="active">
        <span>
          <img :src="reportIcon" />
          Reports
        </span>
      </router-link>
      </li>

      <li>
        <router-link to="/admin/user" class="sidebar-link" active-class="active">
        <span>
          <img :src="userIcon" />
          User Management
        </span>
      </router-link>
      </li>
    </ul>
  </aside>
</template>

<script>
import logoImg from "@/assets/img/alipao.png";
import dashboardIcon from "@/assets/icons/dashboard_white.png";
import customerIcon from "@/assets/icons/customer_white.png";
import billIcon from "@/assets/icons/bill_white.png";
import paymentIcon from "@/assets/icons/payment_white.png";
import reportIcon from "@/assets/icons/report_white.png";
import userIcon from "@/assets/icons/user_management_white.png";

export default {
  name: "AdminSidebar",
  data() {
    return {
      logo: logoImg,
      dashboardIcon,
      customerIcon,
      billIcon,
      paymentIcon,
      reportIcon,
      userIcon,
    };
  },
};
</script>

<style scoped>
.sidebar {
  width: 250px;
  background: linear-gradient(180deg, #007bff, #0056b3);
  color: white;
  display: flex;
  z-index: 99;
  flex-direction: column;
  align-items: center;
  padding: 25px 0;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
}

.logo-section img.logo {
  width: 100px;
  margin-bottom: 10px;
  border-radius: 10px;
  margin-left: 30px;
}

.sidebar h2 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 25px;
  text-align: center;
}

.sidebar ul {
  list-style: none;
  margin: 0;
  padding: 0;
  width: 100%;
}

.sidebar ul li {
  margin: 5px 0;
}

/* router-link wrapper */
.sidebar-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

.sidebar-link span {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  font-size: 15px;
  cursor: pointer;
}

/* Active state for current page */
.sidebar-link.active span {
  background-color: rgba(255, 255, 255, 0.15);
  color: #fff;
  border-radius: 8px;
}

.sidebar-link.active span img {
  filter: brightness(0) invert(0); /* black icon */
}

/* Static links (non-clickable) */
.sidebar ul li span {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  font-size: 15px;
  cursor: default;
}

.sidebar ul li span img {
  width: 20px;
  height: 20px;
  margin-right: 10px;
}
</style>