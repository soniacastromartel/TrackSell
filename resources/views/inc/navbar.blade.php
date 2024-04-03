<link rel="stylesheet" href="{{ asset('/css/navbar.css') }}">

<!-- Navbar -->
<div id="{{ Request::is('home') ? 'navbar-home' : 'navbar'}}">

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


