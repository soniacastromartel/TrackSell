<link rel="stylesheet" href="{{ asset('/css/services.css') }}">

<div class="row">
    <!-- Columna para Imagen y Requisitos -->
    <div class="col-lg-6 text-center">
        <div id="containerImg">
            <label class="label" for="image">Imagen de Servicio <span class="obligatory">*</span></label>
            <div>
                @php
                    $imagePath =
                        isset($service) && $service->image
                            ? asset('storage/' . $service->image)
                            : asset('storage/img/default.png');
                @endphp
                <img id="serviceImg" src="{{ $imagePath }}" width="400" height="342">
                <div class="mt-3">
                    <input type="file" class="button upload-box" name="image" id="image" />
                </div>
                <div class="float-right">
                    <div class="float-right mt-2" style="margin-right: 115px; margin-bottom: 48px;">
                        @if (!isset($service))
                            <label class="label" for="alias_img">Alias de imagen <span
                                    class="obligatory">*</span></label><br>
                            <input type="text" class="text-center" name="alias_img" id="alias_img"
                                placeholder="¿Alias?" value="{{ isset($service) ? $service->alias_img : '' }}">
                        @endif
                    </div>
                </div>
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
    </div>
    <!-- Columna para el Formulario -->
    <div class="col-lg-6">
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="label" for="centre_id">Centro <span class="obligatory">*</span></label>
                    <select name="centre_id" id="centre_id" class="selectpicker" data-style="btn btn-red-icot btn-round"
                        title="Seleccione un Centro" tabindex="-98">
                        @foreach ($centres as $centre)
                            <option value="{{ $centre->id }}">{{ $centre->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-7" style="margin-bottom: 30px;">
                <div class="form-group">
                    <label class="label" for="category">Categoría <span class="obligatory">*</span></label>
                    <select class="selectpicker text-center" name="category" id="category" class="text-center"
                        data-size="6" data-style="btn btn-red-icot btn-round"
                        title="* Seleccione Categoría de servicio" tabindex="-98">
                        @if (isset($categories))
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    @if (isset($service) && isset($categories) && $category->id == $service->category_id) selected="selected" @endif>{{ $category->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="label" for="name">Nombre <span class="obligatory">*</span></label>
            <input type="text" class="form-control" name="name" id="name" placeholder=""
                value="{{ isset($service) ? $service->name : '' }}">
        </div>
        <div class="form-group">
            <label class="label" for="url">URL <span class="obligatory">*</span></label>
            <input type="text" class="form-control" name="url" id="url" placeholder=""
                value="{{ isset($service) ? $service->url : '' }}">
        </div>
        <div class="form-group">
            <label class="label" for="description">Descripción <span class="obligatory">*</span></label>
            <textarea rows="6" cols="135" name="description" id="description" style="padding: 10px; max-width:100%;">{{ isset($service) ? $service->description : '' }}</textarea>
        </div>
    </div>
</div>
<div class="row mt-4 text-right">
    <div class="col-12">
        <button id="btnSubmitSave" type="submit" class="btn-save">
            <span class="material-icons">save</span>
        </button>
        <button id="btnSubmitLoadSave" type="submit" class="btn-save" style="display: none">
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
        border: 5px solid var(--red-icot);
        border-radius: 16px;
        margin-bottom: 16px;
    }

    #obligatory {
        color: #CC0000;
        font-weight: bold;
    }
</style>
