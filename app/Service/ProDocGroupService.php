<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/19
 * Time: 15:42
 */
namespace  App\Service;

use App\Models\System\ProductDocumentGroup;

class ProDocGroupService{


    public function getProDocGroupId($parentID,$group_id=[])
    {
        $group = ProductDocumentGroup::select('id')
            ->where('parent_id', $parentID)
            ->get();
        if( !$group){
            return $group_id;
        }

        foreach ( $group as &$g){
            array_push($group_id, $g['id']);
            $group_id = $this -> getProDocGroupId($g['id'],$group_id);
        }
          return $group_id;
    }


    public function getProDocGroup($parent_id)
    {
        $group = ProductDocumentGroup::select('id', 'title', 'parent_id')
            ->where('parent_id', $parent_id)
            ->orderBy('sort', 'desc')
            ->get();

        if( !$group ){
            return $group;
        }

        $group = $group->toArray();

        foreach ( $group as &$g){
            $g['sub'] = $this->getProdocGroup($g['id']);
        }

        return $group;
    }


    public function getProDocGroupHtml( $group, $level = 0, $choose_id = 0 )
    {
        $html = '';
        $level++;

        if ($level == 1) {
            $html .= '<div class="list-group">';
        }

        foreach ( $group as $g){
            $ml = $level * 15;
            if( $choose_id == $g['id']){
                $html .= '<a href="javascript:;" class="list-group-item active" style="padding-left: ' . $ml . 'px;" data-id="' . $g['id'] . '">' . $g['title'] . '</a>';
            }else{
                $html .= '<a href="javascript:;" class="list-group-item" style="padding-left: ' . $ml . 'px;" data-id="' . $g['id'] . '">' . $g['title'] . '</a>';
            }
            if (!empty($g['sub'])) {
                $html .= $this -> getProDocGroupHtml($g['sub'],$level,$choose_id);
            }
        }

        if ($level == 1) {
            $html .= '</div>';
        }
        return $html;
    }


    public function getProDocGroupSelectHtml($group, $level = -1)
    {
        $html = '';
        $level++;

        foreach ( $group as $g){
            if ($level == 0) {
                $html .= '<option value="' . $g['id'] . '">' . $g['title'] . '</option>';
            }else {
                $ml = '';
                for($i = 1; $i <= $level*4; $i++) {
                    $ml .= '&nbsp;';
                }
                $html .= '<option value="' . $g['id'] . '">' . $ml . $g['title'] . '</option>';
            }
            if (!empty($g['sub'])) {
                $html .= $this->getProDocGroupSelectHtml($g['sub'], $level);
            }
        }
        return $html;

   }
}