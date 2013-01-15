<?php

class Admin_report_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getDeptId_of_empqnaire() {
        $data = array();
        $query = $this->db->query('SELECT DISTINCT emp_id FROM emp_qnaire WHERE emp_qnaire_status>=2');        
        foreach ($query->result() as $row) {
            $query2 = $this->db->query('SELECT dept_id FROM employee WHERE id=' . $row->emp_id);            
            foreach ($query2->result() as $row2) {                
                if (!in_array($row2->dept_id,$data)){                    
                    array_push($data, $row2->dept_id);
                }
            }
        } 
        $data2 = array();
        foreach ($data as $key => $value) {
            $query = $this->db->query('SELECT name FROM dept WHERE id=' . $value);
            foreach ($query->result() as $row) {                
                $data2[$value] = $row->name;
            }            
        }         
            return $data2;
    }

    function getQnaire($id) {
        $employees = array();
        $query = $this->db->query('SELECT emp_id, id FROM emp_qnaire WHERE qnaire_id=' . $id);
        foreach ($query->result() as $row) {
            if (isset($employees[$row->emp_id]) AND is_array($employees[$row->emp_id]))
                array_push($employees[$row->emp_id], $row->id);
            else {
                $employees[$row->emp_id] = array();
                array_push($employees[$row->emp_id], $row->id);
            }
        }
        $dept = array();
        foreach ($employees as $key => $value) {
            $query2 = $this->db->query('SELECT dept_id FROM employee WHERE id=' . $key);
            foreach ($query2->result() as $row) {
                if (isset($dept[$row->dept_id]) AND is_array($dept[$row->dept_id])

                    )array_push($dept[$row->dept_id], array($key => $value));
                else {
                    $dept[$row->dept_id] = array();
                    array_push($dept[$row->dept_id], array($key => $value));
                }
            }
        }
        // print_r($this->get_num_of_qnaire($dept));
        return $dept;
    }

    function getHeadScores($array) {///gets the headScores in each dept
        $revHeadScores = array();
        $selfHeadScores = array();
        foreach ($array as $key => $value) {
            foreach ($value as $key2 => $value2)//employee id is the key here
                foreach ($value2 as $key3 => $value3) {
                    $query2 = $this->db->query('SELECT qnaire_head_id, eqh_self_score, eqh_reviewer_score FROM emp_qnairehead WHERE eq_id=' . $value3);
                    foreach ($query2->result() as $row2) {
                        if (isset($revHeadScores[$row2->qnaire_head_id]) AND is_array($revHeadScores[$row2->qnaire_head_id])
                        ) {
                            array_push($revHeadScores[$row2->qnaire_head_id], $row2->eqh_self_score);
                            array_push($selfHeadScores[$row2->qnaire_head_id], $row2->eqh_reviewer_score);
                        } else {
                            $revHeadScores[$row2->qnaire_head_id] = array();
                            $selfHeadScores[$row2->qnaire_head_id] = array();
                            array_push($revHeadScores[$row2->qnaire_head_id], $row2->eqh_self_score);
                            array_push($selfHeadScores[$row2->qnaire_head_id], $row2->eqh_reviewer_score);
                        }
                    }
                }
        }
        return $revHeadScores;
    }

    function getQnaireName($qId) {
        $title = "";
        $query2 = $this->db->query('SELECT title FROM qnaire WHERE id=' . $qId);
        foreach ($query2->result() as $row) {
            $title = $row->title;
            break;
        }
        return $title;
    }

    function getHeadName($headId) {
        $title = "";
        $query2 = $this->db->query('SELECT title FROM qnaire_head WHERE id=' . $headId);
        foreach ($query2->result() as $row) {
            $title = $row->title;
            break;
        }
        return $title;
    }

    function getDeptName($deptId) {
        $name = "";
        $query2 = $this->db->query('SELECT name FROM dept WHERE id=' . $deptId);
        foreach ($query2->result() as $row) {
            $name = $row->name;
            break;
        }
        return $name;
    }

    function get_num_of_qnaire($array) { //array input is in this format, it counts the number of qnaire in each dept
        //Array ( [1] => Array ( [0] => Array ( [1001] => Array ( [0] => 22 [1] => 23 ) ) )
        //[2] => Array ( [0] => Array ( [1003] => Array ( [0] => 26 ) ) ) )
        $numOfQnaire = array();
        foreach ($array as $key => $value) {
            $numOfQnaire[$key] = 0;
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2)
                    foreach ($value2 as $key3 => $value3) {
                        $numOfQnaire[$key]++;
                    }
            }
        }
        return $numOfQnaire;
    }

    function getQnaireStatus($status) {
        $qty = 0;
        $query = $this->db->query('SELECT id FROM emp_qnaire WHERE emp_qnaire_status=' . $status);
        $qty = $query->num_rows();
        return $qty;
    }

    function getQnaireStatusById($status, $qnaireId) {
        $qty = 0;
        $query = $this->db->query('SELECT id FROM emp_qnaire WHERE emp_qnaire_status =' . $status . ' AND qnaire_id=' . $qnaireId);
        $qty = $query->num_rows();
        return $qty;
    }

    function getNumOfProjects($qnaireId) {
        $qty = 0;
        $query = $this->db->query('SELECT DISTINCT project_id FROM emp_qnaire WHERE qnaire_id=' . $qnaireId);
        $qty = $query->num_rows();
        return $qty;
    }

    function getNumOfEmployees($qnaireId) {
        $qty = 0;
        $query = $this->db->query('SELECT DISTINCT emp_id FROM emp_qnaire WHERE qnaire_id=' . $qnaireId);
        $qty = $query->num_rows();
        return $qty;
    }

}

?>
