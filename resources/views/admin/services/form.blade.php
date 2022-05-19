  <div class="row justify-content-end">
  
  <div class="justify-content-start">
    <div id="containerImg" class="text-center">
        <label class="label" for="image">Imagen de Servicio <span class="obligatory">*</span></label>
        <br>
        <div>
          <img src="{{ isset($service) && $service->image != '' ? $service->image : asset('storage/img/default.png') }}" name="image" id="image"  width="600px" height="342px">
          <br>
          <input type="file" class="btn btn-red-icot btn-sm mt-1" name="changeImg" id="changeImg" style="width:100%;"/>
          <br><br>
          <div class="row">
            <div class="col-6">
              <input type="text" id="msg-imag1" readonly="" placeholder="Tamaño máximo de fichero son 2MB" style="width:100%;border:none;font-weight:900;">
              <input type="text" id="msg-imag2" readonly="" placeholder="Resolución 600x342 px" style="width:100%;border:none;font-weight:900;">
            </div>
            <div class="col-6">
              <div class="float-right mt-2">
                @if(!isset($service))
                  <label id="lbl" for="alias_img">Alias de imagen <span class="obligatory">*</span></label>
                  <input type="text" class="text-center" name="alias_img" id="alias_img" placeholder="¿Alias?" value="{{isset($service) ? $service->alias_img : ''}}">
                @endif
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
  
  <div class="row col-7 mr-3 justify-content">
        <div class="col-6">
          <label id="lbl" for="centre_id">Centro <span class="obligatory">*</span></label>
          <br>
            <textarea class="text-center" rows="3" cols="135" name="name" id="name" style="padding: 10px; max-width:95%;margin-left:5px; font-weight:900;" disabled>{{!empty($centres) ? $centres : '¡AVISO!, NO HAY CENTROS ASOCIADOS'}}</textarea>
          <br>
        </div>

      <div class="col-6">
        <br>
          <label id="lbl" for="category">Categoría <span class="obligatory">*</span></label>
          <select class="selectpicker text-center" name="category" id="category" class="text-center" data-size="6" data-style="btn btn-red-icot btn-round" title="* Seleccione Categoría de servicio" tabindex="-98">
          @if(isset($categories))
            @foreach ($categories as  $category)
              <option value="{{$category->id}}" 
                @if (isset($service) && isset($categories) && $category->id == $service->category_id)
                      selected="selected"
                @endif             
                >{{$category->name}}</option>
            @endforeach
          @endif
          </select>
      </div>

      <div class="col-12">
        <label id="lbl" for="name">Nombre <span class="obligatory">*</span></label>
        <input type="text" class="form-control" name="name" id="name"  placeholder="" value="{{ isset($service) ? $service->name : ''}}">     
      </div>
      <div class="col-12">
        <label id="lbl" for="url">URL <span class="obligatory">*</span></label>
        <input type="text" class="form-control" name="url" id="url"  placeholder="" value="{{ isset($service) ? $service->url : ''}}">
    </div>
    <div class="col-12">
      <label id="lbl" for="description">Descripción <span class="obligatory">*</span></label>
        <textarea rows="6" cols="135" name="description" id="description" style="padding: 10px; max-width:100%;">{{ isset($service) ? $service->description : ''}}</textarea>
    </div>
  </div>

  <div class="row mr-4">
    <div class="row">
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
    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
  }

</style>