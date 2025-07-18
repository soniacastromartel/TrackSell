@extends('layouts.logged')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

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
                    <div class="card card-banner">
                        <div class="card-header card-header-info card-header-text ">
                            <div class="card-text" style="border-radius: 50px">
                                <h4 class="card-title">Liga de Centros</h4>
                            </div>
                        </div>
                        <div class="card-body" style="margin-top:30px;">
                            <form id="leagueForm" method="POST">
                                @csrf
                                @method('POST')
                                <div class="row d-flex align-items-start">
                                    <!-- Período -->
                                    <div class="col-md-3 period-container">
                                        <label class="col-form-label-lg">Período</label>
                                        <div class="dropdown bootstrap-select">
                                            <span class="icon-select material-symbols-outlined" style="margin-left: 6px;">manage_history</span>
                                            <select class="selectpicker" name="datepickerType" id="datepickerType"
                                                data-size="7" data-style="btn btn-red-icot btn-round"
                                                title=" Mensual / Anual">
                                                <option value="1" selected>Mensual</option>
                                                <option value="2">Anual</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Fecha -->
                                    <div class="col-md-3 fecha-container">
                                        <label class="col-form-label-lg">Fecha</label>
                                        <div id="monthYearPickerContainer">
                                            <div class="date-picker-wrapper">
                                                <input id="monthYearPicker" type="text" placeholder="yyyy/mm" />
                                                <span class="icon-select material-symbols-outlined" style="margin-left: 6px;">calendar_month</span>
                                                <input type="hidden" name="monthYear" id="monthYear" />
                                            </div>
                                        </div>
                                        <div id="yearPickerContainer">
                                            <div class="date-picker-wrapper">
                                                <input id="yearPicker" type="text" placeholder="yyyy" />
                                                <span class="icon-select material-symbols-outlined">calendar_month</span>
                                            </div>
                                        </div>
                                    </div>
                                {{-- </div> --}}

                                <div class="col-md-3 picker-container" style="";>
                                    <button id="btnSubmit" type="submit" class="btn-export">
                                        <span id="icon-export" class="material-symbols-outlined">
                                            file_download
                                        </span> {{ __('EXPORTAR') }}
                                    </button>
                                    <button id="btnSubmitLoad" type="submit" class="btn-export" style="display: none">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                    </button>

                                    <button id="btnClear" href="#" class="btn-refresh">
                                        <span id="icon-refresh" class="material-symbols-outlined">refresh
                                        </span> {{ __('REFRESCAR') }}
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
                        <h4 style="margin-left: 15px;">Liga de <strong>CENTROS</strong></h4>
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

                <div class="" id="leagueDatatable" style="margin-top: 150px;">
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
    </div>
    <style>
        .row {
            display: flex;
            justify-content: start;
            /* Alinea los elementos al inicio */
            gap: 20px;
            /* Espaciado entre Período y Fecha */
        }

        .period-container,
        .fecha-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            /* Espaciado entre label y select/input */
        }

        .date-picker-wrapper {
            position: relative;
            width: 100%;
        }

        .date-picker-wrapper input {
            width: 100%;
            padding: 10px;
            padding-right: 35px;
            /* Espacio para el icono */
            border-radius: 5px;
        }

        button.ui-datepicker-trigger {
            border: none !important;
        }

        #centreName {
            padding: 10px;
            display: block;
            text-align: center;
            font-weight: bold;
            font-size: xx-large;
            font-family: "Nunito", sans-serif;
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

        .card-banner {
            background-image: url(/assets/img/banners/2.jpg);
            background-repeat: no-repeat;
            background-size: contain;
            background-position-x: right;
            min-height: 340px;
        }
    </style>

    <script type="text/javascript">
        $(function() {
            // handles sidebar selection
            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $('#pagesTracking').addClass('show');
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
                    title: '',
                    labels: [], // Etiquetas generales
                    data: [], // Datos generales
                    label: '' // Dinámico, se setea en loadData
                },
                chartCentreLeague: {
                    id: 'chartCentreLeague',
                    type: 'bar',
                    title: '',
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

                if ($("#datepickerType").val() == 1) {
                    $('#centre').selectpicker('refresh');
                }

                if (!selectedCentre) {
                    hideElements('#centreName', '#league-centre-datatable', '#chartCentreLeague',
                        '#centerLeagueDatatable', '#centreChart');
                    showElements('#league-month-datatable', '#chartLeague', '#leagueDatatable', '#leagueChart');
                    if (params['state'] == 'Mensual') {
                        chartConfigs.chartLeague.label = `Coeficiente de Venta`;
                        chartConfigs.title = `Liga ${params['state']}`
                    } else {
                        chartConfigs.chartLeague.label = `Puntos`;
                        chartConfigs.title = `Liga ${params['state']}`;

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
                    chartConfigs.chartCentreLeague.title = params['centre']; //TODO poner el mes??
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

            let barValues = [0, 0];

            async function drawLeagueChart(chartId, centre, state) {
                var animationOffset = 0; // Desplazamiento actual de la animación
                var animationDirection = 1; // Dirección de la animación (1 = subir, -1 = bajar)
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
                    let data = response.data;
                    console.log('Response data:', response.data);

                    let labels = centre ?
                        data.map(item => item.month.month) :
                        data.map(item => item.centre);

                    let chartConfig = chartConfigs[chartId];
                    chartConfig.labels = labels;
                    let barValues = [0.6, 0.4];

                    if (state === 'Mensual') {
                        chartConfig.data = data.map(item => item.average);
                        chartConfig.title = 'Liga Mensual';
                    } else if (centre) {
                        chartConfig.title = centre;
                        chartConfig.data = data.map(item => item.cv);
                    } else {
                        chartConfig.data = data.map(item => item.points);
                        chartConfig.title = 'Liga Anual';
                        barValues = [20, 10];
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
                                backgroundColor: chartConfig.data.map(value => getDynamicColor(
                                    value, false, barValues)),
                                borderColor: chartConfig.data.map(value => getDynamicColor(
                                    value, true, barValues)),
                                borderWidth: 2,
                                maxBarThickness: 50,
                                minBarLength: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    suggestedMin: -1.3,
                                    suggestedMax: 1,
                                }
                            },
                            plugins: {
                                legend: {
                                    title: {
                                        display: true,
                                        text: chartConfig.title,
                                        font: {
                                            size: 20,
                                            weight: 'bold',
                                        },
                                        color: '#000'
                                    }
                                },
                                datalabels: {
                                    display: true, // Ensure this is true
                                    color: '#000',
                                    font: {
                                        weight: 'bold',
                                        size: 12
                                    },
                                    anchor: 'end', // Position of the label relative to the bar
                                    align: 'bottom', // Align the label at the bottom of the bar
                                    formatter: (value) => value // Show the value on the bar
                                }
                            }
                        },

                        // Plugin con animación para la burbuja del primer lugar
                        plugins: [{
                            id: 'bubblePlugin',
                            afterDatasetsDraw: (chart) => {
                                const ctx = chart.ctx;
                                ctx.save();
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                ctx.font = 'bold 12px Arial';

                                const topThree = [1, 2, 3];
                                const defaultBubbleSize =
                                    15; // Tamaño estándar de la burbuja
                                const extraBubbleSize =
                                    3; // Incremento para la burbuja del primer lugar

                                chart.data.datasets.forEach((dataset, i) => {
                                    const meta = chart.getDatasetMeta(i);

                                    meta.data.forEach((bar, index) => {
                                        const value = dataset.data[index];

                                        if (value !== null && value !==
                                            undefined && value !== 0 &&
                                            index < 3) {
                                            const isFirstPlace = index ===
                                                0; // Verifica si es el primer lugar
                                            const bubbleSize =
                                                isFirstPlace ?
                                                defaultBubbleSize +
                                                extraBubbleSize :
                                                defaultBubbleSize;

                                            // Calcular la posición vertical de la burbuja
                                            const baseTextPositionY = bar
                                                .y - 30;
                                            const textPositionY =
                                                isFirstPlace ?
                                                baseTextPositionY +
                                                animationOffset :
                                                baseTextPositionY;

                                            // Cambiar el color de la barra
                                            bar.options.backgroundColor =
                                                getDynamicColor(value,
                                                    false, barValues,
                                                    isFirstPlace, false);
                                            bar.options.borderColor =
                                                getDynamicColor(value, true,
                                                    barValues, isFirstPlace,
                                                    false);

                                            // Dibujar la burbuja
                                            ctx.beginPath();
                                            ctx.arc(bar.x, textPositionY,
                                                bubbleSize, 0, 2 * Math
                                                .PI);
                                            ctx.fillStyle = getDynamicColor(
                                                value, false, barValues,
                                                isFirstPlace, true,
                                                index + 1
                                            ); // Color dinámico para la burbuja
                                            ctx.fill();
                                            ctx.closePath();

                                            // Dibujar el borde de la burbuja
                                            ctx.strokeStyle = 'black';
                                            ctx.lineWidth = 1;
                                            ctx.stroke();

                                            // Agregar el borde negro a la barra si es la primera posición
                                            if (isFirstPlace) {
                                                bar.options.borderWidth = 2;
                                                bar.options.borderColor =
                                                    'black';
                                            }

                                            // Agregar el número de posición dentro de la burbuja
                                            ctx.fillStyle = 'black';
                                            ctx.fillText(topThree[index],
                                                bar.x, textPositionY);
                                        }
                                    });
                                });

                                ctx.restore();
                            }
                        }],

                        afterUpdate: function(chart) {
                            if (!animationInterval) {
                                animationInterval = setInterval(() => {
                                    // Animación de la burbuja del primer lugar
                                    if (animationDirection === 1 && animationOffset < 2) {
                                        // Subir
                                        animationOffset += 0.5;
                                    } else if (animationDirection === -1 &&
                                        animationOffset > -2) {
                                        // Bajar
                                        animationOffset -= 0.5;
                                    }

                                    // Cambiar dirección cuando se llegue a los límites
                                    if (animationOffset >= 2 || animationOffset <= -2) {
                                        animationDirection *= -1;
                                    }

                                    // Actualizar el gráfico
                                    chart.update();
                                }, 50); // Intervalo de 50ms para controlar la velocidad
                            }
                        },

                        beforeDestroy: function() {
                            // Detener la animación cuando el gráfico se destruya
                            if (animationInterval) {
                                clearInterval(animationInterval);
                                animationInterval = null;
                            }
                        }

                    });
                } catch (error) {
                    console.error('Error fetching chart data:', error);
                }
            }

            Chart.register(ChartDataLabels);

            // plugin to show a no data message inside chart
            Chart.register({
                id: 'noDataPlugin',
                beforeDraw: (chart) => {
                    if (chart.data.datasets.length > 0) {
                        const data = chart.data.datasets[0].data;
                        const allValuesEmpty = data.every(value => value === 0 || value === null ||
                            value === '');

                        if (data.length === 0 || allValuesEmpty) {
                            const ctx = chart.ctx;
                            const {
                                width,
                                height
                            } = chart;
                            ctx.save();
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.font = '22px Arial';
                            ctx.fillStyle = 'red';
                            ctx.fillText('No hay datos disponibles para mostrar.', width / 2, height /
                                2);
                            ctx.restore();
                        }
                    } else {
                        const ctx = chart.ctx;
                        const {
                            width,
                            height
                        } = chart;
                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.font = '16px Arial';
                        ctx.fillStyle = 'gray';
                        ctx.fillText('No hay datos disponibles para mostrar.', width / 2, height / 2);
                        ctx.restore();
                    }
                },
            });

            // Function to dynamically assign colors based on value
            function getDynamicColor(value, isBorder = false, barValues, isFirstPlace = false, isBubble = false,
                position = 0) {
                const greenShades = ["#80f46c", "#a7f799", "#e3ecf5", "#24F840"]; // Brilliant to light green
                const redShades = ["#d75959", "#df7b7b", "#f7d7c2", "#C70053"]; // Dark to light red

                let color;

                if (isFirstPlace) {
                    if (value < 0) {
                        // Valor negativo: barra roja, burbuja verde (cuarto color del array correspondiente)
                        color = isBubble ? greenShades[3] : redShades[3];
                    } else {
                        // Valor positivo: barra verde, burbuja roja (cuarto color del array correspondiente)
                        color = isBubble ? redShades[3] : greenShades[3];
                    }
                } else if (position == 2 || position == 3) {
                    // Segunda y tercera posición
                    if (value < 0) {
                        color = isBubble ? greenShades[position - 2] : redShades[position - 2];
                    } else {
                        color = isBubble ? redShades[position - 2] : greenShades[position - 2];
                    }
                } else {
                    // Lógica estándar para valores positivos o negativos
                    if (value < 0) {
                        if (value <= -barValues[0]) color = redShades[0]; // Darkest red
                        else if (value <= -barValues[1]) color = redShades[1]; // Medium red
                        else color = redShades[2]; // Lightest red
                    } else {
                        if (value >= barValues[0]) color = greenShades[0]; // Brightest green
                        else if (value >= barValues[1]) color = greenShades[1]; // Medium green
                        else color = greenShades[2]; // Lightest green
                    }
                }

                if (isBorder && isFirstPlace) {
                    color = 'black'; // Borde negro para la primera posición
                }

                const alpha = isBorder ? 1 : 0.9; // Full opacity for borders, semi-transparent for fills
                return hexToRgba(color, alpha);
            }



            // Helper function to convert HEX to RGBA
            function hexToRgba(hex, alpha) {
                const bigint = parseInt(hex.replace("#", ""), 16);
                const r = (bigint >> 16) & 255;
                const g = (bigint >> 8) & 255;
                const b = bigint & 255;
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            }

            /**
             * Botón exportar
             */
            $("#btnSubmit").on('click', function(e) {
                e.preventDefault();
                $("#leagueForm").attr('action', '{{ route('league.exportLeague') }}');
                $('#btnSubmit').hide();
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
                            $('#btnSubmit').show();

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
                        $('#btnSubmit').show();
                    }
                });
            });


        });
    </script>
@endsection
