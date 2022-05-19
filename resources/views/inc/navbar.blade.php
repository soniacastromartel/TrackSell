<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent">
    <div class="container-fluid">
      <div class="navbar-wrapper">
        <div class="navbar-minimize">
          <button id="minimizeSidebar" class="btn btn-just-icon btn-white btn-fab btn-round">
            <i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
            <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
          </button>
        </div>
        <a id="title" class="navbar-brand" href="javascript:;">{{$title}}</a>
      </div>

      <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
        <span class="sr-only">Toggle navigation</span>
        <span class="navbar-toggler-icon icon-bar"></span>
        <span class="navbar-toggler-icon icon-bar"></span>
        <span class="navbar-toggler-icon icon-bar"></span>
      </button> 
      <div class="collapse navbar-collapse justify-content-end">
        <form class="navbar-form">
          <span class="bmd-form-group"><div class="input-group no-border">
            &nbsp;
          </div></span>
        </form>
      </div>
      @if ($nDays != "")
          <div class="alert-cutoff-date alert-warning" role="alert">
          <i class="material-icons" id="warning">warning</i>
            En {{$nDays}} días llega próximo corte, 20 de {{$currentMonth}}
          </div>
      @endif
          
    </div>

</nav>
<style>
  #title{
    color: black;
    font-weight: 800;
    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
  }

  .alert-cutoff-date{
    height: 100%;
    color: var(--white);
    background-color: #BA2E29;
    padding: 20px 15px;
    border-radius: 3px;
    border: 2px solid #BA2E29;
    font-weight: 600;
    display: flex;
    align-items: center;
    box-shadow: 0 4px 20px 0 rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(186 46 41/ 40%);
}
  #warning{
    color: var(--white);
    font-size: 20px;
    padding-right: 1rem;
  }
</style>
<!--  End Navbar -->