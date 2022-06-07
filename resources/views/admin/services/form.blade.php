  <div class="row justify-content-end">

    <div class="col-xl-6 col-lg-12 column-center">
      <div id="containerImg" class="text-center">
        <label class="label" for="image">Imagen de Servicio <span class="obligatory">*</span></label>
        <br>
        <div>
          <img id="serviceImg" src="{{ isset($service) && $service->image != '' ? $service->image : asset('storage/img/default.png') }}" name="image" id="image" width="600px" height="342px">
          <br>
          <input type="file" class="button upload-box" name="changeImg" id="changeImg" style="width:71%;" />
          <br><br>
          <div class="row">
            <div class="col-6" style= "margin-left: 140px;">
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
              <!-- <input type="text" id="msg-imag1" readonly="" placeholder="Tamaño máximo de fichero: 2MB" style="width:100%;border:none;font-weight:900;">
              <input type="text" id="msg-imag2" readonly="" placeholder="Resolución 600x342px" style="width:100%;border:none;font-weight:900;"> -->
            </div>
            <div class="col-6">
              <div class="float-right mt-2">
                @if(!isset($service))
                <label class="label" for="alias_img">Alias de imagen <span class="obligatory">*</span></label>
                <input type="text" class="text-center" name="alias_img" id="alias_img" placeholder="¿Alias?" value="{{isset($service) ? $service->alias_img : ''}}">
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row col-xl-6 col-lg-12">
      <div class="col-6">
        <label class="label" for="centre_id">Centro <span class="obligatory">*</span></label>
        <br>
        <textarea class="text-left" rows="3" cols="135" name="name" id="name" style="padding: 10px; max-width:95%;margin-left:5px; font-weight:900;" disabled>{{!empty($centres) ? $centres : '¡AVISO!, NO HAY CENTROS ASOCIADOS'}}</textarea>
        <br>
      </div>

      <div class="col-5">
        <br>
        <label class="label" for="category">Categoría <span class="obligatory">*</span></label>
        <select class="selectpicker text-center" name="category" id="category" class="text-center" data-size="6" data-style="btn btn-red-icot btn-round" title="* Seleccione Categoría de servicio" tabindex="-98">
          @if(isset($categories))
          @foreach ($categories as $category)
          <option value="{{$category->id}}" @if (isset($service) && isset($categories) && $category->id == $service->category_id)
            selected="selected"
            @endif
            >{{$category->name}}</option>
          @endforeach
          @endif
        </select>
      </div>

      <div class="col-11">
        <label class="label" for="name">Nombre <span class="obligatory">*</span></label>
        <input type="text" class="form-control" name="name" id="name" placeholder="" value="{{ isset($service) ? $service->name : ''}}">
      </div>
      <div class="col-11">
        <label class="label" for="url">URL <span class="obligatory">*</span></label>
        <input type="text" class="form-control" name="url" id="url" placeholder="" value="{{ isset($service) ? $service->url : ''}}">
      </div>
      <div class="col-11">
        <label class="label" for="description">Descripción <span class="obligatory">*</span></label>
        <textarea rows="6" cols="135" name="description" id="description" style="padding: 10px; max-width:100%;">{{ isset($service) ? $service->description : ''}}</textarea>
      </div>
    </div>

    <div style="margin-right: 60px;">
      <div class="row mr-4">
        <div class="row">
          <button id="btnSubmit" type="submit" class="btn btn-fill btn-success"> <span class="material-icons">
                            save
                            </span> {{ __('Guardar') }}</button>
          <button id="btnSubmitLoad" type="submit" class="btn btn-success" style="display: none">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            {{ __('Guardando...') }}
          </button>
          <button id="btnBack" href="/config" class="btn btn-fill btn-red-icot">
          <span class="material-icons">
                            arrow_back
                            </span> {{ __('Volver') }}
          </button>
        </div>
      </div>
      <label id="lbl" for="image"><span id="obligatory" class="mr-1">*</span>Estos campos son requeridos</label>
    </div>
  </div>

  <style>
    #lbl {
      color: black;
      font-weight: 600;
      font-family: 'Helvetica', 'Arial', sans-serif;
      margin-top: 25px;
      font-size: 12px;
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


    ul.demo {
      list-style-type: circle;
      margin: 0;
      margin-left: 10px;
      text-align: left !important;
      font-size: 12px;
      font-weight: 600;
      font-family: 'Helvetica', 'Arial', sans-serif;

    }

    #serviceImg {
      border: 3px solid var(--red-icot);
      border-radius: 16px;
      margin-bottom: 16px;
    }

    #obligatory {
      color: #CC0000;
      font-weight: bold;
    }
  </style>