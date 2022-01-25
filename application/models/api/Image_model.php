<?php

class Image_model extends CI_Model {

public function getData() {
    $this->db->select("*");
    $this->db->from("images_upload");
    $query = $this->db->get();
    return $query->result();
}


public function insertData($tableName,$postData) {
    // echo "<pre>"; print_r($postData); die;
    $this->db->insert($tableName,$postData);
    $insert_id = $this->db->insert_id();
// echo $insert_id; die;
    $this->db->select("*");
    $this->db->from($tableName);
    $this->db->where('id',$insert_id);
    $query = $this->db->get();
    return $query->result();

}

}
?>