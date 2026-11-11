<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mensajes de validación en español
    |--------------------------------------------------------------------------
    |
    | Traducción de los mensajes por defecto de Laravel. El arreglo 'attributes'
    | del final convierte el nombre técnico del campo (por ejemplo
    | 'fecha_recepcion') en un nombre legible ('fecha de recepción'), para que
    | el usuario nunca vea nombres de columnas de la base de datos.
    |
    */

    'accepted' => 'Debes aceptar :attribute.',
    'accepted_if' => 'Debes aceptar :attribute cuando :other sea :value.',
    'active_url' => 'El campo :attribute no es una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El campo :attribute solo puede contener letras.',
    'alpha_dash' => 'El campo :attribute solo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute solo puede contener letras y números.',
    'array' => 'El campo :attribute debe ser un listado.',
    'ascii' => 'El campo :attribute solo puede contener caracteres alfanuméricos y símbolos de un byte.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
        'file' => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'can' => 'El campo :attribute contiene un valor no autorizado.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'contains' => 'Al campo :attribute le falta un valor obligatorio.',
    'current_password' => 'La contraseña es incorrecta.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'date_equals' => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El campo :attribute no corresponde al formato :format.',
    'decimal' => 'El campo :attribute debe tener :decimal decimales.',
    'declined' => 'El campo :attribute debe ser rechazado.',
    'declined_if' => 'El campo :attribute debe ser rechazado cuando :other sea :value.',
    'different' => 'Los campos :attribute y :other deben ser diferentes.',
    'digits' => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions' => 'La imagen :attribute tiene dimensiones no válidas.',
    'distinct' => 'El campo :attribute tiene un valor duplicado.',
    'doesnt_end_with' => 'El campo :attribute no debe terminar con ninguno de los siguientes valores: :values.',
    'doesnt_start_with' => 'El campo :attribute no debe comenzar con ninguno de los siguientes valores: :values.',
    'email' => 'El campo :attribute debe ser una dirección de correo válida.',
    'ends_with' => 'El campo :attribute debe terminar con alguno de los siguientes valores: :values.',
    'enum' => 'El valor seleccionado en :attribute no es válido.',
    'exists' => 'El valor seleccionado en :attribute no es válido.',
    'extensions' => 'El archivo :attribute debe tener una de las siguientes extensiones: :values.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute no puede quedar vacío.',
    'gt' => [
        'array' => 'El campo :attribute debe tener más de :value elementos.',
        'file' => 'El archivo :attribute debe pesar más de :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'string' => 'El campo :attribute debe tener más de :value caracteres.',
    ],
    'gte' => [
        'array' => 'El campo :attribute debe tener :value elementos o más.',
        'file' => 'El archivo :attribute debe pesar :value kilobytes o más.',
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'string' => 'El campo :attribute debe tener :value caracteres o más.',
    ],
    'hex_color' => 'El campo :attribute debe ser un color hexadecimal válido.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El valor seleccionado en :attribute no es válido.',
    'in_array' => 'El campo :attribute debe existir en :other.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'ip' => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4' => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6' => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json' => 'El campo :attribute debe ser una cadena JSON válida.',
    'list' => 'El campo :attribute debe ser una lista.',
    'lowercase' => 'El campo :attribute debe estar en minúsculas.',
    'lt' => [
        'array' => 'El campo :attribute debe tener menos de :value elementos.',
        'file' => 'El archivo :attribute debe pesar menos de :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'string' => 'El campo :attribute debe tener menos de :value caracteres.',
    ],
    'lte' => [
        'array' => 'El campo :attribute debe tener :value elementos o menos.',
        'file' => 'El archivo :attribute debe pesar :value kilobytes o menos.',
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'string' => 'El campo :attribute debe tener :value caracteres o menos.',
    ],
    'mac_address' => 'El campo :attribute debe ser una dirección MAC válida.',
    'max' => [
        'array' => 'El campo :attribute no puede tener más de :max elementos.',
        'file' => 'El archivo :attribute no puede pesar más de :max kilobytes.',
        'numeric' => 'El campo :attribute no puede ser mayor que :max.',
        'string' => 'El campo :attribute no puede tener más de :max caracteres.',
    ],
    'max_digits' => 'El campo :attribute no puede tener más de :max dígitos.',
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El archivo :attribute debe pesar al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'min_digits' => 'El campo :attribute debe tener al menos :min dígitos.',
    'missing' => 'El campo :attribute no debe estar presente.',
    'missing_if' => 'El campo :attribute no debe estar presente cuando :other sea :value.',
    'missing_unless' => 'El campo :attribute no debe estar presente a menos que :other sea :value.',
    'missing_with' => 'El campo :attribute no debe estar presente si :values está presente.',
    'missing_with_all' => 'El campo :attribute no debe estar presente si :values están presentes.',
    'multiple_of' => 'El campo :attribute debe ser múltiplo de :value.',
    'not_in' => 'El valor seleccionado en :attribute no es válido.',
    'not_regex' => 'El formato del campo :attribute no es válido.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'password' => [
        'letters' => 'La contraseña debe contener al menos una letra.',
        'mixed' => 'La contraseña debe contener al menos una mayúscula y una minúscula.',
        'numbers' => 'La contraseña debe contener al menos un número.',
        'symbols' => 'La contraseña debe contener al menos un símbolo.',
        'uncompromised' => 'La contraseña ingresada apareció en una filtración de datos. Elige otra.',
    ],
    'present' => 'El campo :attribute debe estar presente.',
    'present_if' => 'El campo :attribute debe estar presente cuando :other sea :value.',
    'present_unless' => 'El campo :attribute debe estar presente a menos que :other sea :value.',
    'present_with' => 'El campo :attribute debe estar presente si :values está presente.',
    'present_with_all' => 'El campo :attribute debe estar presente si :values están presentes.',
    'prohibited' => 'El campo :attribute está prohibido.',
    'prohibited_if' => 'El campo :attribute está prohibido cuando :other sea :value.',
    'prohibited_if_accepted' => 'El campo :attribute está prohibido cuando se acepta :other.',
    'prohibited_if_declined' => 'El campo :attribute está prohibido cuando se rechaza :other.',
    'prohibited_unless' => 'El campo :attribute está prohibido a menos que :other esté en :values.',
    'prohibits' => 'El campo :attribute impide que :other esté presente.',
    'regex' => 'El formato del campo :attribute no es válido.',
    'required' => 'El campo :attribute es obligatorio.',
    'required_array_keys' => 'El campo :attribute debe contener entradas para: :values.',
    'required_if' => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_if_accepted' => 'El campo :attribute es obligatorio cuando se acepta :other.',
    'required_if_declined' => 'El campo :attribute es obligatorio cuando se rechaza :other.',
    'required_unless' => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with' => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all' => 'El campo :attribute es obligatorio cuando :values están presentes.',
    'required_without' => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values está presente.',
    'same' => 'Los campos :attribute y :other deben coincidir.',
    'size' => [
        'array' => 'El campo :attribute debe contener :size elementos.',
        'file' => 'El archivo :attribute debe pesar :size kilobytes.',
        'numeric' => 'El campo :attribute debe ser :size.',
        'string' => 'El campo :attribute debe tener :size caracteres.',
    ],
    'starts_with' => 'El campo :attribute debe comenzar con alguno de los siguientes valores: :values.',
    'string' => 'El campo :attribute debe ser texto.',
    'timezone' => 'El campo :attribute debe ser una zona horaria válida.',
    'unique' => 'El valor de :attribute ya está registrado en el sistema.',
    'uploaded' => 'No se pudo subir el archivo :attribute.',
    'uppercase' => 'El campo :attribute debe estar en mayúsculas.',
    'url' => 'El campo :attribute debe ser una URL válida.',
    'ulid' => 'El campo :attribute debe ser un ULID válido.',
    'uuid' => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Mensajes personalizados por campo
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'password' => [
            'min' => 'La contraseña debe tener al menos :min caracteres.',
            'max' => 'La contraseña no puede superar los :max caracteres.',
        ],
        'email' => [
            'unique' => 'Este correo ya está registrado por otro usuario.',
        ],
        'excel' => [
            'required' => 'Debes seleccionar el archivo Excel que quieres cargar.',
            'mimes' => 'El archivo debe ser un Excel (.xlsx o .xls).',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nombres legibles de los campos
    |--------------------------------------------------------------------------
    |
    | Evita que el usuario vea nombres técnicos como 'provider_id' o
    | 'fecha_recepcion' dentro de los mensajes de error.
    |
    */

    'attributes' => [

        // Usuarios, roles y sesión
        'name' => 'nombre',
        'last_name' => 'apellido',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'role' => 'rol',
        'roles' => 'roles',
        'modulos' => 'vistas habilitadas',
        'status' => 'estado',
        'remember' => 'recordarme',

        // Proveedores
        'rut' => 'RUT',
        'rut_proveedor' => 'RUT del proveedor',
        'razon_social' => 'razón social',
        'provider_id' => 'proveedor',
        'category' => 'categoría',
        'phone' => 'teléfono',
        'business_line' => 'giro',
        'address' => 'dirección',

        // Productos e inventario
        'product_id' => 'producto',
        'producto' => 'producto',
        'productos' => 'productos',
        'categoria' => 'categoría',
        'programa' => 'programa',
        'programas' => 'programas',
        'lote' => 'lote',
        'lotes' => 'lotes',
        'cantidad' => 'cantidad',
        'cantidades' => 'cantidades',
        'stock_actual' => 'stock actual',
        'stock_minimo' => 'stock mínimo',
        'fecha_vencimiento' => 'fecha de vencimiento',
        'vencimientos' => 'fechas de vencimiento',
        'valor_neto_unitario' => 'valor neto unitario',
        'estado' => 'estado',
        'motivo' => 'motivo',
        'observaciones' => 'observaciones',
        'excel' => 'archivo Excel',

        // Ubicación en bodega
        'pasillo' => 'pasillo',
        'estante' => 'estante',
        'nivel' => 'nivel',
        'posicion' => 'posición',
        'color' => 'color',
        'aisle_id' => 'pasillo',
        'shelf_id' => 'estante',
        'level_id' => 'nivel',

        // Recepción
        'fecha_recepcion' => 'fecha de recepción',
        'tipo_documento' => 'tipo de documento',
        'numero_documento' => 'número de documento',
        'orden_de_compra' => 'orden de compra',
        'origen' => 'origen',
        'origen_mercaderia' => 'origen de la mercadería',
        'correlativo_interno' => 'correlativo interno',
        'firma_receptor' => 'firma del receptor',

        // Solicitudes
        'establecimiento' => 'establecimiento',
        'fecha_solicitud' => 'fecha de solicitud',
        'responsable' => 'responsable',
        'cantidad_solicitada' => 'cantidad solicitada',
        'firma_recibe' => 'firma de quien recibe',
        'firma_solicitante' => 'firma del solicitante',
        'establishment_request_id' => 'solicitud asociada',
        'assigned_user_id' => 'usuario asignado',

        // Despacho
        'fecha_despacho' => 'fecha de despacho',
        'correlativo_despacho' => 'correlativo de despacho',
        'clasificacion' => 'clasificación del despacho',
        'estado_liberacion' => 'estado de liberación',
        'establecimiento_destino' => 'establecimiento de destino',
        'tipo_externo_despacho' => 'tipo de destino externo',
        'direccion_destino' => 'dirección de destino',
        'condiciones_transporte' => 'condiciones de transporte',
        'firma_despacha' => 'firma de quien despacha',

        // Reportes y filtros
        'tipo' => 'tipo',
        'desde' => 'fecha desde',
        'hasta' => 'fecha hasta',
        'order_by' => 'orden',
        'search' => 'búsqueda',
    ],

];
