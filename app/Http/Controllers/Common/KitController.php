<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use Gregwar\Captcha\PhraseBuilder;
use Gregwar\Captcha\CaptchaBuilder;
use App\Http\Controllers\Controller;

class KitController extends Controller
{

    /**
     * 生成图片验证码
     * @param Request $request
     */
    public function captcha(Request $request)
    {
        global $WS;

        $width = $request->input('w', 100);
        $height = $request->input('h', 40);

        $phrase = new PhraseBuilder;

        //生成图片验证码内容
        $code = $phrase->build();

        //生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder($code, $phrase);

        //可以设置图片宽高及字体
        $builder->build($width, $height, $font = null);

        //获取验证码的内容
        $phrase = $builder->getPhrase();

        //把内容存入session
        $WS->sessionSet('yzm' . session()->getId(), $phrase, 3600, true);

        //生成图片
        header('Content-Type: image/jpeg');
        $builder->output();

    }

}
