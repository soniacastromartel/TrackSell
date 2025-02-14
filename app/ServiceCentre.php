<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCentre extends Model
{
    use HasFactory;

    protected $table = 'service_centres';

    protected $fillable = [
        'id',
        'service_id',
        'centre_id',
    ];

    public function createServiceCentre(array $data)
    {
        try {
            return self::create([
                'service_id' => $data['service_id'],
                'centre_id' => $data['centre_id']
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al crear ServiceCentre: ' . $e->getMessage());
            return null;
        }
    }

    public function getServiceCentres($serviceId, $centreId)
    {
        $serviceCentres = self::where('service_id', $serviceId)
            ->where('centre_id', $centreId)
            ->get();
        return $serviceCentres;
    }

    public function updateServiceCentre($id, array $data)
    {
        try {
            $serviceCentre = self::find($id);
            if ($serviceCentre) {
                $serviceCentre->update($data);
                return $serviceCentre;
            }
            return null;
        } catch (\Exception $e) {
            \Log::error('Error al actualizar ServiceCentre: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteServiceCentre($serviceId, $centreId)
    {
        try {
            $serviceCentre = self::where('service_id', $serviceId)
            ->where('centre_id', $centreId
            )->delete();
            return $serviceCentre > 0;
        } catch (\Exception $e) {
            \Log::error('Error al eliminar ServiceCentre: ' . $e->getMessage());
            return false;
        }
    }
}
