<style>
    /* File: toast.css */

.toast-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.toast {
    padding: 10px 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    font-size: 14px;
    color: #ffffff;
    display: none;
}
.toast-red { background-color: #f8d7da; border: 1px solid #f5c6cb; }
.toast-yellow { background-color: #fff3cd; border: 1px solid #ffeeba; }
.toast-blue { background-color: #d1ecf1; border: 1px solid #bee5eb; }
.toast-green { background-color: #d4edda; border: 1px solid #c3e6cb; }
.toast-custom { background-color: #365486; border: 1px solid #2c3e50; }
.show { display: block; }

</style>
]<?php
// Cek jika ada pesan yang diteruskan
$type = isset($_GET['type']) ? $_GET['type'] : 'custom';
$message = isset($_GET['message']) ? $_GET['message'] : 'Default message';

?>

<div class="toast-container">
    <div id="toast-<?php echo $type; ?>" class="toast toast-<?php echo $type; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
</div>

<script>
    // Menampilkan toast dengan jenis yang diterima
    document.addEventListener("DOMContentLoaded", function() {
        const toast = document.getElementById('toast-<?php echo $type; ?>');
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000x);
    });
</script>
    