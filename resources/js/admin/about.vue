<template>
    <div class="about-page-wrapper">
      <div class="about-page-container">
  
        <button class="about-page-back-btn" @click="goBack">
          <span class="back-icon">←</span> Back
        </button>
  
        <div class="about-page-section">
          <h3>System Information</h3>
          <div class="about-page-info-box">
            Water Billing System is a web-based platform designed to manage water 
            consumption records, generate monthly bills, and track customer payments efficiently.
          </div>
        </div>
  
        <div class="about-page-section">
          <h3>System Name</h3>
          <div class="about-page-info-box center-text">
            Barangay Alipao Water Billing System
          </div>
          <div class="about-page-info-box center-text">
            Version 1.0
          </div>
        </div>
  
        <div class="about-page-section">
          <h3>Developer/Project Team</h3>
          <div class="about-page-info-box">
            <p><strong>Developed by:</strong></p>
            <p>Moreno, Jasmine G.</p>
            <p>Salino, Famila N.</p>
            <p>Balladares, Rasha S.</p>
  
            <br>
  
            <p><strong>Course:</strong> BS Information Technology</p>
            <p><strong>Instructor:</strong> Cabanero, Ralph C.</p>
          </div>
        </div>
  
      </div>
    </div>
  </template>
  
  <script setup>
  const goBack = () => window.history.back()
  </script>
  
  <style scoped>
  /* Wrapper & Container */
  .about-page-wrapper {
    padding: 60px 20px;
    background: linear-gradient(160deg, #f0f4f8, #ffffff);
    min-height: 100vh;
    font-family: "Inter", "Segoe UI", sans-serif;
    display: flex;
    justify-content: center;
    align-items: flex-start;
  }
  
  .about-page-container {
    width: 100%;
    max-width: 720px;
    background: #ffffff;
    padding: 40px 35px;
    border-radius: 20px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s, box-shadow 0.3s;
  }
  
  .about-page-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12);
  }
  
  /* Back Button */
  .about-page-back-btn {
    background: linear-gradient(90deg, #007bff, #0056b3);
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 6px 18px rgba(0, 123, 255, 0.3);
    transition: all 0.25s ease;
  }
  
  .about-page-back-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 123, 255, 0.4);
  }
  
  .back-icon {
    font-size: 18px;
    font-weight: bold;
  }
  
  /* Sections */
  .about-page-section {
    margin-top: 30px;
  }
  
  .about-page-section h3 {
    font-size: 20px;
    color: #007bff;
    margin-bottom: 12px;
    font-weight: 700;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 6px;
  }
  
  /* Info Box */
  .about-page-info-box {
    background: #f7f9fc;
    padding: 18px 20px;
    border-radius: 12px;
    margin-top: 8px;
    font-size: 15px;
    color: #333;
    line-height: 1.6;
    box-shadow: inset 0 1px 4px rgba(0,0,0,0.05);
    transition: background 0.3s;
  }
  
  .about-page-info-box:hover {
    background: #f1f5f9;
  }
  
  .center-text {
    text-align: center;
  }
  </style>