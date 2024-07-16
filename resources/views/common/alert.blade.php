{{-- Message --}}
<link rel="stylesheet" href="{{ asset('css/sweetalert-custom.css') }}">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (Session::has('success'))
    <script>
        Swal.fire({
            title: '¡Perfecto!',
            text: '{{ session('success') }}',
            icon: 'success',
            timer: 4000,
            showConfirmButton: false,
        });
    </script>
@endif

@if (Session::has('error'))
    <script>
        Swal.fire({
            title: '¡Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            timer: 4000,
            showConfirmButton: false,
        });
    </script>
@endif
<script type="text/javascript">
    var time = 4000;

    function showAlert(type, message) {
        Swal.fire({
            title: type === 'success' ? '¡Perfecto!' : '¡Error!',
            text: message,
            icon: type,
            timer: time,
            showConfirmButton: false
        });
    }

    function showToast(icon, message) {
        const notification = Swal.mixin({
            toast: true,
            position: 'top-end',
            iconColor: 'white',
            customClass: {
                popup: 'colored-toast',
            },
            showConfirmButton: false,
            timer: time,
            timerProgressBar: true,
        });
        notification.fire({
            icon: icon,
            title: message,
        });
    }

    function showWelcomeToast(message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'center',
            iconColor: 'white',
            customClass: {
                popup: 'colored-toast',
            },
            background: '#a5dc86',
            showConfirmButton: false,
            timer: time,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        Toast.fire({
            icon: 'success',
            title: message
        });
    }

    function confirmedRequest() {
        return Swal.fire({
            title: '¿Está seguro?',
            text: "Está a punto de eliminar ¿Desea Continuar?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#bc012e',
            confirmButtonText: '¡Sí, eliminarlo!',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-primary btn-lg mr-2',
                cancelButton: 'btn btn-danger btn-lg',
                loader: 'custom-loader',
            },
            loaderHtml: '<div class="spinner-border text-primary"></div>',
            preConfirm: () => {
                Swal.showLoading();
                return new Promise((resolve) => {
                    setTimeout(() => {
                        resolve(true);
                    }, 3000);
                });
            }
        });
    }

    async function confirmWithInput() {
        const {
            value: reason,
            isConfirmed
        } = await Swal.fire({
            title: '¿Está seguro?',
            text: "Está a punto de eliminar. ¡No podrá revertir esto!",
            icon: 'question',
            input: "text",
            inputLabel: "Motivo:",
            inputValue: "",
            showCancelButton: true,
            inputValidator: (value) => {
                if (!value) {
                    return "Debe escribir un motivo";
                }
            }

        });
        // if (reason) {
        //     Swal.fire(`La razón es ${isConfirmed}`);
        // }


        return isConfirmed ? reason : null;
    }
</script>
