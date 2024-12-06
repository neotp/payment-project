<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <title>Dynamic Loader Example</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .loading-spinner {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        display: none;  /* Initially hidden */
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid rgba(0, 0, 0, 0.1);
        border-top-color: #333;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    </style>
</head>
<body>

<!-- Spinner HTML -->
<button onclick="closePopup('open')">เปิด</button>
 <button onclick="closePopup('close')">ปิด</button>
<div class="loading-spinner" id="loading-spinner">
    <div class="spinner"></div>
</div>


<script>
    function closePopup(prm) {
            switch (prm) {
                case 'open':
                    document.getElementById('loading-spinner').style.display = 'flex';
                    break;
                case 'close':
                    document.getElementById('loading-spinner').style.display = 'none';
                    break;
            }
        };

</script>

</body>
</html>
