<?php
if (isset($_SESSION['success'])) {
?>
<script>
    toastr.success("<?php echo $_SESSION['success']; ?>", "Success");
</script>
<?php
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
?>
<script>
    toastr.error("<?php echo $_SESSION['error']; ?>", "Error");
</script>
<?php
    unset($_SESSION['error']);
}
?>