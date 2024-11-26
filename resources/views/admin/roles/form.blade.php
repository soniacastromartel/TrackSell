<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('/css/roles.css') }}">

<div class="row" style="margin-bottom:40px; margin-top: 20px;">
    <div class="form-group col-md-3" style="margin-right:20px;margin-left: 60px;">
        <label for="name">Nombre <span id="obligatory">*</span></label>
        <input type="text" class="form-control" name="name" id="name" placeholder=""
            value="{{ isset($role) ? $role->name : '' }}">
    </div>
    <div class="form-group col-md-3" style="margin-right:20px;margin-left:20px;">
        <label for="name">Descripción <span id="obligatory">*</span></label>
        <input type="text" class="form-control" name="description" id="description" placeholder=""
            value="{{ isset($role) ? $role->description : '' }}">
    </div>
    <br>
    <div class="form-group col-md-4 dropdown bootstrap-select">
        <label for="name" class="label">Rol <span id="obligatory">*</span></label>
        <div class="select-wrapper">
            <span id="icon-select" class="icon-select material-symbols-outlined">
                business
            </span>
            <select class="selectpicker" name="level_id" id="level_id" data-size="7"
                data-style="btn btn-red-icot btn-round" title="* Seleccione Nivel de Acceso" tabindex="-98">
                @foreach ($roles as $roleOption)
                    <option value="{{ $roleOption->id }}"
                        {{ isset($role) && $role->level_id == $roleOption->id ? 'selected' : '' }}>
                        {{ $roleOption->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row mt-2" style="margin-left: 30px;">
    <div class="col text-right">
        <button id="btnSubmitSave" type="submit" class="btn-save">
            <span class="material-icons mr-1">save </span>
        </button>

        <button id="btnSubmitLoadSave" type="submit" class="btn-save" style="display: none">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

        </button>

        <button id="btnBack" href="/config" class="btn-return">
            <span class="material-icons">arrow_back</span>
        </button>
    </div>
</div>

<style>
    #obligatory {
        color: #CC0000;
        font-weight: bold;
    }

    #lbl {
        color: black;
        /* font-weight: 800; */
        font-family: 'Helvetica', 'Arial', sans-serif;
        margin-top: 25px;
        font-size: 12px;
    }
</style>
