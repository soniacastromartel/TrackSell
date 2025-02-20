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

    function confirmAdvice($title, $message) {
        return Swal.fire({
            title: $title,
            text: $message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#bc012e',
            confirmButtonText: 'Continuar',
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

    async function confirmWithSelect(title, list) {
        const {
            value: item
        } = await Swal.fire({
            title: title,
            input: 'select',
            inputOptions: list,
            inputPlaceholder: "Selecciona un centro",
            showCancelButton: true,
            inputValidator: (value) => {
                return !value ? 'Debe seleccionar uno' : null;
            },
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-primary btn-lg mr-2',
                cancelButton: 'btn btn-danger btn-lg'
            }
        });

        return item || null;
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

    async function showDateAlert(isPresent = false) {
    const { value: date } = await Swal.fire({
        title: "📅 Seleccione una fecha",
        html: `
            <div style="font-size: 18px; color: #555;">
                Por favor, elija una fecha ${isPresent ? "a partir de hoy" : "en cualquier momento"}:
            </div>
            <input type="date" id="custom-date-input" class="swal2-input" style="margin-top: 10px; width: 80%;">
        `,
        showCancelButton: true,
        confirmButtonText: "Seleccionar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        width: "400px",
        customClass: {
            popup: "swal2-popup-custom"
        },
        didOpen: () => {
            const input = document.getElementById("custom-date-input");
            if (isPresent) {
                const today = new Date().toISOString().split("T")[0];
                input.min = today; // Aplica la restricción solo si isPresent es true
            }

            Swal.getConfirmButton().addEventListener("click", () => {
                Swal.close();
            });
        },
        preConfirm: () => {
            const selectedDate = document.getElementById("custom-date-input").value;
            if (!selectedDate) {
                Swal.showValidationMessage("Debe seleccionar una fecha");
                return false;
            }
            if (isPresent) {
                const today = new Date().toISOString().split("T")[0];
                if (selectedDate < today) {
                    Swal.showValidationMessage("La fecha no puede ser anterior a hoy");
                    return false;
                }
            }
            return selectedDate;
        }
    });

    return date || null;
}



    function showListAlert(title, listItems, emptyMessage = "No hay elementos disponibles.") {
        if (!listItems || listItems.length === 0) {
            Swal.fire({
                title: "Información",
                text: emptyMessage,
                icon: "warning",
                confirmButtonText: "OK"
            });
            return;
        }
        const itemList = listItems.map(item => `<li>${item}</li>`).join('');
        Swal.fire({
            title: title,
            html: `<ul style="text-align:left; padding-left: 20px; font-size: 16px;">${itemList}</ul>`,
            icon: "info",
            confirmButtonText: "OK",
            width: '50%',
            customClass: {
                popup: 'swal-wide'
            },
            allowOutsideClick: false,
            allowEscapeKey: false,
        });
    }
</script>
