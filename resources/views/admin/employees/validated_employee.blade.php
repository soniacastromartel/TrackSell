
@extends('layouts.logged')
@section('content')
@include('inc.navbar')
@include('common.alert')


  <div class="modal" tabindex="-1" role="dialog" id="modal-validate">
  <input type="hidden" id="idEmployee"/>
  <input type="hidden" id="nameEmployee"/>
  <input type="hidden" id="emailEmployee"/>
  <div id="okConfirmIdEmployee" class="alert alert-success alert-timeout alertOkAction" role="alert" style="display:none">
    <span style="display: inline-block !important" class="spinner-border spinner-border-sm" role="status" aria-hidden="true">
    </span>
    Se ha validado el empleado
  </div>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar validación empleado</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="message-validation" class="px-4 center text-center">¿Confirma la validación con el nombre de usuario: <bold>GENEMP</bold>?</p>
      </div>
      <div class="modal-footer row">
        <div class="col-12 text-center">
          <button id="btnConfirmIdEmployee" type="button" class="btn btn-red-icot">SI</button>        
          <button id="btnCancelConfirmIdEmployee" type="button" class="btn btn-default" data-dismiss="modal">NO</button>
          <br><br>
          <button id="btnConfirmChangeIdEmployee" type="button" class="btn-change-username  btn btn-warning">
            <span class="material-icons mr-1">update</span>CAMBIAR DATOS DE USUARIO
          </button>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal" tabindex="-1" role="dialog" id="modal-change-username">
  <div id="okConfirmChangeIdEmployee" class="alert alert-success alert-timeout alertOkAction" role="alert" style="display:none">
    <span style="display: inline-block !important" class="spinner-border spinner-border-sm" role="status" aria-hidden="true">
    </span>
    Se ha cambiado los datos de usuario
  </div>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar datos de usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <input type="text" class="form-control" name="idConfirmEmployee" id="idConfirmEmployee"  placeholder="" value="">
          <label for="username">Usuario</label>
          <input type="text" class="form-control" name="nameConfirmEmployee" id="nameConfirmEmployee"  placeholder="" value="">
          <label for="name">Nombre</label>
          <input type="text" class="form-control" name="emailConfirmEmployee" id="emailConfirmEmployee"  placeholder="" value="">
          <label for="email">Email</label>


      </div>
      <div class="modal-footer">
        
      <button id="btnChangeIdEmployee" type="button" class="btn btn-red-icot" data-dismiss="modal-validate">
        <span class="material-icons mr-1">update</span>CAMBIAR
      </button> <br>
        <button id="btnCancelChangeIdEmployee" type="button" class="btn btn-default" data-dismiss="modal">
          <span class="material-icons mr-1">cancel</span>  
          CANCELAR
        </button>        
      </div>
    </div>
  </div>
</div>
<div class="content">
    <div class="container-fluid">
        <div class="row col-md-12 mb-3 ">
            <div class="col-md-8">
            </div>
        </div>    
        <table class="table table-striped table-bordered employees-datatable col-md-12">
            <thead class="table-header">
                <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table> 
    </div>    
</div>   
<style>

.alertOkAction{
    width: 400px !important;
    min-width: 400px;
    top: 100px !important;
    position: absolute;
    left: 725px;
    height: 40px;
    padding: 10px;
  }

  #modal-title{
    font-weight: 900;
    color: black;
    font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
  }

  td{
    font-weight: bold;
  }
</style>


