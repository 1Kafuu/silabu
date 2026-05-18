<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('images/faces/face1.jpg') }}" alt="profile" />
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{ auth()->user()?->name }}</span>
          <span class="text-secondary text-small">{{session('user.role_name')}}</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('dashboard')}}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('user') }}">
        <span class="menu-title">User Management</span>
        <i class="mdi mdi-account-settings menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('role') }}">
        <span class="menu-title">Role Management</span>
        <i class="mdi mdi-shield-edit menu-icon"></i>
      </a>
    </li>
    @php
      $poliActive = request()->is('poli*');
    @endphp
    <li class="nav-item {{ $poliActive ? 'active' : '' }}">
      <a class="nav-link {{ $poliActive ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#poli-menu" aria-expanded="{{ $poliActive ? 'true' : 'false' }}" aria-controls="poli-menu">
        <span class="menu-title">Poli Management</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-hospital-building menu-icon"></i>
      </a>
      <div class="collapse {{ $poliActive ? 'show' : '' }}" id="poli-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('poli') ? 'active' : '' }}" href="{{ route('poli') }}">
              <span class="menu-title">Daftar Poli</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('poli.assign-loket') ? 'active' : '' }}" href="{{ route('poli.assign-loket') }}">
              <span class="menu-title">Assign Admin Loket</span>
            </a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('book-list') }}">
        <span class="menu-title">Book</span>
        <i class="mdi mdi-book-open-page-variant menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('category-list') }}">
        <span class="menu-title">Category</span>
        <i class="mdi mdi-book menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('items-list') }}">
        <span class="menu-title">Items</span>
        <i class="mdi mdi-package menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('vendor-list') }}">
        <span class="menu-title">Vendor</span>
        <i class="mdi mdi-truck menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('toko-list') }}">
        <span class="menu-title">Toko</span>
        <i class="mdi mdi-store menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('manage-customerBlob') }}">
        <span class="menu-title">Customer (Blob)</span>
        <i class="mdi mdi-account-group menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('manage-customerPath') }}">
        <span class="menu-title">Customer (Path)</span>
        <i class="mdi mdi-account-group menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('shipment') }}">
        <span class="menu-title">Shipment</span>
        <i class="mdi mdi-cart menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('kota') }}">
        <span class="menu-title">Kota</span>
        <i class="mdi mdi-city menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('wilayah-ajax') }}">
        <span class="menu-title">Wilayah (Ajax)</span>
        <i class="mdi mdi-city-variant menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('wilayah-axios') }}">
        <span class="menu-title">Wilayah (Axios)</span>
        <i class="mdi mdi-city-variant menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('pos-ajax') }}">
        <span class="menu-title">Point Of Sales (Ajax)</span>
        <i class="mdi mdi-cart-arrow-down menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('pos-axios') }}">
        <span class="menu-title">Point Of Sales (Axios)</span>
        <i class="mdi mdi-cart-arrow-down menu-icon"></i>
      </a>
    </li>
  </ul>
</nav>