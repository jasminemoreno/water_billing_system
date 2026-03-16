<template>
    <aside class="sidebar">
      <!-- Logo -->
      <div class="seal">
        <img src="@/assets/img/alipao.png" alt="Official Seal" class="logo-img" />
      </div>
  
      <ul>
        <!-- Home -->
            <li :class="{ active: $route.name === 'customer.dashboard' }">
            <router-link to="/customer/dashboard">
                <HomeIcon class="icon" />
                <span>Home</span>
            </router-link>
            </li>
            
        <!-- My Bills -->
       
          <li :class="{ active: $route.name === 'customer.mybills' }">
            <router-link to="/customer/mybills">
            <DocumentIcon class="icon" />
            <span>My Bills</span>
            </router-link>
            
        </li>
  
        <!-- Pay Bills -->
        <li :class="{ active: $route.name === 'customer.paybills'}">
          <router-link to ="/customer/paybills">
            <CreditCardIcon class="icon" />
            <span>Pay Bills</span>
          </router-link>
        </li>
  
        <!-- Notifications -->
        <li :class ="{ active: $route.name === 'customer.notification'}">
          <router-link to ="/customer/notifications">
            <BellIcon class="icon" />
            <span>Notifications</span>
          </router-link>
        </li>
  
        <!-- Payment History -->
        <li :class ="{ active: $route.name === 'customer.history'}">          
           <router-link to ="/customer/history">
          <ClockIcon class="icon" />
            <span>Payment History</span>
          </router-link>
        </li>
      </ul>
    </aside>
  </template>
  
  <script setup>
  /* Heroicons outline (v2) */
  import { 
    HomeIcon, 
    BellIcon, 
    CreditCardIcon, 
    DocumentIcon, 
    ClockIcon 
  } from '@heroicons/vue/24/outline'
  </script>
  
  <style scoped>
  .sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 250px;
  height: 100vh;
  background-color: #0f4c75;
  color: white;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 20px;
  overflow: hidden;
  z-index: 1000; /* <-- make sure sidebar is on top of content if needed */
}
  .seal {
    margin-bottom: 30px;
  }
  
  .logo-img {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    border: 2px solid white;
  }
  
  ul {
    list-style: none;
    padding: 0;
    width: 100%;
  }
  
  li {
    width: 100%;
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
  a, .menu-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 14px 0 14px 22px;
    color: white;
    text-decoration: none;
  }
  
  .icon {
    width: 20px;
    height: 20px;
  }
  
  a:hover, .menu-item:hover {
    background-color: rgba(255,255,255,0.15);
  }
  
  .active a {
    background-color: rgba(255,255,255,0.25);
  }
  </style>