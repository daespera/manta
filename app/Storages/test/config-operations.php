<?php

return [

    'create' => [

        'operation' => 'create',
        'drivers' => ['mysql', 'elastic'],
        'sync' => [
            'source' => [
                'connection' => 'mysql.urls.id'
            ],
            'destination' => [
                'connection' => 'elastic.url.list._id',
            ],
            'set' => [
                'fields' => ['id']
            ]
        ]

    ],

    'update' => [

        'operation' => 'update',
        'identby'   => ['id'=>'id'],
        'drivers' => ['mysql', 'elastic']

    ],

    'delete' => [

        'operation' => 'delete',
        'identby'   => ['id'=>'id'],
        'drivers' => ['mysql', 'elastic']

    ]

];