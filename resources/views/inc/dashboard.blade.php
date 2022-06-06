
  <div id="alertErrorCalculate" class="alert alert-danger" role="alert" style="display: none">
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
  <div class="container-fluid mt-3">
    <div class="row">
      <div class="col-lg-4">
        <div id="employee-info" class="card"  style= "min-height: 462px;">
          <div class="card-header card-header-danger">
            <h4 class="card-title">Búsqueda</h4>
          </div>
          <div class="card-body">

          <form id="rankingForm" method="POST">

@csrf
@method('POST')

            <div class="row">
              <div class="col-sm-10" style="padding-top: 40px;">
                <label class="label" for="monthYearPicker">Mes / Año </label>
                <div class="mt-2 input-group date">
                  <input id="monthYearPicker" class='form-control' type="text"  placeholder="yyyy/mm" />
                  <input type="hidden" name="monthYear" id="monthYear"/>
                </div>
              </div>
              <div class="form-group col-sm-10">
                <div class="dropdown bootstrap-select">
                  <label class="label" for="centre_id" style="margin-top: 35px; margin-bottom: 25px;">Centro prescriptor<span class="obligatory">*</span> </label>
                  <select class="selectpicker"  name="centre_id" id="centre_id" data-size="7" data-style="btn btn-red-icot btn-round" 
                  title="* Seleccione Centro" tabindex="-98"
                  @if (isset($employee) && $employee->rol_id != 1)
                  disabled="disabled"
                  @endif
                  >
                  @foreach ($centres as  $centre)
                  <option value="{{$centre->id}}" 
                  @if (isset($employee) && $centre->id == $employee->centre_id )
                  selected="selected"
                  @endif
                  >{{$centre->name}}</option>
                  @endforeach
                  </select>
                  <input type="hidden" name="centre" id="centre"/>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row" style="margin-top:20px;">
              
              @if (isset($employee) && $employee->rol_id == 1)
                  <button id="btnClear" href="#" class="btn btn-fill btn-warning">
                  {{ __('Limpiar formulario') }}
                  </button> 
              @endif
            </div>
            </div>
            </div>
            </form>

          </div>
        </div>

      </div>
      <div class="col-lg-7" >
        <div id="employee-info" class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title" id="title-target">Objetivos</h4>
          </div>
          <div class="card-body">
            <div>
              <h4 class="card-title">Venta cruzada</h4>
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
    <hr id="separator">
    <div class="row">
    <div class="col-lg">
      
      <div id="formRadio">
        <form >
          <div class="form-check">
          <h4 id="typeRanking">VER RANKING: </h4>
            <label class="form-check-label" id="selected-label">
              <input id="monthly" class="form-check-input"  type="radio" name="optradio" checked>Mensual<span class="circle">
                                                        <span class="check"></span>
                                                    </span>
            </label>
            <label class="form-check-label">
              <input id="annual" class="form-check-input"  type="radio" name="optradio">Anual<span class="circle">
                                                        <span class="check"></span>
                                                    </span>
            </label>
          </div>
        </form>
      </div>
    </div>
    <div style="margin-right: 85px;margin-top: 15px;">
    <button id="btnSubmit" type="submit" class="btn btn-fill btn-dark-black" >Exportar</button>
      <button id="btnSubmitLoad" type="submit" class="btn btn-dark-black" style="display: none">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        {{ __('Obteniendo datos...') }}
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
svg.ct-chart-bar, svg.ct-chart-line{
	overflow: visible;
}
.ct-label.ct-label.ct-horizontal.ct-end {
  position: relative;
  justify-content: flex-end;
  text-align: right;
  transform-origin: 100% 0;
  transform: translate(-100%) rotate(0deg);
  white-space:nowrap;
}

.card-header-table{
  width: 100%;
  margin-top: 0px !important; 
}
.sales-datatable {
  table-layout: fixed;
  width: 100% !important;
}
.sales-datatable td,
.sales-datatable th{
  /* width: auto !important; */
  /* white-space: normal; */
  text-overflow: ellipsis;
  overflow: hidden;
}

.form-group{
  display: flex;
  justify-content: space-evenly;
  width: 100%;
}

.row{
  justify-content: center;
}

#DataTables_Table_0_paginate > ul.pagination{ margin: 16px 0 !important; }

#btnClear{  
  align-self: end; 
  /* margin-top: 15px; */
}

.employee-info{
  padding-left: 15px;
  margin-top: 50px;
  /* min-height: 5000px; */
}

.employee-info span{
  color: var(--red-icot);
}

