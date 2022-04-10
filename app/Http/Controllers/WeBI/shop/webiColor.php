<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/4
 * Time: 15:42
 */

namespace App\Http\Controllers\WeBI\shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Classes\Console\BiColorClass;

class webiColor extends Controller
{

    public function get(Request $request){
       $colorResult = BiColorClass::getList([],['pageSize' => 999], 'color_code');
        if($colorResult['count']){
            $color = [];
            foreach($colorResult['data'] as $key => $col){
                $color[] = $col['color_code'];
            }

            $colors = array_values($color);
			
        }else{
            $colors = [
                'f44336','e91e63','9c27b0','673ab7','3f51b5','2196f3','03a9f4'
                ,'00bcd4','009688','4caf50','8bc34a','cddc39','ffeb3b','ffc107'
                ,'ff9800','ff5722','795548','607d8b'
            ];
        }

        $return_data = [
            'code' => 200,
            'message' => 'ok',
            'data' => $colors
        ];

        return response()->json($return_data);
    }

}