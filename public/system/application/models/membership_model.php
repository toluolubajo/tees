<?php

class Membership_model extends CI_Model {

    function validate() {
        $user = array();        
        if ($this->input->post('username')!="" AND $this->input->post('password')!="") {
            $this->db->where('username', $this->input->post('username'));
            $this->db->where('password', md5($this->input->post('password')));
            $query = $this->db->get('passwords');
            if ($query->num_rows == 1) {
                foreach ($query->result() as $row) {
                    $user['id'] = $row->empid;
                    $user['isAdmin'] = $row->isAdmin;
                    $user['isReviewer'] = $row->isReviewer;
                    $user['isApprover'] = $row->isApprover;
                }
            }
        }

        return $user;
    }

    function create_member() {
        $new_member_insert_data = array(
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'email_address' => $this->input->post('email_address'),
            'username' => $this->input->post('username'),
            'password' => md5($this->input->post('password'))
        );
        $insert = $this->db->insert('membership', $new_member_insert_data);
        return $insert;
    }

}

?>
