<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\ServiceCentre;
use DB;

class ServicePrice extends Pivot
{
    protected $table = 'service_prices';

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

    public static function getCentreIdsByServiceId($serviceId)
    {
        return self::where('service_id', $serviceId)
            ->whereNull('cancellation_date')
            ->pluck('centre_id')
            ->toArray();
    }

    public function previousPrices()
    {
        return $this->hasMany(self::class, 'service_id', 'service_id')
            ->where('id', '!=', $this->id);
    }

    public static function createServicePrice(array $data)
    {
        try {
            $servicePrice = self::create([
                'price' => $data['price'],
                'service_id' => $data['service_id'],
                'centre_id' => $data['centre_id'],
                'service_price_direct_incentive' => $data['service_price_direct_incentive'],
                'service_price_incentive1' => $data['service_price_incentive1'],
                'service_price_incentive2' => $data['service_price_incentive2'],
                'service_price_super_incentive1' => $data['service_price_super_incentive1'],
                'service_price_super_incentive2' => $data['service_price_super_incentive2'],
            ]);
            ServiceCentre::firstOrCreate([
                'service_id' => $data['service_id'],
                'centre_id' => $data['centre_id']
            ]);
            return $servicePrice;
        } catch (\Exception $e) {
            \Log::error('Error al crear ServicePrice: ' . $e->getMessage());
            return null;
        }
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

    /**
     * Cancels a service_price and cancel its corresponding service_centre
     * @param mixed $userId
     * @return static
     */
    public function scopeCancelServicePrice($query, $servicePriceId, $userId = null)
    {
        $servicePrice = ServicePrice::find($servicePriceId);
        if ($servicePrice) {
            $servicePrice->update([
                'cancellation_date' => now(),
                'user_cancellation_date' => $userId
            ]);
            ServiceCentre::where('service_id', $servicePrice->service_id)
                ->where('centre_id', $servicePrice->centre_id)
                ->delete();
        }

        return $this;
    }

    public static function cancelAllServicePrices($serviceId, $userId = null)
    {
        ServicePrice::where('service_id', $serviceId)->update([
            'cancellation_date' => now(),
            'user_cancellation_date' => $userId
        ]);

        ServiceCentre::where('service_id', $serviceId)->delete();
        return true;
    }



    /**
     * Soft delete all active ServicePrice records before an import.
     * Calls cancelServicePrice on each record.
     * Returns the list of modified records.
     */
    public static function softDeleteAllActiveIncentives($userId)
    {
        $services = self::whereNull('cancellation_date')->get();
        foreach ($services as $service) {
            $service->cancelServicePrice($userId);
        }
        return $services;
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
     *Massive import of incentives from Excell, deleting all incentives before.
     */
    public static function importIncentives(array $rows, $userId)
    {
        DB::transaction(function () use ($rows, $userId) {
            self::softDeleteAllActiveIncentives($userId);

            foreach ($rows as $row) {
                self::create([
                    'service_id' => $row['serviceId'],
                    'centre_id' => $row['centreId'],
                    'price' => $row['precio'],
                    'service_price_direct_incentive' => $row['incentivo directo'],
                    'service_price_incentive1' => $row['incentivo individual obj1'],
                    'service_price_incentive2' => $row['incentivo individual obj2'],
                    'service_price_super_incentive1' => $row['bonus supervisor obj1'],
                    'service_price_super_incentive2' => $row['bonus supervisor obj2'],
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
