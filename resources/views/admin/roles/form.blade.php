<div class="row" style="margin-bottom:40px; margin-top: 20px;">
  <div class="form-group col-md-3"  style="margin-right:20px;margin-left: 60px;">
    <label for="name">Nombre <span id="obligatory">*</span></label>
    <input type="text" class="form-control" name="name" id="name"  placeholder="" value="{{ isset($role) ? $role->name : ''}}">
  </div>
  <div class="form-group col-md-3"  style="margin-right:20px;margin-left:20px;">
    <label for="name">Descripción <span id="obligatory">*</span></label>
    <input type="text" class="form-control" name="description" id="description"  placeholder="" value="{{ isset($role) ? $role->description : ''}}">
  </div>
  <div class="form-group col-md-3 dropdown bootstrap-select"  style="margin-left:60px;">
    <select class="selectpicker" name="level_id" id="level_id" data-size="7" data-style="btn btn-red-icot btn-round" 
      title="* Seleccione Nivel de Acceso" tabindex="-98">
      
      <option value="1" @if (isset($role) && $role->level_id == 1)  selected="selected" @endif>Admin</option>
      <option value="2" @if (isset($role) && $role->level_id == 2)  selected="selected" @endif>Supervisor/Centro</option>
      <option value="3" @if (isset($role) && $role->level_id == 3)  selected="selected" @endif>Empleado</option>
    </select>
  </div>
</div>

<div class="row mt-2" style="margin-left: 30px;">
    <div class="col-md-5">
        <button id="btnSubmit" type="submit" class="btn btn-success">
            {{ __('Guardar') }}
        </button>
        <button id="btnSubmitLoad" type="submit" class="btn btn-success" style="display: none">
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          {{ __('Guardando...') }}
        </button>
        <button id="btnBack" href="/config" class="btn btn-red-icot">
            {{ __('Volver') }}
        </button>
    </div>
</div>
<div class="row mb-0" >
  <div class=" px-5 col-md-8" style="margin-left: 25px;">
    <label id="lbl" class="label"><span class="obligatory">*</span> Estos campos son requeridos </label>
  </div>
</div>
<style>

  #obligatory{
    color: #CC0000;
    font-weight: bold;
  }
  #lbl {
        color: black;
        /* font-weight: 800; */
        font-family: 'Helvetica', 'Arial', sans-serif;
        margin-top: 25px;
        font-size:12px;
      }
</style>