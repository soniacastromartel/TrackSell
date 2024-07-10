
@include('common.alert')

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">


<div id="alertErrorCalculate" class="alert alert-danger" role="alert" style="display: none">
</div>

<div class="content">
  <div class="container-fluid" style="margin-top: 120px">
    <div class="row">
      <div class="col-lg-4">
        <div id="employee-info" class="card" style="min-height: 482.5px;">
          <div class="card-header card-header-danger">
            <h4 class="card-title">Búsqueda</h4>
          </div>
          <div class="card-body">
            <form id="rankingForm" method="POST">
              @csrf
              @method('POST')

              <div class="row bg-white">

                <div class="form-group col-sm-10">
                    <div id="monthYearPickerContainer"> 
                        <input id="monthYearPicker" type="text" placeholder="yyyy/mm">
                        <span id="icon-date" class="material-symbols-outlined"> calendar_month</span>
                        <input type="hidden" name="monthYear" id="monthYear" />
                    </div>

              

                    <div id="yearPickerContainer">
                        <input id="yearPicker" class='form-control' type="text" placeholder="yyyy" />
                        <span id="icon-date" class="material-symbols-outlined">calendar_month</span>
                    </div>
                  </div>

                <div class="form-group col-sm-10">
                  <div class="dropdown bootstrap-select">
            
                    <select class="selectpicker" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-red-icot btn-round" title="Centro" tabindex="-98">

                      <!-- <option selected>GRUPO ICOT</option> -->
                      @if (!in_array($employee->rol_id, [1,4]))
                      {{-- @if (isset($employee) && $employee->rol_id != 1) --}}
                      <option value="{{$employee->centre_id}}" selected>{{$employee->centre}}</option>
                      @endif
                      
                      @if (in_array($employee->rol_id, [1,4]))
                      {{-- @if (isset($employee) && $employee->rol_id == 1) --}}
                      @foreach ($centres as $centre)
                      <option value="{{$centre->id}}">{{$centre->name}}</option>
                      @endforeach
                      @endif
                    </select>
                    <input type="hidden" name="centre" id="centre" />
                  </div>
                </div>

                <div class="form-group col-sm-10">
                  <div class="row">
                    <button id="btnClear" class="btn-refresh">Limpiar Formulario<span id=icon-refresh
                        class="material-icons">refresh</span>
                      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                        style="display: none;"></span>
                    </button>
                  </div>
                </div>

              </div>
              <div class="btn-radio-container">
                <div class="form-check">
                    <label class="form-check-label" id="selected-label">
                        <input id="monthly" class="form-check-input" type="radio" name="optradio"
                            value="1" checked>Mensual
                        <span class="circle"><span class="check"></span></span>
                    </label>
                    <label class="form-check-label">
                        <input id="annual" class="form-check-input" type="radio" name="optradio"
                            value="2">Anual
                        <span class="circle"><span class="check"></span></span>
                    </label>
                </div>
            </div>
          </div>
        </div>

      </div>
      <div class="col-lg-7">
        <div id="employee-info" class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title" id="title-target">Objetivos</h4>
          </div>
          <div class="card-body ">
             
            <div id='vp_ok' class="card-header">
            <h4 class="card-title">Venta cruzada</h4>
            </div>

            <div>
              <div id="chart_div_vc"></div>
            </div>

            <div id='vp_ok' class="card-header">
              <h4 class="card-title">Venta privada</h4>
            </div>

            <div>
              <div id="chart_div_vp"></div>
            </div>

          </div>

        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg">

       
      </div>

      <div style="margin-right: 85px;margin-top: 15px;">

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
    <div class="row" id="monthlyData">
      <div class="col-lg-11">
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title" id="title-sales">Ranking Mensual</h4>
          </div>
          <div class="card-header-table">
            <table class="table table-striped table-bordered sales-month-datatable col-lg-12">
              <thead class="table-header">
                <tr>
                  <th>Posicion</th>
                  <th>Empleado</th>
                  <th>Total Venta</th>
                  <th>Total Incentivo</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="row" id="annualData">
      <div class="col-lg-11">
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title" id="title-ranking">Ranking Anual</h4>
          </div>
          <div class="card-header-table">
            <table class="table table-striped table-bordered sales-year-datatable col-lg-12">
              <thead class="table-header">
                <tr>
                  <th>Posicion</th>
                  <th>Empleado</th>
                  <th>Centro</th>
                  <th>Total Venta</th>
                  <th>Total Incentivo</th>
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
  table.dataTable.dataTable_width_auto {
    width: 100%;
  }

  svg.ct-chart-bar,
  svg.ct-chart-line {
    overflow: visible;
  }

  .ct-label.ct-label.ct-horizontal.ct-end {
    position: relative;
    justify-content: flex-end;
    text-align: right;
    transform-origin: 100% 0;
    transform: translate(-100%) rotate(0deg);
    white-space: nowrap;
  }

  .card-header-table {
    width: 100%;
    margin-top: 0px !important;
  }

  .sales-datatable {
    table-layout: fixed;
    width: 100% !important;
  }

  .sales-datatable td,
  .sales-datatable th {
    /* width: auto !important; */
    /* white-space: normal; */
    text-overflow: ellipsis;
    overflow: hidden;
  }

  .form-group {
    display: flex;
    justify-content: space-evenly;
    width: 100%;
  }

  .row {
    justify-content: center;
  }

  #DataTables_Table_0_paginate>ul.pagination {
    margin: 16px 0 !important;
  }

  #btnClear {
    align-self: end;
    
  }

  .employee-info {
    /* padding-left: 15px; */
    /* margin-top: 50px; */
    /* min-height: 5000px; */
  }

  .employee-info span {
    color: var(--red-icot);
  }

  #typeRanking {
    margin-bottom: 0px;
    margin-right: 10px;
    margin-left: 100px;
    display: inline-block;
    font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    font-weight: 700;
    color: black;
  }

  #formRadio {
    display: inline-block;
    color: red;
  }

  #monthly {
    margin-right: 15px;
    color: var(--red-icot);
  }

  #annual {
    margin-right: 15px;
    color: var(--red-icot);
  }

  #separator {
    margin-bottom: 0;
    padding-bottom: 0;
  }

  #selected-label {
    color: var(--red-icot);
    font-weight: bold;
  }

  .content {
  background-image: url(/assets/img/background_continue.png) !important;
  background-position: center center !important;
  background-size: 1000px;
  height: 180vh !important;
}

