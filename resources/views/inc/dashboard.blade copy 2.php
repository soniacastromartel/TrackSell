<div class="content">
  <div class="container-fluid">
    @if ( $user->levelAccess == 1)
    <div class="row">
      <div class="form-group col-lg-3">
        
      </div>
      <div class="form-group col-lg-4">
        <div class="dropdown bootstrap-select">
          <label for="centre_employee_id">Centro prescriptor. * </label>
          <select class="selectpicker" name="centre_employee_id" id="centre_employee_id" data-size="7" data-style="btn btn-primary btn-round" title="* Seleccione Centro" tabindex="-98">
            
            @foreach ($centres as  $centre)
            <option value="{{$centre->id}}">{{$centre->name}}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    @endif
    {{-- <div class="row"> 
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title">Datos de empleado</h4>
          </div>
          <div class="card-body" style="min-height:412px">
            @foreach ($employees as  $employee)
            <h3>Bienvenido:
              <small>{{ $employee->employee_name }}</small>
            </h3>
            <h3>Su centro:
              <small>{{ $employee->centre }}</small>
            </h3>
            @endforeach
          </div>
          
          <div class="card-footer">
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title">Objetivos</h4>
          </div>
          <div class="card-header">
            <h4 class="card-title">Venta Cruzada</h4>
            <div id="chart_div_vc"></div>
          </div>
          <div class="card-header">
            <h4 class="card-title">Venta Privada</h4>
            <div id="chart_div_vp"></div>
          </div>
          <div class="card-footer">
          </div>
        </div>
      </div>
      </div>
    </div> --}}
    {{-- <div class="row"> 
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title">Venta del centro</h4>
          </div>
          <div class="card-header-table">
            <table class="table table-striped table-bordered sales-datatable col-lg-6">
              <thead>
                  <tr>
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
    </div>    --}}
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
  width: 450px;
  margin-top: 0px !important; 
}
#DataTables_Table_0_paginate > ul.pagination{
  margin: 16px 0 !important; 
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

</style>
<script type="text/javascript">

  $(function () {
    
  //google.charts.load('current', {packages: ['corechart', 'bar']});
  //google.charts.setOnLoadCallback(drawAnnotations);
  /*@if (isset($vc['value'])) 
  @endif
  @if (isset($vp['value'])) 
  @endif*/


  google.charts.load('current', {
    packages:['corechart', 'bar']
  }).then(drawGraphVC);//IF ISSET VP  FIXME
  
  google.charts.load('current', {
    packages:['corechart', 'bar']
  }).then(drawGraphVP); //IF ISSET VP  FIXME
  
  const colors = [
   
    { color: '#1a5e07' },      //medium
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
        title: 'Enero',
        minValue: 0,
      },
      vAxis: {
        title: ''
      },
      //isStacked: true,
      series: colors

  };

  function drawGraphVP() {
    
    var data = new google.visualization.arrayToDataTable([
      ['Objetivo VP', 'Venta Privada', 'Objetivo Venta Privada'],
      
        ['', {{$vp['value']}}, {{$vp['target']}}],
      
    ]);
    
    var chart = new google.visualization.BarChart(document.getElementById('chart_div_vp'));
    chart.draw(data, options);
  }

  function drawGraphVC() {
    
    var data = new google.visualization.arrayToDataTable([
      ['Objetivo VC', 'Venta Cruzada', 'Objetivo Venta Cruzada'],

        ['', {{$vc['value']}}, {{$vc['target']}}],

    ]);

    var chart = new google.visualization.BarChart(document.getElementById('chart_div_vc'));
    chart.draw(data, options);
  }

  var table = $('.sales-datatable').DataTable({
            processing: true,
            serverSide: true,
            searching:false,
            autoWidth: true,
            language:{
                "url": "{{ asset('dataTables/Spanish.json') }}"
            },
            ajax: {
                url: "{{ route('getSales') }}",
                data: function (d) {
                    //d.status = $('#status').val(),
                    d.search = $('input[type="search"]').val()
                }
            },
            columns: [ 
                {data: 'employee', name: 'employee'},
                {data: 'total_price', name: 'total_price'},
                {data: 'total_incentive', name: 'total_incentive'},
              
            ],
            search: {
                "regex": false,
                "smart":false
            },
            order:[1,'desc'],
            responsive: true,
            initComplete: function () {
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
  

});
    
</script> 