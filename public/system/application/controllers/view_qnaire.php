<?php

class View_qnaire extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');      
        $this->load->library('form_validation');
        $this->load->library('table');
        $this->load->model('view_qnaire_model');
        $this->load->helper(array('form', 'url'));
    }

    function index($emp_qnaireId) {
        $data=array();
        $questions=array();
        $data['emp_qnaire_data']=$this->view_qnaire_model->get_emp_qnaire_by_id($emp_qnaireId);        
        $data['headings']=$this->view_qnaire_model->get_qnaire_headings($data['emp_qnaire_data'][1]);
        $data['headScores']=$this->view_qnaire_model->getHeadScores($emp_qnaireId);
        foreach($data['headings'] as $row){
            array_push($questions,$this->view_qnaire_model->get_qnaire_questions($row->id));
        }
        $data['questions']=$questions;
        $this->load->view('view_qnaire',$data);
    }

    function emp() {

    }

    function reviewer() {

    }

    function approver() {
        
    }

}

?>
