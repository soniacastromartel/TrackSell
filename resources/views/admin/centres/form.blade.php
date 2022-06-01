<div class="row">

  <div class="col-xl-6 col-lg-12 column-center">

    <div id="containerImg" class="text-center">
      <label class="label" for="image">Imagen de Centro <span id="obligatory">*</span></label>
      <br>
      <img id="centreImg" src="{{ isset($centre) && $centre->image != '' ? $centre->image : asset('storage/img/default.png') }}" name="image" id="image" width="600px" height="342px">
      <br>
      <div class="col-lg-6 m-3">
        <input type="file" class="button upload-box" name="image" id="image" style="margin-left:100px;" />
      </div>
      <div>
        <div class="col-6"  style="margin-left: 115px;">
          <details close>
            <summary class="row label align-items-center" id="lbl">
              <i class="material-icons pr-2" id="icInfo" style="color: var(--red-icot)">info</i>
              Requisitos de imagen
            </summary>
            <ul class="demo">
              <li>Tamaño máximo de fichero son 2MB</li>
              <li>Resolución 2320x1547 px</li>
            </ul>
          </details>
        </div>
        @if(!isset($centre))
        <div class="col-6 col-md-12">
          <div class="float-right mt-2">
            <label class="label" for="alias_img">Alias de imagen <span id="obligatory">*</span></label>
            <br>
            <input type="text" class="text-center" name="alias_img" id="alias_img" placeholder="¿Alias?" value="{{isset($centre) ? $centre->alias_img : ''}}">
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>


  <div class="row col-xl-6 col-lg-12">

    <div class="col-6">
      <label class="label" for="centre_id">Centro <span id="obligatory">*</span></label>
      @if(isset($centre))
      <input type="text" class="form-control" name="name" id="name" value="{{ isset($centre) ? $centre->name : ''}}" readonly>
      @else
      <input type="text" class="form-control" name="name" id="name" value="{{ isset($centre) ? $centre->name : ''}}">
      @endif
    </div>
    <div class="col-5">
      <label class="label" for="category">Isla <span id="obligatory">*</span></label>
      <select class="selectpicker" style="font-family:monospace;" name="island" id="island" data-size="7" data-style="btn btn-red-icot btn-round" title="* Seleccione Isla" tabindex="-98">
        @if(isset($islands))
        @foreach ($islands as $isla)
        <option value="{{$isla->island}}" @if (isset($centre) && $centre->island == $isla->island)
          selected="selected"
          @endif
          >{{$isla->island}}</option>
        @endforeach
        @endif
      </select>
    </div>
    <div class="col-6">
      <label class="label" for="name">Nombre a mostrar <span id="obligatory">*</span></label>
      <input type="text" class="form-control" name="label" id="label" placeholder="" value="{{ isset($centre) ? $centre->label : ''}}">
      <br>
    </div>
    <div class="col-5">
      <label class="label" for="name">Teléfono <span id="obligatory">*</span></label>
      <input type="text" class="form-control" name="phone" id="phone" placeholder="" value="{{ isset($centre) ? $centre->phone : ''}}">
    </div>
    <div class="col-11 ">
      <label class="label" for="url">Dirección <span id="obligatory">*</span></label>
      <input type="text" class="form-control" name="address" id="address" placeholder="" value="{{ isset($centre) ? $centre->address : ''}}">
    </div>
    <div class="col-6">
      <label class="label" for="name">Email <span id="obligatory">*</span></label>
      <input type="text" class="form-control" name="email" id="email" placeholder="" value="{{ isset($centre) ? $centre->email : ''}}">
    </div>
    <div class="col-5">
      <label class="label" for="name">Horario <span id="obligatory">*</span></label>
      <input type="text" class="form-control" name="timetable" id="timetable" placeholder="" value="{{ isset($centre) ? $centre->timetable : ''}}">
    </div>
  </div>
</div>
<br>

<div class="row mr-4 text-right">
  <div class="col-lg-12 ml-4">
    <button id="btnSubmit" type="submit" class="btn btn-fill btn-success">{{ __('Guardar') }}</button>
    <button id="btnSubmitLoad" type="submit" class="btn btn-success" style="display: none">
      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
      {{ __('Guardando...') }}
    </button>
    <button id="btnBack" href="/config" class="btn btn-fill btn-red-icot">
      {{ __('Volver') }}
    </button>
    <br>
    <label id="lbl" for="image"><span id="obligatory" class="mr-1">*</span>Estos campos son requeridos</label>
  </div>
</div>
<style>
  #centreImg {
    border: 3px solid var(--red-icot);
    border-radius: 16px;
    margin-bottom: 16px;
  }

  #obligatory {
    color: #CC0000;
    font-weight: bold;
  }

  .colum-center {
    width: 600px;
    display: flex;
    justify-content: center;
  }

  .upload-box {
    background: var(--white);
    outline: none;
    border-radius: 30px;

  }

  ::-webkit-file-upload-button {
    font-family: 'Helvetica', 'Arial', sans-serif;
    color: var(--white);
    background: var(--red-icot);
    padding: 8px 23px;
    border: none;
    border-radius: 30px;
    outline: none;
    text-transform: uppercase;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    border: 2px solid var(--red-icot);


  }

  ::-webkit-file-upload-button:hover {
    background: var(--white);
    color: var(--red-icot);
    border: 2px solid var(--red-icot);

  }

  summary {
    padding: 1em;
    font-family: 'Helvetica', 'Arial', sans-serif;
    font-size: 12px;
    /* background-color: var(--light-grey); */
    /* border: 2px solid var(--red-icot); */
  }

  details>summary::-webkit-details-marker {
    display: none;
  }

  details>summary {
    list-style-type: none;
  }

  #lbl {
    color: black;
    font-weight: 600;
    font-family: 'Helvetica', 'Arial', sans-serif;
    margin-top: 25px;
    font-size: 12px;
  }

  ul.demo {
    list-style-type: circle;
    margin: 0;
    margin-left: 10px;
    text-align: left !important;
    font-size: 12px;
    font-weight: 600;
    font-family: 'Helvetica', 'Arial', sans-serif;

  }

  @media (min-width: 1200px) and (max-width: 1559px) {
    .col-xl-6 {
      flex: 0 0 100%;
      max-width: 100%;
      /* background-color: peru; */
    }

    /* #containerImg{
      display: flex;
      flex-direction: column;
    } */

  }
</style>