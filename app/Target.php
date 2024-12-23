<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $table = 'targets';

    protected $fillable = [
        'id',
        'year',
        'month',
        'obj1',
        'obj2',
        'centre_id',
        'vd',
        'obj1_done',
        'obj2_done',
        'calc_month'
    ];

    /**
     * Obtiene el target correspondiente para el año, mes y centro específicos.
     *
     * @param int $year
     * @param int $month
     * @param int $centre_id
     * @return \App\Target|null
     */
    public static function getTargetByYearMonthAndCentre($year, $month, $centre_id)
    {
        return self::where('year', $year)
            ->where('month', $month)
            ->where('centre_id', $centre_id)
            ->first();
    }
    /**
     * Actualizar Target dependiendo de si ya existe.
     *
     * @param int $year
     * @param int $month
     * @param int $centre_id
     * @param array $data
     * @return \App\Target
     */
    public static function updateTarget($year, $month, $centre_id, $data = null)
    {
        $target = self::getTargetByYearMonthAndCentre($year, $month, $centre_id);

        if ($target) {
            $updatedRows = $target->update($data);

            if ($updatedRows > 0) {
                return $target;
            } else {
                throw new \Exception('La actualización falló. No se realizaron cambios en el objetivo.');
            }
        } else {
            throw new \Exception('El objetivo especificado no existe.');
        }
    }

    /**
     * Obtiene los Targets que no tienen el campo 'vd' actualizado para un mes y año específicos.
     *
     * @param int $year
     * @param int $month
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUnupdatedVDTargets($year, $month)
    {
        return self::where('year', $year)
            ->where('month', $month)
            ->whereNull('vd')
            ->orWhere('vd', '=', 0)
            ->get();
    }

    /**
     * Obtiene los targets filtrados según los campos proporcionados en $whereFields.
     *
     * @param string $whereFields
     * @return array
     */
    public static function getTargetsByFields($whereFields)
    {
        return self::whereRaw($whereFields)
            ->orderBy('month', 'asc')
            ->orderBy('year', 'asc')
            ->get()
            ->toArray();
    }

}
