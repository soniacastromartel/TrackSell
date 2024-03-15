<link rel="stylesheet" href="{{ asset('/css/navbar.css') }}">

<!-- Navbar -->
<div id="{{ Request::is('home') ? 'navbar-home' : 'navbar' }}" >
    @if(Request::is('home'))
    <div id="text-banner-home">
        <p style="white-space: nowrap;">
         <span id="text-banner" style="margin-right:60px;">REHABILITACIÓN</span>
         <img style="margin-right:500px"; src="{{ asset('/assets/img/banner1.jpg') }}" height="165">

         <span id="text-banner" style="margin-right:60px;">BOMBA MAGNÉTICA</span>
         <img style="margin-right:500px"; src="{{ asset('/assets/img/banner2.jpg') }}"height="165">

         <span id="text-banner" style="margin-right: 60px;">PLANTILLAS 3D</span>
         <img style="margin-right:500px"; src="{{ asset('/assets/img/banner3.png') }}" height="165">

         <span id="text-banner" style="margin-right: 60px;"">DIAGNÓSTICO POR IMAGEN</span>
         <img style="margin-right:500px"; src="{{ asset('/assets/img/banner4.jpg') }}" height="165">
        </p>
      </div>
      @endif

    <div class="container-fluid">
        <div class="navbar-wrapper">
                <button id="minimizeSidebar" class="{{ Request::is('home') ? 'minimize-sidebar-home' : 'minimize-sidebar' }}">
                    <i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
                    <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
                </button>
        </div>
        <div class="navbar-collapse justify-content-end collapse">
            <form class="navbar-form">
                <span class="bmd-form-group">
                    <div class="input-group no-border">
                        &nbsp;
                    </div>
                </span>
            </form>
        </div>
        @if ($nDays != '')
        <div id="{{ Request::is('home') ? 'alert-cutoff-date-home' : 'alert-cutoff-date' }}" role="alert">
                <i class="material-icons"  id="{{ Request::is('home') ? 'warning-home' : 'warning ' }}" >warning</i>
                En {{ $nDays }} días llega próximo corte, 20 de {{ $currentMonth }}
            </div>
        @endif
    </div>
</div>

