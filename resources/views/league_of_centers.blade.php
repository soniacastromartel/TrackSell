
@extends('layouts.logged')
@section('content')
@include('inc.navbar')
@include('common.alert')

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">

<div id="alertErrorLeague" class="alert alert-danger" role="alert" style="display: none">
</div>

<div class="content">
    <div class="container-fluid" style="margin-top:120px" >
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header card-header-info card-header-text">
                        <div class="card-text" style="border-radius: 50px" >
                            <h4 class="card-title">Informes</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="leagueForm" method="POST">
                            @csrf
                            @method('POST')
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <div class="dropdown bootstrap-select">
                                        <select class="selectpicker" name="datepickerType" id="datepickerType" data-size="7" data-style="btn btn-red-icot btn-round" title=" Mensual / Anual" tabindex="-98">
                                            <option value="1" selected>Mensual</option>
                                            <option value="2">Anual</option>
                                        </select>
                                    </div>
                              
                                <div class="form-group  centre_picker">
                                    <div class="dropdown bootstrap-select">
                                        <select class="selectpicker" name="centre_id" id="centre_id_picker" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Centro" tabindex="-98">

                                            @foreach ($centres as $centre)
                                            <option value="{{$centre->id}}">{{$centre->name}}</option>
                                            @endforeach

                                        </select>
                                        <input type="hidden" name="centre" id="centre" />
                                    </div>
                                </div>
                            </div>
                                <div id="monthYearPickerContainer">
                                    <div class="input-group date mt-2">
                                        <input id="monthYearPicker" class='form-control' type="text" placeholder="yyyy/mm" />
                                        <span id="icon-date" class="material-symbols-outlined">calendar_month</span>
                                        <input type="hidden" name="monthYear" id="monthYear" />
                                    </div>
                                </div>
                                <div id="yearPickerContainer">
                                    <div class="input-group date mt-2">
                                    <input id="yearPicker" class='form-control' type="text" placeholder="yyyy" />
                                    <span id="icon-date" class="material-symbols-outlined"> calendar_month</span>
                                </div>
                                </div>
                     
                       

                                <div class="col-lg-5" style="display:flex;flex-direction:column;align-items:flex-end"; >

                                    {{-- <button id="btnSubmit" type="submit" class="btn-search">
                                        <span id="icon-search" class="material-icons">
                                            search</span> {{ __('Buscar') }}
                                    </button>
                                    <button id="btnSubmitLoad" type="submit" class="btn-search" style="display: none">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </button> --}}

                                    <button id="btnSubmitExport" type="submit" class="btn-export">
                                        <span id="icon-export" class="material-icons">
                                            file_download
                                        </span> {{ __('Exportar') }}
                                    </button>


                                    <button id="btnClear" href="#" class="btn-refresh">
                                        <span id="icon-refresh" class="material-icons">refresh
                                        </span> {{ __('Limpiar Formulario') }}
                                    </button>

                                </div>

                            </div>


                        </form>
                    </div>
                </div>
                <div class="col-sm-3" style="margin-left: 560px;">
                    <label id="centreName" class=""></label>
                </div>
                <div class="card-header-table" style="display: none;">
                    <table id="league-month-datatable" class="table table-striped table-bordered league-month-datatable col-lg-12">
                        <thead class="table-header">
                            <tr>
                                <th name="position">Posición</th>
                                <th name="centre">Centro</th>
                                <th name="points">Puntos</th>
                                <th name="average" id="average">Coeficiente de Venta</th>
                            </tr>
                        </thead>
                        <tbody id="bodyContent">
                        </tbody>
                    </table>
                    <table id="league-centre-datatable" class="table table-striped table-bordered league-centre-datatable col-lg-12">
                        <thead class="table-header">
                            <tr>
                                <th name="month">Mes</th>
                                <th name="points">Puntos Recibidos</th>
                                <th name="cv">Coeficiente de Venta</th>
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


    button.ui-datepicker-trigger {
        border: none !important;
    }

 

    #centreName {
        display: block;
        text-align: center;
        font-weight: bold;
        font-size: xx-large;
        font-family: monospace;
        color: white !important;
        border-radius: 50px !important;
        min-height: 30px !important;
        padding: 1px !important;
        background-color: var(--red-icot) !important;
        box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1) !important;
    }

    td {
        font-weight: bold;
        text-align: center;
    }


    #icInfo {
        float: right;
        margin-top: 6px;
    }

    .card .card-header {
        width: unset;
        
    }
</style>

