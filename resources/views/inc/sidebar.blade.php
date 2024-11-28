<div class="sidebar" data-color="danger" data-background-color="grey" {{-- data-image="../assets/img/sidebar-1.jpg" --}}>
    <div class="sidebar-wrapper d-flex flex-column">
        <img src="{{ asset('assets/img/logoIcot2.png') }}" style="margin:10px;">

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
                            <a class="nav-link" href="{{ route('profile') }}">
                                <i class="material-icons">account_box</i>
                                <span class="sidebar-normal"> Mis datos </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
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
                <a class="nav-link"href="{{ route('home') }}">
                    <i class="material-icons">home</i>
                    <p style="font-weight: bold"> Home </p>
                </a>
            </li>

            @if (session()->get('user')->rol_id == 1)
                <li id="menuConfig" class="nav-item ">

                    <a class="nav-link collapsed" data-toggle="collapse" href="#pagesConfig" aria-expanded="false">
                        <i class="material-icons">admin_panel_settings</i>
                        <p style="font-weight: bold"> Administración
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="collapse" id="pagesConfig" style="">
                        <ul class="nav">
                            <li id="adminRole" class="nav-item ">
                                <a class="nav-link" href="{{ route('roles.index') }}">
                                    <i class="material-icons">gpp_good</i>
                                    <span class="sidebar-normal"> Roles </span>
                                </a>
                            </li>
                            <li id="adminUser" class="nav-item ">
                                <a class="nav-link" href="{{ route('employees.index') }}">
                                    <i class="material-icons">engineering</i>
                                    <span class="sidebar-normal"> Empleados </span>
                                </a>
                            </li>


                            <li id="adminCentre" class="nav-item ">
                                <a class="nav-link" href="{{ route('centres.index') }}">
                                    <i class="material-icons">business</i>
                                    <span class="sidebar-normal"> Centros </span>
                                </a>
                            </li>
                            <li id="adminService" class="nav-item ">
                                <a class="nav-link" href="{{ route('services.index') }}">
                                    <i class="material-icons">medical_services</i>
                                    <span class="sidebar-normal"> Servicios </span>
                                </a>
                            </li>
                            <li id="adminServiceIncentive" class="nav-item ">
                                <a class="nav-link" href="{{ route('services.incentives') }}">
                                    <i class="material-icons">local_atm</i>
                                    <span class="sidebar-normal"> Precios & Incentivos </span>
                                </a>
                            </li>
                    </div>
                </li>
            @endif


            <li class="nav-item ">
                <a class="nav-link collapsed" data-toggle="collapse" href="#pagesTracking" aria-expanded="false">
                    <i class="material-icons">manage_accounts</i>
                    <p style="font-weight: bold"> Supervisión
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse" id="pagesTracking" style="">
                    <ul class="nav">
                        <li id="trackingStarted" class="nav-item ">
                            <a class="nav-link" href="{{ route('tracking.index') }}">
                                <i class="material-icons">shopping_cart</i>
                                <span class="sidebar-normal"> Registro de Ventas </span>
                            </a>
                        </li>
                        <li id="requestChange" class="nav-item ">
                            <a class="nav-link" href="{{ route('tracking.requestChange') }}">
                                <i class="material-symbols-outlined">
                                    transfer_within_a_station
                                </i> <span class="sidebar-normal"> Cambio de Centro </span>
                            </a>
                        </li>
                        <li id="trackingValidateFinal" class="nav-item ">
                            <a class="nav-link" href="{{ route('tracking.index_validation_final') }}">
                                <i class="material-icons">groups</i>
                                <span class="sidebar-normal"> Validar RRHH </span>
                            </a>
                        </li>
                        <li id="trackingRemove" class="nav-item ">
                            <a class="nav-link" href="{{ route('tracking.deleteForm') }}">
                                <i class="material-icons">delete</i>
                                <span class="sidebar-normal"> Borrar seguimiento </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>



            <li class="nav-item">
                <a class="nav-link collapsed" data-toggle="collapse" href="#pagesReport" aria-expanded="false">
                    <i class="material-icons">description</i>
                    <p style="font-weight: bold"> Informes
                        <b class="caret"></b>
                    </p>
                </a>

                <div class="collapse" id="pagesReport">
                    <ul class="nav">
                        <li class="nav-item {{ request()->routeIs('calculateIncentive') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('calculateIncentive') }}">
                                <i class="material-icons">calculate</i>
                                <span class="sidebar-normal"> Calculadora de Incentivos </span>
                            </a>
                        </li>

                        @if (session()->get('user')->rol_id == 1 || session()->get('user')->rol_id == 4)
                            <li id="calculateService" class="nav-item">
                                <a class="nav-link" href="{{ route('calculateServices') }}">
                                    <i class="material-icons">track_changes</i>
                                    <span class="sidebar-normal"> Dinámica de Servicios </span>
                                </a>
                            </li>
                        @endif

                        <li id="centerLeague" class="nav-item">
                            <a class="nav-link" href="{{ route('centerLeague') }}">
                                <i class="material-icons">emoji_events</i>
                                <span class="sidebar-normal"> Liga de Centros </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="nav-item">
                <a class="nav-link collapsed" data-toggle="collapse" href="#pagesNotification"
                    aria-expanded="false">
                    <i class="material-icons">notifications</i>
                    <p style="font-weight: bold"> Notificaciones
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse" id="pagesNotification">
                    <ul class="nav">
                        <li id="supervisorNotificationsIndex" class="nav-item">
                            <a class="nav-link" href="{{ route('notifications.index') }}">
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

<script type="text/javascript">
    $(document).ready(function() {
        $('.nav-item').on('click', function() {
            // Remover 'active' de todos los elementos
            $('.nav-item').removeClass('active');
            // Agregar 'active' solo al elemento clicado
            $(this).addClass('active');
        });
    });
</script>

<style>
    .img-logo-sidebar {
        background-color: var(--white-icot);
        padding: 13px;
    }

    #userData {
        font-weight: 900;
    }

    hr {
        margin-left: 16px;
        margin-right: 16px;
    }

    .lblVersion {
        bottom: 50px;
        width: 100%;
        color: var(--red-icot);
        text-align: center !important;
        font-weight: 900;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        vertical-align: bottom;
    }

    .versionContainer {
        flex-grow: 1;
        display: flex;
        justify-content: flex-end;
        flex-direction: column;
    }
</style>
