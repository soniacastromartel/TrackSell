<div class="row justify-content-end">

  <div class="justify-content-start">
    <div id="containerImg" class="text-center">
        <label id="lbl" for="image">Imagen de Centro <span id="obligatory">*</span></label>
        <br>
        <div>
          <img src="{{ isset($centre) && $centre->image != '' ? $centre->image : asset('storage/img/default.png') }}" name="image" id="image"  width="600px" height="342px">
          <br>
          <input type="file" class="btn btn-danger btn-sm mt-1" name="image" id="image" style="width:100%;"/>
          <br><br>
          <div class="row">
            <div class="col-6">
          <input type="text" id="msg-imag1" readonly="" placeholder="Tamaño máximo de fichero son 2MB" style="width:100%;border:none;">
          <input type="text" id="msg-imag2" readonly="" placeholder="Resolución 2320x1547 px" style="width:100%;border:none;">
         </div>
            <div class="col-6">
              <div class="float-right mt-2">
                @if(!isset($centre))
                  <label id="lbl" for="alias_img">Alias de imagen <span id="obligatory">*</span></label>
                  <br>
                  <input style="font-family:Helvetica;" type="text" class="text-center" name="alias_img" id="alias_img" placeholder="¿Alias?" value="{{isset($centre) ? $centre->alias_img : ''}}">
                @endif
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>


  <div class="row col-7 mr-3 justify-content">

    <div class="col-6">
      <label id="lbl" for="centre_id">Centro <span id="obligatory">*</span></label>
      @if(isset($centre))
      <input type="text" class="form-control" name="name" id="name"  style="font-family:Helvetica;" value="{{ isset($centre) ? $centre->name : ''}}" disabled>          
      @else
      <input type="text" class="form-control" name="name" id="name"  style="font-family:Helvetica;" value="{{ isset($centre) ? $centre->name : ''}}">     
      @endif
    </div>
    <div class="col-6">
      <label id="lbl" for="category">Isla <span id="obligatory">*</span></label>
      <select class="selectpicker" style="font-family:monospace;" name="island" id="island" data-size="7" data-style="btn btn-danger btn-round" title="* Seleccione Isla" tabindex="-98">
      @if(isset($islands))
        @foreach ($islands as  $isla)
          <option value="{{$isla->island}}" 
            @if (isset($centre) && $centre->island == $isla->island)
                  selected="selected"
            @endif
            >{{$isla->island}}</option>
        @endforeach
      @endif
      </select>
    </div>        
    <div class="col-6 ">
      <label id="lbl" for="name">Nombre a mostrar <span id="obligatory">*</span></label>
      <input type="text" class="form-control" style="font-family:Helvetica;" name="label" id="label"  placeholder="" value="{{ isset($centre) ? $centre->label : ''}}">
      <br>
    </div>
    <div class="col-6">
      <label id="lbl" for="name">Teléfono <span id="obligatory">*</span></label>
      <input type="text" class="form-control" style="font-family:Helvetica;" name="phone" id="phone"  placeholder="" value="{{ isset($centre) ? $centre->phone : ''}}">
    </div>
    <div class="col-12 ">
      <label id="lbl" for="url">Dirección <span id="obligatory">*</span></label>
      <input type="text" class="form-control" style="font-family:Helvetica;" name="address" id="address"  placeholder="" value="{{ isset($centre) ? $centre->address : ''}}"> 
    </div>
    <div class="col-6">
      <label id="lbl" for="name">Email <span id="obligatory">*</span></label>
      <input type="text" class="form-control" style="font-family:Helvetica;" name="email" id="email"  placeholder="" value="{{ isset($centre) ? $centre->email : ''}}">
    </div>
    <div class="col-6">
      <label id="lbl" for="name">Horario <span id="obligatory">*</span></label>
      <input type="text" class="form-control" style="font-family:Helvetica;" name="timetable" id="timetable"  placeholder="" value="{{ isset($centre) ? $centre->timetable : ''}}">
    </div>
  </div>
</div>
<br>
  
<div class="row float-right mr-4">
  <div class="col">
      <button id="btnSubmit" type="submit" class="btn btn-fill btn-success">{{ __('Guardar') }}</button>
      <button id="btnSubmitLoad" type="submit" class="btn btn-success" style="display: none">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        {{ __('Guardando...') }}
      </button>
      <button id="btnBack" href="/config" class="btn btn-fill btn-danger">
        {{ __('Volver') }}
      </button>
      <br>
      <span id="lbl"> * Estos campos son requeridos </span>
  </div>
</div>
<style>
  #lbl{
    color: black;
    font-weight: 800;
    font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
  }

  #obligatory{
    color: #CC0000;
    font-weight: bold;
  }
</style>