#typeRanking{
  margin-bottom: 0px;
  margin-right: 10px;
  margin-left: 100px;
  display: inline-block;
  font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
  font-weight: 700;
  color: black;
}

#formRadio{
  display: inline-block;
  color: red;
}

#monthly{
  margin-right: 15px;
  color: var(--red-icot);
}

#annual{
  margin-right: 15px;
  color: var(--red-icot);
}

#separator{
  margin-bottom: 0;
  padding-bottom: 0;
}

#selected-label{
        color: var(--red-icot);
        font-weight: bold;
    }

</style>
<script type="text/javascript">
  

  $(function () {

    @if (isset($employee) && $employee->rol_id != 1)
      $("#btnClear").hide(); 
    @else
      $("#btnClear").show(); 
    @endif

    $(".form-check-input").change(function() {
            $(".form-check-label").removeAttr('id');
            $(this).parent().attr('id','selected-label');
            // $("#radio_1").prop("checked", true);
        });



        $("#btnSubmit").on('click', function(e) {
                $('#alertErrorCalculate').hide();
                e.preventDefault();
                $("#rankingForm").attr('action', '{{ route('ranking.calculateRankings')}}');
                $('#btnSubmit').hide();
                $('#btnSubmitLoad').show();
                $('#btnSubmitLoad').prop('disabled', true);
                $('#centre').val($("#centre_id option:selected").text());

                params = {};
                params["_token"] = "{{ csrf_token() }}";
                params["centre"] = $('#centre').val();
                params["monthYear"] = $("#monthYearPicker").val();

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
                        $('#alertErrorCalculate').show();
                        $('#btnSubmitLoad').hide();
                        $('#btnSubmit').show();
                        timeOutAlert($('#alertErrorCalculate'));
                    }

                });
            });

  
  var d = new Date();
  var textMonthYear = (d.getMonth()+1) + '/' + d.getFullYear()   ;  
  $('#monthYearPicker').val(textMonthYear);

  $('#annualData').hide();

  google.charts.load('current', {
    packages:['corechart', 'bar']
  });

  $('#monthly').on('click', function(e){
    $('#annualData').hide();
    $('#monthlyData').show();
  });

  $('#annual').on('click', function(e){
    $('#monthlyData').hide();
    $('#annualData').show();
  });

  const colors = [
    // { color: '#1a5e07' },      //medium
    { color: '#b01c2e'},
    { color: '#cccccc' },  //high
    { color: 'seagreen' },   //low
  ];
  
  const options = {
      title: '',
      chartArea: {width: '60%'},
      height:150,
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
            x1: '0%', y1: '0%',
            x2: '100%', y2: '100%'
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
      //isStacked: true,
      series: colors

  };

  function getValueCentre() {

    @if (isset($employee) && $employee->rol_id != 1)
      var centro_id =  '{!! $employee->centre_id !!}';
      var centro    =  '{!! $employee->centre !!}'; 
      $('.bootstrap-select .filter-option').text(centro);
      $("#title-ranking").html("Ranking Anual del GRUPO ICOT");
    @else
      var centro_id =  $('#centre_id option:selected').val();
      var centro    =  $('#centre_id option:selected').text(); 
    @endif

    if (centro_id != "") {
      $('#centre_id option:selected').val(centro_id); 
      
      $("#employee-centre").html(centro);
      $("#title-target").html("Objetivos " + centro);
      $("#title-sales").html("Ranking Mensual de  " + centro);

      if ( centro_id == {{ env('ID_CENTRE_HCT') }} ) {
        $("#title-ranking").html("Ranking Anual de  " + centro);
      } else {
        $("#title-ranking").html("Ranking Anual del GRUPO ICOT");
      }
    
    } else {
      $("#employee-centre").html(centro);
      $("#title-target").html("Objetivos  del  GRUPO ICOT");
      $("#title-sales").html("Ranking Mensual del  GRUPO ICOT");
      $("#title-ranking").html("Ranking Anual del GRUPO ICOT");
    }

  }

  function getLabelMonth(month){
    var  months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    var monthName=months[month-1];
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
      $("#vp_ok").hide();
    } else {
      $("#vp_pending").hide();
      $("#vp_ok").show();
    }
    //options.hAxis.title = getLabelMonth($("#monthYearPicker").val().substr(0,$("#monthYearPicker").val().indexOf('/'))); 
    chart.draw(data, options);
  }

  function drawGraphVC(val) {
    bb = [
      ['Objetivo VC', 'Venta Cruzada', 'Objetivo Venta Cruzada'],
      ['', val['value'], val['target']],   
    ];
    var data =  new google.visualization.arrayToDataTable(bb);
    
    var chart = new google.visualization.BarChart(document.getElementById('chart_div_vc'));
    chart.draw(data, options);
  }

  function drawTarget(centre_id) {
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
                if(textStatus === 'success') {
                    var target = JSON.parse(data); 
                    google.charts.setOnLoadCallback(function () {
                      options.hAxis.title = getLabelMonth($("#monthYearPicker").val().substr(0,$("#monthYearPicker").val().indexOf('/'))); 
                      drawGraphVC(target.data.vc); 
                      drawGraphVP(target.data.vp);
                    });
                    
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            //console.log('fail submit');
            
        });
  }
  
  $('.sales-month-datatable').on('error.dt', function(e, settings, techNote, message) {
    if (message.indexOf("Error") > -1) {
        message = message.substr(message.indexOf("Error")); 
        alert(message);     
    }
  })
  $.fn.dataTable.ext.errMode = 'none';

  function getSales(idDataTable) {
      var table; 
      var columnsRank =  [{data: 'position', name: 'position'},
                          {data: 'employee', name: 'employee'}];
      if (idDataTable == '.sales-year-datatable') {
        columnsRank.push({data: 'centre', name: 'centre'});
      }
      columnsRank.push({data: 'total_price', name: 'total_price'});
      columnsRank.push({data: 'total_incentive', name: 'total_incentive'});

      table = $(idDataTable).DataTable({
            processing: true,
            serverSide: true,
            searching:false,
            autoWidth: false,
            bDestory: true,
            bRetrieve: true,
            language:{
                "url": "{{ asset('dataTables/Spanish.json') }}"
            },
            ajax: {
                url: "{{ route('home.getSales') }}",
                data: function (d) {
                    //d.status = $('#status').val(),
                    //d.start     = 0,
                    d.search    = $('input[type="search"]').val(),
                    d.monthYear = $('#monthYearPicker').val(),


                    /** Opciones Ranking **/ 

                    /** Si es seleccionado HCT siempre pasar HCT
                        Si no es HCT si es anual (null centros) , mensual el centro */
                    d.centre    = $('#centre_id option:selected').val() == {{ env('ID_CENTRE_HCT')}}
                                  ? $('#centre_id option:selected').val() :
                                  idDataTable != '.sales-month-datatable'  ? null : $('#centre_id option:selected').val() , 
                    d.type      = idDataTable == '.sales-month-datatable' ? 'monthly' : 'anual'
                }
            },
            columns: columnsRank,
            search: {
                "regex": false,
                "smart":false
            },
            // order:[1,'desc'],
            responsive: true,
            initComplete: function (data) {
                this.api().columns().every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    $(input).appendTo($(column.footer()).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        //column.search(val ? val : '', true, false).draw();
                        column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                    });
                    
                });
                
            }
      });
      if ($.fn.dataTable.isDataTable( idDataTable)) {
        $(idDataTable).DataTable().ajax.reload();
      }
  }

  function clearForms()
  {
    
      $('select').val('');
      //$('select').selectpicker("refresh");
      $("#employee-centre").html("GRUPO ICOT");
      $("#title-target").html("Objetivos  del  GRUPO ICOT");
      $("#title-sales").html("Ranking Mensual del  GRUPO ICOT");
      $("#title-ranking").html("Ranking Anual del GRUPO ICOT");
  }
  
  $("#btnClear").on('click', function(e){
      e.preventDefault();
      var d = new Date();
  var textMonthYear = (d.getMonth()+1) + '/' + d.getFullYear()   
  $('#monthYearPicker').val(textMonthYear);
      clearForms();
      $('#centre_id').trigger("change");
  });

  getValueCentre(); 
  drawTarget({{$user->centre_id}}); 
  getSales('.sales-month-datatable');
  getSales('.sales-year-datatable');
  //clearForms();

  $("#centre_id").on('change', function () {
    getValueCentre(); 

    drawTarget($('#centre_id option:selected').val()); 
    getSales('.sales-month-datatable');
    getSales('.sales-year-datatable');

  });
  
  $('#monthYearPicker').MonthPicker({
    OnAfterChooseMonth: function() { 
        drawTarget($('#centre_id option:selected').val()); 
        getSales('.sales-month-datatable');
        getSales('.sales-year-datatable');
      //}
    } 
  });
  //$('#monthPicker').datepicker( $.datepicker.regional[ "es" ] );
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