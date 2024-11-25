<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            z-index: 1050;
            width: 400px;
            max-height: 20vh; /* Mengatur batas maksimal tinggi modal */
            overflow-y: auto; /* Menambahkan scroll jika konten lebih panjang */
        }

.modal-backdrop {
    z-index: 1040;
}


        .modal h2 {
            font-size: 24px;
            margin-bottom: 20px;
            word-wrap: break-word; /* Membuat teks tidak keluar dari area */
        }

        .modal p {
            font-size: 14px;
            margin-bottom: 20px;
            word-wrap: break-word;
        }

        .modal button {
            margin: 5px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .modal button:hover {
            opacity: 0.9;
        }

        .modal .confirm {
            background-color: #007bff;
            color: white;
        }

        .modal .cancel {
            background-color: #365486;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Modal untuk konfirmasi logout -->
    <div class="modal" id="logoutModal">
        <h2>Logout Confirmation</h2>
        <p>Are you sure you want to logout?</p>
        <button class="confirm" onclick="confirmLogout()">Logout</button>
        <button class="cancel" onclick="cancelLogout()">Cancel</button>
    </div>
    


    <script>
    // Get the modal and logout link
    const modal = document.getElementById('logoutModal');
    const logoutLink = document.getElementById('logout-link');

    // Show modal on logout link click
    logoutLink.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent default link behavior
        modal.style.display = 'block'; // Show modal
    });

    // Confirm logout
    function confirmLogout() {
        window.location.href = 'logout.php'; // Redirect to logout script
    }

    // Cancel logout
    function hideLogoutModal() {
        modal.style.display = 'none'; // Hide modal
    }
</script>
</body>
</html>
