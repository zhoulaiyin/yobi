<?php
// +----------------------------------------------------------------------
// | ebSIG
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.ebsig.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liudaojian <liudaojian@ebsig.com>
// +----------------------------------------------------------------------

/**
 * 部门服务类
 * @author   liudaojian <liudaojian@ebsig.com>
 * @version 1.0
 */

namespace App\Service;

use App\Models\System\Department;

class DepartmentService
{

    /**
     * 获取无限级部门id
     * @param int $parentID 上级部门id
     * @param array $department_id 部门id数组
     * @return array
     */
    public function getDepartmentsId($parentID, $department_id = []) {

        $department = Department::select('id')
            ->where('parent_id', $parentID)
            ->get();
        if (!$department) {
            return $department_id;
        }

        foreach ($department as &$d) {
            array_push($department_id, $d['id']);
            $department_id = $this->getDepartmentsId($d['id'], $department_id);

        }

        return $department_id;

    }

    /**
     * 获取无限级部门信息
     * @param int $parent_id 上级部门id
     * @return mixed
     */
    public function getDepartments($parent_id)
    {

        $department = Department::select('id', 'department_name', 'parent_id')
            ->where('parent_id', $parent_id)
            ->orderBy('sort', 'desc')
            ->get();

        if (!$department) {
            return $department;
        }
        $department = $department->toArray();

        foreach ($department as &$d) {
            $d['sub'] = $this->getDepartments($d['id']);
        }

        return $department;

    }

    /**
     * 生成部门列表html代码
     * @param array $department 部门数组
     * @param int $level 层级，默认为0
     * @param int $choose_id 选中的部门id，默认为0
     * @return string
     */
    public function getDepartmentsHtml($department, $level = 0, $choose_id = 0)
    {

        $html = '';
        $level++;

        if ($level == 1) {
            $html .= '<div class="list-group">';
        }

        foreach($department as $d) {
            $ml = $level * 15;
            if ($choose_id == $d['id']) {
                $html .= '<a href="javascript:;" class="list-group-item active" style="padding-left: ' . $ml . 'px;" data-id="' . $d['id'] . '">' . $d['department_name'] . '</a>';
            } else {
                $html .= '<a href="javascript:;" class="list-group-item" style="padding-left: ' . $ml . 'px;" data-id="' . $d['id'] . '">' . $d['department_name'] . '</a>';
            }

            if (!empty($d['sub'])) {
                $html .= $this->getDepartmentsHtml($d['sub'], $level, $choose_id);
            }
        }

        if ($level == 1) {
            $html .= '</div>';
        }

        return $html;

    }

    //  生成部门select的html代码
    public function getDepartmentsSelectHtml($department, $level = -1)
    {

        $html = '';
        $level++;

        foreach($department as $d) {
            if ($level == 0) {
                $html .= '<option value="' . $d['id'] . '">' . $d['department_name'] . '</option>';
            } else {
                $ml = '';
                for($i = 1; $i <= $level*4; $i++) {
                    $ml .= '&nbsp;';
                }
                $html .= '<option value="' . $d['id'] . '">' . $ml . $d['department_name'] . '</option>';
            }

            if (!empty($d['sub'])) {
                $html .= $this->getDepartmentsSelectHtml($d['sub'], $level);
            }
        }

        return $html;

    }

}