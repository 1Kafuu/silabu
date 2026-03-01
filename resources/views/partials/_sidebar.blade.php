<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('images/faces/face1.jpg') }}" alt="profile" />
          <span class="login-status online"></span>
          <!--change to offline or busy as needed-->
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{ auth()->user()?->name }}</span>
          <span class="text-secondary text-small">Project Manager</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('/dashboard*') ? 'active' : '' }}" href="{{route('dashboard')}}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('/user*') ? 'active' : '' }}" href="{{ route('user') }}">
        <span class="menu-title">User Management</span>
        <i class="mdi mdi-account-settings menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('/book*') ? 'active' : '' }}" href="{{ route('book-list') }}">
        <span class="menu-title">Book</span>
        <i class="mdi mdi-book-open-page-variant menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('/category*') ? 'active' : '' }}" href="{{ route('category-list') }}">
        <span class="menu-title">Category</span>
        <i class="mdi mdi-book menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('/items*') ? 'active' : '' }}" href="{{ route('items-list') }}">
        <span class="menu-title">Items</span>
        <i class="mdi mdi-package menu-icon"></i>
      </a>
    </li>
  </ul>
</nav>