<?php

namespace App\Http\Controllers\API;

use App\Centre;
use App\Department;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;

class CentreController extends BaseController
{
    public function getCenters()
    {
        try {
            $centers = Centre::select('id', 'name', 'image')
                ->whereNull('cancellation_date')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get();

            return $this->sendResponse($centers, '');
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos', 500);
        }
    }

    public function getDepartments()
    {
        try {
            $departments = Department::all();
            return $this->sendResponse($departments, '');
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos', 500);
        }
    }

    public function getDepartmentById($id)
    {
        try {
            $department = Department::find($id);

            if (!$department) {
                return $this->sendError('Departamento no encontrado', 404);
            }

            return $this->sendResponse($department, '');
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos', 500);
        }
    }

    public function getDepartmentsBySupervisorId($supervisorId)
    {
        try {
            $departments = Department::where('supervisor_id', $supervisorId)->get();

            if ($departments->isEmpty()) {
                return $this->sendError('No se encontraron departamentos para este supervisor', 404);
            }

            return $this->sendResponse($departments, '');
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos', 500);
        }
    }

    public function getCentersByService($serviceId)
    {
        try {
            $centers = Centre::getCentersByServiceId($serviceId);
            return $centers;
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos', 500);
        }
    }
}
