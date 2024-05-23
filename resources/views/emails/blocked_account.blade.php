
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>

    <img src="{{ $message->embed($logoHeader) }}" style="width: 50%; height: auto; margin-bottom: 2%;" alt="Logo"><br>
 
<div>
    Solicitud de desbloqueo de cuenta desde PDI App <br>
    <p>
        Usuario: {{ $employeeData['username'] }}<br>
        Nombre del empleado: {{ $employeeData['name'] }} <br>
    </p>
</div>

</html>







