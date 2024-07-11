# crear tablas de database/migrations
#docker-compose run --rm artisan migrate 
ALTER TABLE
    pdi2.employees
add
    column user_id INT;

ALTER TABLE
    pdi2.employees CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE
    pdi2.tmp_usuarios CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE
    pdi2.trackings DROP FOREIGN KEY started_user_id;

ALTER TABLE
    pdi2.trackings DROP FOREIGN KEY apointment_user_id;

ALTER TABLE
    pdi2.trackings DROP FOREIGN KEY service_user_id;

ALTER TABLE
    pdi2.trackings DROP FOREIGN KEY invoiced_user_id;

ALTER TABLE
    pdi2.trackings DROP FOREIGN KEY validation_user_id;

ALTER TABLE
    pdi2.trackings DROP FOREIGN KEY cancellation_user_id;

ALTER TABLE
    pdi2.trackings
ADD
    CONSTRAINT `trackings_apointment_user_id_foreign` FOREIGN KEY (`started_user_id`) REFERENCES `employees` (`id`);

ALTER TABLE
    pdi2.trackings
ADD
    CONSTRAINT `trackings_apointment_user_id_foreign` FOREIGN KEY (`apointment_user_id`) REFERENCES `employees` (`id`);

ALTER TABLE
    pdi2.trackings
ADD
    CONSTRAINT `trackings_service_user_id_foreign` FOREIGN KEY (`service_user_id`) REFERENCES `employees` (`id`);

ALTER TABLE
    pdi2.trackings
ADD
    CONSTRAINT `trackings_invoiced_user_id_foreign` FOREIGN KEY (`invoiced_user_id`) REFERENCES `employees` (`id`);

ALTER TABLE
    pdi2.trackings
ADD
    CONSTRAINT `trackings_validation_user_id_foreign` FOREIGN KEY (`validation_user_id`) REFERENCES `employees` (`id`);

ALTER TABLE
    pdi2.trackings
ADD
    CONSTRAINT `trackings_cancellation_user_id_foreign` FOREIGN KEY (`cancellation_user_id`) REFERENCES `employees` (`id`);

ALTER TABLE
    pdi2.trackings
add
    centre_employee_id INT;

ALTER TABLE
    pdi2.trackings
ADD
    CONSTRAINT `trackings_centre_employee_id_foreign` FOREIGN KEY (`centre_employee_id`) REFERENCES `centres` (`id`);

ALTER TABLE
    pdi2.trackings
ADD
    observations varchar(255) DEFAULT NULL;

ALTER TABLE
    pdi2.trackings
ADD
    quantity INT UNSIGNED DEFAULT 1;

ALTER TABLE
    pdi2.centres
ADD
    island varchar(100);

insert into
    pdi2.roles(id, name, description)
values
    (1, 'ADMIN', 'Administrador'),
    (2, 'EMPLEADO', 'Empleado básico'),
    (3, 'SUPERVISOR', 'Supervisor');

insert into
    pdi2.centres (id, name)
select
    c.idCentros as id,
    c.Descripcion as name
from
    pdi.centros c
insert into
    pdi2.services (id, name)
select
    s.idServicios as id,
    s.Descripcion as name
from
    pdi.servicios s #Forzar a traer usuarios de LDAP
    #docker-compose run web php artisan adldap:import
UPDATE
    pdi2.employees,
    pdi2.tmp_usuarios
set
    pdi2.employees.user_id = pdi2.tmp_usuarios.idUsuarios
WHERE
    pdi2.tmp_usuarios.Nombre like concat('%', employees.name, '%')
UPDATE
    pdi2.employees,
    pdi2.tmp_empleados
set
    pdi2.employees.idEmpleado = pdi2.tmp_empleados.idEmpleados
WHERE
    pdi2.tmp_empleados.Nombre like concat('%', employees.name, '%')
insert into
    pdi2.employee_empleado (employee_id, idEmpleado)
select
    pdi2.employees.id,
    pdi2.tmp_empleados.idEmpleados as idEmpleado
from
    pdi2.employees,
    pdi2.tmp_empleados
WHERE
    pdi2.tmp_empleados.Nombre like concat('%', employees.name, '%')
insert into
    pdi2.trackings (
        id,
        hc,
        patient_name,
        started_date,
        started_user_id,
        apointment_user_id,
        apointment_done,
        service_date,
        service_user_id,
        service_done,
        invoiced_date,
        invoice_user_id,
        invoiced_done,
        validation_date,
        validation_user_id,
        validation_done,
        cancellation_date,
        cancellation_user_id,
        cancellation_reason,
        service_id,
        centre_id,
        employee_id
    )
select
    t.idTraza as id,
    t.Historia_Clinica as hc,
    t.Paciente as patient_name,
    t.Inicio_Fecha as started_date,
    /*t.Inicio_idUsuarios  as started_user_id,*/
    ei.user_id as started_user_id,
    /*t.Citado_idUsuarios as apointment_user_id,*/
    ec.user_id as apointment_user_id,
    t.Citado_Realizado as apointment_done,
    t.Servicio_Fecha as service_date,
    /*t.Servicio_idUsuarios as service_user_id,*/
    es.user_id as service_user_id,
    t.Servicio_Realizado as service_done,
    t.Facturado_Fecha as invoiced_date,
    /*t.Facturado_idUsuarios as invoice_user_id,*/
    ef.user_id as invoice_user_id,
    t.Facturado_Realizado as invoiced_done,
    t.Validacion_Fecha as validation_date,
    /*t.Validacion_idUsuarios as validation_user_id,*/
    ev.user_id as validation_user_id,
    t.Validacion_Realizado as validation_done,
    t.Cancelado_Fecha as cancellation_date,
    /*t.Cancelado_idUsuarios as cancellation_user_id,*/
    eca.user_id as cancellation_user_id,
    t.Cancelado_Motivo as cancellation_reason,
    t.idServicios as service_id,
    t.idCentros as centre_id,
    ee.employee_id
from
    pdi.traza as t
    join (
        select
            employee_id,
            idEmpleado
        from
            pdi2.employee_empleado
    ) ee on ee.idEmpleado = t.idEmpleados
    join (
        select
            user_id
        from
            pdi2.employees
    ) ei on ei.user_id = t.Inicio_idUsuarios
    left join (
        select
            user_id
        from
            pdi2.employees
    ) ec on ec.user_id = t.Citado_idUsuarios
    left join (
        select
            user_id
        from
            pdi2.employees
    ) es on es.user_id = t.Servicio_idUsuarios
    left join (
        select
            user_id
        from
            pdi2.employees
    ) ef on ef.user_id = t.Facturado_idUsuarios
    left join (
        select
            user_id
        from
            pdi2.employees
    ) ev on ev.user_id = t.Validacion_idUsuarios
    left join (
        select
            user_id
        from
            pdi2.employees
    ) eca on eca.user_id = t.Cancelado_idUsuarios create
    or replace view pdi2.export_tracking as
select
    distinct t.patient_name,
    t.hc,
    e.name as employee,
    t.employee_id,
    c.name as centre,
    c2.name as centre_employee,
    t.centre_id,
    e.centre_id as employee_centre_id,
    s.name as service,
    t.service_id,
    t.state as state,
    t.cancellation_reason,
    sp.price,
    t.state_date,
    t.service_date,
    t.invoiced_date,
    t.cancellation_date,
    t.service_done,
    t.invoiced_done,
    t.validation_date,
    t.validation_done,
    t.quantity
from
    pdi2.trackings t
    join pdi2.employees e on e.id = t.employee_id
    join pdi2.centres c on c.id = t.centre_id
    join pdi2.centres c2 on c2.id = t.centre_employee_id
    join pdi2.services s on s.id = t.service_id
    join pdi2.service_prices sp on sp.service_id = t.service_id
    and sp.centre_id = t.centre_id
where
    sp.cancellation_date is null create
    or replace view pdi2.ranking as
select
    distinct et.employee,
    et.centre_employee_id as centre_id,
    sum (et.price) as total_sale,
    sum (et.employee_incentive) as total_incentive
from
    pdi2.export_target et
UPDATE
    pdi2.centres
set
    island = 'GRAN CANARIA'
where
    name in (
        'VEGUETA',
        'POLICLINICO LAS PALMAS',
        '7 PALMAS',
        'ARNAO TELDE',
        'ESCALERITAS',
        'GÁLDAR',
        'INSURE',
        'MONTEBELLO',
        'SAN AGUSTÍN',
        'SANTA CATALINA',
        'VECINDARIO',
        'HOSPITAL TELDE'
    )
UPDATE
    pdi2.centres
set
    island = 'TENERIFE'
where
    name in (
        'CANDELARIA',
        'LA CUESTA',
        'LA LAGUNA',
        'LA OROTAVA',
        'LAS AMÉRICAS',
        'PARQUE TENERIFE',
        'SANTA CRUZ',
        'SAN ISIDRO'
    )
UPDATE
    pdi2.centres
set
    island = 'FUERTEVENTURA'
where
    name in ('PARQUE FUERTEVENTURA')
UPDATE
    pdi2.centres
set
    island = 'LANZAROTE'
where
    name in ('PARQUE LANZAROTE') create
    or replace view pdi2.export_target as
select
    distinct e.id as employee_id,
    e.name as employee,
    e.rol_id,
    s.name as service,
    c.name as centre,
    c.id as tracking_centre_id,
    c2.name as centre_employee,
    c2.id as centre_employee_id,
    t.patient_name,
    t.hc,
    t.apointment_date,
    t.service_date,
    t.invoiced_date,
    t.validation_date,
    t.observations,
    t.quantity,
    sp.price,
    round(sp.price * 10 / 100, 2) as direct_incentive,
    round(sp.price * 5 / 100, 2) as obj1_incentive,
    round(sp.price * 5 / 100, 2) as obj2_incentive,
    round(sp.price * 2.5 / 100, 2) as superv_obj1_incentive,
    round(sp.price * 2.5 / 100, 2) as superv_obj2_incentive,
    round(sp.service_price_direct_incentive, 2) as service_price_direct_incentive,
    round(sp.service_price_incentive1, 2) as service_price_incentive1,
    round(sp.service_price_incentive2, 2) as service_price_incentive2,
    round(sp.service_price_super_incentive1, 2) as service_price_super_incentive1,
    round(sp.service_price_super_incentive2, 2) as service_price_super_incentive2
from
    pdi2.trackings t
    join pdi2.employees e on e.id = t.employee_id
    join pdi2.centres c on c.id = t.centre_id
    join pdi2.services s on s.id = t.service_id
    join pdi2.service_prices sp on sp.service_id = t.service_id
    join pdi2.centres c2 on c2.id = t.centre_employee_id
where
    t.cancellation_date is null -- left join (select distinct centre_id, created_at , employee_id		
    --       		from pdi2.employee_centres )  ec on ec.employee_id  = t.employee_id 
    --    		     and date(ec.created_at) <= t.invoiced_date 
    --where ec.centre_id is not null
    /** Incluir campo centro prescriptor **/
ALTER TABLE
    pdi2.trackings
ADD
    centre_employee_id INT UNSIGNED NULL;

CREATE INDEX trackings_centre_employee_id_foreign USING BTREE ON pdi2.trackings (centre_employee_id);

ALTER TABLE
    pdi2.trackings
