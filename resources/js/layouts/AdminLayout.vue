<template>
    <div class="admin-layout">
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
  import Sidebar from "@/components/admin/Sidebar.vue";
  import Navbar from "@/components/admin/Navbar.vue";
  
  export default {
    name: "AdminLayout",
    components: {
      Sidebar,
      Navbar
    }
  };
  </script>
  
  