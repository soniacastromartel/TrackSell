<div class="content">
  <div class="container-fluid mt-3">
    <div class="row">
      <div class="col-lg-4">
      <div id="employe-info" class="card " style="margin: 0;">
          <div class="card-header card-header-danger">
            <h4 class="card-title">Datos de empleado</h4>
          </div>
          <div class="card-body employee-info">
            <h5 class="label">Nombre:
              <span>{{ $employee->name}}</span>
            </h5>
            <h5 class="label">Centro de trabajo:
              <span id="employee-centre">{{ is_null($employee->centre) ? 'GRUPO ICOT':  $employee->centre }}</span>
            </h5>
          </div>
        </div>
        <div id="employee-info" class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title">Búsqueda</h4>
          </div>
          <div class="card-body">
            <div class="form-group row">
              <div class="col-sm-12" style="padding-top: 15px;">
                <label class="label" for="monthYearPicker">Mes / Año </label>
                <div class="mt-2 input-group date">
                  <input id="monthYearPicker" class='form-control' type="text"  placeholder="yyyy/mm" />
                  <input type="hidden" name="monthYear" id="monthYear"/>
                </div>
              </div>
              <div class="form-group col-sm-12">
                <div class="dropdown bootstrap-select">
                  <label class="label" for="centre_employee_id" style="margin-top: 11px;">Centro prescriptor <span class="obligatory">*</span> </label>
                  <select class="selectpicker" name="centre_employee_id" id="centre_employee_id" data-size="7" data-style="btn btn-red-icot btn-round" 
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
                  <input type="hidden" id="centre_selected"/>
                </div>
              </div>
              @if (isset($employee) && $employee->rol_id == 1)
                <div class="form-group col-lg-5 col-sm-12 btn-end" >
                  <button id="btnClear" href="#" class="btn btn-fill btn-warning">
                  {{ __('Limpiar formulario') }}
                  </button> 
                </div>
              @endif
            </div>
          </div>
        </div>
       
      </div>
      <div class="col-lg-7" >
        <div class="card" style=" margin-top: 0;">
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
    <div class="row">
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
    <div class="row">
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
  width: 90%;
  margin-top: 0px !important; 
}
.sales-datatable {
  table-layout: fixed;
  width: 100% !important;
}
.sales-datatable td,
.sales-datatable th{
  width: auto !important;
  white-space: normal;
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

#btnClear{  align-self: end; }

.employee-info{
  padding-left: 15px;
}

.employee-info span{
  color: var(--red-icot);
}



</style>
<script type="text/javascript">

  $(function () {
  
  var d = new Date();
  var textMonthYear = (d.getMonth()+1) + '/' + d.getFullYear()   ;  
  $('#monthYearPicker').val(textMonthYear);


  google.charts.load('current', {
    packages:['corechart', 'bar']
  });
 
  const colors = [
   
    { color: '#B01C2E' },      //medium
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
      var centro_id =  $('#centre_employee_id option:selected').val();
      var centro    =  $('#centre_employee_id option:selected').text(); 
    @endif
   
    if (centro_id != "") {
      $('#centre_employee_id option:selected').val(centro_id); 
      
      <? /*$("#employee-centre").html(centro); */ ?>
      $("#title-target").html("Objetivos " + centro);
      $("#title-sales").html("Ranking Mensual de  " + centro);

      if ( centro_id == {{ env('ID_CENTRE_HCT') }} ) {
        $("#title-ranking").html("Ranking Anual de  " + centro);
      } else {
        $("#title-ranking").html("Ranking Anual del GRUPO ICOT");
      }
    
    } else {

      // $("#employee-centre").html(centro);
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

  function drawGraph (val, titles, idDiv){
    let x = [ titles,
      ['', val['value'], val['target']]
    ];

    let data = new google.visualization.arrayToDataTable(x);

    var chart = new google.visualization.BarChart(document.getElementById(idDiv));
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

                      let VC = drawGraph(target.data.vc, ['Objetivo VC', 'Venta Cruzada', 'Objetivo Venta Cruzada'] , 'chart_div_vc');
                      let VP = drawGraph(target.data.vp, ['Objetivo VP', 'Venta Privada', 'Objetivo Venta Privada'] , 'chart_div_vp');
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
            autoWidth: true,
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
                    d.centre    = $('#centre_employee_id option:selected').val() == {{ env('ID_CENTRE_HCT')}}
                                  ? $('#centre_employee_id option:selected').val() :
                                  idDataTable != '.sales-month-datatable'  ? null : $('#centre_employee_id option:selected').val() , 
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
      clearForms();
      $('#centre_employee_id').trigger("change");
  });

  getValueCentre(); 
  drawTarget({{$user->centre_id}}); 
  getSales('.sales-month-datatable');
  getSales('.sales-year-datatable');
  //clearForms();

  $("#centre_employee_id").on('change', function () {
    getValueCentre(); 

    drawTarget($('#centre_employee_id option:selected').val()); 
    getSales('.sales-month-datatable');
    getSales('.sales-year-datatable');

  });
  
  $('#monthYearPicker').MonthPicker({
    OnAfterChooseMonth: function() { 
        drawTarget($('#centre_employee_id option:selected').val()); 
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