ADD
    CONSTRAINT `trackings_centre_employee_id_foreign` FOREIGN KEY (centre_employee_id) REFERENCES pdi2.centres(id);

create
or replace view pdi2.export_target as
select
    e.id as employee_id,
    e.name as employee,
    e.rol_id,
    s.name as service,
    c.name as centre,
    c.id as tracking_centre_id,
    c2.name as centre_employee,
    c2.id as centre_employee_id,
    t.patient_name,
    t.id as tracking_id,
    t.hc,
    t.apointment_date,
    t.service_date,
    t.invoiced_date,
    t.validation_date,
    t.observations,
    t.quantity,
    sp.price,
    round(sp.price * 10 / 100, 2) as direct_incentive,
    round(sp.price * 5 / 100, 2) as obj1_incentive,
    round(sp.price * 5 / 100, 2) as obj2_incentive,
    round(sp.price * 2.5 / 100, 2) as superv_obj1_incentive,
    round(sp.price * 2.5 / 100, 2) as superv_obj2_incentive,
    round(sp.service_price_direct_incentive, 2) as service_price_direct_incentive,
    round(sp.service_price_incentive1, 2) as service_price_incentive1,
    round(sp.service_price_incentive2, 2) as service_price_incentive2,
    round(sp.service_price_super_incentive1, 2) as service_price_super_incentive1,
    round(sp.service_price_super_incentive2, 2) as service_price_super_incentive2,
    GROUP_CONCAT(distinct(super.employee_id) SEPARATOR ', ') as supervisor
from
    pdi2.trackings t
    join pdi2.employees e on e.id = t.employee_id
    join pdi2.centres c on c.id = t.centre_id
    join pdi2.services s on s.id = t.service_id
    join pdi2.service_prices sp on sp.service_id = t.service_id
    join pdi2.centres c2 on c2.id = t.centre_employee_id
    left join (
        select
            employee_id,
            centre_id,
            eh.cancellation_date,
            eh.created_at,
            rol_id
        from
            pdi2.employee_history eh
            join pdi2.roles r on r.id = eh.rol_id
            and r.name = 'SUPERVISOR'
            /*where eh.centre_id = 7*/
    ) as super on super.centre_id = c2.id
    and (
        (
            t.validation_date BETWEEN date(super.created_at)
            and date(super.cancellation_date) -1
        )
        OR super.cancellation_date is null
        and date(super.created_at) <= t.validation_date
    )
where
    t.cancellation_date is null
group by
    1,
    2,
    3,
    4,
    5,
    6,
    7,
    8,
    9,
    10,
    11,
    12,
    13,
    14,
    15,
    16,
    17,
    18,
    19,
    20,
    21,
    22,
    23,
    24,
    25,
    26,
    27,
    28 truncate employee_history;

insert into
    employee_history (
        created_at,
        employee_id,
        centre_id,
        rol_id,
        cancellation_date
    )
select
    ec.created_at,
    ec.employee_id,
    ec.centre_id,
    e.rol_id,
    ec.cancellation_date
from
    employee_centres ec
    join employees e on e.id = ec.employee_id
where
    e.cancellation_date is null
    and e.centre_id is not null drop table tmp_eh_repetidos;

create table tmp_eh_repetidos as
select
    eh.employee_id,
    count(eh.centre_id)
from
    employee_history eh
group by
    1
having
    count(eh.centre_id) > 1 create table tmp_min_eh as
select
    eh2.*
from
    employee_history eh2
    join (
        select
            min(id) as id,
            employee_id
        from
            employee_history eh
        where
            employee_id in (
                select
                    employee_id
                from
                    tmp_eh_repetidos
            )
        group by
            2
    ) eh on eh2.id <> eh.id
    and eh2.employee_id = eh.employee_id
where
    eh2.employee_id in (
        select
            employee_id
        from
            tmp_eh_repetidos
    )
    and cancellation_date is null
order by
    eh2.employee_id;

update
    employee_history
set
    created_at = '2020-12-21 00:00:00'
where
    id in (
        select
            id
        from
            tmp_min_eh
    );

create table tmp_borrar_eh as
select
    eh3.id,
    eh3.employee_id,
    eh3.cancellation_date
from
    employee_history eh3
    join tmp_min_eh eh4 on eh4.employee_id = eh3.employee_id
    and eh4.id <> eh3.id;

delete from
    employee_history
where
    id in (
        select
            id
        from
            tmp_borrar_eh
    );

ALTER TABLE
    pdi2.targets
ADD
    calc_month varchar(10);

ALTER TABLE
    pdi2.employees
ADD
    COLUMN validated tinyint(1);

ALTER TABLE
    pdi2.employees
ADD
    COLUMN pending_password varchar(250);

ALTER TABLE
    pdi2.employees
ADD
    COLUMN username_temp varchar(10);

ALTER TABLE
    pdi2.employees
ADD
    COLUMN email varchar(100);

ALTER TABLE
    pdi2.employees
ADD
    COLUMN dni varchar(10);

ALTER TABLE
    pdi2.employees
ADD
    COLUMN phone varchar(20);

ALTER TABLE
    pdi2.employees
ADD
    COLUMN mobile_phone varchar(20);

ALTER TABLE
    pdi2.centres
ADD
    COLUMN label varchar(255);

ALTER TABLE
    pdi2.centres
ADD
    COLUMN address text;

ALTER TABLE
    pdi2.centres
ADD
    COLUMN phone varchar(100);

ALTER TABLE
    pdi2.centres
ADD
    COLUMN email varchar(100);

ALTER TABLE
    pdi2.centres
ADD
    COLUMN timetable varchar(100);

update
    pdi2.centres c
    join pdi2.tmp_centros tc on tc.CENTRO = c.name
SET
    c.label = tc.DESCRIPCION,
    c.address = tc.DIRECCIÓN,
    c.phone = tc.TELÉFONO,
    c.email = tc.EMAIL,
    c.timetable = tc.HORARIO,
    c.image = tc.IMAGE_CENTRE;

ALTER TABLE
    pdi2.employees
ADD
    COLUMN job_category_id INT UNSIGNED NULL;

ALTER TABLE
    pdi2.employees
ADD
    CONSTRAINT `employees_job_category_id_foreign` FOREIGN KEY (`job_category_id`) REFERENCES `job_categories` (`id`);

ALTER TABLE
    pdi2.services
ADD
    COLUMN image varchar(255);

ALTER TABLE
    pdi2.services
ADD
    COLUMN description text;

ALTER TABLE
    pdi2.services
ADD
    COLUMN url varchar(255);

ALTER TABLE
    pdi2.services
ADD
    COLUMN category_id INT UNSIGNED NULL;

ALTER TABLE
    pdi2.services
ADD
    CONSTRAINT `services_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`);

update
    pdi2.services
set
    category_id = (
        SELECT
            ID
        FROM
            service_categories
        WHERE
            NAME = 'Rehabilitación'
    )
WHERE
    NAME IN (
        'Consulta médica - Rehabilitación',
        'Rehabilitación - sesión',
        'Rehabilitación - sesión intensiva',
        'Rehabilitación - sesión infantil',
        'Bono Rehabilitación 5 sesiones',
        'Bono Rehabilitación 10 sesiones',
        'Bono Rehabilitación Intensiva 10 sesiones',
        'Fisioterapia en piscina - sesión individual',
        'Fisioterapia en piscina - con auxiliar de apoyo'
    );

update
    pdi2.services
set
    category_id = (
        SELECT
            ID
        FROM
            service_categories
        WHERE
            NAME = 'Masaje terapéutico'
    )
WHERE
    NAME IN ('Masaje terapéutico');

update
    pdi2.services
set
    category_id = (
        SELECT
            ID
        FROM
            service_categories
        WHERE
            NAME = 'Ondas de choque'
    )
WHERE
    NAME IN ('Ondas de choque - sesión');

update
    pdi2.services
set
    category_id = (
        SELECT
            ID
        FROM
            service_categories
        WHERE
            NAME = 'Logopedia'
    )
WHERE
    NAME IN ('Logopedia - sesión individual');

update
    pdi2.services
set
    category_id = (
        SELECT
            ID
        FROM
            service_categories
        WHERE
            NAME = 'Traumatología'
    )
WHERE
    NAME IN ('Consulta médica - Traumatología');

update
    pdi2.services
set
    category_id = (
        SELECT
            ID
        FROM
            service_categories
        WHERE
            NAME = 'Neurología'
    )
WHERE
    NAME IN ('Consulta médica - Neurología');

update
    pdi2.services
set
    category_id = (
        SELECT
            ID
        FROM
            service_categories
        WHERE
            NAME = 'Medicina intervencionista'
    )
WHERE
    NAME IN ('Consulta médica - Medicina intervencionista');

update
    pdi2.services
set
    category_id = (
        SELECT
            ID
        FROM
            service_categories
        WHERE
            NAME = 'Podología'
    )
WHERE
    NAME IN ('Consulta Podología');

update
    pdi2.services
set
    category_id = (
        SELECT
            ID
        FROM
            service_categories
        WHERE
            NAME = 'Plantillas 3D'
    )
WHERE
    NAME IN ('Plantillas 3D', 'Plantillas 3D PRO');

update
    pdi2.services
set
    category_id = (
        SELECT
            ID
        FROM
            service_categories
        WHERE
            NAME = 'Resonancia magnética'
    )
WHERE
    NAME IN ('Resonancia magnética');

ALTER TABLE
    pdi2.tracking
ADD
    COLUMN dni varchar(20);

ALTER TABLE
    pdi2.tracking
ADD
    COLUMN phone varchar(20);

ALTER TABLE
    pdi2.employees DROP CONSTRAINT employees_job_category_id_foreign;

ALTER TABLE
    pdi2.employees DROP COLUMN job_category_id;

ALTER TABLE
    pdi2.employees
add
    column centro_a3 int;

ALTER TABLE
    pdi2.employees
add
    column baja_a3 boolean default false;

ALTER TABLE
    pdi2.employees
ADD
    COLUMN category varchar(255);

ALTER TABLE
    pdi2.employees
ADD
    COLUMN user_cancellation_date int;

ALTER TABLE
    pdi2.employees DROP COLUMN cod_business;

ALTER TABLE
    pdi2.employees DROP COLUMN cod_employee;

ALTER TABLE
    pdi2.employees
ADD
    COLUMN cod_business varchar(255);

ALTER TABLE
    pdi2.employees
ADD
    COLUMN cod_employee varchar(255);

ALTER TABLE
    pdi2.employee_history
ADD
    COLUMN contract_startdate timestamp;

ALTER TABLE
    pdi2.employee_history
ADD
    COLUMN user_cancellation_date int;

