<link rel="stylesheet" href="{{ asset('/css/sidebar.css') }}">

<div class="sidebar" data-color="danger" data-background-color="grey" {{-- data-image="../assets/img/sidebar-1.jpg"--}}>
  <div class="sidebar-wrapper d-flex flex-column">
    <img src="{{ asset('assets/img/LOGOICOT.png') }}" style="margin:10px;">

    <ul class="nav">
      <li id="userInfo" class="nav-item ">
        <a class="nav-link collapsed" data-toggle="collapse" href="#profileEmployee" aria-expanded="false">
          <i class="material-icons">person</i>
          <p style="font-weight: bold"> {{ session()->get('user')->username }}
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="profileEmployee">
          <ul class="nav">
            <li id="menuProfile" class="nav-item ">
              <a class="nav-link" href="{{route('profile')}}">
                <i class="material-icons">account_box</i>
                <span class="sidebar-normal"> Mis datos </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{route('logout')}}" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                <i class="material-icons"> exit_to_app</i>
                <span class="sidebar-normal"> Cerrar sesión </span>
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </li>
          </ul>
        </div>
      </li>

      <hr>

      <li class="nav-item active" id="menuHome">
        <a class="nav-link"href="{{route('home')}}">
          <i class="material-icons">home</i>
          <p style="font-weight: bold"> Home </p>
        </a>
      </li>

      <li id="menuConfig" class="nav-item ">
        <a class="nav-link collapsed" data-toggle="collapse" href="#pagesConfig" aria-expanded="false">
          <i class="material-icons">settings</i>
          <p style="font-weight: bold"> Configuración
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="pagesConfig" style="">
          <ul class="nav">
            <li id="adminRole" class="nav-item ">
              <a class="nav-link" href="{{route('roles.index')}}">
                <i class="material-icons">admin_panel_settings</i>
                <span class="sidebar-normal"> Roles </span>
              </a>
            </li>
            <li id="adminUser" class="nav-item ">
              <a class="nav-link" href="{{route('employees.index')}}">
                <i class="material-icons">supervisor_account</i>
                <span class="sidebar-normal"> Empleados </span>
              </a>
            </li>
           
           
            <li id="adminCentre" class="nav-item ">
              <a class="nav-link" href="{{route('centres.index')}}">
                <i class="material-icons">business</i>
                <span class="sidebar-normal"> Centros </span>
              </a>
            </li>
            <li id="adminService" class="nav-item ">
              <a class="nav-link" href="{{route('services.index')}}">
                <i class="material-icons">hotel</i>
                <span class="sidebar-normal"> Servicios </span>
              </a>
            </li>
            <li id="adminServiceIncentive" class="nav-item ">
              <a class="nav-link" href="{{route('services.incentives')}}">
                <i class="material-icons">money</i>
                <span class="sidebar-normal"> Precios & Incentivos </span>
              </a>
            </li>
        </div>
      </li>

      <li class="nav-item ">
        <a class="nav-link collapsed" data-toggle="collapse" href="#pagesTracking" aria-expanded="false">
          <i class="material-icons">remove_red_eye</i>
          <p style="font-weight: bold"> Seguimiento
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="pagesTracking" style="">
          <ul class="nav">
            <li id="trackingStarted" class="nav-item ">
              <a class="nav-link" href="{{route('tracking.index')}}">
                <i class="material-icons">playlist_add_check</i>
                <span class="sidebar-normal"> Recomendaciones </span>
              </a>
            </li>
            <li id="requestChange" class="nav-item ">
              <a class="nav-link" href="{{route('tracking.requestChange')}}">
                <i class="material-icons">transform</i>
                <span class="sidebar-normal"> Solicitudes de cambio </span>
              </a>
            </li>
            <li id="trackingValidateFinal" class="nav-item ">
              <a class="nav-link" href="{{route('tracking.index_validation_final')}}">
                <i class="material-icons">check</i>
                <span class="sidebar-normal"> Validar RRHH </span>
              </a>
            </li>
            <li id="trackingRemove" class="nav-item ">
              <a class="nav-link" href="{{route('tracking.deleteForm')}}">
                <i class="material-icons">delete</i>
                <span class="sidebar-normal"> Borrar seguimiento </span>
              </a>
            </li>
          </ul>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-toggle="collapse" href="#pagesReport" aria-expanded="false">
          <i class="material-icons">insert_drive_file</i>
          <p style="font-weight: bold"> Informes
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="pagesReport">
          <ul class="nav">

            <li id="calculateIncentive" class="nav-item">
              <a class="nav-link" href="{{route('calculateIncentive')}}">
                <i class="material-icons">calculate</i>
                <span class="sidebar-normal"> Calculadora de Incentivos </span>
              </a>
            </li>

            <li id="calculateService" class="nav-item">
              <a class="nav-link" href="{{route('calculateServices')}}">
                <i class="material-icons">track_changes</i>
                <span class="sidebar-normal"> Dinámica de Servicios </span>
              </a>
            </li>

            <li id="centerLeague" class="nav-item">
              <a class="nav-link" href="{{route('centerLeague')}}">
                <i class="material-icons">
                emoji_events
                </i>
                <span class="sidebar-normal"> Liga de Centros </span>
              </a>
            </li>

          </ul>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-toggle="collapse" href="#pagesNotification" aria-expanded="false">
          <i class="material-icons">notifications</i>
          <p style="font-weight: bold"> Notificaciones
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="pagesNotification">
          <ul class="nav">
            <li id="supervisorNotificationsIndex" class="nav-item">
              <a class="nav-link" href="{{route('notifications.index')}}">
                <i class="material-icons">mail</i>
                <span class="sidebar-normal"> Avisos de supervisor</span>
              </a>
            </li>
          </ul>
        </div>
      </li>

    </ul>
    
    
    <div class="versionContainer">
      <img src="{{ asset('/assets/img/logoIncentivos.png') }}" style="margin:10px; margin-top:40px;">
      <hr>
      <label class="lblVersion"> Versión {{ env('VERSION_WEB') }} </label>
    </div>
  </div>
  <div class="sidebar-background"></div>
</div>

