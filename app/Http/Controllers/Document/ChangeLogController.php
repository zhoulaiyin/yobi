<?php

namespace App\Http\Controllers\Document;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Classes\Console\Document\DocChangeLogClass;

class ChangeLogController extends Controller
{

    public function index()
    {
        $changeLog = DocChangeLogClass::getList([], ['pageSize' => 9999]);

        $data ['log'] = empty($changeLog['data']) ? [] : $changeLog['data'];

        return view('document/wiki/changeLog', $data);
    }

    public function detail(Request $request)
    {
        $log_id = $request->input('log_id');

        if (!$log_id || !ebsig_is_int($log_id)) {
            return response()->json(['code' => 10000, 'message' => '参数错误']);
        }

        $DocChangeLog = DocChangeLogClass::fetch($log_id);
        if (!$DocChangeLog) {
            return response()->json(['code' => 10001, 'message' => '文档不存在']);
        }

        return response()->json([
            'code' => 200,
            'message' => 'OK',
            'data' => $DocChangeLog['doc_html']
        ]);
    }

}