create
or replace view pdi2.export_target as
select
    DISTINCT `e`.`id` AS `employee_id`,
    `e`.`name` AS `employee`,
    `e`.`rol_id` AS `rol_id`,
    `e`.`dni` AS `dni`,
    `e`.`cod_business` AS `cod_business`,
    `e`.`cod_employee` AS `cod_employee`,
    `s`.`name` AS `service`,
    `c`.`name` AS `centre`,
    `c`.`id` AS `tracking_centre_id`,
    `c2`.`name` AS `centre_employee`,
    `c2`.`id` AS `centre_employee_id`,
    `t`.`id` AS `tracking_id`,
    `t`.`patient_name` AS `patient_name`,
    `t`.`hc` AS `hc`,
    `t`.`started_date` AS `started_date`,
    `t`.`apointment_date` AS `apointment_date`,
    `t`.`service_date` AS `service_date`,
    `t`.`invoiced_date` AS `invoiced_date`,
    `t`.`validation_date` AS `validation_date`,
    `t`.`observations` AS `observations`,
    `t`.`quantity` AS `quantity`,
    `sp`.`price` AS `price`,
    round(((`sp`.`price` * 10) / 100), 2) AS `direct_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj1_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj2_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj1_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj2_incentive`,
    round(`sp`.`service_price_direct_incentive`, 2) AS `service_price_direct_incentive`,
    round(`sp`.`service_price_incentive1`, 2) AS `service_price_incentive1`,
    round(`sp`.`service_price_incentive2`, 2) AS `service_price_incentive2`,
    round(`sp`.`service_price_super_incentive1`, 2) AS `service_price_super_incentive1`,
    round(`sp`.`service_price_super_incentive2`, 2) AS `service_price_super_incentive2`,
    group_concat(distinct `super`.`employee_id` separator ', ') AS `supervisor`,
    CASE
        WHEN apointment_date is null
        and t.cancellation_date is NULL THEN 'Pendiente'
        WHEN apointment_date is not null
        and service_date is null
        and t.cancellation_date is NULL THEN 'Citado'
        WHEN service_date is not null
        and invoiced_date is NULL
        and t.cancellation_date is NULL THEN 'Realizado'
        WHEN invoiced_date is not null
        and validation_date is NULL
        and t.cancellation_date is NULL THEN 'Facturado'
        WHEN validation_date is not null
        and t.cancellation_date is NULL THEN 'Validado'
    END AS current_state
from
    (
        (
            (
                (
                    (
                        (
                            `trackings` `t`
                            join `employees` `e` on ((`e`.`id` = `t`.`employee_id`))
                        )
                        join `centres` `c` on ((`c`.`id` = `t`.`centre_id`))
                    )
                    join `services` `s` on ((`s`.`id` = `t`.`service_id`))
                )
                join `service_prices` `sp` on ((`sp`.`service_id` = `t`.`service_id`))
            )
            join `centres` `c2` on ((`c2`.`id` = `t`.`centre_employee_id`))
        )
        left join (
            select
                `eh`.`employee_id` AS `employee_id`,
                `eh`.`centre_id` AS `centre_id`,
                `eh`.`cancellation_date` AS `cancellation_date`,
                `eh`.`created_at` AS `created_at`,
                `eh`.`rol_id` AS `rol_id`
            from
                (
                    `employee_history` `eh`
                    join `roles` `r` on (
                        (
                            (`r`.`id` = `eh`.`rol_id`)
                            and (`r`.`name` = 'SUPERVISOR')
                        )
                    )
                )
        ) `super` on (
            (
                (`super`.`centre_id` = `c2`.`id`)
                and (
                    (
                        `t`.`validation_date` between cast(`super`.`created_at` as date)
                        and (cast(`super`.`cancellation_date` as date) - 1)
                    )
                    or (
                        (`super`.`cancellation_date` is null)
                        and (
                            cast(`super`.`created_at` as date) <= `t`.`validation_date`
                        )
                    )
                )
            )
        )
    )
where
    (`t`.`cancellation_date` is null)
group by
    `e`.`id`,
    `e`.`name`,
    `e`.`rol_id`,
    `s`.`name`,
    `c`.`name`,
    `c`.`id`,
    `c2`.`name`,
    `c2`.`id`,
    `t`.`id`,
    `t`.`patient_name`,
    `t`.`hc`,
    `t`.`started_date`,
    `t`.`apointment_date`,
    `t`.`service_date`,
    `t`.`invoiced_date`,
    `t`.`validation_date`,
    `t`.`observations`,
    `t`.`quantity`,
    `sp`.`price`,
    round(((`sp`.`price` * 10) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(`sp`.`service_price_direct_incentive`, 2),
    round(`sp`.`service_price_incentive1`, 2),
    round(`sp`.`service_price_incentive2`, 2),
    round(`sp`.`service_price_super_incentive1`, 2),
    round(`sp`.`service_price_super_incentive2`, 2);

ALTER TABLE
    pdi2.employees DROP CONSTRAINT employees_job_category_id_foreign;

ALTER TABLE
    pdi2.employees DROP COLUMN job_category_id;

ALTER TABLE
    pdi2.employees
ADD
    COLUMN category varchar(255);

drop table job_categories;

/** Crear tablas a3_centres y a3_empleados */
docker - compose exec app2 php artisan migrate :refresh --path=database/migrations/2021_06_08_104023_create_a3_centres_table.php
docker - compose exec app2 php artisan migrate :refresh --path=database/migrations/2021_06_18_171156_create_a3_empleados_table.php
truncate a3_centres;

insert into
    a3_centres (code_business, code_centre, centre_id)
values
    (
        8,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'VEGUETA'
        )
    ),
    (
        8,
        2,
        (
            select
                id
            from
                centres
            where
                name = 'VEGUETA'
        )
    ),
    (
        19,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'SANTA CRUZ'
        )
    ),
    (
        19,
        2,
        (
            select
                id
            from
                centres
            where
                name = 'SANTA CRUZ'
        )
    ),
    (
        19,
        10,
        (
            select
                id
            from
                centres
            where
                name = 'SANTA CRUZ'
        )
    ),
    (
        6,
        1,
        (
            select
                id
            from
                centres
            where
                name = '7 PALMAS'
        )
    ),
    (
        16,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'ESCALERITAS'
        )
    ),
    (
        17,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'ESCALERITAS'
        )
    ),
    (
        9,
        1,
(
            select
                id
            from
                centres
            where
                name = 'SANTA CATALINA'
        )
    ),
    (
        2,
        3,
        (
            select
                id
            from
                centres
            where
                name = 'SANTA CATALINA'
        )
    ),
    (
        4,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'GALDAR'
        )
    ),
    (
        4,
        3,
        (
            select
                id
            from
                centres
            where
                name = 'GALDAR'
        )
    ),
    (
        7,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'ARNAO TELDE'
        )
    ),
    (
        7,
        2,
        (
            select
                id
            from
                centres
            where
                name = 'ARNAO TELDE'
        )
    ),
    (
        12,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'INSURE'
        )
    ),
    (
        2,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'INSURE'
        )
    ),
    (
        5,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'MONTEBELLO'
        )
    ),
    (
        5,
        3,
        (
            select
                id
            from
                centres
            where
                name = 'MONTEBELLO'
        )
    ),
    (
        1,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'POLICLÍNICO LAS PALMAS'
        )
    ),
    (
        2,
        2,
        (
            select
                id
            from
                centres
            where
                name = 'POLICLÍNICO LAS PALMAS'
        )
    ),
    (
        21,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'HOSPITAL TELDE'
        )
    ),
    (
        3,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'VECINDARIO'
        )
    ),
    (
        3,
        2,
        (
            select
                id
            from
                centres
            where
                name = 'VECINDARIO'
        )
    ),
    (
        13,
        1,
        (
            select
                id
            from
                centres
            where
                name = 'VECINDARIO'
        )
    ),
    -- SIC VOS
    (
        3,
        3,
        (
            select
                id
            from
                centres
            where
                name = 'SAN AGUSTÍN'
        )
    ),
    (
        19,
        6,
        (
            select
                id
            from
                centres
            where
                name = 'LAS AMÉRICAS'
        )
    ),
    (
        19,
        4,
        (
            select
                id
            from
                centres
            where
                name = 'LA LAGUNA'
        )
    ),
    (
        19,
        3,
        (
            select
                id
            from
                centres
            where
                name = 'LA CUESTA'
        )
    ),
    (
        19,
        5,
        (
            select
                id
            from
                centres
            where
                name = 'LA OROTAVA'
        )
    ),
    (
        19,
        7,
        (
            select
                id
            from
                centres
            where
                name = 'SAN ISIDRO'
        )
    ),
    (
        19,
        8,
        (
            select
                id
            from
                centres
            where
                name = 'PARQUE TENERIFE'
        )
    ),
    (
        19,
        9,
        (
            select
                id
            from
                centres
            where
                name = 'CANDELARIA'
        )
    ),
    (
        1,
        7,
        (
            select
                id
            from
                centres
            where
                name = 'PARQUE LANZAROTE'
        )
    ),
    (
        1,
        2,
        (
            select
                id
            from
                centres
            where
                name = 'PARQUE FUERTEVENTURA'
        )
    );

ALTER TABLE
    pdi2.employees
add
    column baja_a3 boolean default false;

ALTER TABLE
    pdi2.employees
ADD
    COLUMN cod_business int;

ALTER TABLE
    pdi2.employees
ADD
    COLUMN cod_employee int;

insert into
    chaman_centres (code, centre_id)
values
    (
        '7PAL',
        (
            select
                id
            from
                centres
            where
                name = '7 PALMAS'
        )
    ),
    (
        'ARNA',
        (
            select
                id
            from
                centres
            where
                name = 'ARNAO TELDE'
        )
    ),
    (
        'CECA',
        (
            select
                id
            from
                centres
            where
                name = 'VECINDARIO'
        )
    ),
    (
        'VCND',
        (
            select
                id
            from
                centres
            where
                name = 'VECINDARIO'
        )
    ),
    (
        'ESCA',
        (
            select
                id
            from
                centres
            where
                name = 'ESCALERITAS'
        )
    ),
    (
        'GALD',
        (
            select
                id
            from
                centres
            where
                name = 'GALDAR'
        )
    ),
    (
        'GALR',
        (
            select
                id
            from
                centres
            where
                name = 'GALDAR'
        )
    ),
    (
        'ICOT',
        (
            select
                id
            from
                centres
            where
                name = 'POLICLÍNICO LAS PALMAS'
        )
    ),
    (
        'INSU',
        (
            select
                id
            from
                centres
            where
                name = 'INSURE'
        )
    ),
    (
        'MONT',
        (
            select
                id
            from
                centres
            where
                name = 'MONTEBELLO'
        )
    ),
    (
        'RHIC',
        (
            select
                id
            from
                centres
            where
                name = 'SANTA CATALINA'
        )
    ),
    (
        'SANA',
        (
            select
                id
            from
                centres
            where
                name = 'SAN AGUSTÍN'
        )
    ),
    (
        'VEGU',
        (
            select
                id
            from
                centres
            where
                name = 'VEGUETA'
        )
    ),
    (
        'HCDT',
        (
            select
                id
            from
                centres
            where
                name = 'HOSPITAL TELDE'
        )
    ),
    (
        'IFV1',
        (
            select
                id
            from
                centres
            where
                name = 'PARQUE FUERTEVENTURA'
        )
    ),
    (
        'ILZ1',
        (
            select
                id
            from
                centres
            where
                name = 'PARQUE LANZAROTE'
        )
    ),
    (
        'TFE1',
        (
            select
                id
            from
                centres
            where
                name = 'SANTA CRUZ'
        )
    ),
    (
        'TFE2',
        (
            select
                id
            from
                centres
            where
                name = 'PARQUE TENERIFE'
        )
    ),
    (
        'TFE3',
        (
            select
                id
            from
                centres
            where
                name = 'LA CUESTA'
        )
    ),
    (
        'TFE4',
        (
            select
                id
            from
                centres
            where
                name = 'LA LAGUNA'
        )
    ),
    (
        'TFE5',
        (
            select
                id
            from
                centres
            where
                name = 'LAS AMÉRICAS'
        )
    ),
    (
        'TFE6',
        (
            select
                id
            from
                centres
            where
                name = 'LA OROTAVA'
        )
    ),
    (
        'TFE7',
        (
            select
                id
            from
                centres
            where
                name = 'SAN ISIDRO'
        )
    ),
    (
        'TFE8',
        (
            select
                id
            from
                centres
            where
                name = 'CANDELARIA'
        )
    );

