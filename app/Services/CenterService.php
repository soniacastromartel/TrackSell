<?php

namespace App\Services;

use App\Centre;

class CenterService
{
    public static function getPrescriptorCenters($employee)
    {
        $centres = [];

        $principalCentre = Centre::find($employee->centre_id);
        if (!$principalCentre) {
            return $centres;
        }

        $centres[] = self::formatCentreData($principalCentre, true);

        if ($principalCentre->island === 'TENERIFE') {
            $tfeCentres = Centre::where('island', 'TENERIFE')
                ->whereNull('cancellation_date')
                ->get();

            foreach ($tfeCentres as $c) {
                if ($c->id != $principalCentre->id) {
                    $centres[] = self::formatCentreData($c, false);
                }
            }
        } elseif (
            in_array($principalCentre->id, [
                env('ID_CENTRE_ICOT'),
                env('ID_CENTRE_PARQUE_LANZAROTE'),
                env('ID_CENTRE_PARQUE_FUERTEVENTURA')
            ])
        ) {
            $lpaCentres = Centre::where('island', 'GRAN CANARIA')
                ->where('name', 'not like', "%HOSPITAL%")
                ->whereNull('cancellation_date')
                ->get();

            foreach ($lpaCentres as $c) {
                if ($c->id != $principalCentre->id) {
                    $centres[] =self::formatCentreData($c, false);
                }
            }
        }

        return $centres;
    }

    public static function formatCentreData($centre, $principal = false)
    {
        return [
            'centre' => $centre->label,
            'centre_id' => $centre->id,
            'centre_address' => $centre->address,
            'centre_phone' => $centre->phone,
            'centre_email' => $centre->email,
            'timetable' => $centre->timetable,
            'island' => $centre->island,
            'image' => env('BASE_API_URL') . $centre->image,
            'principal' => $principal,
        ];
    }
}
