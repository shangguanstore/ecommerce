<?php
namespace http\base\models;
use base\Model;

class BaseModel extends Model {

    /**
     * 查询一条数据
     * @return array
     */
    public function one() {
        $field = $this->options['field'];
        $data = $this->find();
        return isset($data[$field]) ? $data[$field] : array();
    }
}