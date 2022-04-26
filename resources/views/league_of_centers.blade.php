@extends('layouts.logged')

@section('content')

<div id="alertErrorLeague" class="alert alert-danger" role="alert" style="display: none">
</div>

@if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

                <div class="card ">
                    <div class="card-header card-header-info card-header-text">
                        <div class="card-text">
                            <h4 class="card-title">LIGA DE CENTROS ICOT</h4>
                        </div>
                            <i class="material-icons" id="icInfo" style="color: #b61836">info</i>
                            <label id="infoLeague"></label>
                    </div>
                    <div class="card-body">
                        <form id="leagueForm"  method="POST" >
                        
                            @csrf
                            @method('POST')
                        <div class="row px-5">
                            <div class="form-group col-md-2 centre_picker">
                                <div class="dropdown bootstrap-select">
                                    <select class="selectpicker" name="centre_id" id="centre_id_picker" data-size="7" data-style="btn btn-primary btn-round" title=" Seleccione Centro" tabindex="-98">

                                        @foreach ($centres as  $centre)
                                        <option value="{{$centre->id}}">{{$centre->name}}</option>
                                        @endforeach

                                    </select>
                                    <input type="hidden" name="centre" id="centre"/>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <div class="dropdown bootstrap-select">
                                    <select class="selectpicker" name="datepickerType" id="datepickerType" data-size="7" data-style="btn btn-primary btn-round" title=" Mensual / Anual" tabindex="-98">
                                        <option value="1" selected>Mensual</option>
                                        <option value="2">Anual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group date px-2" style="padding-top: 15px;">
                                <input id="monthYearPicker" class='form-control' type="text"  placeholder="yyyy/mm" style="width: 60px; display:inline; width: 60px" />
                            </div>
                            <div class="form-group date px-2" style="padding-top: 15px;">
                                <input id="yearPicker" class='form-control' type="text"  placeholder="yyyy"  style="width: 60px; display:inline; width: 60px"/>
                            </div>
                        </div>
                            <div class="row mt-2 px-5">
                                <div class="col-md-5">
                                    <button id="btnClear" href="#" class="btn btn-fill btn-warning">
                                        {{ __('Limpiar formulario') }}
                                        </button> 
                                    <button id="btnSubmit" type="submit" class="btn btn-fill btn-success">{{ __('Ver Liga de Centros') }}</button>
                                    <button id="btnSubmitLoad" type="submit" class="btn btn-success" style="display: none">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        {{ __('Obteniendo datos...') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <label id="centreName"></label>
                <div class="card-header-table" style="display: none;">
                    <table id="league-month-datatable" class="table table-striped table-bordered league-month-datatable col-lg-12">
                    <thead>
                        <tr id="trLeague">
                            <th name="position">Posición</th>
                            <th name="centre">Centro</th>
                            <th name="points">Puntos</th>                           
                            <th name="average">Promedio coeficiente de venta</th>
                        </tr>
                    </thead>
                    <tbody id="bodyContent">
                    </tbody>
                    </table>
                    <table id="league-centre-datatable" class="table table-striped table-bordered league-centre-datatable col-lg-12">
                        <thead>
                            <tr id="trLeague">
                                <th name="month">Mes</th>
                                <th name="points">Puntos Recibidos</th>                           
                                <th name="cv">Coeficiente de venta</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>  
                </div>
            </div>
        </div>
    </div>       
</div>
<style>
    .month-picker-open-button {
        vertical-align: middle !important; 
    }
    button.ui-datepicker-trigger {
        border: none !important; 
    }
    

    .ui-datepicker-calendar { display: none; }

    #trLeague{
        background-color: #d50000;
        color: white;
        font-weight: 900;
        text-align: center;
        width: 90%;
        margin-top: 0px !important; 
    }

    #centreName{
        display: block;
        text-align: center;
        font-weight: bold;
        font-size: xx-large;
        font-family: monospace;
        color: black;
    }

    td{
        font-weight: bold;
        text-align: center;
    }

    #infoLeague{
        float: right;
        margin: 16px;
        text-align: center;
        font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        color: black;
    }

    #icInfo{
        float: right;
        margin-top: 6px;
    }