ALTER TABLE
    pdi2.trackings
MODIFY
    COLUMN apointment_done tinyint(1) DEFAULT false NULL;

ALTER TABLE
    pdi2.trackings
MODIFY
    COLUMN invoiced_done tinyint(1) DEFAULT false NULL;

ALTER TABLE
    pdi2.trackings
MODIFY
    COLUMN service_done tinyint(1) DEFAULT false NULL;

ALTER TABLE
    pdi2.trackings
MODIFY
    COLUMN validation_done tinyint(1) DEFAULT false NULL;

ALTER TABLE
    pdi2.empleados_a3
ADD
    created_at timestamp NULL;

ALTER TABLE
    pdi2.empleados_a3
ADD
    updated_at timestamp NULL;

ALTER TABLE
    pdi2.a3_empleados
MODIFY
    COLUMN Fecha_de_alta_en_compañia DATETIME(6);

ALTER TABLE
    empleados_a3
MODIFY
    NIF VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE
    empleados_a3
MODIFY
    Nombre_Completo VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

/*Importar CSV listadoCategoryServ.csv en tabla tmp_categorias_servicios;  */
ALTER TABLE
    service_categories
add
    column image_portrait varchar(255);

UPDATE
    service_categories
    join tmp_categorias_servicios tmp on tmp.IDCATEGORY = service_categories.id
set
    name = tmp.CATEGORIA,
    image_portrait = tmp.IMAGENPORTRAIT,
    image = tmp.IMAGENCATEGORY,
    description = tmp.DESCRIPCION;

UPDATE
    services
    join tmp_categorias_servicios tmp on tmp.IDSERVICE = services.id
set
    name = tmp.SERVICIOS,
    image = tmp.IMAGENSERVICIO,
    description = tmp.`DESCRIPCION SERVICIO`;

/*Eliminar tabla temporal tmp_categorias_servicios;  */
DROP TABLE tmp_categorias_servicios;

ALTER TABLE
    pdi2.trackings
ADD
    paid_done tinyint;

ALTER TABLE
    pdi2.trackings
ADD
    paid_date date;

ALTER TABLE
    employees
MODIFY
    COLUMN count_access INT NOT NULL DEFAULT 0;

ALTER TABLE
    pdi2.employees
MODIFY
    COLUMN validated INT DEFAULT -1;

ALTER TABLE
    pdi2.trackings
ADD
    COLUMN dni varchar(20);

ALTER TABLE
    pdi2.trackings
ADD
    COLUMN phone varchar(100);

ALTER TABLE
    pdi2.trackings
MODIFY
    patient_name varchar(100) null;

truncate a3_centres;

insert into
    a3_centres (
        code_business,
        name_business,
        code_centre,
        centre_id
    )
values
    (
        1,
        'ICOT SERVICIOS INTEGRALES, S.L.U.',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'POLICLÍNICO LAS PALMAS'
        )
    ),
    (
        1,
        'ICOT SERVICIOS INTEGRALES, S.L.U.',
        5,
        (
            select
                id
            from
                centres
            where
                name = 'POLICLÍNICO LAS PALMAS'
        )
    ),
    (
        1,
        'ICOT SERVICIOS INTEGRALES, S.L.U.',
        6,
        (
            select
                id
            from
                centres
            where
                name = 'POLICLÍNICO LAS PALMAS'
        )
    ),
    (
        1,
        'ICOT SERVICIOS INTEGRALES, S.L.U.',
        7,
        (
            select
                id
            from
                centres
            where
                name = 'PARQUE LANZAROTE'
        )
    ),
    (
        1,
        'ICOT SERVICIOS INTEGRALES, S.L.U.',
        2,
        (
            select
                id
            from
                centres
            where
                name = 'PARQUE FUERTEVENTURA'
        )
    ),
    (
        2,
        'INST. INSULAR DE REHABLITACION, S.L.',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'INSURE'
        )
    ),
    (
        2,
        'INST. INSULAR DE REHABLITACION, S.L.',
        2,
        (
            select
                id
            from
                centres
            where
                name = 'POLICLÍNICO LAS PALMAS'
        )
    ),
    (
        2,
        'INST. INSULAR DE REHABLITACION, S.L.',
        3,
        (
            select
                id
            from
                centres
            where
                name = 'SANTA CATALINA'
        )
    ),
    (
        3,
        'CECA REHABILITACION, S.L.',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'VECINDARIO'
        )
    ),
    (
        3,
        'CECA REHABILITACION, S.L.',
        2,
        (
            select
                id
            from
                centres
            where
                name = 'VECINDARIO'
        )
    ),
    (
        3,
        'CECA REHABILITACION, S.L.',
        3,
        (
            select
                id
            from
                centres
            where
                name = 'SAN AGUSTÍN'
        )
    ),
    (
        4,
        'CENTRO REHABILITACION DE GALDAR, S.L',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'GALDAR'
        )
    ),
    (
        4,
        'CENTRO REHABILITACION DE GALDAR, S.L',
        3,
        (
            select
                id
            from
                centres
            where
                name = 'GALDAR'
        )
    ),
    (
        5,
        'CENTRO DE REHABILITACION MONTEBELLO, S.L.',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'MONTEBELLO'
        )
    ),
    (
        5,
        'CENTRO DE REHABILITACION MONTEBELLO, S.L.',
        3,
        (
            select
                id
            from
                centres
            where
                name = 'MONTEBELLO'
        )
    ),
    (
        6,
        'CENTRO DE REHABILITACION SIETE PALMAS, S.L',
        1,
        (
            select
                id
            from
                centres
            where
                name = '7 PALMAS'
        )
    ),
    (
        7,
        'CENTRO DE REHABILITACION ARNAO, S.L',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'ARNAO TELDE'
        )
    ),
    (
        7,
        'CENTRO DE REHABILITACION ARNAO, S.L',
        2,
        (
            select
                id
            from
                centres
            where
                name = 'ARNAO TELDE'
        )
    ),
    (
        8,
        'CENTRO DE REHABILITACION DE VEGUETA, S.L.',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'VEGUETA'
        )
    ),
    (
        8,
        'CENTRO DE REHABILITACION DE VEGUETA, S.L.',
        2,
        (
            select
                id
            from
                centres
            where
                name = 'VEGUETA'
        )
    ),
    (
        9,
        'RH ICOT LAS PALMAS, S.L.U.',
        1,
(
            select
                id
            from
                centres
            where
                name = 'SANTA CATALINA'
        )
    ),
    (
        12,
        'INSURE - ICOT, UTE',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'INSURE'
        )
    ),
    (
        13,
        'SIC VOS, S.L.',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'VECINDARIO'
        )
    ),
    -- SIC VOS
    (
        16,
        'CENTRO DE REHABILITACION ESCALERITAS, S.L.',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'ESCALERITAS'
        )
    ),
    (
        17,
        'CENTRO DE REHABILITACION ESCALERITAS, S.L.',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'ESCALERITAS'
        )
    ),
    (
        19,
        'ICOT TENERIFE, S.L.',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'SANTA CRUZ'
        )
    ),
    (
        19,
        'ICOT TENERIFE, S.L.',
        2,
        (
            select
                id
            from
                centres
            where
                name = 'SANTA CRUZ'
        )
    ),
    (
        19,
        'ICOT TENERIFE, S.L.',
        10,
        (
            select
                id
            from
                centres
            where
                name = 'SANTA CRUZ'
        )
    ),
    (
        19,
        'ICOT TENERIFE, S.L.',
        6,
        (
            select
                id
            from
                centres
            where
                name = 'LAS AMÉRICAS'
        )
    ),
    (
        19,
        'ICOT TENERIFE, S.L.',
        4,
        (
            select
                id
            from
                centres
            where
                name = 'LA LAGUNA'
        )
    ),
    (
        19,
        'ICOT TENERIFE, S.L.',
        3,
        (
            select
                id
            from
                centres
            where
                name = 'LA CUESTA'
        )
    ),
    (
        19,
        'ICOT TENERIFE, S.L.',
        5,
        (
            select
                id
            from
                centres
            where
                name = 'LA OROTAVA'
        )
    ),
    (
        19,
        'ICOT TENERIFE, S.L.',
        7,
        (
            select
                id
            from
                centres
            where
                name = 'SAN ISIDRO'
        )
    ),
    (
        19,
        'ICOT TENERIFE, S.L.',
        8,
        (
            select
                id
            from
                centres
            where
                name = 'PARQUE TENERIFE'
        )
    ),
    (
        19,
        'ICOT TENERIFE, S.L.',
        9,
        (
            select
                id
            from
                centres
            where
                name = 'CANDELARIA'
        )
    ),
    (
        21,
        'HOSPITAL CIUDAD DE TELDE, S.L.',
        1,
        (
            select
                id
            from
                centres
            where
                name = 'HOSPITAL TELDE'
        )
    );

docker - compose exec app2 php artisan migrate :refresh --path=database/migrations/2021_07_28_093156_create_promotions_table.php
docker - compose exec app2 php artisan migrate :refresh --path=database/migrations/2021_07_28_110224_create_faq_table.php
create
or replace view pdi2.export_target as
select
    DISTINCT `e`.`id` AS `employee_id`,
    `e`.`name` AS `employee`,
    `e`.`rol_id` AS `rol_id`,
    `e`.`dni` AS `dni`,
    `e`.`cod_business` AS `cod_business`,
    `e`.`cod_employee` AS `cod_employee`,
    `e`.`cancellation_date` AS `cancellation_date`,
    `s`.`name` AS `service`,
    `c`.`name` AS `centre`,
    `c`.`id` AS `tracking_centre_id`,
    `c2`.`name` AS `centre_employee`,
    `c2`.`id` AS `centre_employee_id`,
    `t`.`id` AS `tracking_id`,
    `t`.`patient_name` AS `patient_name`,
    `t`.`hc` AS `hc`,
    `t`.`started_date` AS `started_date`,
    `t`.`apointment_date` AS `apointment_date`,
    `t`.`service_date` AS `service_date`,
    `t`.`invoiced_date` AS `invoiced_date`,
    `t`.`validation_date` AS `validation_date`,
    `t`.`observations` AS `observations`,
    `t`.`quantity` AS `quantity`,
    `t`.`state` AS `current_state`,
    `sp`.`price` AS `price`,
    round(((`sp`.`price` * 10) / 100), 2) AS `direct_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj1_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj2_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj1_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj2_incentive`,
    round(`sp`.`service_price_direct_incentive`, 2) AS `service_price_direct_incentive`,
    round(`sp`.`service_price_incentive1`, 2) AS `service_price_incentive1`,
    round(`sp`.`service_price_incentive2`, 2) AS `service_price_incentive2`,
    round(`sp`.`service_price_super_incentive1`, 2) AS `service_price_super_incentive1`,
    round(`sp`.`service_price_super_incentive2`, 2) AS `service_price_super_incentive2`,
    group_concat(distinct `super`.`employee_id` separator ', ') AS `supervisor`
