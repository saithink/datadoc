<?php
namespace saithink\datadoc\controller;

use think\facade\Db;
use think\facade\View;

class Index
{
    /**
     * 生成数据库字典html
     * 可直接另存为再copy到word文档中使用
     *
     * @return mixed
     */
    public function index()
    {
        $whiteList = config('datadoc.white_list');
        $tables = Db::query('SHOW TABLE STATUS');
        $table_list = array_map('array_change_key_case', $tables);
        $table_data = [];
        foreach ($table_list as $item) {
            $table_name = $item['name'];
            $table_fields = $this->showColumns($table_name);
            foreach ($table_fields as &$fieldItem) {
                $fieldItem['comment'] = $this->getDbColumnComment($table_name, $fieldItem['name']);
            }
            // 过滤白名单
            if (in_array($item['name'], $whiteList)) {
                continue;
            }
            $table_data[] = [
                'table_name' => $item['name'],
                'table_comment' => $item['comment'],
                'table_fields' => $table_fields
            ];
        }
        $viewPath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
        View::config(['view_path' => $viewPath]);
        View::assign('app', config('datadoc.app_name') . ' ' . config('datadoc.app_ver'));
        View::assign('table_data', $table_data);
        return View::fetch('index/index');
    }

    /**
     * 显示表结构信息
     *
     * @param string $table
     * @return array
     */
    private function showColumns($table){

        $sql = 'SHOW COLUMNS FROM `'.$table.'`';
        $result = Db::query($sql);
        if ($result === false) return array();
        $array = array();
        if (!empty($result)) {
            foreach ($result as $k=>$v) {
                $array[$v['Field']] = [
                    'name'    => $v['Field'],
                    'type'    => $v['Type'],
                    'null'       => $v['Null'],
                    'default' => $v['Default'],
                    'primary' => (strtolower($v['Key']) == 'pri'),
                    'autoinc' => (strtolower($v['Extra']) == 'auto_increment'),
                ];
            }
        }
        return $array;
    }

    /**
     * 获取数据库字段注释
     *
     * @param string $table_name 数据表名称(必须，不含前缀)
     * @param string|boolean $field 字段名称(默认获取全部字段,单个字段请输入字段名称)
     * @param string $table_schema 数据库名称(可选)
     * @return string
     */
    private function getDbColumnComment($table_name = '', $field = true, $table_schema = ''){
        
        $config = Db::getConfig();
        $database = $config['connections'][$config['default']];

        $table_schema = empty($table_schema) ? $database['database'] : $table_schema;
        $table_name = $table_name;

        // 处理参数
        $param = [
            $table_name,
            $table_schema
        ];

        // 字段
        $columnName = '';
        if($field !== true){
            $param[] = $field;
            $columnName = "AND COLUMN_NAME = ?";
        }

        // 查询结果
        $result = Db::query("SELECT COLUMN_NAME as field,column_comment as comment FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = ? AND table_schema = ? $columnName", $param);
        if(empty($result) && $field !== true){
            return $table_name . '表' . $field . '字段不存在';
        }

        // 处理结果
        foreach($result as $k => $v){
            $data[$v['field']] = $v['comment'];
            if(strpos($v['comment'], '#*#') !== false){
                $tmpArr = explode('#*#', $v['comment']);
                $data[$v['field']] = json_decode(end($tmpArr), true);
            }
        }
        // 字段注释格式不正确
        if(empty($data)){
            return $table_name . '表' . $field . '字段注释格式不正确';
        }
        return count($data) == 1 ? reset($data) : $data;
    }
}
