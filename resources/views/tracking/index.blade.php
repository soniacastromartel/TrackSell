
@extends('layouts.logged')
@section('content')
    @include('inc.navbar')

    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">


    <div class="content">
        <div class="container-fluid">
            <div class="col-12">
                <div class="card" style="margin-top:120px">
                    <div class="card-header card-header-danger">

                        <h4 class="card-title">Registro de Ventas</h4>

                    </div>

                    <div class="card-body">
                        <form id="exportTracking" action="{{ route('tracking.export') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="informes-container">

                                <div class="date-informes-container">

                                    <label class="label" for="dateFrom" style="padding: 10px" >Fecha desde </label>
                                    <div class="icon-container">
                                        <input type="date" id="date_from" name="date_from" max="3000-12-31"
                                            min="1000-01-01"class="form-date">
                                        <span id="icon-date-left" class="material-symbols-outlined"> calendar_month</span>
                                    </div>
                                    </input>

                                    <label class="label" for="dateTo" style="padding: 10px" >Fecha hasta </label>
                                    <div class="icon-container">
                                        <input type="date" id="date_to" name="date_to" max="3000-12-31"
                                            min="1000-01-01" class="form-date">
                                        <span id="icon-date-left" class="material-symbols-outlined"> calendar_month</span>
                                    </div>
                                    </input>
                                </div>


                                <div class="picker-btn-container">

                                    <div class="picker-container">

                                        <select class="selectpicker" name="centre_id" id="centre_id" data-size="7"
                                            data-style="btn btn-red-icot btn-round" title=" Centro" tabindex="-98">
                                            @if ($user->rol_id != 1)
                                                @foreach ($centres as $centre)
                                                    @if ($centre->id == $user->centre_id)
                                                        <option class="text-uppercase" value="{{ $centre->id }}" selected
                                                            @if (isset($tracking) && $centre->id == $tracking->centre_id) selected="selected" @endif>
                                                            {{ $centre->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @else
                                                @foreach ($centres as $centre)
                                                    <option class="text-uppercase" value="{{ $centre->id }}"
                                                        @if (isset($tracking) && $centre->id == $tracking->centre_id) selected="selected" @endif>
                                                        {{ $centre->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <input type="hidden" name="centre" id="centre" />
                                        <select class="selectpicker" name="employee_id" id="employee_id" data-size="7"
                                            data-style="btn btn-red-icot btn-round" title=" Empleado" tabindex="-98">
                                            <option>SIN SELECCION </option>
                                            @if ($user->rol_id != 1)
                                                @foreach ($employees as $employee)
                                                    @if ($employee->centre_id == $user->centre_id)
                                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                    @endif
                                                @endforeach
                                            @else
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <input type="hidden" name="employee" id="employee" />

                                        <select class="selectpicker" name="service_id" id="service_id" data-size="7"
                                            data-style="btn btn-red-icot btn-round" title=" Servicio" tabindex="-98">
                                            <option>SIN SELECCION </option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}"
                                                    @if (isset($tracking) && $service->id == $tracking->service_id) selected="selected" @endif>
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                        <input type="hidden" name="service" id="service" />

                                        <select class="selectpicker" name="patient_name" id="patient_name" data-size="7"
                                            data-style="btn btn-red-icot btn-round" title=" Paciente" tabindex="-98">
                                            <option>SIN SELECCION </option>
                                            @foreach ($patients as $patient)
                                                <option value="{{ $patient->patient_name }}">
                                                    {{ $patient->patient_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <select class="selectpicker" name="state_id" id="state_id" data-size="7"
                                            data-style="btn btn-red-icot btn-round" title=" Estado" tabindex="-98">
                                            <option>SIN SELECCION </option>
                                            @foreach ($states as $state)
                                                <option class="text-uppercase" value="{{ $state->texto }}">
                                                    {{ $state->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="btn-container-box">

                                            <button id="btnClear" href="#" class="btn-refresh">
                                                Limpiar Formulario
                                                <span id=icon-refresh class="material-icons">
                                                    refresh
                                                </span>
                                            </button>

                                            {{-- <button id="btnSubmitFind" type="submit" class="btn-search">
                                                Buscar
                                                <span id=icon-search class="material-icons">
                                                    search
                                                </span>
                                            </button> --}}

                                            {{-- <button id="btnSubmitFindLoad" type="submit" class="btn-search"
                                                style="display: none">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                            </button> --}}

                                            <button id="btnSubmit" type="submit" class="btn-export">
                                                Exportar
                                                <span id=icon-export class="material-icons">file_download</span>
                                            </button>

                                            <button id="btnSubmitLoad" type="submit" class="btn-export"
                                                style="display: none">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                            </button>
                                     
                                    </div>

                                </div>

                            </div>
                        </form>
                    </div>
                </div>
   
                <div class="col-12 text-right">
                        <a href="{{ route('tracking.create') }}" id="btnNewTracking" class="service-btn-add"><span
                                class="material-icons">add</span></a>
                    </div> 

            <div class="table-responsive">
                <table class="table table-striped table-bordered tracking-datatable ">
                    <thead class="table-header">
                        <tr>
                            <th>Centro Prescriptor</th>
                            <th>Empleado</th>
                            <th>H.C.</th>
                            <th>Paciente</th>
                            <th>Servicio</th>
                            <th>Estado</th>
                            <th>F. Inicio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var table;

        var columnsFilled = [];
        columnsFilled.push({
            data: 'centre',
            name: 'centre'
        });
        columnsFilled.push({
            data: 'employee',
            name: 'employee',
            searchable: true
        });
        columnsFilled.push({
            data: 'hc',
            name: 'hc'
        });
        columnsFilled.push({
            data: 'patient_name',
            name: 'patient_name'
        });
        columnsFilled.push({
            data: 'service',
            name: 'service',
            searchable: true
        });
        columnsFilled.push({
            data: 'state',
            name: 'state',
            searchable: true
        });
        columnsFilled.push({
            name: 'started_date',
            data: 'started_date'
        });

        columnsFilled.push({
            data: 'action',
            name: 'action',
            searchable: true,
            
        });


        $(function() {

            setDate();
            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $('#pagesTracking').addClass('show');
            $('#trackingStarted').addClass('active');
            $('#centre_id, #employee_id, #service_id, #patient_name, #state_id').on('change', function() {
                getTrackingData();
            });
            var state = "{{ collect(request()->segments())->last() }}";
            state = state.split("_")[1];

             var tableHtml = '';

             tableHtml = '<tr><th>Centro Prescriptor</th></tr>';
             getTrackingData();
         
            // Buscar
            // $("#btnSubmitFind").on('click', function(e) {
            //     e.preventDefault();

            //     //$("#finalValidationForm").attr('action','{{ route('tracking.index_validation_final') }}');
            //     $('#btnSubmitFind').hide();
            //     $('#btnSubmitFindLoad').show();
            //     $('#btnSubmitFindLoad').prop('disabled', true);
            //     //$('#centre').val($( "#centre_id option:selected" ).text());
            //     getTrackingData();

            // });

            function clearForms() {
                setDate();
                $('select#centre_id').val('');
                $('select#state_id').val('');
                $('select#employee_id').val('');
                $('select#service_id').val('');
                $('select#patient_name').val('');
                $('select#centre_id').selectpicker("refresh");
                $('select#state_id').selectpicker("refresh");
                $('select#employee_id').selectpicker("refresh");
                $('select#service_id').selectpicker("refresh");
                $('select#patient_name').selectpicker("refresh");
                $('input[type="search"]').val('');
                // $('input[type="search"]').selectpicker("refresh");
                //table.ajax.draw();
                 table.search('').draw();
                 table.ajax.reload();
             
            }


            $("#btnClear").on('click', function(e) {

                e.preventDefault();
                clearForms();
            });
        });


        //FIXME este método no se está usando al parecer
        function updateDateTracking(state, trackingId, back) {
            $('#alertErrorTrackingDate').hide();
            var trackingDate = $("#tracking_date_" + trackingId).val();
            $.ajax({
                url: 'updateState/' + state + '/' + trackingId + '/' + trackingDate + '/' + back,
                type: 'get',
                success: function(response, textStatus, jqXHR) {
                    // if success, HTML response is expected, so replace current
                    table.columns.adjust().draw();
                    return;
                    if (textStatus === 'success') {
                        //$("div.alert-success").show();
                        //alert(response.mensaje);
                        window.location = response.url;
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    $('#alertErrorTrackingDate').text(response.mensaje);
                    $('#alertErrorTrackingDate').show().delay(2000).slideUp(300);
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmitFindLoad').hide();
                    $('#btnSubmitFind').show();
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error' + jqXHR.responseText);

            });
        }

        function setDate() {
            var date = new Date();
            var day = date.getDate();
            var month = date.getMonth() + 1;
            var year = date.getFullYear();
            var startDay = 20;

            day = day < 10 ? '0' + day : day;
            month = month < 10 ? '0' + month : month;

            var dateTo = year + '-' + month + '-' + day;

            var previousMonth = month;
            var previousYear = year;

            if (month === '01' && day < 21) {
                previousMonth = '12';
                previousYear = year - 1;

            } else {
                previousMonth = parseInt(month, 10);
                previousMonth = (day < 21) ? previousMonth - 1 : previousMonth;
                previousMonth = previousMonth < 10 ? '0' + previousMonth : previousMonth.toString();
            }

            var dateFrom = previousYear + '-' + previousMonth + '-' + startDay;

            document.getElementById("date_from").value = dateFrom;
            document.getElementById("date_to").value = dateTo;
        }

        function getTrackingData() {
            if ($.fn.dataTable.isDataTable('.tracking-datatable')) {
                table = $('.tracking-datatable').DataTable();
            } else {
                table = $('.tracking-datatable').DataTable({
                    order: [6, "desc"],
                    processing: true,
                    serverSide: true,
                    language: {
                        "url": "{{ asset('dataTables/Spanish.json') }}"
                    },
                    ajax: {
                        url: '{{ route('tracking.index') }}',
                        type: "POST",
                        data: function(d) {
                            d._token = "{{ csrf_token() }}",
                                d.centre_id = $('#centre_id option:selected').val(),
                                d.employee = $('#employee_id option:selected').text(),
                                d.patient = $('#patient_name option:selected').val(),
                                d.service = $('#service_id option:selected').text(),
                                d.state = $('#state_id option:selected').text(),
                                date1 = $('#date_from').val().replaceAll('-', '/');
                            date2 = $('#date_to').val().replaceAll('-', '/');
                            d.dateFrom = (date1),
                                d.dateTo = (date2),
                                d.search = $('input[type="search"]').val()
                        },
                        dataSrc: function(json) {
                            $('#btnSubmitFind').show();
                            $('#btnSubmit').show();
                            $('#btnSubmitLoad').hide();
                            $('#btnSubmitFindLoad').hide();

                            return json.data;
                        }
                    },
                    // autoWidth:true,
                    columns: columnsFilled,
                    columnDefs: [{
                            targets: 3,
                            className: 'myclass'
                            // render:function(data, type, row){
                            //     d= data.split('')[0].toUpperCase() + data.slice(1)
                            //     var d = data.toLowerCase();
                            //     return d;
                            // }

                        },
                        // {
                        //     targets:  8,
                        //     // data: "cancellation_date",
                        //     type: "date",
                        //     render: function(data, type, row) {

                        //         if (data != null) {
                        //             var datetime = moment(data, 'YYYY-M-D');
                        //             var displayString = moment(datetime).format('D-M-YYYY');

                        //             if (type === 'display' || type === 'filter') {
                        //                 return displayString;
                        //             } else {
                        //                 return datetime; // for sorting
                        //             }
                        //         } else {
                        //             return null;
                        //         }

                        //     }
                        // },
                        {
                            width: "10%",
                            targets: 0
                        },
                        {
                            width: "15%",
                            targets: [1, 3, 4]
                        },
                        {
                            width: "5%",
                            targets: 2
                        },

                        {
                            width: "5%",
                            targets: 5
                        },
                        {
                            width: "10%",
                            targets:  7
                        },
                        {
                            targets: -1,
                            width: '30%'
                        },
                        {
                            targets: '_all',
                            className: 'dt-body-center',
                        }

                    ],
                    search: {
                        "regex": true,
                        "smart": true
                    },
                    initComplete: function() {
                        this.api().columns().every(function() {
                            var column = this;
                        });
                    }
                });
            }
            table.columns.adjust().draw();
        }

        // <!--Export-->
        $("#btnSubmit").on('click', function(e) {
            e.preventDefault();
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            $('#centre').val($("#centre_id option:selected").text());
            $('#employee').val($("#employee_id option:selected").text());
            $('#service').val($("#service_id option:selected").text());
            $('#patient_name').val($("#patient_name option:selected").val());
            $('#trackingState').val($("#state_id option:selected").val());

            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["centre"] = $('#centre').val();
            params["employee"] = $('#employee').val();
            params["service"] = $('#service').val();
            params["patient_name"] = $('#patient_name').val();
            params["trackingState"] = $("#state_id option:selected").val();
            params["date_from"] = $('#date_from').val();
            params["date_to"] = $('#date_to').val();


            $.ajax({
                url: $("#exportTracking").attr('action'),
                type: 'post',
                data: params,
                // dataType: 'binary',
                xhrFields: {
                    'responseType': 'blob'
                },
                success: function(data, textStatus, jqXHR) {
                    // if success, HTML response is expected, so replace current
                    if (textStatus === 'success') {
                        $('#btnSubmitLoad').hide();
                        $('#btnSubmit').show();

                        var link = document.createElement('a'),
                            filename = 'tracking.xls';
                        link.href = URL.createObjectURL(data);
                        link.download = filename;
                        link.click();
                    }
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error' + jqXHR.responseText)

            });
        });
    </script>

@endsection

<style>

    
.btn-add-container{
   position: relative;
   display: flex;
  justify-content: flex-end; 
}

.service-btn-add {
  position: absolute;
  bottom: 50px;
  background-color:var(--red-icot);
  border: none;
  color: white !important;
  cursor: pointer;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  align-items: center;
  justify-content: center;
  box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
}
/* ADD BUTTON */

.service-btn-add::after {
  position: absolute;
  transform: translateX(-50%);
  white-space: nowrap;
  visibility: hidden;
  opacity: 0;
  transition: opacity 0.2s, visibility 0.2s;
  color: white;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 12px;
}
.service-btn-add:hover  {
  background-color: white;
  color: var(--red-icot) !important;
}
.service-btn-add::after  {
  visibility: visible;
  opacity: 1;
}
.content {
  background-image: url(/assets/img/background_continue.png) !important;
  background-position: center center !important;
  background-size: 1000px;
  height: 300vh !important;
}

/*VIEW : traking/index,requestChange,calculateServices*/

.informes-container,
.solicitud-container,
.service-container {
  display: flex;
  flex-direction: row;
  min-height: 420px;
}

.date-informes-container,
.date-solicitud-container,
.date-services-container {
  /* margin-left: 80px; */
  padding-top: 50px;
  background-image: url(/assets/img/calendarImage.png);
  background-size: cover;
  background-repeat: no-repeat;
  min-width: 400px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.form-date {
  position: relative !important;
  background-color: var(--info) !important;
  width: 230px;
  height: 45px;
  margin: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center !important;
  border: none;
  border-radius: 50px;
  padding-left: 20px;
}

.icon-container {
  position: relative;
  display: flex;
}


.picker-btn-container {
  width: 100%;
  display: flex;
}

.observaciones-container {
  border: 4px solid var(--red-icot);
  box-shadow: 0px 4px 8px rgba(255, 0, 0, 0.6);
  padding: 30px;
  border-radius: 20px;
  flex-direction: column;
  display: flex;
  justify-content: center;
  align-self: center;
  height: 200px;

}

.picker-container {
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
}


.col-md-6 {
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
}

.myclass {
  text-transform: capitalize;
}


#lbl {
  color: black;
  font-weight: 600;
  font-family: "Helvetica", "Arial", sans-serif;
  font-size: 12px;
}

.card-container {
  display: flex;
}


.tracking-validation-datatable tr > :nth-child(6) {
  color: #959ba3;
}

.month-picker-open-button {
  margin-right: 15px;
}

.actions-container {
  display: flex;
  align-items: flex-end;
  justify-content: flex-end;
  height: 100%;
}


.btn-container-box {

    display: flex;
    flex-direction: column;
    justify-content: flex-end;

  
}
.date-container {
  background-color: #959ba3;
  width: 25%;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
}


#subLabel {
  color: var(--red-icot);
  font-weight: bold !important;
  font-size: 11px;
}
#lbl {
  color: black;
  font-weight: 800;
  font-family: "Helvetica", "Arial", sans-serif;
  margin-top: 25px;
  font-size: 12px;
}

#containerBtns {
  margin-top: 40px;
}

.btn-back-container {
  display: flex;
  justify-content: flex-end;
}

/*VIEW INDEX VALIDATION*/

.btn-container{
  display: flex;
  flex-direction: row;
  justify-content: space-around;

}

/*VIEW CALCULATE INCENTIVE*/
    </style>