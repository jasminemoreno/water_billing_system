<template>
    <div class="about-container">
      
      <!-- Back Button -->
      <button class="back-btn" @click="goBack">← Back</button>
  
      <!-- System Information -->
      <div class="about-section">
        <h3 class="section-title">System Information</h3>
        <p class="section-text">
          The <strong>Water Billing System</strong> is a web-based platform designed to manage water 
          consumption records, generate monthly bills, and track customer payments efficiently.
        </p>
      </div>
  
      <!-- System Name -->
      <div class="about-section">
        <h3 class="section-title">System Name</h3>
        <p class="center-text system-name">Barangay Alipao Water Billing System</p>
        <p class="center-text version">Version 1.0</p>
      </div>
  
      <!-- Developer / Project Team -->
      <div class="about-section">
        <h3 class="section-title">Developer / Project Team</h3>
        <div class="team-info">
          <p><strong>Developed by:</strong></p>
          <ul>
            <li>Moreno, Jasmine G.</li>
            <li>Salino, Famila N.</li>
            <li>Balladares, Rasha S.</li>
          </ul>
          <p><strong>Course:</strong> BS Information Technology</p>
          <p><strong>Instructor:</strong> Cabanero, Ralph C.</p>
        </div>
      </div>
  
    </div>
  </template>
  
  <script setup>
  function goBack() {
    if (window.history.length > 1) {
      window.history.back();
    } else {
      window.location.href = "/customer/dashboard";
    }
  }
  </script>
  
  <style scoped>
  /* Container */
  .about-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    font-family: 'Poppins', sans-serif;
    background: #CBDDE9;
    border-radius: 25px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
  }
  
  /* Back Button */
  .back-btn {
    background:#FFFFFF ;
    color: #5D89C2;
    padding: 10px 20px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: transform 0.15s ease, background 0.3s ease;
    margin-bottom: 30px;
  }
  .back-btn:hover {
    transform: scale(0.95);
    background: linear-gradient(135deg,#574bff,#7f6eff);
  }
  
  /* Sections */
  .about-section {
    background: white;
    padding: 25px 30px;
    border-radius: 20px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .about-section:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 45px rgba(0,0,0,0.1);
  }
  
  /* Titles */
  .section-title {
    font-size: 20px;
    font-weight: 700;
    color: #4a90e2;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 8px;
    margin-bottom: 15px;
  }
  
  /* Text */
  .section-text {
    font-size: 16px;
    line-height: 1.7;
    color: #444;
  }
  
  /* Centered Text */
  .center-text {
    text-align: center;
    font-weight: 600;
    font-size: 17px;
    margin: 5px 0;
  }
  
  /* System Name / Version */
  .system-name {
    font-size: 18px;
    color: #6c63ff;
  }
  .version {
    font-size: 15px;
    color: #888;
  }
  
  /* Team Info */
  .team-info p {
    font-size: 15px;
    margin: 5px 0;
  }
  .team-info ul {
    list-style: disc;
    padding-left: 20px;
    margin: 5px 0 15px 0;
  }
  .team-info li {
    font-size: 15px;
    margin-bottom: 3px;
  }
  
  /* Responsive */
  @media (max-width: 600px) {
    .about-container {
      padding: 15px;
      margin: 30px 15px;
    }
    .back-btn {
      width: 100%;
    }
  }
  </style>