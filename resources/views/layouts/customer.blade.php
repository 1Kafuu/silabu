<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
   <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>@yield('title') | Purple Admin</title>
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
  <link rel="stylesheet" href="{{ asset('css/preloader.css') }}">
  <!-- End layout styles -->
  <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
  <!-- Style page -->
  @stack('style-page')
</head>

<body>
  @include('partials._preloader')
  <div class="container-scroller">
    <!-- partial:../../partials/_navbar.html -->
    @include('layouts.customer_partials._navbar')
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <div class="content-wrapper">
          @include('partials._breadcrumb')
          @yield('content')
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        <!-- partial -->
      <!-- main-panel ends -->
    </div>
    @include('partials._footer')
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="{{ asset('vendors/js/vendor.bundle.base.js') }}"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{ asset('js/jquery-4.0.0.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
  <script src="{{ asset('js/preloader.js') }}"></script>
  <script src="{{ asset('js/jquery.cookie.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- endinject -->
  <!-- Custom js for this page -->
  @stack('js-page')
  <!-- End custom js for this page -->
</body>

</html>