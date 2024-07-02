{{-- Message --}}
<link rel="stylesheet" href="{{ asset('css/sweetalert-custom.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (Session::has('success'))
    <script>
        Swal.fire({
            title: '¡Perfecto!',
            text: '{{ session('success') }}',
            icon: 'success',
            timer: 4000,
            showConfirmButton: false,
            footer: ' ',
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
            footer: ' ',
        });
    </script>
@endif
<script type="text/javascript">
    function showAlert(type, message) {
        Swal.fire({
            title: type === 'success' ? '¡Perfecto!' : '¡Error!',
            text: message,
            icon: type,
            timer: 4000,
            showConfirmButton: false
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
            timer: 4000,
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
            confirmButtonColor: '#388e3c',
            cancelButtonColor: '#d32f2f',
            confirmButtonText: '¡Sí, eliminarlo!',
            cancelButtonText: 'Cancelar'
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

        return isConfirmed ? reason : null;
    }
</script>
