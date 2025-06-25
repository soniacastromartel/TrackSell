<?php

namespace App\Services;

use App\Employee;
use Illuminate\Support\Facades\Log;

class EmployeeService
{
    public static function checkLogin(string $username): array
    {
        $employee = Employee::findActiveByUsername($username);

        if (!$employee) {
            return self::buildResponse('RESPONSE_NO_VALID', 'El usuario no existe.', 404);
        }

        if (!$employee->validated) {
            return self::buildResponse('RESPONSE_PENDING_VALIDATION', 'Usuario no validado.', 403);
        }

        if (is_null($employee->pending_password)) {
            if ($employee->count_access >= 3) {
                return self::buildResponse('ACCOUNT_BLOCK', 'Cuenta bloqueada.', 423);
            }

            try {
                Employee::updatingAccess($employee->id, 0);
            } catch (\Throwable $e) {
                Log::error('Error actualizando count_access: ' . $e->getMessage());
                return self::buildResponse('ACCESS_UPDATE_ERROR', 'Error al actualizar accesos.', 500);
            }

            return self::buildResponse('RESPONSE_OK', 'Acceso correcto.', 200);
        }

        if ($employee->pending_password === env('RESPONSE_PENDING_VALIDATION')) {
            return self::buildResponse('RESPONSE_PENDING_VALIDATION', 'Pendiente de validación.', 403);
        }

        return self::buildResponse('RESPONSE_PENDING_CHANGE_PASS', 'Pendiente de cambio de contraseña.', 403);
    }

    private static function buildResponse(string $code, string $message, int $http)
    {
        return [
            'code' => $code,
            'message' => $message,
            'status' => $code === 'RESPONSE_OK' ? 'ok' : 'error',
            'http' => $http,
        ];
    }
}