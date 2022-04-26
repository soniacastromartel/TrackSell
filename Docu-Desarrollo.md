## 1. Proceso Cron para sincronizar empleados con datos A3

    1.- Inicialmente hacemos consulta a A3 con datos necesarios. 

    2.- Distinguimos proceso si se realiza para todos los empleados en PDI o se fuerza un empleado. 
        Este job se lanza una vez al día según configuración en app/Console/Kernel.php metodo schedule
        Si se quiere forzar de manera manual ir a PDI Web->Configuración->Empleados:

            - botón verde Sincronizar A3  
            - botón azul Sincronizar A3 para un empleado en particular
            
    3.- Buscamos empleados en PDI, según parametros (nombre empleado o no)

    4.- Recorremos empleados que nos ha dado a3 y comparamos a nivel de nombre para extraerlos

    5.- Con el empleado que encontramos lo tratamos, siempre y cuando no tenga marca de employee->force_centre_id; 

        - actualizamos datos del empleado ( dni, category, phone, email, cod_employee, cod_business)
        - obtenemos el centro pdi según datos de a3 ( normalizado en tabla a3_centres)
        - segun fecha de a3:
        - el empleado se va a actualizar (incluimos en updatedEmployeeIDS)
        - el empleado se va a borrar ( incluimos en deletedEmployeeIDS)
        - creamos o actualizamos datos en employee_history

    6.- Con empleados marcados a borrar: 
        - Realizamos actualización en employee_history
        - Si no están para actualizar, buscar última fecha de baja para marcarla en el cancellation_date

    7.- Marcamos el centro principal: 

        - Según fecha de contratación más antigüa.
  

 Mejoras del proceso ( incluidos fixme)

        - quitar parte de código uso de dateCancel
        - asignarCentroPrincipal sólo para los que se actualizan



## Testing

Create new unit test

    php artisan make:test TargetTest --unit

Launch doing test

    docker-compose exec app2 php artisan test

TargetTest: 

    metodo: test_tracing_get_target
    
        - se realiza test de metodo TargetService->getTarget ( utilizado para cálculos de incentivos)

        - se pasa a testear funcionalidad para todos los centros activos y todos los meses

    metodo: test_tracing_state_target

        - se realiza test de metodo TargetService->stateTarget
        - cuidado control usos , se cambia parametros

Se logea en fichero storage/logs/testing.log