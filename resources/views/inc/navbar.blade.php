
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

<style>
    
#navbar-home {
    background-image: url(/assets/img/background-nav.png);
    height: 450px;
    width: 100%;
    position: absolute;
}

.minimize-sidebar-home {
    position: absolute !important;
    top: 120px;
    left: 0 !important;
    background: none !important;
    border: none !important;
    color: white !important;
}
.minimize-sidebar {
    position: absolute !important;
    padding-top: 50px !important;
    background: none !important;
    border: none !important;
    color: var(--red-icot) !important;
}
#alert-cutoff-date-home{
    position: absolute;
    right: 0;
    margin: 20px;
    color: var(--red-icot);
    background-color: white;
    padding: 20px 15px;
    border-radius: 3px;
    border: 2px;
    font-weight: 600;
    display: flex;
    align-items: center;
    box-shadow: 0 8px 25px 0 rgba(0, 0, 0, 0.678), 0 10px 20px -5px rgb(186 46 41 / 50%) !important;
}
#alert-cutoff-date{
    position: absolute;
    right: 0;
    margin: 20px;
    top:20px;
    color: white;
    background-color:var(--red-icot);
    padding: 20px 15px;
    margin-right: 20px;
    border-radius: 3px;
    border: 2px;
    font-weight: 600;
    display: flex;
    align-items: center;
    box-shadow: 0 8px 25px 0 rgba(0, 0, 0, 0.678), 0 10px 20px -5px rgb(186 46 41 / 50%) !important;
}
#warning-home {
    color: var(--red-icot);
    font-size: 20px;
    padding-right: 1rem;
}
#warning {
    color: white;
    font-size: 20px;
    padding-right: 1rem;
}

    </style>
