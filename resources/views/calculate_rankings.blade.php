@extends('layouts.logged')

@section('content')
@include('inc.navbar')
@include('common.alert')

    <div id="alertErrorCalculate" class="alert alert-danger" role="alert" style="display: none">
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card ">
                        <div class="card-header card-header-danger">
                            <h4 class="card-title">Informes</h4>
                        </div>
                        <div class="card-body">
                            <form id="rankingForm" method="POST">

                                @csrf
                                @method('POST')

                                <div class="row px-5" style="justify-content: space-between;" >
                                    <div class="row col-md-8">
                                        <div class="form-group col-md-4">
                                            <div class="dropdown bootstrap-select">
                                                <select class="selectpicker" name="centre_id" id="centre_id" data-size="7"
                                                    data-style="btn btn-red-icot btn-round" title=" Seleccione Centro"
                                                    tabindex="-98">
    
                                                    @foreach ($centres as $centre)
                                                        <option value="{{ $centre->id }}">{{ $centre->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="centre" id="centre" />
                                            </div>
                                        </div>
    
                                        <div class="form-group col-md-4">
                                            <div class="dropdown bootstrap-select">
                                                <select class="selectpicker" name="datepickerType" id="datepickerType"
                                                    data-size="7" data-style="btn btn-red-icot btn-round"
                                                    title=" Mensual / Anual" tabindex="-98">
                                                    <option value="1" selected>Mensual</option>
                                                    <option value="2">Anual</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="monthYearPickerContainer" class="col-md-2">
                                            <div class="input-group date mt-2">
                                                <input id="monthYearPicker" class='form-control' type="text" placeholder="yyyy/mm"/>
                                                <input type="hidden" name="monthYear" id="monthYear" />
                                            </div>
                                        </div>
                                        <div id="yearPickerContainer" class="form-group date col-md-1">
                                            <input id="yearPicker" class='form-control' type="text" placeholder="yyyy"/>
                                        </div>
                                        

                                    </div>
                                    <div class="col-md-3">
                                        <button id="btnClear" href="#" class="btn btn-fill btn-warning"><span class="material-icons">
                            clear_all
                            </span>
                                            {{ __('Limpiar formulario') }}
                                        </button>
                                        <button id="btnSubmit" type="submit"
                                            class="btn btn-fill btn-default"><span class="material-icons">
                            file_download
                            </span>  Exportar</button>
                                        <button id="btnSubmitLoad" type="submit" class="btn btn-dark-black"
                                            style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            {{ __('Obteniendo datos...') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
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

        .ui-datepicker-calendar {
            display: none;
        }

        .file-upload {
            margin: 0 10px 0 25px;
        }

        .file-upload input.upload {
            position: absolute;
            top: 0;
            right: 0;
            margin: 0;
            padding: 0;
            z-index: 10;
            font-size: 20px;
            cursor: pointer;
            height: 36px;
            opacity: 0;
            filter: alpha(opacity=0);
        }

        #fileuploadurl {
            border: none;
            font-size: 12px;
            padding-left: 0;
            width: 250px;
        }

        .ui-datepicker-calendar {
            display: none;
        }

    </style>
    
    <script type="text/javascript">
        $(function() {

            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $('#pagesReport').addClass('show');
            $('#calculateRanking').addClass('active');

            function clearForms() {
                $('select').val('');
                $('select').selectpicker("refresh");
            }
            $("#btnClear").on('click', function(e) {
                e.preventDefault();
                clearForms();
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
                if ($("#datepickerType").val() == 1) { //MENSUAL
                    params["monthYear"] = $("#monthYearPicker").val();
                }
                if ($("#datepickerType").val() == 2) { //ANUAL
                    params["year"] = $("#yearPicker").val();
                }

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
                        $('#btnSubmitLoad').hide();
                        $('#btnSubmit').show();
                        timeOutAlert($('#alertErrorCalculate'), response.errors);
                    }

                });
            });

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
                } else {
                    showYearPicker();
                    $('#yearPickerContainer').show();
                    $('#monthYearPickerContainer').hide();
                }
            });

            function showMonthYearPicker() {
                var textMonthYear = (d.getMonth() + 1) + '/' + d.getFullYear();
                $('#monthYearPicker').val(textMonthYear);
                // Default functionality.
                $('#monthYearPicker').MonthPicker();
                $('#monthYearPicker').MonthPicker({
                    ShowIcon: true,
                //     Button: '<img src="assets/img/calendar.gif" title="Select date" />'
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
                    // showOn: "button",
                    // buttonImage: "assets/img/calendar.gif",
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

        });

        function timeOutAlert($alert, $message) {
        $alert.text($message);
        $alert.show().delay(2000).slideUp(300);
    }
    </script>
@endsection
