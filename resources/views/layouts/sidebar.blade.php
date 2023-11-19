<div class="sidebar d-flex flex-column flex-shrink-0 p-3 text-white bg-dark">
  <a href="{{ url('/') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
    <span class="fs-4">Cyrange</span>
  </a>
  <hr>
  <ul class="nav nav-pills flex-column mb-auto" id="nav-main">    
    <li class='nav-item'>
        <a class="nav-link text-white" href='{{ action('VMController@index') }}'><i class="fas fa-laptop me-2"></i> My machines</a>
    </li>

    @if (Auth::check() && Auth::user()->isAdmin())
    <hr>
    <li class='nav-item'>
        <a class="nav-link text-white" href='{{ action('VMController@all') }}'><i class="fas fa-laptop me-2"></i> All machines</a>
    </li>
    <li class='nav-item'>
        <a class="nav-link text-white" href='{{ action('NetworkController@index') }}'><i class="fas fa-network-wired me-2"></i> Networks</a>
    </li>
    <li class='nav-item'>
        <a class="nav-link text-white" href='{{ action('TemplateController@index') }}'><i class="fas fa-edit me-2"></i> Templates</a>
    </li>
    <li class='nav-item'>
        <a class="nav-link text-white" href='{{ action('ImageController@index') }}'><i class="fas fa-list me-2"></i> Images</a>
    </li>
    <li class='nav-item'>
        <a class="nav-link text-white" href='{{ action('ScenarioController@index') }}'><i class="fas fa-tasks me-2"></i>Scenarios</a>
    </li>
    <li class='nav-item'>
        <a class="nav-link text-white" href='{{ action('JobController@index') }}'><i class="fas fa-cogs me-2"></i> Jobs</a>
    </li>
    <li class='nav-item'>
        <a class="nav-link text-white" href='{{ route('status') }}'><i class="fas fa-chart-line me-2"></i> Status</a>
    </li>
    <li class='nav-item'>
        <a class="nav-link text-white" href='{{ action('AccountController@index') }}'><i class="fas fa-desktop me-2"></i> Guacamole</a>
    </li>
    <li class='nav-item'>
        <a class="nav-link text-white" href='{{ action('VBoxVMController@index') }}'><i class="fas fa-server me-2"></i>VirtualBox</a>
    </li>
    <li class='nav-item'>
        <a class="nav-link text-white" href='{{ action('SettingController@edit') }}'><i class="fas fa-tools me-2"></i> Settings</a>
    </li>
    <li class='nav-item'>
        <a class="nav-link text-white" href="{{ action('UserController@index') }}"><i class="fas fa-users me-2"></i> Users</a>
    </li>
    @endif
  </ul>
  
  
  <ul class='nav nav-pills flex-column mb-0'>
  <hr>
    @guest
    <li class="nav-item">
        <a class="nav-link text-white" href="{{ route('login') }}">{{ __('Login') }}</a>
    </li>
    
    @else
    <li class="nav-item">
        <a class="nav-link text-white" href="{{ action('ProfileController@edit') }}">
            <i class="fas fa-cog me-2"></i> Profile
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link text-white" href="{{ route('logout') }}"
           onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
        </a>
    </li>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endguest

  </ul>
</div>

<div class="b-example-divider"></div>