table.dataTable.dataTable_width_auto {
  width: 100%;
}

svg.ct-chart-bar,
svg.ct-chart-line {
  overflow: visible;
}

.ct-label.ct-label.ct-horizontal.ct-end {
  position: relative;
  justify-content: flex-end;
  text-align: right;
  transform-origin: 100% 0;
  transform: translate(-100%) rotate(0deg);
  white-space: nowrap;
}

.card-header-table {
  width: 100%;
  margin-top: 0px !important;
}

.sales-datatable {
  table-layout: fixed;
  width: 100% !important;
}

.sales-datatable td,
.sales-datatable th {
  text-overflow: ellipsis;
  overflow: hidden;
}

#DataTables_Table_0_paginate > ul.pagination {
  margin: 16px 0 !important;
}
.label {
  /* margin-top: 20px;
  margin: 10px; */
}



.btn-clear-container {
  padding: 10px;
  display: flex;
  justify-content: center;
}

#typeRanking {
  margin-bottom: 0px;
  margin-right: 10px;
  margin-left: 100px;
  display: inline-block;
  font-family: "Franklin Gothic Medium", "Arial Narrow", Arial, sans-serif;
  font-weight: 700;
  color: black;
}

#btn-radio-refresh {
  display: flex;
  justify-content: space-between;
  color: var(--red-icot);
}


#selected-label {
  color: var(--red-icot);
  font-weight: bold;
}

.btn-radio-container {
  height: 45px;
  margin: 10px;
  margin-top: 30px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  text-align: center;
}




