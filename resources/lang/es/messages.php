<?php

return [
    'errors' => [
        'centre_id_required' => 'El ID del centro es obligatorio.',
        'department_not_found' => 'No se encontró un departamento asociado a este supervisor.',
        'validation' => 'El campo de cantidad es obligatorio y debe ser un número.',
        'import_failed' => 'Error durante la importación: :message',
        'file_not_selected' => 'Error! No se seleccionó ningún archivo.',
        'file_format' => 'Error de formato de fichero a importar.',
        'file_size_or_format' => 'Error, superado tamaño de fichero o formato no válido.',
        'incentive_calculation_failed' => 'Error durante el cálculo de incentivos: :message',  // Para el caso de error en el cálculo
    ],
    'success' => [
        'target_updated' => 'Objetivo actualizado correctamente.',
        'targets_imported' => '¡Importados objetivos!',
        'targets_edited' => '¡Editados objetivos!',
        'incentive_calculation_success' => 'Cálculo de incentivos realizado correctamente.',  // Mensaje de éxito para el cálculo
    ],
];
