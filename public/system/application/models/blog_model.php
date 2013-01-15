<?php

class Blog_model extends CI_Model{

    function getComments() {
        $myarray = array();
        $i = 0;
        do {
            ++$i;
            $this->db->where('entry_id', $i);
            $query = $this->db->get('comments');
            array_push($myarray, $query);
        } while ($query->num_rows() > 0);
        return $myarray;
    }

}

?>
