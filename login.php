<?php
session_start();
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Panel | Autographix Poster Shop</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
   <!-- Toastr -->
   <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    


    <!-- Main content -->
    <div class="content">
      <div class="container">
        <div class="row">
          <div class="col-lg-6 mx-auto pt-5">
            <div class="mt-5 text-center">
              <img src="assets/images/logo.png" alt="Logo" class="brand-image" style="max-width:170px;" style="opacity: .8"><br>
              <span class="brand-text font-weight-light">USER LOGIN</span>
            </div>
            <div class="card card-primary card-outline mt-2">
                <div class="card-header">
                  <h5 class="card-title m-0">Sign-in to start your session.</h5>
                </div>
                <form class="form-horizontal" method="POST" action="processes.php">
                    <div class="card-body">
                      <div class="form-group row">
                        <label for="inputEmail3" class="col-xl-2 col-form-label">Email</label>
                        <div class="col-xl-10">
                          <input type="email" name="email" class="form-control" id="inputEmail3" autocomplete="email" placeholder="Email" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputPassword3" class="col-xl-2 col-form-label">Password</label>
                        <div class="col-xl-10">
                          <input type="password" name="password" class="form-control" autocomplete="current-password" id="inputPassword3" placeholder="Password"required>
                        </div>
                      </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer bg-white">
                      <button type="submit" name="login" class="btn btn-primary float-right">Login</button>
                    </div>
                    <!-- /.card-footer -->
                  </form>
              </div>

          </div>
          <!-- /.col-md-6 -->


          
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

 

</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- Toastr -->
<script src="plugins/toastr/toastr.min.js"></script>

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
</body>
</html>
