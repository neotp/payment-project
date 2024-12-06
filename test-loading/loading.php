<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <title>Dynamic Loader Example</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Spinner CSS */
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
        }
        
        .spinner {
            display: none; /* Hidden by default */
            position: fixed; /* Fixed position on the screen */
            top: 50%; /* Vertical center */
            left: 50%; /* Horizontal center */
            width: 50px;
            height: 50px;
            border: 5px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top-color: #007bff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
<button id="custom-amtZero-button" onclick="closePopup('amtZero')">
                ✔ ตกลง
            </button>
<!-- Spinner HTML -->
<div class="loading-spinner" id="loading-spinner">
    <div class="spinner"></div>
</div>


<script>
    function startProcess() {
        // Show the loader spinner when process starts
        document.getElementById('loading-spinner').style.display = 'flex';

        // Example AJAX request (this could be any server-side process)
        $.ajax({
            url: 'process.php',  // URL to your server-side script
            method: 'POST',
            data: { action: 'process' },  // Data for the process
            success: function(response) {
                // Hide the spinner when the process is complete
                document.getElementById('loading-spinner').style.display = 'none';
                alert('Process completed!');
            },
            error: function() {
                // Hide the spinner if there's an error
                document.getElementById('loading-spinner').style.display = 'none';
                alert('An error occurred.');
            }
        });
    }
</script>

</body>
</html>
