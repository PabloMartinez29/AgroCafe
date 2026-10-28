<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Responsable del tratamiento de datos personales
    |--------------------------------------------------------------------------
    |
    | Estos datos se muestran en los documentos legales. Completa el NIT y la
    | dirección en el archivo .env antes de publicar el sitio.
    |
    */

    'controller' => [
        'name' => env('LEGAL_COMPANY_NAME', 'AgroCafé'),
        'nit' => env('LEGAL_COMPANY_NIT'),
        'address' => env('LEGAL_COMPANY_ADDRESS'),
        'city' => env('LEGAL_COMPANY_CITY', 'Colombia'),
        'email' => env('LEGAL_CONTACT_EMAIL', 'contacto@agrocafe.com'),
        'privacy_email' => env('LEGAL_PRIVACY_EMAIL', env('LEGAL_CONTACT_EMAIL', 'contacto@agrocafe.com')),
        'phone' => env('LEGAL_CONTACT_PHONE', '+57 300 123 4567'),
        'schedule' => env('LEGAL_CONTACT_SCHEDULE', 'Lunes a viernes, de 8:00 AM a 6:00 PM'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Documentos legales y su versión
    |--------------------------------------------------------------------------
    |
    | Cada vez que cambies el contenido de un documento debes subir su
    | "version" y su "effective_date". Los documentos que exigen autorización
    | ("consent" => true) se registran en la tabla consent_records junto con la
    | versión y el hash del archivo Blade que el titular vio al aceptar.
    |
    */

    'documents' => [
        'terms' => [
            'title' => 'Términos y Condiciones',
            'version' => '1.0',
            'effective_date' => '2026-10-27',
            'route' => 'legal.terms',
            'view' => 'legal.terms',
            'consent' => true,
            'checkbox' => 'He leído y acepto los Términos y Condiciones de uso de AgroCafé.',
            'checkbox_assisted' => 'Confirmo que el titular leyó y aceptó los Términos y Condiciones de uso de AgroCafé.',
            'link_label' => 'Leer Términos y Condiciones',
        ],
        'privacy' => [
            'title' => 'Política de Privacidad',
            'version' => '1.0',
            'effective_date' => '2026-10-27',
            'route' => 'legal.privacy',
            'view' => 'legal.privacy',
            'consent' => true,
            'checkbox' => 'Autorizo de manera previa, expresa e informada el tratamiento de mis datos personales, incluida su transmisión o transferencia a proveedores tecnológicos dentro o fuera de Colombia, conforme a la Política de Privacidad de AgroCafé.',
            'checkbox_assisted' => 'Confirmo que informé al titular y que autorizó de manera previa, expresa e informada el tratamiento de sus datos personales, incluida su transmisión o transferencia a proveedores tecnológicos dentro o fuera de Colombia, conforme a la Política de Privacidad de AgroCafé.',
            'link_label' => 'Leer Política de Privacidad',
        ],
        'cookies' => [
            'title' => 'Política de Cookies',
            'version' => '1.0',
            'effective_date' => '2026-10-27',
            'route' => 'legal.cookies',
            'view' => 'legal.cookies',
            'consent' => false,
        ],
        'refund' => [
            'title' => 'Política de Reembolsos y Retracto',
            'version' => '1.0',
            'effective_date' => '2026-10-27',
            'route' => 'legal.refund',
            'view' => 'legal.refund',
            'consent' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Venta en línea
    |--------------------------------------------------------------------------
    |
    | AgroCafé hoy NO vende productos ni cobra servicios por internet. Si algún
    | día habilitas ventas a distancia, cambia este valor a true y revisa la
    | Política de Reembolsos (derecho de retracto, Ley 1480 de 2011).
    |
    */

    'sells_online' => (bool) env('LEGAL_SELLS_ONLINE', false),

];
