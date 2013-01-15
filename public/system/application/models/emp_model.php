<?php

class Emp_model extends CI_Model {

    function get_qnaire_by_id($id, $token) {
        //token 0 is yet to be filled qnaire, 1= filled qnaire, 2=reviewed qnaire, 3=approved1 qnaire
        //4=approved 2 qnaire, 5=submitted qnaire
        $qnaire_id = array();
        $rows = 0;
        $query = $this->db->query('SELECT qnaire_id FROM emp_qnaire WHERE id=' . $id . ' AND emp_qnaire_status=' . $token);
        $rows = $query->num_rows();
        foreach ($query->result() as $row) {
            array_push($qnaire, $row->id);
        }
        return $qnaire_id;
    }

    function get_headings($qnaireId) {
        
    }
    function get_question_answers(){
        
    }
    function get_questions($qnaire_id,$heading_id){
        
    }

}

?>