from
    (
        (
            (
                (
                    (
                        (
                            `trackings` `t`
                            join `employees` `e` on ((`e`.`id` = `t`.`employee_id`))
                        )
                        join `centres` `c` on ((`c`.`id` = `t`.`centre_id`))
                    )
                    join `services` `s` on ((`s`.`id` = `t`.`service_id`))
                )
                join `service_prices` `sp` on (
                    (
                        `sp`.`service_id` = `t`.`service_id`
                        and sp.centre_id = t.centre_id
                    )
                )
            )
            join `centres` `c2` on ((`c2`.`id` = `t`.`centre_employee_id`))
        )
        left join (
            select
                `eh`.`employee_id` AS `employee_id`,
                `eh`.`centre_id` AS `centre_id`,
                `eh`.`cancellation_date` AS `cancellation_date`,
                `eh`.`created_at` AS `created_at`,
                `eh`.`rol_id` AS `rol_id`
            from
                (
                    `employee_history` `eh`
                    join `roles` `r` on (
                        (
                            (`r`.`id` = `eh`.`rol_id`)
                            and (`r`.`name` = 'SUPERVISOR')
                        )
                    )
                )
        ) `super` on (
            (
                (`super`.`centre_id` = `c2`.`id`)
                and (
                    (
                        `t`.`validation_date` between cast(`super`.`created_at` as date)
                        and (cast(`super`.`cancellation_date` as date) - 1)
                    )
                    or (
                        (`super`.`cancellation_date` is null)
                        and (
                            cast(`super`.`created_at` as date) <= `t`.`validation_date`
                        )
                    )
                )
            )
        )
    )
where
    (`t`.`cancellation_date` is null)
