<!-- template -->
<template>

  <!-- Criteria -->
  <div class="row">

    <!-- Invoice Field -->
    <div class="column">
      <div class="flex-container">
        <label for="InvoiceInput">Invoice No.</label>
      </div>
      <input type="text" id="InvoiceInput" v-model="invoiceNo" />
    </div>

    <!-- Customer Field -->
    <div class="column">
      <div class="flex-container">
        <label for="CustomerInput">Customer Code</label>
      </div>
      <input type="text" id="CustomerInput" v-model="customerCode" />
    </div>

    <!-- Buttons -->
    <div class="button-container">
      <button class="button search-button" @click="searchInvoices">
        <span class="material-icons">search</span> Search
      </button>
      <button class="button create-payment-button" @click="createPayment">
        <span class="material-icons">add</span> Create Payment
      </button>
    </div>
  </div>

  <!-- Table -->
  <div class="table-container">
    <table>

      <!-- columnHeader -->
      <thead>
        <tr>
          <th class="size-cbx"><input type="checkbox" class="checkbox-custom" v-model="selectHeader" @change="toggleSelectAll" /></th>
          <th class="size-doctype">Doc. Type</th>
          <th class="size-docno">Doc. No.</th>
          <th class="size-docdate">Doc. Date</th>
          <th class="size-duedate">Due Date</th>
          <th class="size-docamount">Amount</th>
          <th class="size-balamount">Balance</th>
        </tr>
      </thead>

      <!--rowData -->
      <tbody>
        <tr v-for="(item, index) in filteredData" :key="index">
          <td class="size-cbx"><input type="checkbox" class="checkbox-custom" v-model="item.selected" @change="onCheckboxChange(item)"/></td>
          <td class="text-center size-doctype">{{ item.DOC_TYPE_DISPLAY }}</td>
          <td class="text-center size-docno">{{ item.BILL_DOC }}</td>
          <td class="text-center size-docdate">{{ item.DOC_DATE }}</td>
          <td class="text-center size-duedate">{{ item.DUE_DATE }}</td>
          <td class="text-right size-docamount">{{ item.DOC_AMOUNT }}</td>
          <td class="text-right size-balamount">{{ item.BALANCE_AMOUNT }}</td>
        </tr>
      </tbody>

    </table>
  </div>

  <!-- warning popup -->
  <div>
    <PopupComponent v-if="warning_pop" 
      title="WARNING MESSAGE"
      message="คุณยังไม่ได้เลือกรายการ กรุณาเลือกรายการก่อน ทำการ Payment." 
      :headerColor="'#FCD69D'" 
      :confirmButton="true" 
      @close="warning_pop = false" 
      @confirm="handleConfirm('warning')" />
  </div>

  <!-- payment popup -->
  <div>
    <PopupComponent v-if="payment_pop" 
      title="PAYMENT INFORMATION"
      message="ยอดชำระเงินที่คุณเลือก ได้มีการหักลบยอดแล้วขณะนี้ Grand Total (ยอดรวม) = 0 คุณต้องการดำเนินการต่อใช่หรือไม่."
      :headerColor="'#C8DEFF'" 
      :confirmButton="true"
      @close="payment_pop = false" 
      @confirm="handleConfirm('payment')" />
      <!--  :closeButton="true"  -->
  </div>

  <!-- success popup -->
  <div>
    <PopupComponent v-if="success_pop" 
      title="SUCCESS MESSAGE !" 
      message="ขอบคุณที่ใช้บริการ." 
      :headerColor="'#CCEFE8'" 
      :confirmButton="true" 
      @close="success_pop = false" 
      @confirm="handleConfirm('success')" />
  </div>

  <!-- fail popup -->
  <div>
    <PopupComponent v-if="fail_pop" 
      title="FAIL MESSAGE !" 
      message="ทำรายการไม่สำเร็จ." 
      :headerColor="'#FFDBDB'" 
      :confirmButton="true" 
      @close="fail_pop = false" 
      @confirm="handleConfirm('fail')" />
  </div> 

</template>
<!-- END template -->

<!-- script -->
<script>
import axios from 'axios';
import https from 'https';

import PopupComponent from './PopupComponent.vue';
// import mockData from './data/MockData';

const api = axios.create({
  baseURL: 'https://10.20.1.11:9000/api/PimCore/v1',
  httpsAgent: new https.Agent({
    rejectUnauthorized: false
  }),
  headers: {
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': '*',
  },
});

