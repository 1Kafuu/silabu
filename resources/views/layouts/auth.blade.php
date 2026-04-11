<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
     <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') | Purple Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/font-awesome/css/font-awesome.min.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
    @stack('style-page')
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="row flex-grow">
            <div class="col-lg-4 mx-auto">
              <!-- Template -->
              @yield('content')
              <!-- Template End -->
              
              <!-- Notifications -->
              @if (session('status') && session('message'))
                <script>
                  const status = "{{ session('status') }}";
                  const message = "{{ session('message') }}";
                  
                  if (status === 'danger') {
                    Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: message,
                      confirmButtonText: 'OK'
                    });
                  } else if (status === 'success') {
                    Swal.fire({
                      icon: 'success',
                      title: 'Berhasil',
                      text: message,
                      confirmButtonText: 'OK'
                    });
                  } else if (status === 'warning') {
                    Swal.fire({
                      icon: 'warning',
                      title: 'Perhatian',
                      text: message,
                      confirmButtonText: 'OK'
                    });
                  }
                </script>
              @endif
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset ('js/jquery.cookie.js') }}"></script>
    @stack('js-page')
    <!-- endinject -->
  </body>
</html>