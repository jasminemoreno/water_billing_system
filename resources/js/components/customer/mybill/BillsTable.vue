<template>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Bill ID</th>
            <th>Meter No</th>
            <th>Consumption</th>
            <th>Total Amount</th>
            <th>Due Date</th>
            <th>Disconnection Date</th>
            <th>Created At</th>
            <th>Status</th>
          </tr>
        </thead>
  
        <tbody>
          <tr v-if="bills.length === 0">
            <td colspan="8">No bills found.</td>
          </tr>
  
          <tr v-for="bill in bills" :key="bill.id">
            <td>{{ bill.id }}</td>
            <td>{{ bill.meter_no }}</td>
            <td>{{ bill.consumption }} m³</td>
            <td>₱{{ Number(bill.total ?? 0).toFixed(2) }}</td>
            <td>{{ formatDate(bill.due_date) }}</td>
            <td>{{ formatDate(bill.disconnection_date) }}</td>
            <td>{{ formatDate(bill.created_at, 'YYYY-MM-DD') }}</td>
            <td>{{ bill.status || 'Unpaid' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue'
  import dayjs from 'dayjs'
  
  const props = defineProps({
    bills: {
      type: Array,
      required: true,
      default: () => []
    }
  })
  
  const formatDate = (date, format = 'MMM DD, YYYY') => {
    return date ? dayjs(date).format(format) : '-'
  }
  </script>
  
  <style scoped>
  .table-container {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.07);
    overflow-x: auto;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
  }
  
  thead {
    background-color: #0f4c75;
    color: #ffffff;
  }
  
  thead th {
    padding: 12px 15px;
    text-align: left;
    font-weight: 600;
    text-transform: uppercase;
  }
  
  tbody td {
    padding: 12px 15px;
    border-bottom: 1px solid #e9ecef;
    font-size: 0.95em;
    color: #333;
  }
  
  tbody tr:hover {
    background-color: #f1f3f5;
    transition: background 0.2s ease;
  }
  </style>