</style>

<script type="text/javascript">
    $(function() {
        function filterSearch() {
            if ($('input[name="optradio"]:checked').val() == "1") { //MENSUAL
                params["monthYear"] = $("#monthYearPicker").val();
                return 1;
            }
            if ($('input[name="optradio"]:checked').val() == "2") { //ANUAL
                params["year"] = $("#yearPicker").val();
                return 2;
            }
        }

        // @if (isset($employee) && $employee->rol_id == 2)
        //     $("#btnClear").hide();
        // @else
        //     $("#btnClear").show();
        // @endif

        $(".form-check-input").change(function() {
            $(".form-check-label").removeAttr('id');
            $(this).parent().attr('id', 'selected-label');
        });

        /**
         * Botón exportar 
         */

        $("#btnSubmit").on('click', function(e) {
            $('#alertErrorCalculate').hide();
            e.preventDefault();
            $("#rankingForm").attr('action', '{{ route('ranking.calculateRankings') }}');
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            $('#centre').val($("#centre_id option:selected").text());
            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["centre"] = $('#centre').val();

            filterSearch();

            $.ajax({
                url: $("#rankingForm").attr('action'),
                type: 'post',
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
                    // if success, HTML response is expected, so replace current
                    if (textStatus === 'success') {
                        $('#btnSubmitLoad').hide();
                        $('#btnSubmit').show();

                        var link = document.createElement('a'),
                            filename = 'ranking.xls';
                        link.href = URL.createObjectURL(data);
                        link.download = filename;
                        link.click();
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    $('#alertErrorCalculate').text(response.errors);
                    $('#alertErrorCalculate').show().delay(2000).slideUp(300);
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmit').show();
                    timeOutAlert($('#alertErrorCalculate'));
                }
            });
        });

        var d = new Date();
        var year;
        var month;
        var textMonthYear;
        var fecha;

        $("#yearPicker").datepicker("destroy");
        $("#yearPickerContainer").hide();
        showMonthYearPicker();

        function setDate() {
            date = new Date();
            year = date.getFullYear();
            month = date.getMonth() + 1;
            textMonthYear = month >= 10 ? month : '0' + month;
            fecha = textMonthYear + '/' + year;
            return fecha;

        }

        function showMonthYearPicker() {
            fecha = setDate();
            $('#monthYearPicker').val(fecha);
            $('#monthYearPicker').MonthPicker();
            $('#monthYearPicker').MonthPicker({
                ShowIcon: false,
            });
        }
        $('#icon-date').click(function() {
            $('#monthYearPicker').MonthPicker('Open');
        });



        $("#yearPicker").datepicker("destroy");
        $("#yearPickerContainer").hide();
        showMonthYearPicker();

        // var textMonthYear = month >= 10 ? month : '0' + month;

        function showYearPicker() {
            $('#monthYearPicker').MonthPicker({
                ShowIcon: false
            });

            var textYear = d.getFullYear();
            $.datepicker.setDefaults($.datepicker.regional['es']);
            $('#yearPicker').val(textYear);
            $('#yearPicker').datepicker({
                selectedDate: true,
                changeMonth: false,
                changeYear: true,
                showButtonPanel: true,
                closeText: 'Seleccionar',
                currentText: 'Año actual',
                onClose: function(dateText, inst) {
                    $(this).val($.datepicker.formatDate("yy", new Date(inst['selectedYear'], 0,
                        1)));
                },

            });
        }

        $('#annualData').hide();

        google.charts.load('current', {
            packages: ['corechart', 'bar']
        });

        /** 
         * Radio Button Mensual
         */
        $('#monthly').on('click', function(e) {
            showMonthYearPicker();
            fecha = setDate();
            getTargets($('#centre_id option:selected').val());
            getSales('.sales-month-datatable');
            getSales('.sales-year-datatable');
            $('#monthYearPicker').val(fecha);
            $("#yearPicker").datepicker("destroy");
            $("#yearPickerContainer").hide();
            $('#monthYearPickerContainer').show();
            $('#annualData').hide();
            $('#monthlyData').show();
        });

        /** 
         * //!Radio Button Anual
         */

        $('#annual').on('click', function(e) {
            showYearPicker();
            getTargets($('#centre_id option:selected').val());
            getSales('.sales-month-datatable');
            getSales('.sales-year-datatable');
            $('#yearPickerContainer').show();
            $('#monthYearPickerContainer').hide();
            $('#monthlyData').hide();
            $('#annualData').show();
        });

        const colors = [{
                color: '#b01c2e'
            },
            {
                color: '#cccccc'
            }, //high
            {
                color: 'seagreen'
            }, //low
        ];

        const options = {
            title: '',
            chartArea: {
                width: '60%'
            },
            height: 150,
            annotations: {
                alwaysOutside: true,
                textStyle: {
                    fontSize: 12,
                    auraColor: 'none',
                    color: '#555'
                },
                boxStyle: {
                    stroke: '#ccc',
                    strokeWidth: 1,
                    gradient: {
                        color1: '#f3e5f5',
                        color2: '#f3e5f5',
                        x1: '0%',
                        y1: '0%',
                        x2: '100%',
                        y2: '100%'
                    }
                }
            },
            hAxis: {
                title: 'Enero', //FIXME.... change dynamic
                minValue: 0,
            },
            vAxis: {
                title: ''
            },
            series: colors
        };

        /**
         * Cambia las etiquetas en función del centro seleccionado
         */
        function getValueCentre() {
            var centro_id = $('#centre_id option:selected').val();
            var centro = $('#centre_id option:selected').text();
            if (centro_id != "") {
                $('#centre_id option:selected').val(centro_id);
                $("#employee-centre").html(centro);
                $("#title-target").html("Objetivos " + centro);
                $("#title-sales").html("Ranking Mensual de  " + centro);
                $("#title-ranking").html("Ranking Anual de  " + centro);

            } else {
                $("#employee-centre").html(centro);
                $("#title-target").html("Objetivos  del  GRUPO ICOT");
                $("#title-sales").html("Ranking Mensual del  GRUPO ICOT");
                $("#title-ranking").html("Ranking Anual del GRUPO ICOT");
            }
        }

        function getLabelMonth(month) {
            var months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto",
                "Septiembre", "Octubre", "Noviembre", "Diciembre"
            ];
            var monthName = months[month - 1];
            return monthName;
        }


        function drawGraphVP(val) {

            var aa = [
                ['Objetivo VP', 'Venta Privada', 'Objetivo Venta Privada'],
                ['', val['value'], val['target']],
            ];
            var data = new google.visualization.arrayToDataTable(aa);
            var chart = new google.visualization.BarChart(document.getElementById('chart_div_vp'));
            if (val['value'] == 0) {
                $("#vp_pending").show();

            } else {
                $("#vp_pending").hide();

            }
            chart.draw(data, options);
        }

        function drawGraphVC(val) {

            bb = [
                ['Objetivo VC', 'Venta Cruzada', 'Objetivo Venta Cruzada'],
                ['', val['value'], val['target']],
            ];
            var data = new google.visualization.arrayToDataTable(bb);
            var chart = new google.visualization.BarChart(document.getElementById('chart_div_vc'));
            chart.draw(data, options);
        }

        /**
         * Obtener datos para las Gráficas
         */
        function getTargets(centre_id) {
            var params = {};
            if (centre_id != undefined) {
                params["centre_id"] = centre_id;
            }
            params["_token"] = "{{ csrf_token() }}";
            params["monthYear"] = $("#monthYearPicker").val();
            $.ajax({
                url: "{{ route('home.getTargets') }}",
                type: 'post',
                data: params,
                success: function(data, textStatus, jqXHR) {
                    // if success, HTML response is expected, so replace current
                    if (textStatus === 'success') {
                        var target = JSON.parse(data);
                        google.charts.setOnLoadCallback(function() {
                            options.hAxis.title = getLabelMonth($("#monthYearPicker").val()
                                .substr(0, $("#monthYearPicker").val().indexOf('/')));
                            drawGraphVC(target.data.vc);
                            drawGraphVP(target.data.vp);
                        });
                    }
                    getValueCentre();
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error' + jqXHR.responseText);
            });
        }

        $('.sales-month-datatable').on('error.dt', function(e, settings, techNote, message) {
            if (message.indexOf("Error") > -1) {
                message = message.substr(message.indexOf("Error"));
                alert(message);
            }
        })
        $.fn.dataTable.ext.errMode = 'none';

        /**
         * Tabla Ranking
         */
        function getSales(idDataTable) {
            var table;
            var columnsRank = [{
                    data: 'position',
                    name: 'position'
                },
                {
                    data: 'employee',
                    name: 'employee'
                }
            ];
            if (idDataTable == '.sales-year-datatable') {
                columnsRank.push({
                    data: 'centre',
                    name: 'centre'
                });
            }
            columnsRank.push({
                data: 'total_price',
                name: 'total_price'
            });
            columnsRank.push({
                data: 'total_incentive',
                name: 'total_incentive'
            });

            table = $(idDataTable).DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                autoWidth: false,
                bDestory: true,
                bRetrieve: true,
                language: {
                    "url": "{{ asset('dataTables/Spanish.json') }}"
                },
                ajax: {
                    url: "{{ route('home.getSales') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val(),
                            d.monthYear = $('#monthYearPicker').val(),
                            /** Opciones Ranking **/

                            /** Si es seleccionado HCT siempre pasar HCT
                                Si no es HCT si es anual (null centros) , mensual el centro */
                            d.centre = $('#centre_id option:selected').val(),
                            d.type = idDataTable == '.sales-month-datatable' ? 'monthly' : 'anual'
                    }
                },
                columnDefs: [{
                        targets: idDataTable == '.sales-month-datatable' ? [2, 3] : [3, 4],
                        render: $.fn.dataTable.render.number('.', ',',
                            2
                        ) //columnDefs number renderer (thousands, decimal, precision, simbolo/moneda)
                    },

                ],
                columns: columnsRank,
                search: {
                    "regex": false,
                    "smart": false
                },
                responsive: true,
                initComplete: function(data) {
                    this.api().columns().every(function() {
                        var column = this;
                        var input = document.createElement("input");
                        $(input).appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this)
                                    .val());
                                column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });
                    });
                }
            });
            if ($.fn.dataTable.isDataTable(idDataTable)) {
                $(idDataTable).DataTable().ajax.reload();
            }
        }

        function clearForms() {
            fecha = setDate();
            $('#monthYearPicker').val(fecha);
            $('select#centre_id').val('');
            $('select#centre_id').selectpicker("refresh");
            // $("#employee-centre").html("GRUPO ICOT");
            // $("#title-target").html("Objetivos  del  GRUPO ICOT");
            // $("#title-sales").html("Ranking Mensual del  GRUPO ICOT");
            // $("#title-ranking").html("Ranking Anual del GRUPO ICOT");
        }

        $("#btnClear").on('click', function(e) {
            e.preventDefault();
            clearForms();
            $('#centre_id').trigger("change");
        });

        getValueCentre();
        getTargets({{ $user->centre_id }});
        getSales('.sales-month-datatable');
        getSales('.sales-year-datatable');

        /**
         * Detecta el cambio del selector de Centro
         */
        $("#centre_id").on('change', function() {
            // getValueCentre();
            getTargets($('#centre_id option:selected').val());
            // if (filterSearch() == 1) {
            getSales('.sales-month-datatable');
            // } else {
            getSales('.sales-year-datatable');
            // }
        });

        $('#monthYearPicker').MonthPicker({
            OnAfterChooseMonth: function() {
                getTargets($('#centre_id option:selected').val());
                getSales('.sales-month-datatable');
                getSales('.sales-year-datatable');
            }
        });
        $('#monthPicker').datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'MM yy',
            onClose: function(dateText, inst) {
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
        });
    });
</script>