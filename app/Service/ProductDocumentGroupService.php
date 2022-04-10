<?php
/**
 * 产品文档服务类
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/19
 * Time: 15:42
 */
namespace  App\Service;

use App\Models\System\ProductDocument;

class ProductDocumentGroupService
{
    /**
     * 显示分组列表
     * @param array $group_data
     * @return string
     */
    public function getGroupHtml( $group_data )
    {
        $html = '';
        if (isset($group_data) && !empty($group_data)) {
            foreach ($group_data as $gd) {

                $html .= '<a href="javascript:;" class="list-group-item" id="one-group-' . $gd['id'] . '">' . $gd['title'] . '<span style="float: right;"><i class="layui-icon" title="删除分组" onclick="DocumentGroup.del('. $gd['id'] .')">&#xe640;</i>&nbsp;&nbsp;<i class="layui-icon" title="编辑分组" onclick="DocumentGroup.edit(2,'. $gd['id'] .')">&#xe642;</i></span></a>';

                if (isset($gd['parent_id']) && !empty($gd['parent_id'])) {

                    foreach ($gd['parent_id'] as $g) {

                        $html .= '<a href="javascript:;" class="list-group-item" style="padding-left: 30px;" id="two-group-' . $g['id'] . '">' . $g['title'] . '<span style="float: right;"><i class="layui-icon" title="删除分组" onclick="DocumentGroup.del('. $g['id'] .')">&#xe640;</i>&nbsp;&nbsp;<i class="layui-icon" title="编辑分组" onclick="DocumentGroup.edit(2,'. $g['id'] .')">&#xe642;</i></span></a>';

                        if (isset($g['doc']) && !empty($g['doc'])) {

                            foreach ($g['doc'] as $doc) {

                                $html .= '<a href="javascript:;" class="list-group-item" style="padding-left: 45px;" id="doc-title-' . $doc['id'] . '">' . $doc['title'] . '</a>';


                            }

                        }

                    }

                }

            }
        }
        return $html;
    }

    /**
     * 添加编辑分组 下拉框显示分组
     * @param $group_data
     * @return string
     */
    public function getGroupSelectHtml( $group_data )
    {
        $html = '';
        if (isset($group_data) && !empty($group_data)) {
            foreach ($group_data as $gd) {

                $html .= '<option value="' . $gd['id'] . '">' . $gd['title'] . '</option>';


            }
        }
        return $html;
   }

    /**
     * 添加修改文档分组下拉框
     * @param $group_data
     * @return string
     */
    public function getDocGroupSelectHtml($group_data)
    {
        $html = '';
        if (isset($group_data) && !empty($group_data)) {
            foreach ($group_data as $gd) {

                $html .= '<option value="' . $gd['id'] . '">' . $gd['title'] . '</option>';


                if (isset($gd['parent_id']) && !empty($gd['parent_id'])) {

                    foreach ($gd['parent_id'] as $g) {

                        $html .= '<option value="' . $g['id'] . '">' . '&nbsp;&nbsp;&nbsp;&nbsp;' . $g['title'] . '</option>';

                    }

                }

            }
        }
        return $html;
    }

    //无限极分类方法
    public function getTree($data, $pId)
    {
        $tree = '';
        foreach($data as $k => $v) {
            if($v['parent_id'] == $pId) {         //父级找到子级
                $v['parent_id'] = $this->getTree($data, $v['id']);
                $r = ProductDocument::where('group_id',$v['id'])->orderBy('sort','asc')->get();
                if($r){
                    $r = $r->toArray();
                }
                $v['doc'] = empty($r) ? []:$r;
                $tree[] = $v;
            }
        }
        return $tree;
    }

}