<?php
  $segmentOne = Request::segment(2);
  $permission_lists = Helper::get_permission_list();
  // dd($permission_lists);
?>

<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="{{ asset('dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="{{ route('user.edit',[Auth::id()]) }}" class="d-block">{{ Auth::user()->name }}</a>
          <span style="color:white; font-size: 14px;" class="text-uppercase"> {{ Helper::show_role_name() }} </span>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->

          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link @if($segmentOne =='dashboard') active @endif">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                {{ __('Dashboard') }}
              </p>
            </a>
          </li>


          @if( Helper::show_role_name() == 'superadmin' )
              
              <li class="nav-item">
                  <a href="{{ route('user.index') }}" class="nav-link @if($segmentOne =='user') active @endif">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                      {{ __('User') }}
                    </p>
                  </a>
                </li>


              <li class="nav-item">
                <a href="{{ route('permission.index') }}" class="nav-link @if($segmentOne =='permission') active @endif">
                  <i class="nav-icon fas fa-users"></i>
                  <p>
                    {{ __('Permission') }}
                  </p>
                </a>
              </li>
              <li class="nav-item has-treeview @if($segmentOne =='setting') menu-open @endif ">
                <a href="#" class="nav-link @if($segmentOne =='setting') menu-open @endif">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>{{ __('Setting') }}</p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Website setting</p>
                    </a>
                  </li>
                </ul>
              </li>

          @else
              

              @if(Helper::is_user_access_nav('role'))
                <li class="nav-item">
                  <a href="{{ route('role.index') }}" class="nav-link @if($segmentOne =='role') active @endif">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                      {{ __('Roles') }}
                    </p>
                  </a>
                </li>
              @endif
              
              @if(Helper::is_user_access_nav('user'))
                <li class="nav-item">
                  <a href="{{ route('user.index') }}" class="nav-link @if($segmentOne =='user') active @endif">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                      {{ __('User') }}
                    </p>
                  </a>
                </li>
              @endif

              @if(Helper::is_user_access_nav('setting'))
                <li class="nav-item has-treeview @if($segmentOne =='setting') menu-open @endif ">
                  <a href="#" class="nav-link @if($segmentOne =='setting') menu-open @endif">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>{{ __('Setting') }}</p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Website setting</p>
                      </a>
                    </li>
                  </ul>
                </li>
              @endif

          
          @endif
          
          <li class="nav-item">
            <a href="javascript:void(0);" onclick="document.getElementById('logout-form-submit').submit();" class="nav-link">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>
                {{ __('Log out') }}
              </p>
            </a>
          </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

<form method="POST" id="logout-form-submit" action="{{ route('logout') }}" style="display: none;">
    @csrf
    <x-dropdown-link :href="route('logout')"
            onclick="event.preventDefault();
                        this.closest('form').submit();">
        {{ __('Log out') }}
    </x-dropdown-link>
</form>