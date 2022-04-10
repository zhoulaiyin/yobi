<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis as Redis;

class KeyController extends Controller
{

    /**
     * ��ȡApiͨ��Key
     * @return \Illuminate\Http\JsonResponse
     */
    public function api()
    {

        $master_key = Redis::get('G_API_MASTER_KEY');
        if (!$master_key) {
            return response()->json('ec48d4004f4b02eadaf24af32a281979');
        }

        return response()->json($master_key);

    }

}
