<?php
include_once 'Config.php';

$allData = [];
$filterData = [];
$data = [];
$checkData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (isset($_POST['search'])) {
        $cuscode = isset($_POST['cuscode']) ? $_POST['cuscode'] : '';
        $invNo = isset($_POST['invNo']) ? $_POST['invNo'] : '';

        if ($cuscode && $invNo) {
            $data = fnGetOutStanding($cuscode, fnGetToken());
            if (!is_array($data) || count($data) <= 0) {
                $allData = [];
                $filterData = [];
            } else {
                $checkData = array_filter($data, function ($item) use ($invNo) {
                    return isset($item['BILL_DOC']) && $item['BILL_DOC'] === $invNo;
                });
                $notCheckData = array_filter($data, function ($item) use ($invNo) {
                    return isset($item['BILL_DOC']) && $item['BILL_DOC'] !== $invNo;
                });

                usort($notCheckData, function ($a, $b) {
                    $dateA = strtotime($a['DUE_DATE']);
                    $dateB = strtotime($b['DUE_DATE']);
                    if ($dateA == $dateB) {
                        return strcmp($a['BILL_DOC'], $b['BILL_DOC']);
                    }
                    return ($dateA < $dateB) ? -1 : 1;
                });

                $countCheckData = count($checkData);
                if ($countCheckData > 0) {
                    $allData = array_values($notCheckData);
                    $filterData = array_values($checkData);
                } else {
                    $allData = [];
                    $filterData = [];
                }
            }
        }


        header('Content-Type: application/json');
        echo json_encode([
            'allData' => $allData,
            'filterData' => $filterData,
        ]);
        exit;
    }

    if (isset($_POST['payment'])) {
        $cuscode = isset($_POST['cuscode']) ? $_POST['cuscode'] : '';
        $selectedRows = isset($_POST['selectedRows']) ? json_decode($_POST['selectedRows'], true) : [];
        $bankCode = 'KTB-CREDITCARD';
        $countSelectedRows = count($selectedRows);
        if ($countSelectedRows > 0) {
            $response = fnPayment($cuscode, $selectedRows, $bankCode, fnGetToken());
            $url = isset($response['url']) ? $response['url'] : '';
            if ($url) {
                echo $url;
            } else {
                echo "Error: No URL returned from fnPayment.";
            }
            exit;
        } else {
            $pop = 'notSelect';
            exit;
        }
    }

    if (isset($_GET['status'])) {
        $status = $_GET['status'];
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Credit Card</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>

    <div style="position: relative;">
      <img src="sis.jpg" style="width: 110px; height: auto;">
      <label id="customerLabel"class="tag-name"></label>
    </div>

    <form method="POST" action="" id="paymentForm">
        <div class="row">
            <div class="column">
                <label for="cuscode">Customer Code.<span style="color: red;">*</span></label>
                <input type="text" id="cuscode" name="cuscode" placeholder="ระบุรหัสลูกค้า ที่ปรากฏบนเอกสาร อินวอยซ์" required>
            </div>

            <div class="column">
                <label for="invNo">Invoice No.<span style="color: red;">*</span></label>
                <input type="text" id="invNo" name="invNo" placeholder="ระบุรหัสเลขที่ อินวอยซ์ ที่ปรากฏบนเอกสาร อินวอยซ์" required>
            </div>

            <div class="button-container">
                <button class="button search-button" type="button" name="search" id="searchButton"
                    onclick="searchInv()">
                    <span class="material-icons">search</span>
                    Search
                </button>
                <button class="button create-payment-button" type="button" name="payment" id="paymentButton"
                    onclick="createPayment()">
                    <span class="material-icons">payment</span>
                    Create Payment
                </button>
            </div>
        </div>

        <input type="hidden" name="selectedRows" id="selectedRows" />
    </form>

    <div class="divider-line"></div>

    <div class="container">
        <label class="tag ">Invoice No By Search</label>
    </div>
    <div class="table-container">
        <table id="filterDataTable">
            <thead>
                <tr>
                    <th class="size-cbx"><input type="checkbox" class="checkbox-custom" id="selectAllFiltered"
                            onclick="toggleSelectAll(this, 'filtered')" /></th>
                    <th class="size-doctype">Doc. Type</th>
                    <th class="size-docno">Doc. No.</th>
                    <th class="size-docdate">Doc. Date</th>
                    <th class="size-duedate">Due Date</th>
                    <th class="size-docamount">Amount</th>
                    <th class="size-balamount">Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_array($filterData) && count($filterData) > 0): ?>
                    <?php foreach ($filterData as $item): ?>
                        <tr>
                            <td class="text-center size-cbx"><input type="checkbox"
                                    class="checkbox-custom row-checkbox filtered-checkbox"
                                    data-docno="<?= htmlspecialchars($item['BILL_DOC']) ?>"
                                    data-doctype="<?= htmlspecialchars($item['DOC_TYPE_DISPLAY']) ?>"
                                    data-docdate="<?= htmlspecialchars($item['DOC_DATE']) ?>"
                                    data-duedate="<?= htmlspecialchars($item['DUE_DATE']) ?>"
                                    data-amount="<?= htmlspecialchars($item['DOC_AMOUNT']) ?>" /></td>
                            <td class="text-center size-doctype"><?= htmlspecialchars($item['DOC_TYPE_DISPLAY']) ?></td>
                            <td class="text-center size-docno"><?= htmlspecialchars($item['BILL_DOC']) ?></td>
                            <td class="text-center size-docdate date-cell"
                                data-date="<?= htmlspecialchars($item['DOC_DATE']) ?>">
                                <?= htmlspecialchars($item['DOC_DATE']) ?>
                            </td>
                            <td class="text-center size-duedate date-cell"
                                data-date="<?= htmlspecialchars($item['DUE_DATE']) ?>">
                                <?= htmlspecialchars($item['DUE_DATE']) ?>
                            </td>
                            <td class="text-right size-docamount amount-cell"
                                data-amount="<?= htmlspecialchars($item['DOC_AMOUNT']) ?>">
                                <?= htmlspecialchars($item['DOC_AMOUNT']) ?>
                            </td>
                            <td class="text-right size-balamount amount-cell"
                                data-amount="<?= htmlspecialchars($item['BALANCE_AMOUNT']) ?>">
                                <?= htmlspecialchars($item['BALANCE_AMOUNT']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No results found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="container">
        <label class="tag ">All Invoice No By Customer Code</label>
    </div>
    <div class="table-container">
        <table id="allDataTable">
            <thead>
                <tr>
                    <th class="size-cbx"><input type="checkbox" class="checkbox-custom" id="selectAllAll"
                            onclick="toggleSelectAll(this, 'all')" /></th>
                    <th class="size-doctype">Doc. Type</th>
                    <th class="size-docno">Doc. No.</th>
                    <th class="size-docdate">Doc. Date</th>
                    <th class="size-duedate">Due Date</th>
                    <th class="size-docamount">Amount</th>
                    <th class="size-balamount">Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_array($allData) && count($allData) > 0): ?>
                    <?php foreach ($allData as $item): ?>
                        <tr>
                            <td class="text-center size-cbx"><input type="checkbox"
                                    class="checkbox-custom row-checkbox all-checkbox"
                                    data-docno="<?= htmlspecialchars($item['BILL_DOC']) ?>"
                                    data-doctype="<?= htmlspecialchars($item['DOC_TYPE_DISPLAY']) ?>"
                                    data-docdate="<?= htmlspecialchars($item['DOC_DATE']) ?>"
                                    data-duedate="<?= htmlspecialchars($item['DUE_DATE']) ?>"
                                    data-amount="<?= htmlspecialchars($item['DOC_AMOUNT']) ?>" /></td>
                            <td class="text-center size-doctype"><?= htmlspecialchars($item['DOC_TYPE_DISPLAY']) ?></td>
                            <td class="text-center size-docno"><?= htmlspecialchars($item['BILL_DOC']) ?></td>
                            <td class="text-center size-docdate date-cell"
                                data-date="<?= htmlspecialchars($item['DOC_DATE']) ?>">
                                <?= htmlspecialchars($item['DOC_DATE']) ?>
                            </td>
                            <td class="text-center size-duedate date-cell"
                                data-date="<?= htmlspecialchars($item['DUE_DATE']) ?>">
                                <?= htmlspecialchars($item['DUE_DATE']) ?>
                            </td>
                            <td class="text-right size-docamount amount-cell"
                                data-amount="<?= htmlspecialchars($item['DOC_AMOUNT']) ?>">
                                <?= htmlspecialchars($item['DOC_AMOUNT']) ?>
                            </td>
                            <td class="text-right size-balamount amount-cell"
                                data-amount="<?= htmlspecialchars($item['BALANCE_AMOUNT']) ?>">
                                <?= htmlspecialchars($item['BALANCE_AMOUNT']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No results found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>



    <div id="custom-amtZero" class="custom-amtZero hidden">
        <div id="custom-amtZero-header">Warning Message!</div>
        <span id="custom-amtZero-message"></span>
        <div id="custom-amtZero-bottom">
            <button id="custom-amtZero-button" onclick="closePopup('amtZero')">
                ✔ ตกลง
            </button>
        </div>
    </div>

    <div id="custom-warning" class="custom-warning hidden">
        <div id="custom-warning-header">Warning Message!</div>
        <span id="custom-warning-message"></span>
        <div id="custom-warning-bottom">
            <button id="custom-warning-button" onclick="closePopup('warning')">
                ✔ ตกลง
            </button>
        </div>
    </div>


    <div id="custom-success" class="custom-success hidden">
        <div id="custom-success-header">Success Message!</div>
        <span id="custom-success-message"></span>
        <div id="custom-success-bottom">
            <button id="custom-success-button" onclick="closePopup('success')">
                ✔ ตกลง
            </button>
        </div>
    </div>

    <div id="custom-fail" class="custom-fail hidden">
        <div id="custom-fail-header">Fail Message!</div>
        <span id="custom-fail-message"></span>
        <div id="custom-fail-bottom">
            <button id="custom-fail-button" onclick="closePopup('fail')">
                ✔ ตกลง
            </button>
        </div>
    </div>
    <div id="overlay" class="overlay"></div>

    <div class="loading-spinner" id="loading-spinner">
        <div class="spinner"></div>
    </div>

    <script>

        function searchInv() {
            const cuscode = document.getElementById("cuscode").value
            const invNo = document.getElementById("invNo").value

            if (cuscode && invNo) {
                loading('open');
                const formData = new FormData();
                formData.append('search', true);
                formData.append('cuscode', cuscode);
                formData.append('invNo', invNo);

                fetch('', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('data', data);
                        if (data.allData && data.allData.length > 0) {
                            const cusname = "Chaitanachote";
                            if (cusname) {
                              const customerLabel = document.getElementById("customerLabel");
                              customerLabel.style.display = "inline";
                              customerLabel.innerText = `Customer Name: ${cusname}`;
                              populateTable('allDataTable', data.allData);
                            }
                        } else {
                            customerLabel.style.display = "none";
                            populateTable('allDataTable', []);
                        }

                        if (data.filterData && data.filterData.length > 0) {
                            const cusname = "Chaitanachote";
                            if (cusname) {
                              const customerLabel = document.getElementById("customerLabel");
                              customerLabel.style.display = "inline";
                              customerLabel.innerText = `Customer Name: ${cusname}`;
                            }
                            populateTable('filterDataTable', data.filterData);
                        } else { 
                            customerLabel.style.display = "none";
                            populateTable('filterDataTable', [], 0);
                        }
                    loading('close');
                    })
                    .catch(error => {
                        loading('close');
                        console.error('Error fetching data:', error)
                    });
            } else {
                if (!cuscode && !invNo) {
                    showPopUp('noCusInv');
                } else if (!cuscode && invNo) {
                    showPopUp('noCus');
                } else if (cuscode && !invNo) {
                    showPopUp('noInv');
                }
            }
        };

        function createPayment() {
            const selectedRows = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(checkbox => ({
                docType: checkbox.getAttribute('data-doctype'),
                docNo: checkbox.getAttribute('data-docno'),
                docDate: checkbox.getAttribute('data-docdate'),
                dueDate: checkbox.getAttribute('data-duedate'),
                docAmount: parseFloat(checkbox.getAttribute('data-amount'))
            }));

            const countSelectedRows = selectedRows.length;

            if (countSelectedRows > 0) {

                const totalAmount = selectedRows.reduce((sum, row) => sum + row.docAmount, 0);
                console.log('Total Amount:', totalAmount);

                if (totalAmount > 0) {
                    loading('open');
                    const formData = new FormData();
                    formData.append('payment', true);
                    formData.append('cuscode', cuscode);
                    formData.append('selectedRows', JSON.stringify(selectedRows));

                    fetch('', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.text())
                        .then(url => {
                            console.log('Received URL:', url);  

                            if (url.startsWith("http")) {
                                window.location.href = url;
                            } else {
                                console.error('Invalid response or no URL returned:', url);
                                alert(url.includes("Error") ? url : 'Failed to process payment. Please try again.');
                            }
                            
                        loading('close');
                        })
                        .catch(error => {
                            loading('close');
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                        });
                } else {
                    showPopUp('amtZero');
                }
            } else {
                showPopUp('notSelect');
            }
        };

        function showPopUp(pop) {
            if (pop) {
                hiddenblackground('open')
                const popWarning = document.getElementById('custom-warning');
                const popWarningMessage = document.getElementById('custom-warning-message');
                switch (pop) {
                    case 'amtZero':
                        const popAmtZero = document.getElementById('custom-amtZero');
                        const popAmtZeroMessage = document.getElementById('custom-amtZero-message');
                        popAmtZeroMessage.textContent = 'ยอดชำระเงินที่คุณเลือก ได้มีการหักลบยอดแล้วขณะนี้ Grand Total (ยอดรวม) = 0 คุณต้องการดำเนินการต่อใช่หรือไม่';
                        popAmtZero.classList.add('show');
                        break;
                    case 'notSelect':
                        popWarningMessage.textContent = 'คุณยังไม่ได้เลือกรายการ กรุณาเลือกรายการก่อน ทำการ Payment';
                        popWarning.classList.add('show');
                        break;
                    case 'noCusInv':
                        popWarningMessage.textContent = 'กรุณากรอก Customer Code. และ Invoice No.';
                        popWarning.classList.add('show');
                        break;
                    case 'noCus':
                        popWarningMessage.textContent = 'กรุณากรอก Customer Code.';
                        popWarning.classList.add('show');
                        break;
                    case 'noInv':
                        popWarningMessage.textContent = 'กรุณากรอก Invoice No.';
                        popWarning.classList.add('show');
                        break;
                }
            }
        };


        function handleCallbackStatus() {
            const status = new URLSearchParams(window.location.search).get('status');
            if (status) {
                let message = '';
                switch (status) {
                    case 'cancel':
                        message = 'การทำรายการถูกยกเลิก';
                        break;
                    case 'success':
                        message = 'ขอบคุณที่ใช้บริการ';
                        break;
                    case 'fail':
                        message = 'ทำรายการไม่สำเร็จ';
                        break;
                    default:
                        message = 'Unknown status. Please contact support if you have any questions.';
                        break;
                }

                if (message, status) {
                    showCustomAlert(message, status);
                    history.replaceState(null, '', window.location.pathname);
                }
            }
        };

        function showCustomAlert(message, status) {
            if (status) {
                hiddenblackground('open')
                switch (status) {
                    case 'cancel':
                        const popWarning = document.getElementById('custom-warning');
                        const popWarningMessage = document.getElementById('custom-warning-message');
                        popWarningMessage.textContent = message;
                        popWarning.classList.add('show');
                        break;
                    case 'success':
                        const popSuccess = document.getElementById('custom-success');
                        const popSuccessMessage = document.getElementById('custom-success-message');
                        popSuccessMessage.textContent = message;
                        popSuccess.classList.add('show');
                        break;
                    case 'fail':
                        const popFail = document.getElementById('custom-fail');
                        const popFailMessage = document.getElementById('custom-fail-message');
                        popFailMessage.textContent = message;
                        popFail.classList.add('show');
                        break;
                }
            }

        };

        function closePopup(checkStatus) {
            if (checkStatus) {
                hiddenblackground('close')
                switch (checkStatus) {
                    case 'warning':
                        const popWarning = document.getElementById('custom-warning');
                        popWarning.classList.remove('show');
                        break;
                    case 'success':
                        const popSuccess = document.getElementById('custom-success');
                        popSuccess.classList.remove('show');
                        break;
                    case 'fail':
                        const popFail = document.getElementById('custom-fail');
                        popFail.classList.remove('show');
                        break;
                }
            }
        };

        window.addEventListener('load', function () {
            formatTableData();
            handleCallbackStatus();
        });

        window.addEventListener('popstate', function () {
            formatTableData();
            handleCallbackStatus();
        });

        function toggleSelectAll(source, table) {
            const checkboxClass = table === 'all' ? 'all-checkbox' : 'filtered-checkbox';
            const checkboxes = document.querySelectorAll(`.${checkboxClass}`);
            checkboxes.forEach((checkbox) => {
                checkbox.checked = source.checked;
            });
        }


        function formatDate(dateStr) {
            if (!dateStr || dateStr.length !== 8) return dateStr;
            return `${dateStr.slice(6, 8)}/${dateStr.slice(4, 6)}/${dateStr.slice(0, 4)}`;
        };

        function formatNumber(number) {
            return number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }


        function formatTableData() {
            document.querySelectorAll('.date-cell').forEach(cell => {
                const originalDate = cell.textContent.trim();
                if (originalDate) {
                    const formattedDate = formatDate(originalDate);
                    cell.textContent = formattedDate;
                }
            });
            
            document.querySelectorAll('.amount-cell').forEach(cell => {
                const originalAmount = parseFloat(cell.textContent.trim().replace(/,/g, '')) || 0;
                const formattedAmount = formatNumber(originalAmount);
                cell.textContent = formattedAmount;
            });

        }

        function populateTable(tableId, data) {
            const tableBody = document.getElementById(tableId).getElementsByTagName('tbody')[0];
            tableBody.innerHTML = '';
            const checkboxClass = tableId === 'filterDataTable' ? 'filtered-checkbox' : 'all-checkbox';
            if (data.length === 0) {
                const noDataRow = `<tr><td colspan="7" class="text-center">No results found.</td></tr>`;
                tableBody.insertAdjacentHTML('beforeend', noDataRow);
                return;
            }
        
            data.forEach(item => {
                const row = `
                    <tr>
                        <td class="text-center size-cbx"><input type="checkbox" class="checkbox-custom row-checkbox ${checkboxClass}" 
                            data-docno="${item.BILL_DOC}" data-doctype="${item.DOC_TYPE_DISPLAY}" 
                            data-docdate="${item.DOC_DATE}" data-duedate="${item.DUE_DATE}" 
                            data-amount="${item.DOC_AMOUNT}" /></td>
                        <td class="text-center size-doctype">${item.DOC_TYPE_DISPLAY}</td>
                        <td class="text-center size-docno">${item.BILL_DOC}</td>
                        <td class="text-center size-docdate date-cell">${item.DOC_DATE}</td>
                        <td class="text-center size-duedate date-cell">${item.DUE_DATE}</td>
                        <td class="text-right size-docamount amount-cell">${item.DOC_AMOUNT}</td>
                        <td class="text-right size-balamount amount-cell">${item.BALANCE_AMOUNT}</td>
                    </tr>`;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        
            formatTableData();
        };

        function hiddenblackground(type) {
            switch (type) {
                case 'open':
                    document.getElementById('overlay').style.display = 'block';
                    break;
                case 'close':
                    document.getElementById('overlay').style.display = 'none';
                    break;
            }
        };
        

        function loading(prm) {
            const spinner = document.getElementById('loading-spinner');
            switch (prm) {
                case 'open':
                    spinner.classList.add('show');
                    break;
                case 'close':
                    spinner.classList.remove('show');
                    break;
            }
        };

    </script>
</body>

</html>