export default {
  components: {
    PopupComponent,
  },
  name: 'HelloWorld',
  data() {
    return {
      warning_pop: false,
      payment_pop: false,
      success_pop: false,
      fail_pop: false,
      warning: 'warning',
      payment: 'payment',
      success: 'success',
      fail: 'fail',
      selectedItems: [],
      invoiceNo: '',
      customerCode: '',
      paymentNo: '',
      url: '',
      sumAmount: 0,
      matchInv: 0,
      accessToken: null,
      bank: 'KTB-CREDITCARD',
      selectHeader: false,
      // tableData: mockData,
      filteredData: [],
      tableData: [],
    }
  },

  methods: {
    // start service 
    async getAccessToken() {
      try {
        const response = await api.post('/accessToken', {
          appKey: '48424964',
          appSecret: 'e7082f801c7f4df47338542f2c363d099946331280547ea9578cd11343e4ab86',
        }, {
          headers: {
            'ownerId': 'c4ca4238a0b923820dcc509a6f75849b',
            'requestUid': '9999-9999-9999-9999',
            'timestamp': Math.floor(Date.now() / 1000) + 30,
          }
        });
        this.accessToken = response.data.authorization;
      } catch (error) {
        console.error('Error fetching access token:', error);
      }
    },

    async getOutStanding() {
      if (!this.accessToken) {
        await this.getAccessToken();
      }
      try {
        const response = await api.post('/getOutStanding', {
          customerCode: this.customerCode,
        }, {
          headers: {
            'ownerId': 'c4ca4238a0b923820dcc509a6f75849b',
            'requestUid': '9999-9999-9999-9999',
            'timestamp': Math.floor(Date.now() / 1000) + 30,
            'Authorization': this.accessToken,
          }
        });
        this.tableData = response.data;
      } catch (error) {
        console.error('Error fetching outstanding data:', error);
      }
    },

    async getPayment(paymentData) {
      if (!this.accessToken) {
        await this.getAccessToken();
      }
      try {
        const response = await api.post('/payment', paymentData, {
          headers: {
            'ownerId': 'c4ca4238a0b923820dcc509a6f75849b',
            'requestUid': '9999-9999-9999-9999',
            'timestamp': Math.floor(Date.now() / 1000) + 30,
            'Authorization': this.accessToken,
          }
        });
        this.paymentNo = response.data.paymentNo;
        this.url = response.data.url;
      } catch (error) {
        console.error('Error fetching outstanding data:', error);
      }
    },
    
    // end service 

    async searchInvoices() {
      if (this.customerCode && this.invoiceNo) {
        try {
          await this.getOutStanding();
          this.filteredData = this.tableData.filter(item => {
            const matchesCustomer = (item.PAYER === this.customerCode);
            const matchesInvoice = (item.BILL_DOC === this.invoiceNo);
            return matchesCustomer && matchesInvoice
          });
        } catch (error) {
          console.error('Error filter data:', error);
        }
        if (this.filteredData.length > 0) {
          try {
            this.filteredData = this.tableData.filter(item => {
              const matchesCustomer = (item.PAYER === this.customerCode);
              return matchesCustomer;
            });
          } catch (error) {
            console.error('Error show data:', error);
          }
        } else {
        this.clearData
        }
      } else {
        this.clearData
      }
    },

    createPayment() {
      this.selectedItems = this.filteredData.filter(item => item.selected);
      this.sumAmount = this.selectedItems.reduce((total, item) => {
        return total + parseFloat(item.BALANCE_AMOUNT);
      }, 0);

      if (this.selectedItems.length > 0) {
        if (this.sumAmount === 0) {
          this.popup(this.payment);
        } else {
          const dataBilling = this.selectedItems.map(item => ({
            docType: item.DOC_TYPE_DISPLAY,
            docNo: item.BILL_DOC,
            docDate: item.DOC_DATE,
            dueDate: item.DUE_DATE,
            docAmount: item.DOC_AMOUNT,
          }))
          console.log('list item', this.selectedItems);
          console.log('list map item', dataBilling);
          const paymentData = {
            customerCode: this.customerCode,
            bankCode: this.bank,
            billing: dataBilling,
          };
          console.log('dataSendPayment', paymentData);
          this.getPayment(paymentData);
          // window.location.href = this.url;
        }
      } else {
        this.popup(this.warning);
      }
    },

    toggleSelectAll(event) {
      const checked = event.target.checked;
      this.filteredData.forEach(item => item.selected = checked);
    },

    resetSelections() {
      this.filteredData.forEach(item => {
        item.selected = false;
      });
      this.tableData.forEach(item => {
        item.selected = false;
      });
    },

    clearData() {
      this.filteredData = [];
      this.resetSelections();
    },
    
    popup(pop) {
      if (pop === 'warning') {
        this.warning_pop = true;
      } else if (pop === 'payment') {
        this.payment_pop = true;
      } else if (pop === 'success') {
        this.success_pop = true;
      } else if (pop === 'fail') {
        this.fail_pop = true;
      }
    },
    
    handleConfirm(pop) {
      if (pop === 'warning') {
        this.warning_pop = false;
      } else if (pop === 'payment') {
        this.payment_pop = false;
      } else if (pop === 'success') {
        this.success_pop = false;
      } else if (pop === 'fail') {
        this.fail_pop = false;
      }
    },

    onCheckboxChange() {
      if (this.allSelected) {
        this.selectHeader = true
      } else {
        this.selectHeader = false;
      }
    }
  },
  computed: {
    allSelected() {
      return this.filteredData.length > 0 && this.filteredData.every(item => item.selected);
    }
  },
  watch: {
    allSelected(newVal) {
      this.selectHeader = newVal;
    }
  },
  mounted() {
    this.getAccessToken();
  }
};
</script>
<!-- END script -->

