<?php

return [

    'records' => [

        [

            'table'    => 'urls',

            'fields' => [
                
                'alias' 	=> [
                    'pre'   => 'concat:alias,version',
                    'field' => 'alias'
                ],

                'url' 		=> [
                    'pre'   => 'toUpper',
                    'field' => 'url'
                ]

            ]

        ],

        [

            'table'   => 'url_visitors',

            'pivot'     => [
                'key'       => ['url_id'],
                'iterator'  => ['visitors'=>'visitor_id'],
                'inherit'   => ['id'=>'url_id'],                
            ]

        ],

    ]

];