<script type="text/javascript">
    $(function () {
        $(".nav-item").each(function(){
            $(this).removeClass("active");
        });
        $('#pagesConfig').addClass('show');
        $('#pending_employee').addClass('active');
        var cambioDatos = 0; 


        var table = $('.employees-datatable').DataTable({
            processing: true,
            serverSide: true,
            language:{
                "url": "{{ asset('dataTables/Spanish.json') }}"
            },
            ajax: {
                url: "{{ route('employees.indexPending') }}",
                data: function (d) {
                    d.search = $('input[type="search"]').val()
                }
            },
            columnDefs:[{
              targets: [-1,0,1,2],
              className: 'dt-body-center'


            }],
            columns: [ 
                {data: 'name', name: 'name'},
                {data: 'username', name: 'username'},
                {data: 'email', name: 'email'},
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: true, 
                    searchable: true
                },
            ],
            search: {
                "regex": true,
                "smart":true
            },
            initComplete: function () {
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

    $("table").on('click',"a.btnConfirmValidate",function(event){
        event.stopPropagation();
        event.stopImmediatePropagation();
        var employeeA3  = $(this).data('a3_nombre');
        var employeePDI = $(this).data('pdi_nombre');
        var email       = $(this).data('email');
        var username    = $(this).data('username');
        
        $("#idEmployee").val(username);  
        $("#nameEmployee").val(employeePDI);  
        $("#emailEmployee").val(email);  

        //CONFIRMAR Nombre y NIF ( Nombre  y DNI asociados en PDI , comparando con Nombre de A3)
        $("#message-validation").html( '<strong>¿Es correcto el nombre de usuario: ' + employeePDI +'?</strong>'+ ' <br> <b>El nombre en A3 es: </b> <bold>' + employeeA3 + '</bold>');

        $("#modal-validate").modal('show');

    });

    $("#modal-validate").on('click', 'button.btn-change-username', function(event){
        $("#okConfirmChangeIdEmployee").hide();
        $("#idConfirmEmployee").val($("#idEmployee").val());
        $("#nameConfirmEmployee").val($("#nameEmployee").val());
        $("#emailConfirmEmployee").val($("#emailEmployee").val());
        $("#modal-change-username").modal('show');
    });

    $("#btnConfirmIdEmployee").on('click',function(event){
      $("#modal-validate").modal('hide'); 
      url = "{{ route('employees.confirmUsername') }}"; 
      confirmValidateEmployee(url); 
    });

    $("#btnChangeIdEmployee").on('click',function(event){
      url = "{{ route('employees.changeUsername') }}"; 
      confirmValidateEmployee(url);
      
    });

    

    function confirmValidateEmployee(url) {
        var params = { 'username'     :  $("#idEmployee").val() ,
                      '_token'       : "{{ csrf_token() }}"
        }; 
        if (url.indexOf('changeUsername') != -1) {
          params['username'] = $("#idConfirmEmployee").val();
          params['name']     = $("#nameConfirmEmployee").val();
          params['email']    = $("#emailConfirmEmployee").val();
          $("btnChangeIdEmployee").attr("disabled",true);
          $("btnCancelChangeIdEmployee").attr("disabled",true);
        } else {
          $("#btnConfirmIdEmployee").attr("disabled",true);
          $("#btnCancelConfirmIdEmployee").attr("disabled",true);
          $("#btnConfirmChangeIdEmployee").attr("disabled",true);
        }
        $.ajax({
              url: url,
              type: 'post',
              data: params,
              dataType: 'json',
              success: function(data, textStatus, jqXHR) {
                  // if success, HTML response is expected, so replace current
                  if(textStatus === 'success') {
                    if (url.indexOf('confirmUsername') != -1) {
                      $("#okConfirmIdEmployee").show();
                      setTimeout( function() {
                        $("#modal-validate").modal('hide'); 
                      }, 3000);
                    } else {
                      $("#okConfirmChangeIdEmployee").show();
                      setTimeout( function() {
                        $("#modal-change-username").modal('hide');
                      }, 3000);
                      $("#modal-validate").modal('show');
                      $("#idEmployee").val($("#idConfirmEmployee").val()); 
                      $("#nameEmployee").val($("#nameConfirmEmployee").val()); 
                      $("#emailEmployee").val($("#emailConfirmEmployee").val());
                    }
                  }
              },
              error: function(xhr, status, error) {
                  
              },
              complete: function(){
                table.ajax.reload(); 
              }
        }).fail(function(jqXHR, textStatus, errorThrown) {
          alert('Error'+jqXHR.responseText);
        });
    } 
  });
</script>

@endsection