<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice Management</title>
  <link rel="stylesheet" href="styles/styles.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> 
</head>
<body>
  <!-- Include header -->
  <?php include 'component/header.php'; ?>

  <!-- Start of #app -->
  <div id="app">
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
          <span class="material-icons">search</span>
          Search
        </button>
        <button class="button create-payment-button" @click="createPayment">
          <span class="material-icons">payment</span>
          Create Payment
        </button>
      </div>
    </div>
  
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
        <!-- rowData -->
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
  </div> 
  <!-- End of #app -->

  <!-- Include footer -->
  <!-- <?php include 'component/footer.php'; ?> -->

  <script src="data/MockData.js"></script> 
  <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/https@1.0.0"></script>
  <script>
    const api = axios.create({
      baseURL: 'https://10.20.1.11:9000/api/PimCore/v1',
      headers: {
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
      },
    });

    function startSpinner() {
      document.getElementById('loading-spinner').style.display = 'flex';
    }

    function stopSpinner() {
        document.getElementById('loading-spinner').style.display = 'none';
    }

    const app = new Vue({
      el: '#app',
      data: {
        filteredData: [],
        tableData: tableData, 
        tableData: [], 
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
      },
      methods: {
      // start service 
        // async getAccessToken() {
        //   try {
        //     const response = await api.post('/accessToken', {
        //       appKey: '48424964',
        //       appSecret: 'e7082f801c7f4df47338542f2c363d099946331280547ea9578cd11343e4ab86',
        //     }, {
        //       headers: {
        //         'ownerId': 'c4ca4238a0b923820dcc509a6f75849b',
        //         'requestUid': '9999-9999-9999-9999',
        //         'timestamp': Math.floor(Date.now() / 1000) + 30,
        //       }
        //     });
        //     this.accessToken = response.data.authorization;
        //   } catch (error) {
        //     console.error('Error fetching access token:', error);
        //   }
        // },
    
        // async getOutStanding() {
        //   if (!this.accessToken) {
        //     await this.getAccessToken();
        //   }
        //   try {
        //     const response = await api.post('/getOutStanding', {
        //       customerCode: this.customerCode,
        //     }, {
        //       headers: {
        //         'ownerId': 'c4ca4238a0b923820dcc509a6f75849b',
        //         'requestUid': '9999-9999-9999-9999',
        //         'timestamp': Math.floor(Date.now() / 1000) + 30,
        //         'Authorization': this.accessToken,
        //       }
        //     });
        //     this.tableData = response.data;
        //   } catch (error) {
        //     console.error('Error fetching outstanding data:', error);
        //   }
        // },
    
        // async getPayment(paymentData) {
        //   if (!this.accessToken) {
        //     await this.getAccessToken();
        //   }
        //   try {
        //     const response = await api.post('/payment', paymentData, {
        //       headers: {
        //         'ownerId': 'c4ca4238a0b923820dcc509a6f75849b',
        //         'requestUid': '9999-9999-9999-9999',
        //         'timestamp': Math.floor(Date.now() / 1000) + 30,
        //         'Authorization': this.accessToken,
        //       }
        //     });
        //     this.paymentNo = response.data.paymentNo;
        //     this.url = response.data.url;
        //   } catch (error) {
        //     console.error('Error fetching outstanding data:', error);
        //   }
        // },
        
      // end service 
    
        async searchInvoices() {
          startSpinner();
          if (this.customerCode && this.invoiceNo) {
            try {
              // await this.getOutStanding();
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
                stopSpinner();
              } catch (error) {
                console.error('Error show data:', error);
                stopSpinner();
              }
            } else {
            this.clearData
            stopSpinner();
            }
          } else {
            this.clearData
            stopSpinner();
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
            //   this.getPayment(paymentData);
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
        
        popup(type) {
          this[`${type}_pop`] = true;
        },
        
        handleConfirm(type) {
          this[`${type}_pop`] = false;
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
    });
  </script>
</body>
</html>