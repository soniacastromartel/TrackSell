@extends('layouts.logged')
@section('content')
    @include('inc.navbar')

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">

    <div class="content">
        <div class="container-fluid" style="margin-top:120px">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header card-header-info card-header-text">
                            <div class="card-text" style="border-radius: 50px">
                                <h4 class="card-title">Liga de Centros</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="leagueForm" method="POST">
                                @csrf
                                @method('POST')
                                <div class="row">
                                    {{-- <input type="checkbox" name="month_date" id="monthly" value="mensual" checked> Mensual
                                    <br />
                                    <input type="checkbox" name="year_date" id="yearly" value="anual"> Anual <br /> --}}
                                    <div class="form-group col-md-3">
                                        <div class="dropdown bootstrap-select">
                                            <select class="selectpicker" name="datepickerType" id="datepickerType"
                                                data-size="7" data-style="btn btn-red-icot btn-round"
                                                title=" Mensual / Anual" tabindex="-98">
                                                <option value="1" selected>Mensual</option>
                                                <option value="2">Anual</option>
                                            </select>
                                        </div>

                                        <div class="form-group  centre_picker">
                                            <div class="dropdown bootstrap-select">
                                                <select class="selectpicker" name="centre_id" id="centre_id_picker"
                                                    data-size="7" data-style="btn btn-red-icot btn-round"
                                                    title=" Seleccione Centro" tabindex="-98">
                                                    @foreach ($centres as $centre)
                                                        <option value="{{ $centre->id }}">{{ $centre->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="centre" id="centre" />
                                            </div>
                                        </div>
                                    </div>
                                    <div id="monthYearPickerContainer">
                                        <div class="input-group date mt-2">
                                            <input id="monthYearPicker" class='form-control' type="text"
                                                placeholder="yyyy/mm" />
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

                                    <div class="col-lg-5" style="display:flex;flex-direction:column;align-items:flex-end";>

                                        <button id="btnSubmitExport" type="submit" class="btn-export">
                                            <span id="icon-export" class="material-icons">
                                                file_download
                                            </span> {{ __('Exportar') }}
                                        </button>
                                        <button id="btnSubmitLoad" type="submit" class="btn-export" style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
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
                    <div class="d-flex justify-content-center">
                        <label id="centreName"></label>
                    </div>
                    {{-- charts --}}
                    <div class="card mt-4" id="leagueChart">
                        <div class="chart-container">
                            <div>
                                <canvas id="chartLeague"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-4" id="centreChart">
                        <div class="chart-container">
                            <div>
                                <canvas id="chartCentreLeague"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="" id="leagueDatatable">
                        <div class="card-header-table" style="display: none;">

                            <table id="league-month-datatable"
                                class="table table-striped table-bordered league-month-datatable col-lg-12">
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
                        </div>
                    </div>
                    <div class="" id="centerLeagueDatatable">
                        <div class="card-header-table" style="display: none;">

                            <table id="league-centre-datatable"
                                class="table table-striped table-bordered league-centre-datatable col-lg-12">
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
                padding: 10px;
                display: block;
                text-align: center;
                font-weight: bold;
                font-size: xx-large;
                font-family: monospace;
                color: white !important;
                border-radius: 50px !important;
                background-color: var(--red-icot);
            }

            td {
                font-weight: bold;
                text-align: center;
            }

            .card .card-header {
                width: unset;

            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script type="text/javascript">
            $(function() {
                // handles sidebar selection
                $(".nav-item").each(function() {
                    $(this).removeClass("active");
                });
                $('#pagesReport').addClass('show');
                $('#centerLeague').addClass('active');

                var date = new Date();
                var textMonthYear = (date.getMonth() + 1) + '/' + date.getFullYear();
                var textYear = date.getFullYear();
                let currentChart = null;

                $('#centreName').hide();
                $('.centre_picker').hide();
                $("#yearPicker").datepicker("destroy");
                $("#yearPickerContainer").hide();

                $("#btnClear").on('click', function(e) {
                    e.preventDefault();
                    clearForms();
                });

                const chartConfigs = {
                    chartLeague: {
                        id: 'chartLeague',
                        type: 'bar',
                        labels: [], // Etiquetas generales
                        data: [], // Datos generales
                        label: '' // Dinámico, se setea en loadData
                    },
                    chartCentreLeague: {
                        id: 'chartCentreLeague',
                        type: 'bar',
                        labels: [], // Etiquetas específicas para centro
                        data: [], // Datos específicos para centro
                        label: '' // Dinámico, se setea en loadData
                    }
                };


                configureMonthYearPicker();
                loadData();

                $.datepicker.setDefaults($.datepicker.regional['es']);
                $('#yearPicker').val(textYear);
                $('#yearPicker').datepicker({
                    changeMonth: false,
                    changeYear: true,
                    showButtonPanel: false,
                    dateFormat: 'yy',
                    onClose: function(dateText, inst) {
                        var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                        $(this).val($.datepicker.formatDate('yy', new Date(year, 1, 1)));
                        loadData();
                    },
                    onChangeMonthYear: function() {
                        $(this).datepicker("hide");
                    }
                }).focus(function() {
                    $(".ui-datepicker-month").hide();
                    $(".ui-datepicker-calendar").hide();
                });

                $('#monthYearPicker').MonthPicker({
                    OnAfterChooseMonth: function() {
                        loadData();
                    }
                });

                $("#centre_id_picker").on('change', function(event) {
                    loadData();
                });

                let debounceTimer;

                function debounce(func, delay) {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(func, delay);
                }

                $("#datepickerType").on('change', function() {
                    debounce(() => {
                        $('#centre_id_picker').selectpicker('val', '');
                        if ($(this).val() == "1") {
                            configureMonthYearPicker();
                        } else {
                            configureYearPicker();
                        }
                        loadData();
                    }, 300);
                });


                function configureMonthYearPicker() {
                    setPickerVisibility({
                        yearPicker: false,
                        monthYearPicker: true,
                        centrePicker: false
                    });
                    $('#monthYearPicker').val(textMonthYear).MonthPicker({
                        ShowIcon: false,
                    });
                    destroyPicker("#yearPicker");
                }

                function configureYearPicker() {
                    setPickerVisibility({
                        yearPicker: true,
                        monthYearPicker: false,
                        centrePicker: true
                    });
                    $('#yearPicker').datepicker();
                    destroyPicker("#monthYearPicker");
                }

                function setPickerVisibility({
                    yearPicker,
                    monthYearPicker,
                    centrePicker
                }) {
                    $('#yearPickerContainer').toggle(yearPicker);
                    $('#monthYearPickerContainer').toggle(monthYearPicker);
                    $('.centre_picker').toggle(centrePicker);
                }

                function destroyPicker(selector) {
                    if ($(selector).data('datepicker')) {
                        $(selector).datepicker("destroy");
                    }
                }

                async function loadData() {
                    $('.card-header-table').show();
                    console.log("Loading data...");

                    const selectedCentre = $("#centre_id_picker option:selected").text();
                    const state = $("#datepickerType").val();
                    $('#centre').val(selectedCentre);

                    const params = {
                        centre: selectedCentre || null,
                        state: state === "1" ? "Mensual" : "Anual"
                    };

                    console.log(params);

                    if ($("#datepickerType").val() == 1) {
                        $('#centre').selectpicker('refresh');
                    }

                    if (!selectedCentre) {
                        hideElements('#centreName', '#league-centre-datatable', '#chartCentreLeague',
                            '#centerLeagueDatatable', '#centreChart');
                        showElements('#league-month-datatable', '#chartLeague', '#leagueDatatable', '#leagueChart');
                        if(params['state'] == 'Mensual'){
                            chartConfigs.chartLeague.label = `Liga ${params['state']} - Coeficiente de Venta`;
                        }else {
                            chartConfigs.chartLeague.label = `Liga ${params['state']} - Puntos`;

                        }
                        await drawLeagueDatatable(null, '.league-month-datatable');
                        await drawLeagueChart(chartConfigs.chartLeague.id, null, params['state']);

                    } else {
                        $('#centreName').show();
                        document.getElementById('centreName').innerHTML = params['centre'];
                        showElements('#centreName', '#league-centre-datatable', '#chartCentreLeague',
                            '#centreChart',
                            '#centerLeagueDatatable');
                        hideElements('#league-month-datatable', '#chartLeague', '#leagueChart', '#leagueDatatable');
                        chartConfigs.chartCentreLeague.label = `Coeficiente de Venta`;
                        await drawLeagueDatatable(params['centre'], '.league-centre-datatable');
                        await drawLeagueChart(chartConfigs.chartCentreLeague.id, params['centre'], params[
                            'state']);
                    }
                }

                async function drawLeagueDatatable(centre, idDataTable) {
                    let params = {
                        _token: "{{ csrf_token() }}",
                        state: $("#datepickerType").text()
                    };

                    console.log('drawing table...');
                    console.log(params);

                    // Configuramos los parámetros según si hay "centre" o no
                    if (centre) {
                        params["centre"] = centre;
                        params["year"] = $("#yearPicker").val();
                        params["month"] = null;
                    } else if ($('#monthYearPicker').is(":visible")) {
                        let monthYear = $("#monthYearPicker").val().split('/');
                        params["month"] = monthYear[0];
                        params["year"] = monthYear[1];
                    } else {
                        params["year"] = $("#yearPicker").val();
                        params["month"] = null;
                    }

                    // Configuración del DataTable según el contexto
                    let url = centre ? "{{ route('league.detailsCentreLeague') }}" :
                        "{{ route('league.generateLeague') }}";
                    let lenMenu = centre ? [12] : [25, 10, 15];
                    let columns = centre ? [{
                            data: 'month.month',
                            name: 'month'
                        },
                        {
                            data: 'points',
                            name: 'points'
                        },
                        {
                            data: 'cv',
                            name: 'cv'
                        }
                    ] : [{
                            data: 'position',
                            name: 'position'
                        },
                        {
                            data: 'centre',
                            name: 'centre'
                        },
                        {
                            data: 'points',
                            name: 'points'
                        },
                        {
                            data: 'average',
                            name: 'average'
                        }
                    ];

                    try {
                        let response = await fetchLeagueData(params, url); // Obtenemos los datos
                        let data = response.data;
                        console.log(data);
                        $(idDataTable).dataTable({
                            lengthMenu: lenMenu,
                            processing: true,
                            bDestroy: true,
                            ordering: !centre,
                            language: {
                                emptyTable: "No hay datos disponibles en la tabla",
                                paginate: {
                                    previous: "Anterior",
                                    next: "Siguiente"
                                },
                                infoEmpty: "Sin registros",
                                search: "Buscar:",
                                info: "",
                                loadingRecords: "Cargando...",
                                processing: "Procesando, espere...",
                                lengthMenu: "Mostrando _MENU_ registros",
                                infoFiltered: ""
                            },
                            data: data,
                            columns: columns
                        });
                    } catch (error) {
                        console.error('Error fetching league data:', error);
                    }
                }

                function clearForms() {
                    $('#centreName').hide();
                    $('.centre_picker').hide();
                    $('#centre_id_picker').selectpicker('val', '');
                    $('#datepickerType').selectpicker('val', 1);
                    $('#league-centre-datatable').hide();
                    $('#league-month-datatable').hide();
                    drawLeagueDatatable(null, '.league-month-datatable');
                    $('#league-month-datatable').show();
                }

                //handle ajax request to draw datatables and charts
                function fetchLeagueData(params, url) {
                    console.log(params);
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: params,
                            success: function(response) {
                                resolve(response); // Devolvemos la respuesta al invocador
                            },
                            error: function(xhr) {
                                reject(xhr.responseText); // Manejo de errores
                            }
                        });
                    });
                }

                function showElements(...selectors) {
                    selectors.forEach(selector => $(selector).show());
                }

                function hideElements(...selectors) {
                    selectors.forEach(selector => $(selector).hide());
                }

                async function drawLeagueChart(chartId, centre, state) {
                    let params = {
                        _token: "{{ csrf_token() }}",
                        state: $("#datepickerType").val()
                    };
                    console.log('drawing chart...');
                    console.log(state);

                    let url = centre ? "{{ route('league.detailsCentreLeague') }}" :
                        "{{ route('league.generateLeague') }}";

                    if (centre) {
                        params["centre"] = centre;
                        params["year"] = $("#yearPicker").val();
                        params["month"] = null;
                    } else if ($('#monthYearPicker').is(":visible")) {
                        let monthYear = $("#monthYearPicker").val().split('/');
                        params["month"] = monthYear[0];
                        params["year"] = monthYear[1];
                    } else {
                        params["year"] = $("#yearPicker").val();
                        params["month"] = null;
                    }

                    try {
                        let response = await fetchLeagueData(params, url);
                        let data = response.data; // Obtenemos los datos
                        console.log('Response data:', response.data);
                        // Configuramos las etiquetas dinámicas según la columna
                        let labels = centre ?
                            data.map(item => item.month.month) :
                            data.map(item => item.centre);

                        let chartConfig = chartConfigs[chartId];
                        chartConfig.labels = labels;
                        if(state == 'Mensual'){
                            chartConfig.data =  data.map(item => item.average);
                        }else if (centre){
                            chartConfig.data =   data.map(item => item.cv);
                        }else {
                            chartConfig.data =     data.map(item => item.points);
                        }

                        const ctx = document.getElementById(chartConfig.id).getContext('2d');
                        // Destroy the previous chart if it exists
                        if (currentChart !== null) {
                            currentChart.destroy();
                        }

                        // Create a new chart
                        currentChart = new Chart(ctx, {
                            type: chartConfig.type,
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: chartConfig.label,
                                    data: chartConfig.data,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)'
                                    ],
                                    borderWidth: 1,
                                    maxBarThickness: 50,
                                    minBarLength: 2
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: false
                                    }
                                }
                            }
                        });
                    } catch (error) {
                        console.error('Error fetching chart data:', error);
                    }
                }

                /**
                 * Botón exportar
                 */
                $("#btnSubmitExport").on('click', function(e) {
                    e.preventDefault();
                    $("#leagueForm").attr('action', '{{ route('league.exportLeague') }}');
                    $('#btnSubmitExport').hide();
                    $('#btnSubmitLoad').show();
                    $('#btnSubmitLoad').prop('disabled', true);
                    $('#centre').val($("#centre_id_picker option:selected").text());

                    params = {};
                    params["_token"] = "{{ csrf_token() }}";

                    if ($("#datepickerType").val() == 1) {
                        $('#centre').selectpicker('refresh');
                        params['centre'] = null;
                        params["state"] = 'mensual';

                    } else {
                        params["centre"] = $('#centre').val();
                        params["state"] = 'anual';
                    }

                    if (params["centre"]) {
                        params["year"] = $("#yearPicker").val()
                        params["month"] = null;
                    } else {
                        if ($('#monthYearPicker').is(":visible")) {
                            monthYear = $("#monthYearPicker").val();
                            dateSearch = monthYear.split('/');
                            params["month"] = dateSearch[0];
                            params["year"] = dateSearch[1];

                        } else {
                            params["year"] = $("#yearPicker").val()
                            params["month"] = null;

                        }
                    }
                    $.ajax({
                        url: $("#leagueForm").attr('action'),
                        type: 'GET',
                        data: params,
                        dataType: 'binary',
                        xhrFields: {
                            'responseType': 'blob'
                        },
                        xhr: function() {
                            var xhr = new XMLHttpRequest();
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState == 2) {
                                    if (xhr.status == 200) {
                                        xhr.responseType = "blob";
                                    } else {
                                        xhr.responseType = "text";
                                    }
                                }
                            };
                            return xhr;
                        },
                        success: function(data, textStatus, jqXHR) {
                            if (textStatus === 'success') {
                                $('#btnSubmitLoad').hide();
                                $('#btnSubmitExport').show();

                                var link = document.createElement('a'),
                                    filename = 'league.xls';
                                link.href = URL.createObjectURL(data);
                                link.download = filename;
                                link.click();
                            }
                        },
                        error: function(xhr, status, error) {
                            var response = JSON.parse(xhr.responseText);
                            showAlert('error', response);
                            $('#btnSubmitLoad').hide();
                            $('#btnSubmitExport').show();
                        }
                    });
                });


            });
        </script>
    @endsection
