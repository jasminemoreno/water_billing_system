<template>
    <div class="customer-layout">
      <!-- Sidebar -->
      <Sidebar />
  
      <!-- Right Side -->
      <div class="main-wrapper">
        <!-- Navbar -->
        <Navbar />
  
        <!-- Page Content -->
        <main class="content">
          <router-view />
        </main>
      </div>
    </div>
  </template>
  
  <script>
  import Sidebar from "@/components/customer/Sidebar.vue";
  import Navbar from "@/components/customer/Navbar.vue";
  
  export default {
    name: "CustomerLayout",
    components: {
      Sidebar,
      Navbar
    }
  };
  </script>
  
  