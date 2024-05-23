<link rel="stylesheet" href="{{ asset('/css/navbar.css') }}">
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent">
    <div class="container-fluid">
        <div class="navbar-wrapper">
            <div class="navbar-minimize">
                <button id="minimizeSidebar">
                    <i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
                    <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
                </button>
            </div>
            <a id="title" class="navbar-brand" href="javascript:;">{{ $title ?? '' }}</a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
        </button>
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
            <div class="alert-cutoff-date alert-warning" role="alert">
                <i class="material-icons" id="warning">warning</i>
                En {{ $nDays }} días llega próximo corte, 20 de {{ $currentMonth }}
            </div>
        @endif
    </div>
</nav>
