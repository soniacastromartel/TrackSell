<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class A3Empleado extends Model
{   
    protected $table = 'a3_empleados'; 
    protected $fillable = [
        'Nombre_Completo'
        ,'NIF'
        ,'Telefono'
        ,'Nombre_Empresa'
        ,'Nombre_del_Centro'
        ,'Codigo_Empresa'
        ,'Codigo_Centro'
        ,'Codigo_Empleado'
        ,'Categoria'
        ,'Email'
        ,'Fecha_de_alta_en_compañia'
        ,'Fecha_de_baja_en_compañia'
    ];
}
