<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ServicePrice extends Model
{
    protected $fillable = [
        'id',
        'price',
        'service_id',
        'centre_id',
        'service_price_direct_incentive',
        'service_price_incentive1',
        'service_price_incentive2',
        'service_price_super_incentive1',
        'service_price_super_incentive2',
        'cancellation_date',
        'user_cancellation_date'
    ];

    protected $casts = [
        'price' => 'float',
    ];

    //! RELATIONS
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function previousPrices()
    {
        return $this->hasMany(self::class, 'service_id', 'service_id')
            ->where('id', '!=', $this->id);
    }

    //! SCOPES
    public function scopeGetPricesActive($query, $serviceId, $centreId)
    {
        return $query->where('service_id', $serviceId)
            ->where('centre_id', $centreId)
            ->whereNull('cancellation_date');
    }

    public function scopeGetLatestPrice($query, $serviceId, $centreId)
    {
        return $query->where('service_id', $serviceId)
            ->where('centre_id', $centreId)
            ->whereNull('cancellation_date')
            ->latest('id')
            ->first();
    }

    public function scopeGetAllPrices($query, $serviceId, $centreId)
    {
        return $query->where('service_id', $serviceId)
            ->where('centre_id', $centreId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    //! FUNCTIONS
    public function isActive()
    {
        return is_null($this->cancellation_date);
    }

    public function cancelPrice($userId = null)
    {
        $this->cancellation_date = now();
        $this->user_cancellation_date = $userId;
        $this->save();
    }

    public function getTotalIncentive()
    {
        return $this->service_price_direct_incentive +
            $this->service_price_incentive1 +
            $this->service_price_incentive2 +
            $this->service_price_super_incentive1 +
            $this->service_price_super_incentive2;
    }

    /**
     * Soft delete de todos los registros activos antes de la importación
     */
    public static function softDeleteAllActiveIncentives($userId)
    {
        return self::whereNull('cancellation_date')
            ->update([
                'cancellation_date' => now(),
                'user_cancellation_date' => $userId
            ]);
    }

    /**
     * Importación masiva de incentivos desde un array de datos con borrado lógico antes de importar
     */
    public static function importIncentives(array $data, $userId)
    {
        DB::transaction(function () use ($data, $userId) {
            self::softDeleteAllActiveIncentives($userId);
            foreach ($data as $row) {
                self::create([
                    'price' => $row['price'],
                    'service_id' => $row['service_id'],
                    'centre_id' => $row['centre_id'],
                    'service_price_direct_incentive' => $row['direct_incentive'],
                    'service_price_incentive1' => $row['incentive1'],
                    'service_price_incentive2' => $row['incentive2'],
                    'service_price_super_incentive1' => $row['super_incentive1'],
                    'service_price_super_incentive2' => $row['super_incentive2'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });
    }

    /**
     * Actualiza un ServicePrice existente con nuevos datos
     */
    public static function updateServicePrice($id, array $data)
    {
        $servicePrice = self::find($id);
        if (!$servicePrice) {
            return null;
        }
        $servicePrice->update([
            'price' => $data['price'] ?? $servicePrice->price,
            'service_price_direct_incentive' => $data['direct_incentive'] ?? $servicePrice->service_price_direct_incentive,
            'service_price_incentive1' => $data['incentive1'] ?? $servicePrice->service_price_incentive1,
            'service_price_incentive2' => $data['incentive2'] ?? $servicePrice->service_price_incentive2,
            'service_price_super_incentive1' => $data['super_incentive1'] ?? $servicePrice->service_price_super_incentive1,
            'service_price_super_incentive2' => $data['super_incentive2'] ?? $servicePrice->service_price_super_incentive2,
            'updated_at' => now(),
        ]);

        return $servicePrice;
    }
}
