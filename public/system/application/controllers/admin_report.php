<?php

class Admin_report extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('table');
        $this->load->model('view_qnaire_model');
        $this->load->model('admin_report_model');
        $this->load->helper(array('form', 'url'));
        $this->load->helpers('ofc2');
    }

    public $graphData = array();
    public $headLabel = array();
    public $deptLabel = array();
    public $graphJSON;
    public $scores = array();
    public $reviewer_scores = array();
    public $qnaireData = array();
    public $graphLabel = array();
    public $content = array('title' => 'Administrator',
        'rightNavbar' => '',
        'leftNavbar' => '',
        'headCC' => '',
        'bottomCC1' => 'blank',
        'topRC' => '/admin/adminTopRC',
        'topLC' => '',
        'bottomRC' => '');

    function index() {
        $this->dashboard_org();
    }

    function ajax_report_dept() {
        $allData = array();
        $deptId = "";
        $qnaire = "";
        $data = array();
        $reviewerData = array();
        $emp_qnaireId = array();
        if (isset($_POST['deptId'])

            )$deptId = $_POST['deptId'];
        if (isset($_POST['qnaire'])

            )$qnaire = $_POST['qnaire'];
        if (isset($deptId)) {
            if (isset($qnaire)) {
                $query = $this->db->query('SELECT id FROM employee WHERE dept_id=' . $deptId);
                foreach ($query->result() as $row) {            //
                    for ($i = 0; $i < count($qnaire); ++$i) {
                        $query2 = $this->db->query('SELECT id FROM emp_qnaire WHERE emp_id=' . $row->id .
                                        " AND emp_qnaire_status='2' AND qnaire_id=" . $qnaire[$i]);
                        foreach ($query2->result() as $row2) {
                            array_push($emp_qnaireId, $row2->id);
                        }
                    }
                }
            }
        }
        $allData['emp_qnaireId'] = $emp_qnaireId;
        for ($i = 0; $i < count($emp_qnaireId); ++$i) {
            $query2 = $this->db->query('SELECT eqh_reviewer_score,eqh_self_score,qnaire_head_id, id
                FROM emp_qnairehead WHERE eq_id=' . $emp_qnaireId[$i]);
            foreach ($query2->result() as $row2) {
                if (isset($data[$row2->qnaire_head_id])) {
                    //echo "hi";
                    array_push($data[$row2->qnaire_head_id], $row2->eqh_self_score);
                    array_push($reviewerData[$row2->qnaire_head_id], $row2->eqh_reviewer_score);
                } else {
                    $data[$row2->qnaire_head_id] = array();
                    $reviewerData[$row2->qnaire_head_id] = array();
                    array_push($data[$row2->qnaire_head_id], $row2->eqh_self_score);
                    array_push($reviewerData[$row2->qnaire_head_id], $row2->eqh_reviewer_score);
                }
            }
        }
        $allData['selfScores'] = $data;
        $allData['reviewerData'] = $reviewerData;
        $title = array();
        $max_head_val = array();
        foreach ($data as $key => $value) {
            $query = $this->db->query('SELECT title FROM qnaire_head WHERE id=' . $key);
            foreach ($query->result() as $row) {
                array_push($title, $row->title);
                $query2 = $this->db->query('SELECT EE FROM headingscores WHERE id=' . $key); //
                foreach ($query2->result() as $row) {
                    foreach ($query2->result() as $row2) {
                        array_push($max_head_val, $row->EE);
                    }
                }
            }
        }
        //print_r($max_head_val);
        $allData['heading'] = $title;
        $allData['max_head_val'] = $max_head_val;
        ///find lowest and highest values
        $max_reviewer = $this->getMaxMin($reviewerData, 2);
        $min_reviewer = $this->getMaxMin($reviewerData, 1);

        $allData['min_reviewer'] = $min_reviewer;
        $allData['max_reviewer'] = $max_reviewer;

        $max_self = $this->getMaxMin($data, 2);
        $min_self = $this->getMaxMin($data, 1);

        $allData['min_self'] = $min_self;
        $allData['max_self'] = $max_self;

        //print_r($min_reviewer);
        $this->load->view('/ajax/report_dept', $allData);
    }

    function dashboard_org() { //default is by organization, while there is an option for departmental view
        $myArrayMenu = $this->createTopNav(0);
        $this->content['leftNavbar'] = $myArrayMenu[0];
        $myArraySubmenu = $this->createSubMenu(0, 0);
        $this->content['topLC'] = $myArraySubmenu[0];
        $this->content['headCC'] = $myArraySubmenu[1];
        $this->content['bottomRC'] = "blank";
        $this->content['bottomCC1'] = "dashboard_org";
        $this->content['title'] = "Dashboard :: Organization";
        $data['content'] = $this->content;
        $data['main_content'] = 'admin_index';
        $depId_array = $this->admin_report_model->getDeptId_of_empqnaire();
        //print_r($depId_array);echo "<br>";
        $deptGraphDataArray = array();
        foreach ($depId_array as $key => $value) {
            $deptGraphDataArray[$key] = $this->getHeadAvgByDept($key);
        }
        $this->graphLabel = $this->getLabel($deptGraphDataArray);
        $this->scores = $this->getGraphData2($deptGraphDataArray, 2);
        $this->reviewer_scores = $this->getGraphData2($deptGraphDataArray, 1);
        $data['graphJSON'] = $this->get_data_bar(1);/// 2 is for self scores
        
        $this->load->view('/ext/template', $data);
    }

    function dashboard_dept() { //default is by organization, while there is an option for departmental view
        $myArrayMenu = $this->createTopNav(0);
        $this->content['leftNavbar'] = $myArrayMenu[0];
        $myArraySubmenu = $this->createSubMenu(0, 2);
        $this->content['topLC'] = $myArraySubmenu[0];
        $this->content['headCC'] = $myArraySubmenu[1];
        $this->content['bottomRC'] = "blank";
        $this->content['bottomCC1'] = "dashboard_dept";
        $this->content['title'] = "Dashboard :: Department";
        $data['content'] = $this->content;
        $data['main_content'] = 'admin_index';
        $this->load->view('/ext/template', $data);
    }

    function getMaxMin($data, $token) { //1 is min, 2is max
        $temp = array();
        foreach ($data as $key => $value) {
            if ($token == 1

                )$temp[$key] = min($value);
            if ($token == 2

                )$temp[$key] = max($value);
        }
        return $temp;
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

    function authenticationError() {
        $data['heading'] = "Invalid Credentials";
        $data['message'] = "Please Login to view this page, " . anchor('http://localhost/ci2/index.php/login/', 'Click here to login', 'title="Login"');
        $data['status_code'] = "500";
        show_error($data);
    }

    function report_dept() {
        $myArrayMenu = $this->createTopNav(3);
        $this->content['leftNavbar'] = $myArrayMenu[0];
        $myArraySubmenu = $this->createSubMenu(3, 2);
        $this->content['topLC'] = $myArraySubmenu[0];
        $this->content['headCC'] = $myArraySubmenu[1];
        $this->content['bottomCC1'] = "report_dept";
        $data['content'] = $this->content;
        $data['main_content'] = 'admin_index';
        $this->load->view('/ext/template', $data);
    }

    function report_org() {
        $myArrayMenu = $this->createTopNav(3);
        $this->content['leftNavbar'] = $myArrayMenu[0];
        $myArraySubmenu = $this->createSubMenu(3, 0);
        $this->content['topLC'] = $myArraySubmenu[0];
        $this->content['headCC'] = $myArraySubmenu[1];
        $this->content['bottomCC1'] = "report_org";
        $data['content'] = $this->content;
        $data['main_content'] = 'admin_index';
        $this->form_validation->set_rules('qnaire', 'Questionnaire', 'required|callback_selected_check');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('/ext/template', $data);
        } else {
            $this->getGraphData($_POST['qnaire'][0]);
            $data['graphJSON'] = $this->graphJSON;
            $this->load->view('/ext/template', $data);
        }
    }

    function getGraphData($qId) {        
        $temp = $this->admin_report_model->getQnaire($qId); //get the employees with this questionnaire
        $deptLabel = array();
        $graphData = array();
        $headLabel = array();
        $temp2 = array();
        $numofQnaire = $this->admin_report_model->get_num_of_qnaire($temp); //get the number of questionnaire in each dept
        if (count($temp) > 0)
            $temp2 = $this->admin_report_model->getHeadScores($temp[1]); //get the scores of each heading
 foreach ($numofQnaire as $key => $value) {
            array_push($deptLabel, $this->admin_report_model->getDeptName($key));
        }
        if (count($temp2) > 0)
            foreach ($temp2 as $key => $value) {
                array_push($headLabel, $this->admin_report_model->getHeadName($key));
            }
        if (count($temp) > 0)
            foreach ($temp as $key => $value) {
                $graphData[$key] = array();
                $temp3 = $this->admin_report_model->getHeadScores($temp[$key]);
                foreach ($temp3 as $key1 => $value1) {
                    array_push($graphData[$key], array_sum($value1) / $numofQnaire[$key]);
                }
            }
        $this->graphData = $graphData;
        $this->headLabel = $headLabel;
        $this->deptLabel = $deptLabel;
        $title = "Performance Chart";
        $this->graphJSON = $this->get_data_line($qId . ". " . $this->admin_report_model->getQnaireName($qId),
                        $deptLabel, $graphData, $headLabel);
    }

    public function get_data_line($heading, $title, $data, $label) {
        $heading = new title($heading);
        $chart = new open_flash_chart();
        $i = 0;
        foreach ($data as $key => $value) {
            $hol = new hollow_dot();
            $hol->size(3)->halo_size(1)->tooltip('#x_label#<br>#val#');
            $line_3 = new line();
            $line_3->set_key($title[$i], 10); //title==dept label
            $line_3->set_values($this->convertArrayType($value, 1));
            $line_3->set_default_dot_style($hol);
            $line_3->set_colour($this->generateRandomColor());
            $chart->add_element($line_3);

            $i++;
        }
        $x = new x_axis();
        $x_labels = new x_axis_labels();
        // $x_labels->set_vertical();
        $x_labels->set_colour('#eeeeee');
        $x_labels->set_labels($this->convertArrayType($label, 2));
        $x->set_labels($x_labels);
        $x->grid_colour('#999999');
        $y_axis = new y_axis();
        $y_axis->set_range(0, 100, 10);
        $y_labels = new y_axis_labels();
        $y_axis->set_grid_colour('#999999');
        $y_axis->labels = null;
        $y_axis->set_labels($y_labels);
        $y_axis->set_offset(false);
        $chart->add_y_axis($y_axis);
        $chart->set_x_axis($x);
        $chart->set_bg_colour('#eeeeee');
        $chart->set_title($heading);


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

    function selected_check($str) {//this is a form validation function
        if (count($str) == 0) {
            $this->form_validation->set_message('selected_check', 'Please select a questionnaire');
            return FALSE;
        } elseif (count($str) > 1) {
            $this->form_validation->set_message('selected_check', 'You cant select more than one questionnaire');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function createTopNav($current) {
        $a = base_url() . 'index.php/admin/index/';
        $nav = array("Dashboard", base_url() . "index.php/admin_report/dashboard_org/", "Create", $a . "1/0", "Manage", $a . "2/0", "Report", base_url() . "index.php/admin_report/report_org/", "Notice", $a . "4/0");
        $topnav = "<ul>";
        $heading = "";
        $j = 1;
        for ($i = 0; $i < (sizeof($nav) / 2); $i++) {
            if ($i == $current) {
                $heading = $nav[($i + $j)];
                $topnav.='<li>' . '<a href="' . $nav[($i + $j)] . '" style=" background-color:#AC4B41">' . $nav[($i + $j - 1)] . '</a> ' . '</li>';
            } else {
                $topnav.='<li>' . '<a href="' . $nav[($i + $j)] . '">' . $nav[($i + $j - 1)] . '</a> ' . '</li>';
            }
            $j++;
        }
        $topnav.="</ul>";
        return $myArray = array($topnav, $heading);
    }

    function viewAllQnaire() {
        $this->table->set_heading(array('#', 'id', 'Title', 'Date Created'));
        $results = $this->view_qnaire_model->get_all_qnaire();
        $i = 1;
        foreach ($results as $row) {
            $this->table->add_row(array($i, $row['id'], $row['title'], $row['date_created']));
        }
        echo $this->table->generate();
        echo "<script type=\"text/javascript\" charset=\"utf-8\">
    $('tr:odd').css('background','#e3e3e3');
    </script>";
    }

    function createSubMenu($menu, $submenu) {
        $leftnav = "";
        $subHeading = "";
        $a = base_url() . 'index.php/admin/index/';
        $nav = array(array("By Organization", base_url() . "index.php/admin_report/dashboard_org/",
                "By Department", base_url() . "index.php/admin_report/dashboard_dept/"),
            array("Add Title & Number of Sub-Headings", "$a" . "1/0", "Add Heading Title", "$a" . "1/1",
                "Assign Weight", "$a" . "1/1",
                "Add Number of Question", "$a" . "1/3", "Add Question(s)", "$a" . "1/4",
                "Preview", "$a" . "1/5", "Save and Exit", "$a" . "1/6"), array("View", "$a" . "2/0",
                "Edit", "$a" . "2/1", "Add Project", "http://localhost/ci2/index.php/admin/addProject",),
            array("By Organization", base_url() . "index.php/admin_report/report_org/",
                "By Department", base_url() . "index.php/admin_report/report_dept/"),
            array("Recent Activities", "", "Messages", ""));
        $leftnav.="<ul>";
        for ($i = 0; $i < sizeof($nav); $i++) {
            if ($i == $menu) {
                for ($j = 0; $j < sizeof($nav[$i]); $j++) {
                    if ($j == $submenu) {
                        if ($j == (sizeof($nav[$i]) - 2)) {
                            $leftnav.='<li id="lastLeftNav" class="current">' . '<a href="' . $nav[$i][++$j] . '" style=" background-color:#AC4B41">' . $nav[$i][--$j] . '</a> ' . '</li> ';
                        } else {
                            $leftnav.='<li class="current">' . '<a href="' . $nav[$i][++$j] . '" style=" background-color:#AC4B41">' . $nav[$i][--$j] . '</a> ' . '</li> ';
                        }
                        $subHeading = $nav[$i][$j];
                    } else {
                        if ($j == (sizeof($nav[$i]) - 2)) {

                            $leftnav.='<li id="lastLeftNav"  class="">' . '<a href="' . $nav[$i][++$j] . '">' . $nav[$i][--$j] . '</a> ' . '</li> ';
                        } else {
                            $leftnav.='<li class="">' . '<a href="' . $nav[$i][++$j] . '">' . $nav[$i][--$j] . '</a> ' . '</li> ';
                        }
                    }++$j;
                }
            }
        }
        $leftnav.="</ul>";
        return $myArray = array($leftnav, $subHeading);
    }

    function fixArray($data, $token) { ///input to the graph
        ///the input to this function is in this form array(0=>array(1==>2),1=>array(4=>5))
        $myData = array();
        for ($i = 0; $i < count($data); $i++) {
            foreach ($data[$i] as $key => $value) {
                if ($token == 1

                    )array_push($myData, $key);
                if ($token == 2

                    )array_push($myData, $value);
            }
        }
        return $myData;
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

    function getGraphData2($array, $token) { // token 1 is for self while 2 is for reviewer
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
         $x_labels->set_vertical();
        $x_labels->set_colour('#000000');
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
            //$bar->set_tooltip('#x_label#<br>#val#');
//
//            $hol = new hollow_dot();
//            $hol->size(3)->halo_size(0)->tooltip('#x_label#<br>#val#');
//            $line_3 = new line();
//            $line_3->set_values($this->convertArrayType($value, 1));
//            $line_3->set_default_dot_style($hol);
//            $line_3->set_colour('');
//            $chart->add_element($line_3);

//            $tooltip = new tooltip();
//            $tooltip->set_hover();
//            $tooltip->set_stroke(1);
//            $tooltip->set_colour("#000000");
//            $tooltip->set_background_colour("#000000");
//            $chart->set_tooltip($tooltip);
            $chart->add_element($bar);
            $chart->add_y_axis($y_axis);
            $chart->set_x_axis($x);
        }

        $chart->set_bg_colour('#ffffff');


        $chart->set_title($title);
        return $chart->toPrettyString();
    }

}

?>
