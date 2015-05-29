<?php

return [

    'records' => [

        [
            
            'index' => 'url',
            'type' => 'list',

            'fields' => [

                'alias'     => [
                    'pre'   => 'concat:alias,version',
                    'field' => 'alias'
                ],
                
                'url'  => 'url',

                'visitors[]' => [
                    'aggregate' => [
                        'connection' => 'mysql.visitors.id',
                        'source'     => 'name'
                    ],
                    'field'     => 'visitors'
                ]
                
            ]

        ]

    ]
    
];