group by
    `e`.`id`,
    `e`.`name`,
    `e`.`rol_id`,
    `e`.`dni`,
    `e`.`cod_business`,
    `e`.`cod_employee`,
    `e`.`cancellation_date`,
    `s`.`name`,
    `c`.`name`,
    `c`.`id`,
    `c2`.`name`,
    `c2`.`id`,
    `t`.`id`,
    `t`.`patient_name`,
    `t`.`hc`,
    `t`.`started_date`,
    `t`.`apointment_date`,
    `t`.`service_date`,
    `t`.`invoiced_date`,
    `t`.`validation_date`,
    `t`.`observations`,
    `t`.`quantity`,
    `sp`.`price`,
    round(((`sp`.`price` * 10) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(`sp`.`service_price_direct_incentive`, 2),
    round(`sp`.`service_price_incentive1`, 2),
    round(`sp`.`service_price_incentive2`, 2),
    round(`sp`.`service_price_super_incentive1`, 2),
    round(`sp`.`service_price_super_incentive2`, 2);

ALTER TABLE
    pdi2.trackings
ADD
    state varchar(255) DEFAULT 'Pendiente';

/** Seguimientos iniciados */
UPDATE
    trackings
set
    state = 'Pendiente'
where
    apointment_date is null
    and cancellation_date is null;

/** Seguimientos citados */
UPDATE
    trackings
set
    state = 'Citado'
where
    apointment_date is not null
    and service_date is null
    and cancellation_date is null;

/** Seguimientos realizados */
UPDATE
    trackings
set
    state = 'Realizado'
where
    service_date is not null
    and invoiced_date is null
    and cancellation_date is null;

/** Seguimientos facturados */
UPDATE
    trackings
set
    state = 'Facturado'
where
    invoiced_date is not null
    and validation_date is null
    and cancellation_date is null;

/** Seguimientos validados */
UPDATE
    trackings
set
    state = 'Validado'
where
    validation_date is not null
    and paid_date is null
    and cancellation_date is null;

/** Seguimientos pagados */
UPDATE
    trackings
set
    state = 'Pagado'
where
    paid_date is not null
    and cancellation_date is null;

UPDATE
    services
    join tmp_categorias_servicios tmp on tmp.IDSERVICE = services.id
set
    name = tmp.SERVICIOS,
    image = tmp.IMAGENSERVICIO,
    url = tmp.URLINFO,
    description = tmp.`DESCRIPCION SERVICIO`;

create
or replace view pdi2.export_target as
select
    DISTINCT `e`.`id` AS `employee_id`,
    `e`.`name` AS `employee`,
    `e`.`rol_id` AS `rol_id`,
    `e`.`dni` AS `dni`,
    `e`.`cod_business` AS `cod_business`,
    `e`.`cod_employee` AS `cod_employee`,
    `e`.`cancellation_date` AS `cancellation_date`,
    `s`.`name` AS `service`,
    `c`.`name` AS `centre`,
    `c`.`id` AS `tracking_centre_id`,
    `c2`.`name` AS `centre_employee`,
    `c2`.`id` AS `centre_employee_id`,
    `t`.`id` AS `tracking_id`,
    `t`.`patient_name` AS `patient_name`,
    `t`.`hc` AS `hc`,
    `t`.`started_date` AS `started_date`,
    `t`.`apointment_date` AS `apointment_date`,
    `t`.`service_date` AS `service_date`,
    `t`.`invoiced_date` AS `invoiced_date`,
    `t`.`validation_date` AS `validation_date`,
    `t`.`paid_date` AS `paid_date`,
    `t`.`observations` AS `observations`,
    `t`.`quantity` AS `quantity`,
    `t`.`state` AS `current_state`,
    `sp`.`price` AS `price`,
    round(((`sp`.`price` * 10) / 100), 2) AS `direct_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj1_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj2_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj1_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj2_incentive`,
    round(`sp`.`service_price_direct_incentive`, 2) AS `service_price_direct_incentive`,
    round(`sp`.`service_price_incentive1`, 2) AS `service_price_incentive1`,
    round(`sp`.`service_price_incentive2`, 2) AS `service_price_incentive2`,
    round(`sp`.`service_price_super_incentive1`, 2) AS `service_price_super_incentive1`,
    round(`sp`.`service_price_super_incentive2`, 2) AS `service_price_super_incentive2`,
    group_concat(distinct `super`.`employee_id` separator ', ') AS `supervisor`
from
    (
        (
            (
                (
                    (
                        (
                            `trackings` `t`
                            join `employees` `e` on ((`e`.`id` = `t`.`employee_id`))
                        )
                        join `centres` `c` on ((`c`.`id` = `t`.`centre_id`))
                    )
                    join `services` `s` on ((`s`.`id` = `t`.`service_id`))
                )
                join `service_prices` `sp` on (
                    (
                        `sp`.`service_id` = `t`.`service_id`
                        and sp.centre_id = t.centre_id
                        and sp.cancellation_date is null
                    )
                )
            )
            join `centres` `c2` on ((`c2`.`id` = `t`.`centre_employee_id`))
        )
        left join (
            select
                `eh`.`employee_id` AS `employee_id`,
                `eh`.`centre_id` AS `centre_id`,
                `eh`.`cancellation_date` AS `cancellation_date`,
                `eh`.`created_at` AS `created_at`,
                `eh`.`rol_id` AS `rol_id`
            from
                (
                    `employee_history` `eh`
                    join `roles` `r` on (
                        (
                            (`r`.`id` = `eh`.`rol_id`)
                            and (`r`.`name` = 'SUPERVISOR')
                        )
                    )
                )
        ) `super` on (
            (
                (`super`.`centre_id` = `c2`.`id`)
                and (
                    (
                        `t`.`validation_date` between cast(`super`.`created_at` as date)
                        and (cast(`super`.`cancellation_date` as date) - 1)
                    )
                    or (
                        (`super`.`cancellation_date` is null)
                        and (
                            cast(`super`.`created_at` as date) <= `t`.`validation_date`
                        )
                    )
                )
            )
        )
    )
where
    (`t`.`cancellation_date` is null)
group by
    `e`.`id`,
    `e`.`name`,
    `e`.`rol_id`,
    `e`.`dni`,
    `e`.`cod_business`,
    `e`.`cod_employee`,
    `e`.`cancellation_date`,
    `s`.`name`,
    `c`.`name`,
    `c`.`id`,
    `c2`.`name`,
    `c2`.`id`,
    `t`.`id`,
    `t`.`patient_name`,
    `t`.`hc`,
    `t`.`started_date`,
    `t`.`apointment_date`,
    `t`.`service_date`,
    `t`.`invoiced_date`,
    `t`.`validation_date`,
    `t`.`paid_date`,
    `t`.`observations`,
    `t`.`quantity`,
    `sp`.`price`,
    round(((`sp`.`price` * 10) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(`sp`.`service_price_direct_incentive`, 2),
    round(`sp`.`service_price_incentive1`, 2),
    round(`sp`.`service_price_incentive2`, 2),
    round(`sp`.`service_price_super_incentive1`, 2),
    round(`sp`.`service_price_super_incentive2`, 2);

ALTER TABLE
    pdi2.employees
add
    infoDevice text;

ALTER TABLE
    pdi2.employees
ADD
    force_centre_id BOOLEAN DEFAULT false;

ALTER TABLE
    pdi2.faq
MODIFY
    COLUMN code DOUBLE NOT NULL;

ALTER TABLE
    pdi2.service_prices
add
    user_cancellation_date int;

ALTER TABLE
    pdi2.trackings
add
    state_date date;

ALTER TABLE
    pdi2.employees
add
    version VARCHAR(15) DEFAULT '0.0.1';

update
    trackings
set
    state_date = trackings.started_date
where
    state = 'Pendiente';

ALTER TABLE
    pdi2.centres
ADD
    alias_img varchar(50) NULL DEFAULT '';

ALTER TABLE
    pdi2.service_categories
ADD
    alias_image_land varchar(255) NOT NULL;

ALTER TABLE
    pdi2.service_categories
ADD
    alias_image_portrait varchar(255) NOT NULL;

ALTER TABLE
    pdi2.services
ADD
    alias_img varchar(50) NOT NULL;

update
    pdi2.centres c
    join pdi2.tmp_centros tc on tc.CENTRO = c.name
SET
    c.label = tc.DESCRIPCION,
    c.address = tc.DIRECCIÓN,
    c.phone = tc.TELÉFONO,
    c.email = tc.EMAIL,
    c.timetable = tc.HORARIO,
    c.image = tc.IMAGE_CENTRE,
    c.alias_img = tc.ALIAS_IMG
UPDATE
    service_categories
    join tmp_categorias_servicios tmp on tmp.IDCATEGORY = service_categories.id
set
    name = tmp.CATEGORIA,
    image_portrait = tmp.IMAGENPORTRAIT,
    image = tmp.IMAGENCATEGORY,
    description = tmp.DESCRIPCION,
    alias_image_land = tmp.ALIAS_IMG_CATEGORY,
    alias_image_portrait = tmp.ALIAS_IMG_CATEGORY_PORT;

UPDATE
    services
    join tmp_categorias_servicios tmp on tmp.IDSERVICE = services.id
set
    name = tmp.SERVICIOS,
    image = tmp.IMAGENSERVICIO,
    alias_img = tmp.ALIAS_IMG_SERVICIO,
    description = tmp.`DESCRIPCION SERVICIO`;

ALTER TABLE
    centres
ADD
    exception_island varchar(50) DEFAULT NULL;

update
    centres
set
    exception_island = 'HOSPITAL ICOT CIUDAD DE TELDE'
where
    id = 28;

UPDATE
    services
set
    cancellation_date = '2021-12-21 00:00:00'
where
    name in (
        'REHABILITACIÓN SESIÓN INTENSIVA',
        'BONO REHABILITACIÓN INTENSIVA 10 SESIONES',
        'CONSULTA MÉDICA - MEDICINA INTERVENCIONISTA'
    );

alter table
    employees
modify
    cod_employee varchar(6);

alter table
    validation_rrhh
modify
    cod_employee varchar(6);

update
    employees
set
    cod_employee = LPAD (cod_employee, 6, '0');

update
    validation_rrhh
set
    cod_employee = LPAD (cod_employee, 6, '0');

UPDATE
    services
set
    name = 'CONSULTA MÉDICA NEUROLOGÍA'
where
    name = 'CONSULTA MÉDICA - NEUROLOGÍA';

UPDATE
    services
set
    name = 'CONSULTA MÉDICA TRAUMATOLOGÍA'
where
    name = 'CONSULTA MÉDICA - TRAUMATOLOGÍA';

UPDATE
    services
set
    name = 'CONSULTA MÉDICA MEDICINA INTERNA'
where
    name = 'CONSULTA MÉDICA – MEDICINA INTERNA';

UPDATE
    services
set
    name = 'CONSULTA MÉDICA NEURORREHABILITACIÓN'
where
    name = 'CONSULTA MÉDICA – NEURORREHABILITACIÓN';

UPDATE
    services
set
    name = 'FISIOTERAPIA EN PISCINA CON AUXILIAR DE APOYO'
where
    name = 'FISIOTERAPIA EN PISCINA - CON AUXILIAR DE APOYO';

UPDATE
    services
set
    name = 'FISIOTERAPIA EN PISCINA SESIÓN INDIVIDUAL'
where
    name = 'FISIOTERAPIA EN PISCINA - SESIÓN INDIVIDUAL';

UPDATE
    services
set
    name = 'LOGOPEDIA SESIÓN INDIVIDUAL'
where
    name = 'LOGOPEDIA - SESIÓN INDIVIDUAL';

UPDATE
    services
set
    name = 'ONDAS DE CHOQUE SESIÓN'
where
    name = 'ONDAS DE CHOQUE - SESIÓN';

CREATE
OR REPLACE VIEW pdi2.export_services AS
select
    c.name as center,
    s.name as service,
    sp.price as service_price,
    sp.service_price_direct_incentive as service_direct_incentive,
    sp.service_price_incentive1 as service_incentive1,
    sp.service_price_incentive2 as service_incentive2,
    sp.service_price_super_incentive1 as service_super_incentive1,
    sp.service_price_super_incentive2 as service_super_incentive2,
    d.name as discount,
    spd.price as discount_price,
    spd.direct_incentive as discount_direct_incentive,
    spd.incentive1 as discount_incentive1,
    spd.incentive2 as discount_incentive2,
    spd.super_incentive1 as discount_super_incentive1,
    spd.super_incentive2 as discount_super_incentive2
from
    pdi2.services s
    join pdi2.service_prices sp on sp.service_id = s.id
    and sp.cancellation_date is NULL
    join pdi2.centres c on c.id = sp.centre_id
    and c.cancellation_date is NULL
    left join pdi2.service_prices_discounts spd on spd.service_price_id = sp.id
    and spd.cancellation_date is NULL
    left join discounts as d on d.type = spd.discount_type
where
    s.cancellation_date is NULL
order by
    1,
    2;

UPDATE
    services
set
    name = 'REHABILITACION SESIÓN INFANTIL'
where
    name = 'REHABILLITACION SESIÓN INFANTIL';

ALTER TABLE
    trackings
ADD
    discount varchar(100) DEFAULT NULL;

create
or replace view pdi2.export_target as
select
    DISTINCT `e`.`id` AS `employee_id`,
    `e`.`name` AS `employee`,
    `e`.`rol_id` AS `rol_id`,
    `e`.`dni` AS `dni`,
    `e`.`cod_business` AS `cod_business`,
    `e`.`cod_employee` AS `cod_employee`,
    `e`.`cancellation_date` AS `cancellation_date`,
    `s`.`name` AS `service`,
    `c`.`name` AS `centre`,
    `c`.`id` AS `tracking_centre_id`,
    `c2`.`name` AS `centre_employee`,
    `c2`.`id` AS `centre_employee_id`,
    `t`.`id` AS `tracking_id`,
    `t`.`patient_name` AS `patient_name`,
    `t`.`hc` AS `hc`,
    `t`.`started_date` AS `started_date`,
    `t`.`apointment_date` AS `apointment_date`,
    `t`.`service_date` AS `service_date`,
    `t`.`invoiced_date` AS `invoiced_date`,
    `t`.`validation_date` AS `validation_date`,
    `t`.`paid_date` AS `paid_date`,
    `t`.`observations` AS `observations`,
    `t`.`quantity` AS `quantity`,
    `t`.`state` AS `current_state`,
    `t`.`discount` AS `discount`,
    `d`.`name` AS `discount_name`,
    d.is_calculate,
    `sp`.`price` AS `price`,
    round(((`sp`.`price` * 10) / 100), 2) AS `direct_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj1_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj2_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj1_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj2_incentive`,
    round(`sp`.`service_price_direct_incentive`, 2) AS `service_price_direct_incentive`,
    round(`sp`.`service_price_incentive1`, 2) AS `service_price_incentive1`,
    round(`sp`.`service_price_incentive2`, 2) AS `service_price_incentive2`,
    round(`sp`.`service_price_super_incentive1`, 2) AS `service_price_super_incentive1`,
    round(`sp`.`service_price_super_incentive2`, 2) AS `service_price_super_incentive2`,
    round(`spd`.`price`, 2) AS `discount_price`,
    round(`spd`.`direct_incentive`, 2) AS `discount_direct_incentive`,
    round(`spd`.`incentive1`, 2) AS `discount_incentive1`,
    round(`spd`.`incentive2`, 2) AS `discount_incentive2`,
    round(`spd`.`super_incentive1`, 2) AS `discount_super_incentive1`,
    round(`spd`.`super_incentive2`, 2) AS `discount_super_incentive2`,
    group_concat(distinct `super`.`employee_id` separator ', ') AS `supervisor`
from
    (
        (
            (
                (
                    (
                        (
                            `trackings` `t`
                            join `employees` `e` on ((`e`.`id` = `t`.`employee_id`))
                        )
                        join `centres` `c` on ((`c`.`id` = `t`.`centre_id`))
                    )
                    join `services` `s` on ((`s`.`id` = `t`.`service_id`))
                )
                join `service_prices` `sp` on (
                    (
                        `sp`.`service_id` = `t`.`service_id`
                        and sp.centre_id = t.centre_id
                        and sp.cancellation_date is null
                    )
                )
            )
            join `centres` `c2` on ((`c2`.`id` = `t`.`centre_employee_id`))
        )
        left join `service_prices_discounts` `spd` on (
            (
                `spd`.`service_price_id` = `sp`.`id`
                and `spd`.`cancellation_date` is null
                and spd.cancellation_date is null
                and spd.discount_type = t.discount
            )
        )
        left join `discounts` `d` on ((`d`.`type` = `t`.`discount`))
        left join (
            select
                `eh`.`employee_id` AS `employee_id`,
                `eh`.`centre_id` AS `centre_id`,
                `eh`.`cancellation_date` AS `cancellation_date`,
                `eh`.`created_at` AS `created_at`,
                `eh`.`rol_id` AS `rol_id`
            from
                (
                    `employee_history` `eh`
                    join `roles` `r` on (
                        (
                            (`r`.`id` = `eh`.`rol_id`)
                            and (`r`.`name` = 'SUPERVISOR')
                        )
                    )
                )
        ) `super` on (
            (
                (`super`.`centre_id` = `c2`.`id`)
                and (
                    (
                        `t`.`validation_date` between cast(`super`.`created_at` as date)
                        and (cast(`super`.`cancellation_date` as date) - 1)
                    )
                    or (
                        (`super`.`cancellation_date` is null)
                        and (
                            cast(`super`.`created_at` as date) <= `t`.`validation_date`
                        )
                    )
                )
            )
        )
    )
where
    (`t`.`cancellation_date` is null)
group by
    `e`.`id`,
    `e`.`name`,
    `e`.`rol_id`,
    `e`.`dni`,
    `e`.`cod_business`,
    `e`.`cod_employee`,
    `e`.`cancellation_date`,
    `s`.`name`,
    `c`.`name`,
    `c`.`id`,
    `c2`.`name`,
    `c2`.`id`,
    `t`.`id`,
    `t`.`patient_name`,
    `t`.`hc`,
    `t`.`started_date`,
    `t`.`apointment_date`,
    `t`.`service_date`,
    `t`.`invoiced_date`,
    `t`.`validation_date`,
    `t`.`paid_date`,
    `t`.`observations`,
    `t`.`quantity`,
    `d`.`name`,
    d.is_calculate,
    `sp`.`price`,
    round(((`sp`.`price` * 10) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(`sp`.`service_price_direct_incentive`, 2),
    round(`sp`.`service_price_incentive1`, 2),
    round(`sp`.`service_price_incentive2`, 2),
    round(`sp`.`service_price_super_incentive1`, 2),
    round(`sp`.`service_price_super_incentive2`, 2),
    round(`spd`.`price`, 2),
    round(`spd`.`direct_incentive`, 2),
    round(`spd`.`incentive1`, 2),
    round(`spd`.`incentive2`, 2),
    round(`spd`.`super_incentive1`, 2),
    round(`spd`.`super_incentive2`, 2);

TRUNCATE pdi2.discounts;

INSERT INTO
    pdi2.discounts (name, `type`)
select
    'DESCUENTO EMPLEADO / FAMILIA' as name,
    'DESCUENTO1' as type;

INSERT INTO
    pdi2.discounts (name, `type`)
select
    'DESCUENTO COMPAÑÍA' as name,
    'DESCUENTO2' as type;

INSERT INTO
    pdi2.discounts (name, `type`)
select
    'DESCUENTO FIDELIZACION' as name,
    'DESCUENTO3' as type;

ALTER TABLE
    pdi2.discounts
ADD
    is_calculate BOOLEAN DEFAULT TRUE;

UPDATE
    pdi2.discounts
SET
    is_calculate = FALSE
where
    type = 'DESCUENTO3';

create
or replace view pdi2.export_target as
select
    DISTINCT `e`.`id` AS `employee_id`,
    `e`.`name` AS `employee`,
    `e`.`nombre_a3` AS `nombreA3`,
    `e`.`rol_id` AS `rol_id`,
    `e`.`dni` AS `dni`,
    `e`.`cod_business` AS `cod_business`,
    `e`.`cod_employee` AS `cod_employee`,
    `e`.`cancellation_date` AS `cancellation_date`,
    `s`.`name` AS `service`,
    `c`.`name` AS `centre`,
    `c`.`id` AS `tracking_centre_id`,
    `c2`.`id` as `centre_employee_id`,
    `c3`.`id` as `rc_centre_employee_id`,
    `c2`.`name` AS `centre_employee`,
    `c3`.`name` AS `rc_centre_employee`,
    `t`.`id` AS `tracking_id`,
    `t`.`patient_name` AS `patient_name`,
    `t`.`hc` AS `hc`,
    `t`.`started_date` AS `started_date`,
    `t`.`apointment_date` AS `apointment_date`,
    `t`.`service_date` AS `service_date`,
    `t`.`invoiced_date` AS `invoiced_date`,
    `t`.`validation_date` AS `validation_date`,
    `t`.`paid_date` AS `paid_date`,
    `t`.`observations` AS `observations`,
    `t`.`quantity` AS `quantity`,
    `t`.`state` AS `current_state`,
    `t`.`discount` AS `discount`,
    `d`.`name` AS `discount_name`,
    d.is_calculate,
    `sp`.`price` AS `price`,
    round(((`sp`.`price` * 10) / 100), 2) AS `direct_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj1_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj2_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj1_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj2_incentive`,
    round(`sp`.`service_price_direct_incentive`, 2) AS `service_price_direct_incentive`,
    round(`sp`.`service_price_incentive1`, 2) AS `service_price_incentive1`,
    round(`sp`.`service_price_incentive2`, 2) AS `service_price_incentive2`,
    round(`sp`.`service_price_super_incentive1`, 2) AS `service_price_super_incentive1`,
    round(`sp`.`service_price_super_incentive2`, 2) AS `service_price_super_incentive2`,
    round(`spd`.`price`, 2) AS `discount_price`,
    round(`spd`.`direct_incentive`, 2) AS `discount_direct_incentive`,
    round(`spd`.`incentive1`, 2) AS `discount_incentive1`,
    round(`spd`.`incentive2`, 2) AS `discount_incentive2`,
    round(`spd`.`super_incentive1`, 2) AS `discount_super_incentive1`,
    round(`spd`.`super_incentive2`, 2) AS `discount_super_incentive2`,
    group_concat(distinct `super`.`employee_id` separator ', ') AS `supervisor`
from
    (
        (
            (
                (
                    (
                        (
                            `trackings` `t`
                            join `employees` `e` on ((`e`.`id` = `t`.`employee_id`))
                        )
                        join `centres` `c` on ((`c`.`id` = `t`.`centre_id`))
                    )
                    join `services` `s` on ((`s`.`id` = `t`.`service_id`))
                )
                join `service_prices` `sp` on (
                    (
                        `sp`.`service_id` = `t`.`service_id`
                        and sp.centre_id = t.centre_id
                        and sp.cancellation_date is null
                    )
                )
            )
            join `centres` `c2` on ((`c2`.`id` = `t`.`centre_employee_id`))
        )
        left join `service_prices_discounts` `spd` on (
            (
                `spd`.`service_price_id` = `sp`.`id`
                and `spd`.`cancellation_date` is null
                and spd.cancellation_date is null
            )
        )
        left join `discounts` `d` on ((`d`.`type` = `t`.`discount`))
        left join (
            select
                `eh`.`employee_id` AS `employee_id`,
                `eh`.`centre_id` AS `centre_id`,
                `eh`.`cancellation_date` AS `cancellation_date`,
                `eh`.`created_at` AS `created_at`,
                `eh`.`rol_id` AS `rol_id`
            from
                (
                    `employee_history` `eh`
                    join `roles` `r` on (
                        (
                            (`r`.`id` = `eh`.`rol_id`)
                            and (`r`.`name` = 'SUPERVISOR')
                        )
                    )
                )
        ) `super` on (
            (
                (`super`.`centre_id` = `c2`.`id`)
                and (
                    (
                        `t`.`validation_date` between cast(`super`.`created_at` as date)
                        and (cast(`super`.`cancellation_date` as date) - 1)
                    )
                    or (
                        (`super`.`cancellation_date` is null)
                        and (
                            cast(`super`.`created_at` as date) <= `t`.`validation_date`
                        )
                    )
                )
            )
        )
        left join request_changes rc on rc.id is not null
        and rc.employee_id = e.id
        and rc.validated = 1
        and rc.centre_origin_id = centre_employee_id
        and t.validation_date >= rc.start_date
        and t.validation_date <= rc.end_date
        left join centres c3 on c3.id = rc.centre_destination_id
    )
where
    (`t`.`cancellation_date` is null)
group by
    `e`.`id`,
    `e`.`name`,
    `e`.`rol_id`,
    `e`.`dni`,
    `e`.`cod_business`,
    `e`.`cod_employee`,
    `e`.`cancellation_date`,
    `s`.`name`,
    `c`.`name`,
    `c`.`id`,
    `c2`.`name`,
    `c2`.`id`,
    `c3`.`name`,
    `c3`.`id`,
    `t`.`id`,
    `t`.`patient_name`,
    `t`.`hc`,
    `t`.`started_date`,
    `t`.`apointment_date`,
    `t`.`service_date`,
    `t`.`invoiced_date`,
    `t`.`validation_date`,
    `t`.`paid_date`,
    `t`.`observations`,
    `t`.`quantity`,
    `d`.`name`,
    d.is_calculate,
    `sp`.`price`,
    round(((`sp`.`price` * 10) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(`sp`.`service_price_direct_incentive`, 2),
    round(`sp`.`service_price_incentive1`, 2),
    round(`sp`.`service_price_incentive2`, 2),
    round(`sp`.`service_price_super_incentive1`, 2),
    round(`sp`.`service_price_super_incentive2`, 2),
    round(`spd`.`price`, 2),
    round(`spd`.`direct_incentive`, 2),
    round(`spd`.`incentive1`, 2),
    round(`spd`.`incentive2`, 2),
    round(`spd`.`super_incentive1`, 2),
    round(`spd`.`super_incentive2`, 2);

ALTER TABLE
    pdi2.employees
ADD
    updateRequest INT DEFAULT 0 NULL;

ALTER TABLE
    pdi2.employees
ADD
    excludeRanking varchar(100) NULL;

CREATE
OR REPLACE ALGORITHM = UNDEFINED DEFINER = `root` @`%` SQL SECURITY DEFINER VIEW `export_target` AS
select
    `e`.`id` AS `employee_id`,
    `e`.`name` AS `employee`,
    `e`.`rol_id` AS `rol_id`,
    `e`.`dni` AS `dni`,
    `e`.`cod_business` AS `cod_business`,
    `e`.`cod_employee` AS `cod_employee`,
    `e`.`cancellation_date` AS `cancellation_date`,
    `e`.`excludeRanking` AS `excludeRanking`,
    `s`.`name` AS `service`,
    `c`.`name` AS `centre`,
    `c`.`id` AS `tracking_centre_id`,
    `c2`.`id` AS `centre_employee_id`,
    `c3`.`id` AS `rc_centre_employee_id`,
    `c2`.`name` AS `centre_employee`,
    `c3`.`name` AS `rc_centre_employee`,
    `t`.`id` AS `tracking_id`,
    `t`.`patient_name` AS `patient_name`,
    `t`.`hc` AS `hc`,
    `t`.`started_date` AS `started_date`,
    `t`.`apointment_date` AS `apointment_date`,
    `t`.`service_date` AS `service_date`,
    `t`.`invoiced_date` AS `invoiced_date`,
    `t`.`validation_date` AS `validation_date`,
    `t`.`paid_date` AS `paid_date`,
    `t`.`observations` AS `observations`,
    `t`.`quantity` AS `quantity`,
    `t`.`state` AS `current_state`,
    `t`.`discount` AS `discount`,
    `d`.`name` AS `discount_name`,
    `d`.`is_calculate` AS `is_calculate`,
    `sp`.`price` AS `price`,
    round(((`sp`.`price` * 10) / 100), 2) AS `direct_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj1_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj2_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj1_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj2_incentive`,
    round(`sp`.`service_price_direct_incentive`, 2) AS `service_price_direct_incentive`,
    round(`sp`.`service_price_incentive1`, 2) AS `service_price_incentive1`,
    round(`sp`.`service_price_incentive2`, 2) AS `service_price_incentive2`,
    round(`sp`.`service_price_super_incentive1`, 2) AS `service_price_super_incentive1`,
    round(`sp`.`service_price_super_incentive2`, 2) AS `service_price_super_incentive2`,
    round(`spd`.`price`, 2) AS `discount_price`,
    round(`spd`.`direct_incentive`, 2) AS `discount_direct_incentive`,
    round(`spd`.`incentive1`, 2) AS `discount_incentive1`,
    round(`spd`.`incentive2`, 2) AS `discount_incentive2`,
    round(`spd`.`super_incentive1`, 2) AS `discount_super_incentive1`,
    round(`spd`.`super_incentive2`, 2) AS `discount_super_incentive2`,
    group_concat(distinct `super`.`employee_id` separator ', ') AS `supervisor`
from
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    (
                                        (
                                            `trackings` `t`
                                            join `employees` `e` on ((`e`.`id` = `t`.`employee_id`))
                                        )
                                        join `centres` `c` on ((`c`.`id` = `t`.`centre_id`))
                                    )
                                    join `services` `s` on ((`s`.`id` = `t`.`service_id`))
                                )
                                join `service_prices` `sp` on (
                                    (
                                        (`sp`.`service_id` = `t`.`service_id`)
                                        and (`sp`.`centre_id` = `t`.`centre_id`)
                                        and (`sp`.`cancellation_date` is null)
                                    )
                                )
                            )
                            join `centres` `c2` on ((`c2`.`id` = `t`.`centre_employee_id`))
                        )
                        left join `service_prices_discounts` `spd` on (
                            (
                                (`spd`.`service_price_id` = `sp`.`id`)
                                and (`spd`.`cancellation_date` is null)
                                and (`spd`.`cancellation_date` is null)
                                and (`spd`.`discount_type` = `t`.`discount`)
                            )
                        )
                    )
                    left join `discounts` `d` on ((`d`.`type` = `t`.`discount`))
                )
                left join (
                    select
                        `eh`.`employee_id` AS `employee_id`,
                        `eh`.`centre_id` AS `centre_id`,
                        `eh`.`cancellation_date` AS `cancellation_date`,
                        `eh`.`created_at` AS `created_at`,
                        `eh`.`rol_id` AS `rol_id`
                    from
                        (
                            `employee_history` `eh`
                            join `roles` `r` on (
                                (
                                    (`r`.`id` = `eh`.`rol_id`)
                                    and (`r`.`name` = 'SUPERVISOR')
                                )
                            )
                        )
                ) `super` on (
                    (
                        (`super`.`centre_id` = `c2`.`id`)
                        and (
                            (
                                `t`.`validation_date` between cast(`super`.`created_at` as date)
                                and (cast(`super`.`cancellation_date` as date) - 1)
                            )
                            or (
                                (`super`.`cancellation_date` is null)
                                and (
                                    cast(`super`.`created_at` as date) <= `t`.`validation_date`
                                )
                            )
                        )
                    )
                )
            )
            left join `request_changes` `rc` on (
                (
                    (`rc`.`id` is not null)
                    and (`rc`.`employee_id` = `e`.`id`)
                    and (
                        `rc`.`centre_origin_id` = `t`.`centre_employee_id`
                    )
                    and (`t`.`validation_date` >= `rc`.`start_date`)
                    and (`t`.`validation_date` <= `rc`.`end_date`)
                )
            )
        )
        left join `centres` `c3` on ((`c3`.`id` = `rc`.`centre_destination_id`))
    )
