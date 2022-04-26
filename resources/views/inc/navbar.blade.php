<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
    <div class="container-fluid">
      <div class="navbar-wrapper">
        <div class="navbar-minimize">
          <button id="minimizeSidebar" class="btn btn-just-icon btn-white btn-fab btn-round">
            <i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
            <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
          </button>
        </div>
        <a id="title" class="navbar-brand" href="javascript:;">{{$title}}</a>
        @if ($nDays != "")
          <div class="alert alert-timeout alert-danger animacion" style="width:20%" role="alert">
                EN {{$nDays}} DÍAS LLEGA PROXIMO CORTE 20 DE {{$currentMonth}}
          </div>
        @endif
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
          
    </div>

</nav>
<style>
  #title{
    color: black;
    font-weight: 800;
    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
  }
</style>
<!--  End Navbar -->