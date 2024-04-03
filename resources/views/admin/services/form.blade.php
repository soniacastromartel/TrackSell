<link rel="stylesheet" href="{{ asset('/css/services.css') }}">
 
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
          <div class="float-right">
              <div class="float-right mt-2" style="margin-right: 115px; margin-bottom: 48px;">
                @if(!isset($service))
                <label class="label" for="alias_img">Alias de imagen <span class="obligatory">*</span></label><br>
                <input type="text" class="text-center" name="alias_img" id="alias_img" placeholder="¿Alias?" value="{{isset($service) ? $service->alias_img : ''}}">
                @endif
              </div>
            </div>
            <div style= "margin-left: 140px; margin-top: 98px;">
              <details open>
                <summary class="row label align-items-center" id="lbl">
                  <i class="material-icons pr-2" id="icInfo" style="color: var(--red-icot)">info</i>
                  Requisitos de imagen
                </summary>
                <ul class="demo mt-2">
                  <li>Tamaño máximo de fichero son 2MB</li>
                  <li>Resolución 2320x1547 px</li>
                </ul>
              </details>
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
      <div class="row mr-4 text-right">
        <div class= "col-lg-12 ml-4">
          <button id="btnSubmitSave" type="submit" class="btn-save"> <span class="material-icons">
                            save
                            </span> </button>
          <button id="btnSubmitLoadSave" type="submit" class="btn-save" style="display: none">
            <span class="btn-save" role="status" aria-hidden="true"></span>
          
          </button>
          <button id="btnBack" href="/config" class="btn-return">
          <span class="material-icons">
                            arrow_back
                            </span>
          </button>
        </div>
      </div>
      <label id="lbl" for="image" class="float-right mr-3 font-font-weight-bolder"><span id="obligatory">* </span>Estos campos son requeridos</label>
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
    .btn-save{
            position: relative;   
            background-color: green;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 50%;
            width: 40px; 
            height: 40px;
            padding: 0; 
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
            margin: 10px;
            }

            .btn-save::after {
            position: absolute;
            bottom: 100%; 
            left: 50%; 
            transform: translateX(-50%); 
            white-space: nowrap; 
            visibility: hidden; 
            opacity: 0;
            transition: opacity 0.2s, visibility 0.2s; 
            border-radius: 4px;
            font-size: 12px;
            }
            
            .btn-save:hover {
            background-color: white; 
            color: green;   
            }
            
            .btn-save:hover::after {
            visibility: visible;
            opacity: 1;
            }

            .btn-return{
                position: relative;   
                background-color:var(--red-icot);
                border: none;
                color: white;
                cursor: pointer;
                border-radius: 50%;
                width: 40px; 
                height: 40px;
                padding: 0; 
                display: inline-flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
                margin: 10px;
                }
    
                .btn-return::after {
                position: absolute;
                bottom: 100%; 
                left: 50%; 
                transform: translateX(-50%); 
                white-space: nowrap; 
                visibility: hidden; 
                opacity: 0;
                transition: opacity 0.2s, visibility 0.2s; 
                border-radius: 4px;
                font-size: 12px;
                }
                
                .btn-return:hover {
                background-color: white; 
                color:var(--red-icot);   
                }
                
                .btn-return:hover::after {
                visibility: visible;
                opacity: 1;
                }
  </style>