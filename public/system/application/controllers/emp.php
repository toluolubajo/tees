<?php

class Emp extends CI_Controller {

    public $content = array('title' => '', 'leftNavbar' => '',
        'leftContent' => '', 'headLC' => '', 'rightTop' => '', 'rightBottom' => '');
    public $scores = array();
    public $reviewer_scores = array();
    public $graph_labels = array();
    public $max_head_val = array();
    public $maxLow = array(); //This variable is used to get the minimum and maximum heading for
    //both reviewer ans self into an array, to be displayed on the dashboard
    public $reviewScoreAb = array();
    public $selfScoreAb = array();
    public $qnaireData = array();

    function __construct() {
        parent::__construct();
        $this->load->helper('url_helper');
        $this->load->model('view_qnaire_model');
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));
        $this->load->library('table');
        $this->load->helpers('ofc2');
    }

    function select_reviewer_approver() {
        $data['reviewer'] = $this->view_qnaire_model->getReviewer_apprv('1001', 1);
        $data['approval'] = $this->view_qnaire_model->getReviewer_apprv('1001', 2);
        $this->form_validation->set_rules('reviewerName', 'Reviewer\'s name', 'callback_reviewerName_check');
        $this->form_validation->set_rules('approveName1', 'Approving Officer -1 ', 'callback_approveName_check');
        $this->form_validation->set_rules('approveName2', 'Approving Officer -2 ', 'callback_approveName_check');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('/emp/sel_rev_app', $data);
        }
    }

    function index() {
        if ($this->session->userdata('user_id') != NULL) {
            if ($this->session->userdata('isAdmin') == "NO") {
                $data['allQnaire'] = $this->view_qnaire_model->get_emp_qnaire($this->session->userdata('user_id'));
                $data['reviewQnaire'] = $this->view_qnaire_model->getReview_qnaire($this->session->userdata('user_id'));
                $data['approveQnaire'] = $this->view_qnaire_model->getApprove_qnaire($this->session->userdata('user_id'));
                $this->session->set_userdata('emp_name', $this->view_qnaire_model->get_empName($this->session->userdata('user_id')));
                $p = $this->uri->segment(3, 0);
                if ($p == null

                    )$p = 0;
                $this->toggleView($p);
                $data['content'] = $this->content;
                $this->load->view('emp_index', $data);
            }else {
                $this->authenticationError("Employee Login required");
            }
        } else {
            $this->authenticationError("Invalid Credentials");
        }
    }

    function checkData($data, $val) {
        for ($i = 0; $i < count($data); ++$i) {
            foreach ($data[$i] as $key => $value) {
                if ($val == $key) {
                    return $i;
                }
            }
        }return -1;
    }

    function dashboardSummary($empId) {
        $query = $this->db->query('SELECT suggestions, self_score,reviewerScore,qnaire_id,
            id FROM emp_qnaire WHERE emp_id=' . $empId);
        foreach ($query->result() as $row) {
            $query2 = $this->db->query('SELECT eqh_reviewer_score,eqh_self_score,qnaire_head_id, id
                FROM emp_qnairehead WHERE eq_id=' . $row->id);
        }
    }

    function getAssessment($score) {
       // echo "i am here $score<br>";
        $string = "";
        if ($score >= 90) {
            $string = "Significantly Exceeded Expectation";
        } elseif ($score >= 80) {
            $string = " Exceeded Expectation";
        } elseif ($score >= 65) {
            $string = "Met Expectation Plus";
        } elseif ($score >= 50) {
            $string = "Met Expectation";
        } elseif ($score >= 35) {
            $string = "Met Some Expectation";
        }
        else
            $string="Not Meet Expectation";

        return $string;
    }

    function getGraphData($empId) {
        $qnaireData = array(); //this array is used to store data to be displayed below the graph on emp dashboard
        $data = array();
        $reviewerData = array();
        $maxHeadFactor = array();
        $max_head_val = array();
        $query = $this->db->query('SELECT project_id,suggestions,qnaire_scomment ,self_score,reviewerScore,qnaire_id, id FROM emp_qnaire WHERE emp_id=' . $empId.
                " AND emp_qnaire_status > 1");
        foreach ($query->result() as $row) {
            $qnaireData[$row->id] = array();
            $query5 = $this->db->query('SELECT name FROM project WHERE id=' . $row->project_id);
            foreach ($query5->result() as $row5) {
                $projectName = $row5->name;
                break;
            }
            array_push($qnaireData[$row->id], $projectName);
            array_push($qnaireData[$row->id], $row->self_score);
            array_push($qnaireData[$row->id], $row->reviewerScore);
            array_push($qnaireData[$row->id], $row->suggestions);
            array_push($qnaireData[$row->id], $row->qnaire_scomment);
            $query2 = $this->db->query('SELECT eqh_reviewer_score,eqh_self_score,qnaire_head_id, id
                FROM emp_qnairehead WHERE eq_id=' . $row->id);
            $heading = array();
            foreach ($query2->result() as $row2) {
                $heading[$row2->id] = array();
                $query3 = $this->db->query('SELECT title FROM qnaire_head WHERE id=' . $row2->qnaire_head_id);
                $query4 = $this->db->query('SELECT EE FROM headingscores WHERE id=' . $row2->qnaire_head_id);
                array_push($heading[$row2->id], $row2->eqh_self_score);
                array_push($heading[$row2->id], $row2->eqh_reviewer_score);
                foreach ($query3->result() as $row3) {
                    $title = $row3->title;
                    break;
                }
                array_push($heading[$row2->id], $title);
                foreach ($query4->result() as $row4) {
                    $EE = $row4->EE;
                    break;
                }
                $ratioSelf = ($row2->eqh_self_score / $EE) * 100;
                $ratioReviewer = ($row2->eqh_reviewer_score / $EE) * 100;
                $ratioSelf = number_format($ratioSelf, 2, '.', '');
                $ratioReviewer = number_format($ratioReviewer, 2, '.', '');
                array_push($heading[$row2->id], $ratioSelf);
                array_push($heading[$row2->id], $ratioReviewer);
                if (isset($data[$title]) AND is_array($data[$title])) {
                    array_push($data[$title], $row2->eqh_self_score);
                    array_push($max_head_val [$title], $EE);
                } else {
                    $data[$title] = array();
                    $reviewerData[$title] = array();
                    $max_head_val[$title] = array();

                    array_push($data[$title], $row2->eqh_self_score);
                    array_push($reviewerData [$title], $row2->eqh_reviewer_score);
                    array_push($max_head_val [$title], $EE);
                }
            }
            array_push($qnaireData[$row->id], $heading);
        }
        $this->scores = $this->getSum($data);
        $this->reviewer_scores = $this->getSum($reviewerData);
        $this->graph_labels = $this->getHead($data);
        $this->max_head_val = $this->getSum($max_head_val);
        //print_r($qnaireData);
//        echo "<br>";
        $maxLow = array();
        foreach ($qnaireData as $key => $value) {
            $maxLow[$key] = $this->getMaxLow($value);
            //print_r($maxLow[$key]);echo "<br>";echo "<br>";
        }

//        echo "<br>";
//        echo "<br>";
//        echo "----------------------------- <br>";
        $this->qnaireData = $qnaireData;
        $this->maxLow = $maxLow;
        //print_r($maxLow);
        $selfScoreAb = array(); //questionnaire score abbrevation i.e EE, NME , SEE etc.
        $reviewScoreAb = array(); //questionnaire score abbrevation i.e EE, NME , SEE etc.
        foreach ($qnaireData as $key => $value) {
            $i = 0;
            foreach ($value as $key1 => $value1) {
                if ($i == 1) {
                    $reviewScoreAb[$key] = $this->getAssessment($value1);
                }
                if ($i == 2) {
                    $selfScoreAb[$key] = $this->getAssessment($value1);
                }
                ++$i;
            }
        }
        $this->reviewScoreAb = $reviewScoreAb;
        $this->selfScoreAb = $selfScoreAb;
        //print_r($reviewScoreAb);
        //print_r($selfScoreAb);
    }

    function getMaxLow($data) {
        $myData = array();
        $reviewerHead = array();
        $selfHead = array();
        $headID = array();
        $i = 0;
        foreach ($data as $key1 => $value1) {
            if ($i == 5) {///5 is the index of the heading details in the qnaireData array
                foreach ($value1 as $key2 => $value2) {
                    $j = 0;
                    array_push($headID, $key2);
                    foreach ($value2 as $key3 => $value3) {
                        if ($j == 3) { //3 is the index of the reviewer score the headings
                            // array in qnaireData array
                            array_push($selfHead, $value3);
                            
                        }
                        if ($j == 4) {
                            array_push($reviewerHead, $value3);
                            //4 is the index of
                            //the self score the headings array in qnaireData array
                        }
                        $j++;
                        $i = 0;
                    }
                }
            }
            $i++;
        }
        //print_r($selfHead);echo "<br>";echo "<br>";echo "<br>";
        //print_r($reviewerHead);
        $myData['max_self'] = $headID[array_search(max($selfHead), $selfHead)];
        $myData['min_self'] = $headID[array_search(min($selfHead), $selfHead)];
        $myData['max_reviewer'] = $headID[array_search(max($reviewerHead), $reviewerHead)];
        $myData['min_reviewer'] = $headID[array_search(min($reviewerHead), $reviewerHead)];

        return $myData;
        //echo $max_self." : ".$min_self." : ".$max_reviewer." : ".$min_reviewer;
    }

    function fixArray($data) {//input array is in this form
        ////Array ( [Core Technical] => Array ( [0] => 39 [1] => 19 [2] => 29 )
        //[Statistical Process Control know how] => Array ( [0] => 4 [1] => 4 )
        $mydata = array();
        foreach ($data as $key => $value) {
            array_push($mydata, $value);
        }
        return $mydata;
    }

    function getHead($data) {//input array is in this form
        ////Array ( [Core Technical] => Array ( [0] => 39 [1] => 19 [2] => 29 )
        //[Statistical Process Control know how] => Array ( [0] => 4 [1] => 4 )
        $mydata = array();
        foreach ($data as $key => $value) {
            array_push($mydata, $key);
        }
        return $mydata;
    }

    function getSum($data) {//input array is in this form
        ////Array ( [Core Technical] => Array ( [0] => 39 [1] => 19 [2] => 29 )
        //[Statistical Process Control know how] => Array ( [0] => 4 [1] => 4 )
        $mydata = array();
        foreach ($data as $key => $value) {
            array_push($mydata, array_sum($value));
        }
        return $mydata;
    }

    function dashboard() {
        //default is by organization, while there is an option for departmental view
        $this->content['rightTop'] = "notice";
        $myArrayMenu = $this->createTopNav(0);
        $this->content['leftNavbar'] = $myArrayMenu[0];
        $this->content['headLC'] = $myArrayMenu[1];
        $this->content['leftContent'] = "dashboard";
        $this->content['title'] = $this->content['headLC'];
        if ($this->session->userdata('user_id') == "") {
            $this->logout();
            return;
        }
        $this->getGraphData($this->session->userdata('user_id'));        //Graph 1 Data
        $this->content['barData'] = $this->get_data_bar("Consolidated Performance",
                        $this->scores, $this->reviewer_scores, $this->graph_labels, $this->max_head_val);
        $data['maxLow']=$this->maxLow; //This variable is used to get the minimum and maximum heading for
        //both reviewer ans self into an array, to be displayed on the dashboard
        $data['reviewScoreAb']=$this->reviewScoreAb;
        $data['selfScoreAb']=$this->selfScoreAb;
        $data['qnaireData']=$this->qnaireData;
        $data['content'] = $this->content;
        $this->load->view('emp_index', $data);
    }

    function logout() {
        $this->session->sess_destroy();
        session_unset();
        redirect('http://localhost/ci2/index.php/login/');
    }

    function authenticationError($message) {
        $data['heading'] = $message;
        $data['message'] = "Please Login to view this page, " . anchor('http://localhost/ci2/index.php/login/', 'Click here to login', 'title="Login"');
        $data['status_code'] = "500";
        show_error($data);
    }

    function my_qnaire() {
        $this->load->view('/emp/my_qnaire', $data);
    }

    ///$p for menu, $a for submenu
    function toggleView($p) {
        $this->content['rightTop'] = "notice";
        switch ($p) {
            case 1://My Questionnaire
                $myArrayMenu = $this->createTopNav(1);
                $this->content['leftNavbar'] = $myArrayMenu[0];
                $this->content['headLC'] = $myArrayMenu[1];
                $this->content['leftContent'] = "my_qnaire";
                break;
            case 2: //View
                $myArrayMenu = $this->createTopNav(2);
                $this->content['leftNavbar'] = $myArrayMenu[0];
                $this->content['headLC'] = $myArrayMenu[1];
                $this->content['leftContent'] = "view_qnaire";
                break;
            case 3: //Notice
                $myArrayMenu = $this->createTopNav(3);
                $this->content['leftNavbar'] = $myArrayMenu[0];
                $this->content['headLC'] = $myArrayMenu[1];
                $this->content['leftContent'] = "notice";
                break;
            default: //Dashboard
                redirect('emp/dashboard');
        }
        $this->content['title'] = $this->content['headLC'];
    }

    function createTopNav($current) {
        $a = base_url() . 'index.php/emp/index/';
        $nav = array("Dashboard", 'http://localhost/ci2/index.php/emp/dashboard/', "My Questionnaires", $a . '1', "View", $a . '2', "Notice", $a . '3');
        $topnav = "<ul>";
        $heading = "";
        $j = 1;
        for ($i = 0; $i < (sizeof($nav) / 2); $i++) {
            if ($i == $current) {
                $heading = $nav[($i + $j - 1)];
                $topnav.='<li>' . '<a href="' . $nav[($i + $j)] . '" style=" background-color:#AC4B41">' . $nav[($i + $j - 1)] . '</a> ' . '</li>';
            } else {
                $topnav.='<li>' . '<a href="' . $nav[($i + $j)] . '">' . $nav[($i + $j - 1)] . '</a> ' . '</li>';
            }
            $j++;
        }
        $topnav.="</ul>";
        return $myArray = array($topnav, $heading);
    }

    public function get_data_pie($title, $data, $label) {
        $title = new title($title);
        $pie = new pie();
        $pie->set_alpha(0.6);
        $pie->set_start_angle(35);
        $pie->add_animation(new pie_fade());
        $pie->set_tooltip('#val# of #total#<br>#percent# of 100%');
        $pie->set_colours(array('#ff0033', '#ffff33', '#66ff66'));
        $pie->set_values($data);
        $chart = new open_flash_chart();
        $chart->set_title($title);
        $chart->add_element($pie);
        $chart->set_bg_colour('#eeeeee');
        $chart->x_axis = null;

        return $chart->toPrettyString();
    }

    public function get_data_area($title, $data, $label) {
        $data = array();
        $chart = new open_flash_chart();
        $chart->set_title(new title($title));
        //
        // Make our area chart:
        //
        $area = new area();
        // set the circle line width:
        $area->set_width(2);
        $area->set_default_dot_style(new hollow_dot());
        $area->set_colour('#838A96');
        $area->set_fill_colour('#E01B49');
        $area->set_fill_alpha(0.4);
        $area->set_values($data);

        // add the area object to the chart:
        $chart->add_element($area);

        $y_axis = new y_axis();
        $y_axis->set_range(-2, 2, 2);
        $y_axis->labels = null;
        $y_axis->set_offset(false);

        $x_axis = new x_axis();
        $x_axis->labels = $data;
        $x_axis->set_steps(2);

        $x_labels = new x_axis_labels();
        $x_labels->set_steps(4);
        $x_labels->set_vertical();
        // Add the X Axis Labels to the X Axis
        $x_axis->set_labels($x_labels);

        $chart->add_y_axis($y_axis);
        $chart->x_axis = $x_axis;
        $chart->set_bg_colour('#eeeeee');

        return $chart->toPrettyString();
    }

    function convertArrayType($data, $token) {
        $data1 = array();
        if ($token == 1) {
            for ($i = 0; $i < count($data); $i++) {
                array_push($data1, (int) $data[$i]);
            }
        }
        if ($token == 2) {
            for ($i = 0; $i < count($data); $i++) {
                array_push($data1, $data[$i]);
            }
        }
        return $data1;
    }

    public function get_data_bar($title, $data, $reviewerData, $label, $maxData) {
        $title = new title($title);
        $bar = new bar();
        $bar2 = new bar();
        $bar->set_values($this->convertArrayType($data, 1));
        $bar2->set_values($this->convertArrayType($reviewerData, 1));
        $bar->colour('#AC4B41');
        $bar->set_key('Your Score', 10);

        $bar2->colour('#3F3F3F');
        $bar2->set_key('Reviewer Score', 10);

        $hol = new hollow_dot();
        $hol->size(3)->halo_size(1)->tooltip('#x_label#<br>#val#');
        $line_3 = new line();
        $line_3->set_key('Maximum Score', 10);
        $line_3->set_values($this->convertArrayType($maxData, 1));
        $line_3->set_default_dot_style($hol);
        $line_3->set_colour('#ff0000');
        $x = new x_axis();
        
        $x_labels = new x_axis_labels();
        // $x_labels->set_vertical();
        $x_labels->set_colour('#eeeeee');
        $x_labels->set_labels($this->convertArrayType($label, 2));
        $x->set_labels($x_labels);
        $x->grid_colour('#999999');

        $y_axis = new y_axis();
        if(count($maxData)>0)$rangeMax = max($maxData);
        else $rangeMax=50;
        $y_axis->set_range(0, ($rangeMax + 50), 50);
        $y_labels = new y_axis_labels();

        $y_axis->set_grid_colour('#999999');
        $y_axis->labels = null;
        $y_axis->set_labels($y_labels);
        $y_axis->set_offset(false);
        $chart = new open_flash_chart();
        $chart->set_bg_colour('#eeeeee');
        $chart->add_y_axis($y_axis);
        $chart->set_x_axis($x);
        $chart->set_title($title);
        $chart->add_element($bar);
        $chart->add_element($bar2);
        $chart->add_element($line_3);

        return $chart->toPrettyString();
    }

    function reviewerName_check($str) { ////validation function
        if ($str == '0') {
            $this->form_validation->set_message('reviewerName_check', 'Please select a reviewer');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function approveName_check($str) { ////validation function
        if ($str == '0') {
            $this->form_validation->set_message('approveName_check', 'Please select an approving officer');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

?>
