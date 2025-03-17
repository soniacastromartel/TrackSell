<div class="row">
    <!-- Columna para Imagen y Requisitos -->
    <div class="col-lg-6 text-center">
        <div id="containerImg">
            <label class="label" for="image">Imagen de Centro <span id="obligatory">*</span></label>
            <img id="centreImg"
                src="{{ isset($centre) && $centre->image != '' ? $centre->image : asset('storage/img/default.png') }}"
                alt="Imagen del centro" width="100%" height="auto">
            <div class="mt-3">
                <input type="file" class="button upload-box" name="image" id="image" />
            </div>
            @if (!isset($centre))
                <div class="mt-3">
                    <label class="label" for="alias_img">Alias de imagen <span id="obligatory">*</span></label>
                    <input type="text" class="form-control text-center" name="alias_img" id="alias_img"
                        placeholder="¿Alias?" value="{{ isset($centre) ? $centre->alias_img : '' }}">
                </div>
            @endif
            <details class="mt-4">
                <summary class="label align-items-center" id="lbl">
                    <i class="material-symbols-outlined pr-2" id="icInfo" style="color: var(--red-icot)">info</i>
                    Requisitos de imagen
                </summary>
                <ul class="demo">
                    <li>Tamaño Máximo: 2MB</li>
                    <li>Resolución: 2320x1547px</li>
                </ul>
            </details>
        </div>
    </div>

    <!-- Columna para el Formulario -->
    <div class="col-lg-6">
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="label" for="centre_id">Centro <span id="obligatory">*</span></label>
                    <input type="text" class="form-control text-center" name="name" id="name"
                        value="{{ isset($centre) ? $centre->name : '' }}" {{ isset($centre) ? 'readonly' : '' }}>
                </div>
            </div>
            <div class="col-lg-7" style="margin-bottom: 30px;">
                <div class="form-group">
                    <label class="label" for="island">Isla <span id="obligatory">*</span></label>
                    <select class=" selectpicker" name="island" id="island" title="* Seleccione Isla">
                        @if (isset($islands))
                            @foreach ($islands as $isla)
                                <option value="{{ $isla->island }}" @if (isset($centre) && $centre->island == $isla->island) selected @endif>
                                    {{ $isla->island }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="label" for="label">Nombre a mostrar <span id="obligatory">*</span></label>
            <input type="text" class="form-control" name="label" id="label"
                value="{{ isset($centre) ? $centre->label : '' }}">
        </div>
        <div class="form-group">
            <label class="label" for="phone">Teléfono <span id="obligatory">*</span></label>
            <input type="text" class="form-control" name="phone" id="phone"
                value="{{ isset($centre) ? $centre->phone : '' }}">
        </div>
        <div class="form-group">
            <label class="label" for="address">Dirección <span id="obligatory">*</span></label>
            <input type="text" class="form-control" name="address" id="address"
                value="{{ isset($centre) ? $centre->address : '' }}">
        </div>
        <div class="form-group">
            <label class="label" for="email">Email <span id="obligatory">*</span></label>
            <input type="text" class="form-control" name="email" id="email"
                value="{{ isset($centre) ? $centre->email : '' }}">
        </div>
        <div class="form-group">
            <label class="label" for="timetable">Horario <span id="obligatory">*</span></label>
            <input type="text" class="form-control" name="timetable" id="timetable"
                value="{{ isset($centre) ? $centre->timetable : '' }}">
        </div>
    </div>
</div>

<div class="row mt-4 text-right">
    <div class="col-12">
        <button id="btnSubmit" type="submit" class="btn-save">
            <span class="material-icons">save</span>
        </button>
        <button id="btnSubmitLoad" type="submit" class="btn-save" style="display: none">
            <span class="spinner-border spinner-border-sm" role="status"></span>
        </button>
        <button id="btnBack" href="/config" class="btn-return">
            <span class="material-icons">arrow_back</span>
        </button>
        <label id="lbl" class="d-block mt-2"><span id="obligatory">*</span> Estos campos son
            requeridos</label>
    </div>
</div>

<style>
    .form-control {}

    .content {
        background-image: url(/assets/img/background_continue.png) !important;
        background-position: center center !important;
        background-size: 1000px;
        height: 140vh !important;

    }

    #centreImg {
        border: 5px solid var(--red-icot);
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
        font-family:  "Nunito", sans-serif;
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
        font-family:  "Nunito", sans-serif;
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

    ul.demo {
        list-style-type: circle;
        margin: 0;
        margin-left: 10px;
        text-align: left !important;
        font-size: 12px;
        font-weight: 600;
        font-family:  "Nunito", sans-serif;

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