<!-- style-->
<style scoped>
/* css btn */
.button-container {
  display: flex;
  align-items: center;
}

.button {
  display: flex;
  align-items: center;
  padding: 10px 15px;
  font-size: 16px;
  border-radius: 8px;
  margin: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  border: 1px solid transparent;
  margin-left: 10px;
  margin-top: 25px;
}

.search-button {
  background-color: #f9e3e2;
  border: 1px solid #5b3b4f;
  color: #5b3b4f;
}

.search-button:hover {
  background-color: #f7d6d6;
}

.create-payment-button {
  background-color: #e3f2fd;
  border: 1px solid #00317A;
  color: #00317A;
}

.create-payment-button:hover {
  background-color: #bbdefb;
}

.button .material-icons {
  margin-right: 8px;
}

.container {
  width: 100%;
  padding: 20px;
}

/* css row */
.row {
  display: flex;
  justify-content: space-between;
}

/* css column */
.column {
  flex: 1;
  margin: 0 10px;
  padding: 10px;
  border-radius: 5px;
}

/* css input */
input {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  border: 2px solid #f9e3e2;
  border-radius: 5px;
}

.flex-container {
  display: flex;
  align-items: center;
}

/* css label */
label {
  margin-right: 10px;
}

.table-container {
  max-width: 100%;
  margin: 20px auto;
  border: 1px solid #ccc;
  border-radius: 5px;
  overflow-x: auto;
}

/* css table */
table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 8px 12px;
  border-bottom: 1px solid #f9e3e2;
  color: #503651;
}

th {
  background-color: #f9e3e2;
  font-weight: bold;
  overflow: hidden; 
  text-overflow: ellipsis; 
  white-space: nowrap; 
}

tr:nth-child(even) {
  background-color: #fdf7f6;
}

tr:hover {
  background-color: #f9e3e2;
}

input[type="checkbox"] {
  cursor: pointer;
}

.text-right {
  text-align: right;
}

.text-center {
  text-align: center;
}

.text-left {
  text-align: left;
}

body {
  font-family: 'Noto Sans', sans-serif;
  font-size: 16px;
}

/* css cbx */
.checkbox-custom {
  appearance: none;
  width: 16px;
  height: 16px;
  border: 2px solid #885a8a;
  border-radius: 4px;
  cursor: pointer;
  position: relative;
  display: inline-flex;
  justify-content: center;
  align-items: center;
}

.checkbox-custom:checked {
  background-color: #885a8a;
  border-color: #885a8a;
}

.checkbox-custom:checked::after {
  content: '✔';
  color: #f9e3e2;
  font-size: 14px;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-weight: bold;
}

.size-cbx {
  width: 5%;
}

.size-doctype {
  width: 5%;
}

.size-docno {
  width: 20%;
}

.size-docdate {
  width: 20%;
}

.size-duedate {
  width: 20%;
}

.size-docamount {
  width: 15%;
}

.size-balamount {
  width: 15%;
}

</style>
<!-- END style -->