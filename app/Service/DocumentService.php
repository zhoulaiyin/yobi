<?php
// +----------------------------------------------------------------------
// | ebSIG
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.ebsig.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liudaojian <liudaojian@ebsig.com>
// +----------------------------------------------------------------------

/**
 * 文档服务类
 * @author   liudaojian <liudaojian@ebsig.com>
 * @version 1.0
 */

namespace App\Service;

use App\Models\Document\DevelopDocument;

use App\Models\Document\DevelopDocumentGroup;

class DocumentService
{

    /**
     * 获取文档分组信息
     * @param int $id
     * @return mixed
     */
    public function getDocument( $id )
    {

        $document_group = DevelopDocumentGroup::select('id','sort', 'group_name', 'parent_id')
            ->where('parent_id', $id)
            ->orderBy('sort', 'asc')
            ->get();


        if ( !$document_group ) {
            return $document_group;
        }

        $document_group = $document_group->toArray();

        return $document_group;

    }

    /**
     * 生成部门列表html代码
     * @param array $document_group 文档分组
     * @param int $choose_id 选中的部门id，默认为0
     * @return string
     */
    public function getDepartmentsHtml( $document_group, $choose_id = 0)
    {

        $html = '';

        foreach( $document_group as $k=>$d) {

            if ($choose_id == $d['id']) {
                $html .= '<a href="javascript:;" class="list-group-item active"  data-id="' . $d['id'] . '">' . $d['group_name'] . '<span style="float: right;"><i class="layui-icon" title="删除分组" onclick="Group.del(' . $d['id'] . ');">&#xe640;</i>&nbsp;&nbsp;<i class="layui-icon" title="编辑分组" onclick="Group.edit(2,' . $d['id'] . ');">&#xe642;</i></span></a>';
            } else {
                if ( $k == 0 ) {
                    $html .= '<a href="javascript:;" class="list-group-item active"  data-id="' . $d['id'] . '">' . $d['group_name'] . '<span style="float: right;"><i class="layui-icon" title="删除分组" onclick="Group.del(' . $d['id'] . ');">&#xe640;</i>&nbsp;&nbsp;<i class="layui-icon" title="编辑分组" onclick="Group.edit(2,' . $d['id'] . ');">&#xe642;</i></span></a>';
                } else {
                    $html .= '<a href="javascript:;" class="list-group-item " data-id="' . $d['id'] . '">' . $d['group_name'] . '<span style="float: right;"><i class="layui-icon" title="删除分组" onclick="Group.del(' . $d['id'] . ');">&#xe640;</i>&nbsp;&nbsp;<i class="layui-icon" title="编辑分组" onclick="Group.edit(2,' . $d['id'] . ');">&#xe642;</i></span></a>';
                }
            }

            if (!empty($d['sub'])) {
                $html .= $this->getDepartmentsHtml($d['sub'], $choose_id);
            }
        }

        return $html;

    }

    //  生成部门select的html代码
    public function getDepartmentsSelectHtml( $document_group , $level = -1)
    {

        $html = '';
        $level++;

        foreach( $document_group as $d ) {
            if ($level == 0) {
                $html .= '<option value="' . $d['id'] . '">' . $d['group_name'] . '</option>';
            } else {
                $ml = '';
                for($i = 1; $i <= $level*4; $i++) {
                    $ml .= '&nbsp;';
                }
                $html .= '<option value="' . $d['id'] . '">' . $ml . $d['group_name'] . '</option>';
            }

            if (!empty($d['sub'])) {
                $html .= $this->getDepartmentsSelectHtml($d['sub'], $level);
            }
        }

        return $html;

    }

}