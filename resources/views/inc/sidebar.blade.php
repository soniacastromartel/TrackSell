<div class="sidebar" data-color="danger" data-background-color="grey" {{-- data-image="../assets/img/sidebar-1.jpg"--}}>

  <!--
      Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

      Tip 2: you can also add an image using data-image tag
  -->
  <div class="logo text-center" style="padding: 0px !important">
    <img src="{{ asset('assets/img/background_pdi.png') }}" width="260px" height="120px">
  </div>
  <div class="sidebar-wrapper">
    <div class="user">
      <div class="photo">
        <img src="{{ asset('assets/img/avatar.png') }}">
      </div>
      <div class="user-info">
        <a data-toggle="collapse" href="#profileEmployee" class="username collapsed" aria-expanded="false">
          <span id="userData">
            {{ session()->get('user')->username }}
            <b class="caret"></b>
          </span>
        </a>
        <div class="collapse" id="profileEmployee" style="">
          {{-- <ul class="nav navbar-nav nav-mobile-menu"> --}}
          <ul class="nav">
            <li class="nav-item" id="menuProfile">
              <a class="nav-link" href="{{route('profile')}}">
                <i class="material-icons">account_box</i>
                <span class="sidebar-normal"> Mis datos </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{route('logout')}}" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                <i class="material-icons" style="color:#CC0000"> exit_to_app</i>
                <span class="sidebar-normal" style="color:#CC0000"> Cerrar sesión </span>
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <ul class="nav">
      <li class="nav-item active" id="menuHome">
        <a class="nav-link" href="/home">
          <i class="material-icons">home</i>
          <p style="font-weight: bold"> Home </p>
        </a>
      </li>

      <li id="menuConfig" class="nav-item ">
        <a class="nav-link collapsed" data-toggle="collapse" href="#pagesConfig" aria-expanded="false">
          <i class="material-icons">settings</i>
          <p style="font-weight: bold"> Configuracion
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
            <li id="pending_employee" class="nav-item ">
              <a class="nav-link" href="{{route('employees.indexPending')}}">
                <i class="material-icons">block</i>
                <span class="sidebar-normal"> Pendientes de Validar </span>
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
                <span class="sidebar-normal"> Incentivos - Servicios </span>
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
                <i class="material-icons">play_arrow</i>
                <span class="sidebar-normal"> Listados </span>
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
        <div class="collapse" id="pagesReport" style="">
          <ul class="nav">
            <li id="exportRecommendation" class="nav-item">
              <a class="nav-link" href="{{route('tracking.exportForm')}}">
                <i class="material-icons">import_export</i>
                <span class="sidebar-normal"> Exportar recomendaciones </span>
              </a>
            </li>
            <li id="calculateIncentive" class="nav-item">
              <a class="nav-link" href="{{route('calculateIncentive')}}">
                <i class="material-icons">calculate</i>
                <span class="sidebar-normal"> Calculadora de incentivos </span>
              </a>
            </li>
            <li id="calculateRanking" class="nav-item">
              <a class="nav-link" href="{{route('calculateRanking')}}">
                <i class="material-icons">list</i>
                <span class="sidebar-normal"> Rankings </span>
              </a>
            </li>
            <li id="centerLeague" class="nav-item">
              <a class="nav-link" href="{{route('centerLeague')}}">
                <i class="material-icons">dvr</i>
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
        <div class="collapse" id="pagesNotification" style="">
          <ul class="nav">
            <li id="supervisorNotificationsIndex" class="nav-item">
              <a class="nav-link" href="{{route('notifications.index')}}">
                <i class="material-icons">import_export</i>
                <span class="sidebar-normal"> Avisos de supervisor</span>
              </a>
            </li>
          </ul>
        </div>
      </li>
    </ul>
    <div>
      <hr>
      <label class="lblVersion"> Versión {{ env('VERSION_WEB') }} </label>
    </div>
  </div>
  <div class="sidebar-background"></div>
</div>

<style>
  #userData {
    font-weight: 900;
  }

  hr {
    margin-left: 16px;
    margin-right: 16px;
  }

  .lblVersion {
    position: absolute;
    bottom: 50px;
    width: 100%;
    color: #CC0000;
    text-align: center !important;
    font-weight: 900;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    vertical-align: bottom;
  }
</style>