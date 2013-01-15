<?php

class TestController extends CI_Controller {

    public $content = array('title' => '', 'leftNavbar' => '',
        'leftContent' => '', 'headLC' => '', 'rightTop' => '', 'rightBottom' => '');
    public $scores = array();
    public $reviewer_scores = array();
    public $qnaireData = array();
    public $graphLabel = array();

    function __construct() {
        parent::__construct();
        $this->load->helper('url_helper');
        $this->load->model('view_qnaire_model');
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));
        $this->load->library('table');
        $this->load->helpers('ofc2');
        $this->load->model('admin_report_model');
    }

    function index() {
        $depId_array = $this->admin_report_model->getDeptId_of_empqnaire();
        //print_r($depId_array);echo "<br>";
        $deptGraphDataArray = array();
        foreach ($depId_array as $key => $value) {
            $deptGraphDataArray[$key] = $this->getHeadAvgByDept($key);
        }
        $this->graphLabel = $this->getLabel($deptGraphDataArray);
        $this->scores = $this->getGraphData($deptGraphDataArray, 2);
        $this->reviewer_scores = $this->getGraphData($deptGraphDataArray, 1);
        $data['graphJSON'] = $this->get_data_bar(1);
        $this->load->view('testView', $data);
    }

    function getHeadAvgByDept($deptId) {
        $self = array();
        $reviewer = array();
        $i = 0;
        $query = $this->db->query('SELECT id FROM employee WHERE dept_id=' . $deptId);
        foreach ($query->result() as $row) {
            $i++;
//            echo $row->id;
//            echo
            $temp = $this->getEmpAverage($row->id);
            foreach ($temp['self'] as $key => $value) { ///
                if (isset($self[$key]) AND is_array($self[$key])) {
                    array_push($self[$key], $value);
                } else {
                    $self[$key] = array();
                    array_push($self[$key], $value);
                }
            }
            foreach ($temp['reviewer'] as $key => $value) { ///
                if (isset($reviewer[$key]) AND is_array($reviewer[$key])) {
                    array_push($reviewer[$key], $value);
                } else {
                    $reviewer[$key] = array();
                    array_push($reviewer[$key], $value);
                }
            }
        }
        $self = $this->groupIdbyTitle($self);
        $reviewer = $this->groupIdbyTitle($reviewer);
        return array($self, $reviewer);
    }

    function groupIdbyTitle($ungrouped) {
        $data = array();
        foreach ($ungrouped as $key => $value) {
            $query = $this->db->query('SELECT title FROM qnaire_head WHERE id=' . $key);
            foreach ($query->result() as $row) {
                foreach ($value as $key1 => $value1) {
                    if (isset($data[$row->title]) AND is_array($data[$row->title])) {
                        array_push($data[$row->title], $value1);
                    } else {
                        $data[$row->title] = array();
                        array_push($data[$row->title], $value1);
                    }
                    //echo "title is".$row->title."<br>";
                }
            }
        }
        foreach ($data as $key => $value) {
            $data[$key] = (array_sum($value) / count($value));
        }

        return $data;
    }

    function getEmpAverage($empId) {
        $data = array();
        $selfScore = array();
        $reviewerScore = array();
        $query = $this->db->query('SELECT id FROM emp_qnaire WHERE emp_id=' . $empId . " AND emp_qnaire_status >= 2");
        foreach ($query->result() as $row) {
            $query2 = $this->db->query('SELECT id, qnaire_head_id, eqh_self_score,eqh_reviewer_score
               FROM emp_qnairehead WHERE eq_id=' . $row->id);
            foreach ($query2->result() as $row2) {
                if (isset($selfScore[$row2->qnaire_head_id]) AND is_array($selfScore[$row2->qnaire_head_id])) {
                    array_push($selfScore[$row2->qnaire_head_id], $row2->eqh_self_score);
                    array_push($reviewerScore[$row2->qnaire_head_id], $row2->eqh_reviewer_score);
                } else {
                    $selfScore[$row2->qnaire_head_id] = array();
                    $reviewerScore[$row2->qnaire_head_id] = array();
                    array_push($selfScore[$row2->qnaire_head_id], $row2->eqh_self_score);
                    array_push($reviewerScore[$row2->qnaire_head_id], $row2->eqh_reviewer_score);
                }
            }
        }
        $selfScore = $this->normalize($selfScore);
        $reviewerScore = $this->normalize($reviewerScore);
        //print_r($selfScore);
//        echo "<br>";
//        print_r($reviewerScore);
//        echo "<br>";
//        echo "<br>";
        $data['reviewer'] = $reviewerScore;
        $data['self'] = $selfScore;
        return $data;
    }

    function normalize($rawData) {///this function normalize each scores to percentage, in the ratio of the value
        //to the maximum achievable scores.
        //input array format  is :  Array ( [122] => Array ( [0] => 39 [1] => 19 )
        //[123] => Array ( [0] => 4 [1] => 4 )
        $normalized = array();
        $maxVal = 0;
        foreach ($rawData as $key => $value) {
            $query = $this->db->query('SELECT score FROM qnaire_head WHERE id=' . $key);
            foreach ($query->result() as $row) {
                $maxVal = $row->score;
                break;
            }
            $normalized[$key] = ((array_sum($value) / (count($value) * $maxVal)) * 100);
        }
        return $normalized;
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

    function getGraphData($array, $token) { // token 1 is for self while 2 is for reviewer        
        $data = array();
        foreach ($array as $key => $value) {//self            
            for ($i = 0; $i < count($this->graphLabel); ++$i) {
                $data[$key][$i] = 0;
            }
            if ($token == 1) {
                foreach ($value[0] as $key1 => $value1) {
                    $index = array_search($key1, $this->graphLabel);
                    $data[$key][$index] = $value1;
                }
            }
            if ($token == 2) { //reviewer
                foreach ($value[1] as $key1 => $value1) {
                    $index = array_search($key1, $this->graphLabel);
                    //print_r($this->graphLabel);echo "<br>";echo "<br>";
                    //echo "index is ".$index;echo "<br>";
                    $data[$key][$index] = $value1;
                }
            }
        }

        return $data;
    }

    function generateRandomColor() {
        $randomcolor = '#' . strtoupper(dechex(rand(0, 10000000)));
        if (strlen($randomcolor) != 7) {
            $randomcolor = str_pad($randomcolor, 10, '0', STR_PAD_RIGHT);
            $randomcolor = substr($randomcolor, 0, 7);
        }
        return $randomcolor;
    }

    function getLabel($data) {
        $label = array();
        foreach ($data as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    if (!in_array($key2, $label)

                        )array_push($label, $key2);
                }
            }
        }
        return $label;
    }

    function dummy($data) {
        foreach ($data as $key => $value) {
            echo "<br>";
            echo $this->getGraphData($value[0], 2);
            echo "<br>";
        }
    }

    function get_data_bar($token) {//token is used to determine the graph to draw btw reviewer
        //and self scores
        $chart = new open_flash_chart();
        if ($token == 1) {
            $data = $this->reviewer_scores;
            $title = new title("Organizational Performance - Reviewed");
        }
        if ($token == 2) {
            $data = $this->scores;
            $title = new title("Organizational Performance - Self");
        }

        $x = new x_axis();
        $x_labels = new x_axis_labels();
        // $x_labels->set_vertical();
        $x_labels->set_colour('#eeeeee');
        $x_labels->set_labels($this->convertArrayType($this->graphLabel, 2));
        $x->set_labels($x_labels);
        $x->grid_colour('#999999');
        $y_axis = new y_axis();
        $y_axis->set_range(0, 100, 10);
        $y_labels = new y_axis_labels();
        $y_axis->set_grid_colour('#999999');
        $y_axis->labels = null;
        $y_axis->set_labels($y_labels);
        $y_axis->set_offset(false);
        foreach ($data as $key => $value) {
            $bar = new bar();
            $bar->set_values($this->convertArrayType($value, 1));
            $bar->set_colour($this->generateRandomColor());
            $bar->set_key($this->admin_report_model->getDeptName($key), 10);
            $bar->set_tooltip('#x_label#<br>#val#');

            $hol = new hollow_dot();
            $hol->size(3)->halo_size(0)->tooltip('#x_label#<br>#val#');
            $line_3 = new line();            
            $line_3->set_values($this->convertArrayType($value, 1));
            $line_3->set_default_dot_style($hol);
            $line_3->set_colour('');
            $chart->add_element($line_3);

            $tooltip = new tooltip();
            $tooltip->set_hover();
            $tooltip->set_stroke(1);
            $tooltip->set_colour("#000000");
            $tooltip->set_background_colour("#ffffff");
            $chart->set_tooltip($tooltip);
            $chart->add_element($bar);
            $chart->add_y_axis($y_axis);
            $chart->set_x_axis($x);
        }

        $chart->set_bg_colour('#eeeeee');


        $chart->set_title($title);
        return $chart->toPrettyString();
    }

}

?>