</style>

<script type="text/javascript">

    window.onload = function() {
            window.setTimeout(function() {
            var btn = $('#btnSubmit');
            btn.click();
        }, 600);
    };

    $(function () {

        $('#centreName').hide();
        var d = new Date();
        $( "#yearPicker" ).datepicker( "destroy" );
        $('#yearPicker').show();

        $(".nav-item").each(function(){
            $(this).removeClass("active");
        });
        $('#pagesReport').addClass('show');
        $('#centerLeague').addClass('active');
        $('.centre_picker').hide();

        function clearForms()
        {
            $('select').val('');
            $('select').selectpicker("refresh");
            $('.card-header-table').hide();
            $('#yearPicker').val(d.getFullYear());
            $('#centreName').hide();
            $('.centre_picker').show();
            $('#datepickerType').selectpicker('val', 2);
        }

        $("#btnClear").on('click', function(e){
            e.preventDefault();
            clearForms();
        });   

        $("#btnSubmit").on('click', function(e){
            $('#alertErrorLeague').hide();
            e.preventDefault(); 
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            $('#centre').val($( "#centre_id_picker option:selected" ).text());
            $('.card-header-table').show();

            params = {};
            params["centre"] = $('#centre').val();
            if ($("#datepickerType").val() == 1) {
                $('#centre').selectpicker('refresh');
                params['centre'] = null;
            }
            
            if(!params['centre']){
                $('#centreName').hide();
                $('#league-centre-datatable').hide();
                $('#league-centre-datatable_filter').hide();
                $('#league-centre-datatable_length').hide();
                $('#league-centre-datatable_paginate').hide();
                $('#league-centre-datatable_info').hide();
                $('#league-month-datatable').show();
                drawLeague(null, '.league-month-datatable');
            } else{
                $('#centreName').show();
                document.getElementById('centreName').innerHTML = params['centre'];
                $('#league-month-datatable').hide();  
                $('#league-month-datatable_filter').hide();
                $('#league-month-datatable_length').hide();
                $('#league-month-datatable_paginate').hide();
                $('#league-month-datatable_info').hide();
                $('#league-centre-datatable').show(); 
                drawLeague(params['centre'], '.league-centre-datatable');
            }
        });

        var d = new Date();
        $( "#yearPicker" ).datepicker( "destroy" );
        $( "#yearPicker" ).hide();
        showMonthYearPicker();

        $("#datepickerType").on('change', function(e){
            var type = $(this).val(); 
            if (type == 1 ) {
                showMonthYearPicker();
                $( "#yearPicker" ).datepicker( "destroy" );
                $( "#yearPicker" ).hide();
                $('#monthYearPicker').show();
                $('.centre_picker').hide();
            } else {
                showYearPicker();
                $('#yearPicker').show();
                $('#monthYearPicker').hide();
                $('.centre_picker').show();
            }
        });

        function showMonthYearPicker() {
            var textMonthYear = (d.getMonth()+1) + '/' + d.getFullYear()   ;  
            $('#monthYearPicker').val(textMonthYear);
            // Default functionality.
            $('#monthYearPicker').MonthPicker();
            $('#monthYearPicker').MonthPicker({
                ShowIcon: true,
                Button: '<img src="assets/img/calendar.gif" title="Select date" />'
            }); 
        }

        function showYearPicker() {  
            $('#monthYearPicker').MonthPicker({
                ShowIcon: false
            }); 

            var textYear = d.getFullYear();
            $.datepicker.setDefaults($.datepicker.regional['es']);
            $('#yearPicker').val(textYear);
            $('#yearPicker').datepicker({
                showOn: "button",
                buttonImage: "assets/img/calendar.gif",
                selectedDate: false,
                changeMonth: false,
                changeYear: true,
                formatDate: 'yy',
                showButtonPanel: true,
                closeText:'Seleccionar',
                currentText: 'Año actual',
                onClose: function(dateText, inst) {
                    $(this).val($.datepicker.formatDate("yy", new Date(inst['selectedYear'], 0, 1)));
                },
            });
        }

        function drawLeague(centre, idDataTable){
            var params = {};
            params["_token"] = "{{ csrf_token() }}";

                if (centre) {
                    params["centre"] = centre;
                    params["year"] = $("#yearPicker").val()
                    params["month"] = null;
                    document.getElementById('infoLeague').innerHTML = 'DETALLE ANUAL DE CENTRO';
                } else {
                    if($('#monthYearPicker').is(":visible")) {
                    monthYear = $("#monthYearPicker").val();
                    dateSearch = monthYear.split('/');
                    params["month"] = dateSearch[0];
                    params["year"] = dateSearch[1];
                    document.getElementById('infoLeague').innerHTML = 'CLASIFICACIÓN MENSUAL';
                } else{
                    params["year"] = $("#yearPicker").val()
                    params["month"] = null;
                    document.getElementById('infoLeague').innerHTML = 'CLASIFICACIÓN ANUAL';
                }
            }

            lenMenu = [];
            url = "";
            columnss = [];

            if(centre) {
                // Detalle de clasificacion anual 1 centro
                $('league-centre-datatable').DataTable();
                $('league-centre-datatable').DataTable().ajax.reload();
                lenMenu = [12];
                url = "{{ route('league.detailsCentreLeague') }}";
                columnss = [
                        { data: 'month', name: 'month', 
                        render: {
                            display: 'month',
                            sort: 'id',
                        }}, 
                        
                        { data: 'points', name: 'points'},  
                        { data: 'cv', name: 'cv'}
                ];
            } else {
                // Clasificación anual de todos los centros (Acumulado anual)
                    $('league-month-datatable').DataTable();
                    $('league-month-datatable').DataTable().ajax.reload();
                    lenMenu = [25, 10, 15];
                    url = "{{ route('league.generateLeague') }}";
                    columnss = [{ data: 'position', name: 'position'},
                                { data: 'centre', name: 'centre'},
                                { data: 'points', name: 'points'}, 
                                { data: 'average', name: 'average'}
                            ];
            }

            $(idDataTable).dataTable({
                    lengthMenu: lenMenu,
                    processing: true,
                    bDestroy: true,
                    ordering: false,
                    language: {
                        "emptyTable": "No hay datos disponibles en la tabla",
                        "paginate": {
                            "previous": "Anterior",
                            "next": "Siguiente"
                        },
                        "infoEmpty": "Sin registros",
                        "search": "Buscar:",
                        "info": "",
                        "loadingRecords": "Cargando...",
                        "processing": "Procesando, espere...",
                        "lengthMenu": "Mostrando _MENU_ registros",
                        "infoFiltered": ""
                    },     
                    ajax: {
                        url: url,
                        type: 'post',
                        data: params,
                    }, columns: columnss,
                    initComplete: function (data) {                
                        if(data.jqXHR.statusText === 'OK') {
                            $('#btnSubmitLoad').hide();
                            $('#btnSubmit').show();                    
                        } else{
                            var response = JSON.parse(xhr.responseText);
                            $('#alertErrorLeague').text(response.errors); 
                            $('#alertErrorLeague').show();
                            $('#btnSubmitLoad').hide();
                            $('#btnSubmit').show();
                            timeOutAlert($('#alertErrorLeague')); 
                        }
                        this.api().columns().every(function () {
                            var column = this;
                            var input = document.createElement("input");
                            $(input).appendTo($(column.footer()).empty())
                            .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column
                                        .search( val ? '^'+val+'$' : '', true, false )
                                        .draw();
                            });  
                        });  
                    }
                });
        }
    });
</script>

@endsection