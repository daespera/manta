<?php

$app->get('process/{storage}/{operation}', 'App\Http\Controllers\MantaController@process');

$app->post('yasha/source', 'App\Http\Controllers\YashaController@source');
$app->post('yasha/destination', 'App\Http\Controllers\YashaController@destination');