where
    (`t`.`cancellation_date` is null)
group by
    `e`.`id`,
    `e`.`name`,
    `e`.`rol_id`,
    `e`.`dni`,
    `e`.`cod_business`,
    `e`.`cod_employee`,
    `e`.`cancellation_date`,
    `e`.`excludeRanking`,
    `s`.`name`,
    `c`.`name`,
    `c`.`id`,
    `c2`.`name`,
    `c2`.`id`,
    `c3`.`name`,
    `c3`.`id`,
    `t`.`id`,
    `t`.`patient_name`,
    `t`.`hc`,
    `t`.`started_date`,
    `t`.`apointment_date`,
    `t`.`service_date`,
    `t`.`invoiced_date`,
    `t`.`validation_date`,
    `t`.`paid_date`,
    `t`.`observations`,
    `t`.`quantity`,
    `d`.`name`,
    `d`.`is_calculate`,
    `sp`.`price`,
    round(((`sp`.`price` * 10) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(`sp`.`service_price_direct_incentive`, 2),
    round(`sp`.`service_price_incentive1`, 2),
    round(`sp`.`service_price_incentive2`, 2),
    round(`sp`.`service_price_super_incentive1`, 2),
    round(`sp`.`service_price_super_incentive2`, 2),
    round(`spd`.`price`, 2),
    round(`spd`.`direct_incentive`, 2),
    round(`spd`.`incentive1`, 2),
    round(`spd`.`incentive2`, 2),
    round(`spd`.`super_incentive1`, 2),
    round(`spd`.`super_incentive2`, 2);

-- Variable de control solicitudes de desbloqueo
ALTER TABLE
    pdi2.employees
ADD
    unlockRequest int default 0;

-- //VISTAS INCENTIVOS
-- Incentivos Servicios
Create
or replace view incentivosServicios as
select
    s.name as servicio,
    sp.price as precio,
    sp.service_price_direct_incentive as incentivodirecto,
    sp.service_price_incentive1 as incentivoobjetivo1,
    sp.service_price_incentive2 as incentivoobjetivo2,
    sp.service_price_super_incentive1 as incentivosupervisorobjetivo1,
    sp.service_price_super_incentive2 as incentivosupervisorobjetivo2
from
    services s
    join service_prices sp on s.id = sp.service_id
where
    s.cancellation_date is null -- Incentivos Servicios con Descuento
    Create
    or replace view incentivosDescuentos as
select
    distinct s.name as servicio,
    sp.price as precio,
    spd.price as precioDescuento,
    spd.discount_type as tipoDescuento,
    spd.direct_incentive incentivoDescuento,
    spd.incentive1 incentivoObj1,
    spd.incentive2 incentivoObj2,
    spd.super_incentive1 incentivoSupervisorObj1,
    spd.incentive2 incentivoSupervisorObj2
from
    services s
    join service_prices sp on s.id = sp.service_id
    JOIN service_prices_discounts spd on sp.id = spd.service_price_id
where
    s.cancellation_date is null
    and spd.cancellation_date is null -- // Incentivos + Descuentos
    create
    or replace view incentives_discounts_services as
select
    distinct s.name as servicio,
    sp.price as precio,
    spd.price as precioDescuento,
    spd.discount_type as tipoDescuento,
    sp.service_price_direct_incentive incentivo,
    spd.direct_incentive incentivoDescuento,
    sp.service_price_incentive1 incentivoObj1,
    spd.incentive1 incentivoObj1Descuento,
    sp.service_price_incentive2 incentivoObj2,
    spd.incentive2 incentivoObj2Descuento,
    sp.service_price_super_incentive1 incentivoSupervisorObj1,
    spd.super_incentive1 incentivoSupervisorObj1Descuento,
    sp.service_price_super_incentive2 incentivoSupervisorObj2,
    spd.incentive2 incentivoSupervisorObj2Descuento
from
    services s
    join service_prices sp on s.id = sp.service_id
    JOIN service_prices_discounts spd on sp.id = spd.service_price_id
where
    s.cancellation_date is null
    and spd.cancellation_date is null


-- crear vista export_target con definer
CREATE
OR REPLACE ALGORITHM = UNDEFINED DEFINER = `root` @`%` SQL SECURITY DEFINER VIEW `export_target` AS
select
    `e`.`id` AS `employee_id`,
    `e`.`name` AS `employee`,
    `e`.`nombre_a3` AS `nombreA3`,
    `e`.`rol_id` AS `rol_id`,
    `e`.`dni` AS `dni`,
    `e`.`cod_business` AS `cod_business`,
    `e`.`cod_employee` AS `cod_employee`,
    `e`.`cancellation_date` AS `cancellation_date`,
    `e`.`excludeRanking` AS `excludeRanking`,
    `s`.`name` AS `service`,
    `c`.`name` AS `centre`,
    `c`.`id` AS `tracking_centre_id`,
    `c2`.`id` AS `centre_employee_id`,
    `c3`.`id` AS `rc_centre_employee_id`,
    `c2`.`name` AS `centre_employee`,
    `c3`.`name` AS `rc_centre_employee`,
    `t`.`id` AS `tracking_id`,
    `t`.`patient_name` AS `patient_name`,
    `t`.`hc` AS `hc`,
    `t`.`started_date` AS `started_date`,
    `t`.`apointment_date` AS `apointment_date`,
    `t`.`service_date` AS `service_date`,
    `t`.`invoiced_date` AS `invoiced_date`,
    `t`.`validation_date` AS `validation_date`,
    `t`.`paid_date` AS `paid_date`,
    `t`.`observations` AS `observations`,
    `t`.`quantity` AS `quantity`,
    `t`.`state` AS `current_state`,
    `t`.`discount` AS `discount`,
    `d`.`name` AS `discount_name`,
    `d`.`is_calculate` AS `is_calculate`,
    `sp`.`price` AS `price`,
    round(((`sp`.`price` * 10) / 100), 2) AS `direct_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj1_incentive`,
    round(((`sp`.`price` * 5) / 100), 2) AS `obj2_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj1_incentive`,
    round(((`sp`.`price` * 2.5) / 100), 2) AS `superv_obj2_incentive`,
    round(`sp`.`service_price_direct_incentive`, 2) AS `service_price_direct_incentive`,
    round(`sp`.`service_price_incentive1`, 2) AS `service_price_incentive1`,
    round(`sp`.`service_price_incentive2`, 2) AS `service_price_incentive2`,
    round(`sp`.`service_price_super_incentive1`, 2) AS `service_price_super_incentive1`,
    round(`sp`.`service_price_super_incentive2`, 2) AS `service_price_super_incentive2`,
    round(`spd`.`price`, 2) AS `discount_price`,
    round(`spd`.`direct_incentive`, 2) AS `discount_direct_incentive`,
    round(`spd`.`incentive1`, 2) AS `discount_incentive1`,
    round(`spd`.`incentive2`, 2) AS `discount_incentive2`,
    round(`spd`.`super_incentive1`, 2) AS `discount_super_incentive1`,
    round(`spd`.`super_incentive2`, 2) AS `discount_super_incentive2`,
    group_concat(distinct `super`.`employee_id` separator ', ') AS `supervisor`
from
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    (
                                        (
                                            `trackings` `t`
                                            join `employees` `e` on ((`e`.`id` = `t`.`employee_id`))
                                        )
                                        join `centres` `c` on ((`c`.`id` = `t`.`centre_id`))
                                    )
                                    join `services` `s` on ((`s`.`id` = `t`.`service_id`))
                                )
                                join `service_prices` `sp` on (
                                    (
                                        (`sp`.`service_id` = `t`.`service_id`)
                                        and (`sp`.`centre_id` = `t`.`centre_id`)
                                        and (`sp`.`cancellation_date` is null)
                                    )
                                )
                            )
                            join `centres` `c2` on ((`c2`.`id` = `t`.`centre_employee_id`))
                        )
                        left join `service_prices_discounts` `spd` on (
                            (
                                (`spd`.`service_price_id` = `sp`.`id`)
                                and (`spd`.`cancellation_date` is null)
                                and (`spd`.`cancellation_date` is null)
                                and (`spd`.`discount_type` = `t`.`discount`)
                            )
                        )
                    )
                    left join `discounts` `d` on ((`d`.`type` = `t`.`discount`))
                )
                left join (
                    select
                        `eh`.`employee_id` AS `employee_id`,
                        `eh`.`centre_id` AS `centre_id`,
                        `eh`.`cancellation_date` AS `cancellation_date`,
                        `eh`.`created_at` AS `created_at`,
                        `eh`.`rol_id` AS `rol_id`
                    from
                        (
                            `employee_history` `eh`
                            join `roles` `r` on (
                                (
                                    (`r`.`id` = `eh`.`rol_id`)
                                    and (`r`.`name` = 'SUPERVISOR')
                                )
                            )
                        )
                ) `super` on (
                    (
                        (`super`.`centre_id` = `c2`.`id`)
                        and (
                            (
                                `t`.`validation_date` between cast(`super`.`created_at` as date)
                                and (cast(`super`.`cancellation_date` as date) - 1)
                            )
                            or (
                                (`super`.`cancellation_date` is null)
                                and (
                                    cast(`super`.`created_at` as date) <= `t`.`validation_date`
                                )
                            )
                        )
                    )
                )
            )
            left join `request_changes` `rc` on (
                (
                    (`rc`.`id` is not null)
                    and (`rc`.`employee_id` = `e`.`id`)
                    and (
                        `rc`.`centre_origin_id` = `t`.`centre_employee_id`
                    )
                    and (`t`.`validation_date` >= `rc`.`start_date`)
                    and (`t`.`validation_date` <= `rc`.`end_date`)
                )
            )
        )
        left join `centres` `c3` on ((`c3`.`id` = `rc`.`centre_destination_id`))
    )
