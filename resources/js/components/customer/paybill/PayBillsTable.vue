<template>
    <div class="card">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Bill ID</th>
              <th>Billing Month</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="bills.length === 0">
              <td colspan="5">No unpaid bills.</td>
            </tr>
            <tr v-for="bill in bills" :key="bill.id">
              <td>{{ bill.id }}</td>
              <td>{{ formatMonth(bill.created_at) }}</td>
              <td>₱{{ Number(bill.total ?? 0).toFixed(2) }}</td>
                <td>
              <span :class="['status', bill.status?.toLowerCase()]">
                {{ bill.status }}
              </span>
            </td>
                          <td>
                <button class="pay-btn" @click="$emit('pay', bill)">Pay</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  
  <script setup>
  import dayjs from "dayjs"
  
  defineProps({
    bills: {
      type: Array,
      default: ()=>[]
    }
  })
  
  const formatMonth = (date) => date ? dayjs(date).format("MMMM YYYY") : "-"
  </script>
  
  <style scoped>
  .card{
    background:#fff;
    padding:20px;
    border-radius:10px;
    box-shadow:0 6px 15px rgba(0,0,0,0.08);
  }
  
  .table-container{
    overflow-x:auto;
  }
  
  table{
    width:100%;
    border-collapse:collapse;
  }
  
  th,td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #ddd;
  }
  
  th{
    background:#0f4c75;
    color:white;
  }
  
  .pay-btn{
    background:#0f4c75;
    color:#fff;
    border:none;
    padding:8px 14px;
    cursor:pointer;
    border-radius:6px;
    transition:all .2s ease;
  }
  
  .pay-btn:hover{
    background:#0d3a5a;
    transform:translateY(-2px);
  }
  </style>