<script type="text/javascript">
  $(function() {
    $('#centreName').hide();
    var d = new Date();
    $("#yearPicker").datepicker("destroy");
    $('#yearPickerContainer').show();

    $(".nav-item").each(function() {
        $(this).removeClass("active");
    });
    $('#pagesReport').addClass('show');
    $('#centerLeague').addClass('active');
    $('.centre_picker').hide();

    function clearForms() {
        $('#centreName').hide();
        $('.centre_picker').hide();
        $('#datepickerType').selectpicker('val', 1);
        $('#league-centre-datatable').hide();
        $('#league-month-datatable').hide();
        drawLeague(null, '.league-month-datatable');
        $('#league-month-datatable').show();
    }

    $("#btnClear").on('click', function(e) {
        e.preventDefault();
        clearForms();
    });

    $("#datepickerType, #centre_id_picker, #monthYearPicker, #yearPicker").on('change', function() {
        loadData();
    });

    function loadData() {
        $('#alertErrorLeague').hide();
        $('#btnSubmit').hide();
        $('#btnSubmitLoad').show();
        $('#btnSubmitLoad').prop('disabled', true);
        $('#centre').val($("#centre_id_picker option:selected").text());
        $('.card-header-table').show();

        let params = {};
        params["centre"] = $('#centre').val();

        if ($("#datepickerType").val() == 1) {
            $('#centre').selectpicker('refresh');
            params['centre'] = null;
            params["state"] ='mensual';
        } else {
            params["state"] = 'anual';
        }

        if (!params['centre']) {
            $('#centreName').hide();
            $('#league-centre-datatable').hide();
            $('#league-centre-datatable_filter').hide();
            $('#league-centre-datatable_length').hide();
            $('#league-centre-datatable_paginate').hide();
            $('#league-centre-datatable_info').hide();
            $('#league-month-datatable').show();
            drawLeague(null, '.league-month-datatable');
        } else {
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
    }

    var d = new Date();
    $("#yearPicker").datepicker("destroy");
    $("#yearPickerContainer").hide();
    showMonthYearPicker();

    $("#datepickerType").on('change', function(e) {
        var type = $(this).val();
        if (type == 1) {
            showMonthYearPicker();
            $("#yearPicker").datepicker("destroy");
            $("#yearPickerContainer").hide();
            $('#monthYearPickerContainer').show();
            $('.centre_picker').hide();
        } else {
            showYearPicker();
            $('#yearPickerContainer').show();
            $('#monthYearPickerContainer').hide();
            $('.centre_picker').show();
        }
        loadData();
    });

    function showMonthYearPicker() {
        var textMonthYear = (d.getMonth() + 1) + '/' + d.getFullYear();
        $('#monthYearPicker').val(textMonthYear);
        $('#monthYearPicker').MonthPicker({
            ShowIcon: false,
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
            selectedDate: false,
            changeMonth: false,
            changeYear: true,
            formatDate: 'yy',
            showButtonPanel: true,
            closeText: 'Seleccionar',
            currentText: 'Año actual',
            onClose: function(dateText, inst) {
                $(this).val($.datepicker.formatDate("yy", new Date(inst['selectedYear'], 0, 1)));
            },
        });
    }

    function drawLeague(centre, idDataTable) {
        var params = {};
        params["_token"] = "{{ csrf_token() }}";
        params["state"] = $("#datepickerType").text();

        if (centre) {
            params["centre"] = centre;
            params["year"] = $("#yearPicker").val()
            params["month"] = null;
        } else {
            if ($('#monthYearPicker').is(":visible")) {
                let monthYear = $("#monthYearPicker").val();
                let dateSearch = monthYear.split('/');
                params["month"] = dateSearch[0];
                params["year"] = dateSearch[1];
            } else {
                params["year"] = $("#yearPicker").val();
                params["month"] = null;
            }
        }

        let lenMenu = [];
        let url = "";
        let columnss = [];

        if (centre) {
            $('league-centre-datatable').DataTable();
            $('league-centre-datatable').DataTable().ajax.reload();
            lenMenu = [12];
            url = "{{ route('league.detailsCentreLeague') }}";
            columnss = [
                { data: 'month', name: 'month', render: { display: 'month', sort: 'id' } },
                { data: 'points', name: 'points' },
                { data: 'cv', name: 'cv' }
            ];
            order = false;
        } else {
            $('league-month-datatable').DataTable();
            $('league-month-datatable').DataTable().ajax.reload();
            lenMenu = [25, 10, 15];
            url = "{{ route('league.generateLeague') }}";
            columnss = [
                { data: 'position', name: 'position' },
                { data: 'centre', name: 'centre' },
                { data: 'points', name: 'points' },
                { data: 'average', name: 'average' }
            ];
            order= true;
        }

        $(idDataTable).dataTable({
            lengthMenu: lenMenu,
            processing: true,
            bDestroy: true,
            ordering: order,
            language: {
                "emptyTable": "No hay datos disponibles en la tabla",
                "paginate": { "previous": "Anterior", "next": "Siguiente" },
                "infoEmpty": "Sin registros",
                "search": "Buscar:",
                "info": "",
                "loadingRecords": "Cargando...",
                "processing": "Procesando, espere...",
                "lengthMenu": "Mostrando _MENU_ registros",
                "infoFiltered": ""
            },
            ajax: { url: url, type: 'post', data: params },
            columns: columnss,
            initComplete: function(data) {
                if (data.jqXHR.statusText === 'OK') {
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmit').show();
                } else {
                    var response = JSON.parse(xhr.responseText);
                    $('#alertErrorLeague').text(response.errors);
                    $('#alertErrorLeague').show().delay(2000).slideUp(300);
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmit').show();
                }
                this.api().columns().every(function() {
                    var column = this;
                    var input = document.createElement("input");
                    $(input).appendTo($(column.footer()).empty()).on('change', function() {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });
                });
            }
        });
    }

    // Llamar a loadData() al cargar la página
    loadData();
});

</script>

@endsection