<?php

class Custom extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function admin_login($email, $password) {
        $this->db->where('email', $email);
        $this->db->where('password', $password);

        $q = $this->db->get('admin');

        if ($q->num_rows() > 0) {
            $data = $q->result_object();
            $result = $data[0];
        } else {
            $result = false;
        }
        return $result;
    }

    public function other_login($email, $password) {
        $pwd = md5($password);
        $this->db->where('email', $email);
        $this->db->where('password', $pwd);

        $q = $this->db->get('users');

        if ($q->num_rows() > 0) {
            $data = $q->result_object();
            $result = $data[0];
        } else {
            $result = false;
        }
        return $result;
    }

    public function query($query) {
        $q = $this->db->query($query);
        if ($q->num_rows() > 0) {
            $data = $q->result_object();
            $result = $data;
        } else {
            $result = false;
        }
        return $result;
    }    

    public function insert_data($tablename, $insert_data) {
        //$this->db->db_debug = FALSE;

        if ($this->db->insert($tablename, $insert_data)) {
            $result = $this->db->insert_id();
        } else {
            $result = false;
        }
        return $result;
    }

    public function update($tablename, $update_data, $key, $value) {
        //$this->db->db_debug = FALSE;

        if (!empty($key) && !empty($value)) {
            $this->db->where($key, $value);
        }

        if ($this->db->update($tablename, $update_data)) {
            $result = TRUE;
        } else {
            $result = false;
        }
        return $result;
    }

    public function update_where($tablename, $update_data, $where) {
        $this->db->db_debug = FALSE;

        if (!empty($where)) {
            $this->db->where($where);
        }

        if ($this->db->update($tablename, $update_data)) {
            $result = TRUE;
        } else {
            $result = false;
        }
        return $result;
    }

    public function delete_query($query) {
        $q = $this->db->query($query);
        if ($q) {
            $result = TRUE;
        } else {
            $result = false;
        }
        return $result;
    }

    public function delete_where($tablename, $where) {
        if ($where) {
            $this->db->where($where);
        }

        if ($this->db->delete($tablename)) {
            return true;
        }

        return false;
    }    

    public function get_data($table, $key = '', $val = '') {
        if (!empty($key) && !empty($val)) {
            $this->db->where($key, $val);
        }
        $q = $this->db->get($table);
        if ($q->num_rows() > 0) {
            $datas = $q->result_object();
            $result = $datas;
        } else {
            $result = false;
        }
        return $result;
    }

    public function get_where($tablename, $where = '') {
        if (!empty($where)) {
            $this->db->where($where);
        }

        $q = $this->db->get($tablename);

        if ($q->num_rows() > 0) {
            $result = $q->result_object();
        } else {
            $result = false;
        }
        return $result;
    }
    
    public function GetCitiesForCountry($name){
        $this->db->select('cities.name');
        $this->db->from('countries');
        $this->db->join('states', 'countries.id = states.country_id');
        $this->db->join('cities', 'states.id = cities.state_id');
        $this->db->where('countries.sortname', $name);
        $query = $this->db->get();
        //echo $this->db->last_query();die();
        return $query->result();
    }

}
