<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\SyncContract as Sync;

class YashaController extends BaseController
{
    
    public function source(Request $request, Sync $sync)
    {

        $sync->setSource($request->get('sync_id'), $request->get('data'));

    }

    public function destination(Request $request, Sync $sync)
    {

        $sync->setDestination($request->get('sync_id'), $request->get('data'));

    }

}