where
    (`t`.`cancellation_date` is null)
    AND 
    (`e`.`cancellation_date` is null)
group by
    `e`.`id`,
    `e`.`name`,
    `e`.`rol_id`,
    `e`.`dni`,
    `e`.`cod_business`,
    `e`.`cod_employee`,
    `e`.`cancellation_date`,
    `e`.`excludeRanking`,
    `s`.`name`,
    `c`.`name`,
    `c`.`id`,
    `c2`.`name`,
    `c2`.`id`,
    `c3`.`name`,
    `c3`.`id`,
    `t`.`id`,
    `t`.`patient_name`,
    `t`.`hc`,
    `t`.`started_date`,
    `t`.`apointment_date`,
    `t`.`service_date`,
    `t`.`invoiced_date`,
    `t`.`validation_date`,
    `t`.`paid_date`,
    `t`.`observations`,
    `t`.`quantity`,
    `d`.`name`,
    `d`.`is_calculate`,
    `sp`.`price`,
    round(((`sp`.`price` * 10) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(((`sp`.`price` * 2.5) / 100), 2),
    round(`sp`.`service_price_direct_incentive`, 2),
    round(`sp`.`service_price_incentive1`, 2),
    round(`sp`.`service_price_incentive2`, 2),
    round(`sp`.`service_price_super_incentive1`, 2),
    round(`sp`.`service_price_super_incentive2`, 2),
    round(`spd`.`price`, 2),
    round(`spd`.`direct_incentive`, 2),
    round(`spd`.`incentive1`, 2),
    round(`spd`.`incentive2`, 2),
    round(`spd`.`super_incentive1`, 2),
    round(`spd`.`super_incentive2`, 2);


-- añadir cancellation_date a tabla request_changes
ALTER TABLE request_changes
ADD COLUMN cancellation_date DATE DEFAULT NULL;