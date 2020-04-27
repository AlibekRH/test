<?php

class AdminModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function GetSubSpecialityData($where = NULL) {
        $this->db->select('s.*, subspecility.specility_name as main_speciality_name');
        $this->db->from('specility as s');
        $this->db->join('specility as subspecility', 's.parent_id = subspecility.id');
        $this->db->where('s.parent_id !=', 0);
        if($where)
         $this->db->where($where);
        $query = $this->db->get();
        return $query->result();
    }

}
