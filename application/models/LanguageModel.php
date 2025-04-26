<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LanguageModel extends CI_Model {

    public function update($table_name, $where, $data){
        $this->db->where($where);
        return $this->db->update($table_name, $data);
        // echo $this->db->last_query(); die;
    }

    public function getdata($table_name, $where, $order_by){
        $this->db->from($table_name);
        $this->db->where($where);
        $this->db->order_by($order_by, "ASC");
        return $this->db->get();
        // echo $this->db->last_query(); die;
    }

}