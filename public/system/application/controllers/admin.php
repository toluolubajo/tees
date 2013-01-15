<?php

class Admin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('table');
        $this->load->model('view_qnaire_model');
        $this->load->model('admin_report_model');
        $this->load->helper(array('form', 'url'));
    }

    public $content = array('title' => 'Administrator',
        'rightNavbar' => '',
        'leftNavbar' => '',
        'headCC' => '',
        'bottomCC1' => 'blank',
        'topRC' => '/admin/adminTopRC',
        'topLC' => '',
        'bottomRC' => '');

    function ajax_getKPI() {
        $this->load->view('/ajax/ajax_getKPI');
    }

    function addKPI() {
        $myArrayMenu = $this->createTopNav(2);
        $this->content['leftNavbar'] = $myArrayMenu[0];
        $myArraySubmenu = $this->createSubMenu(2, 6);
        $this->content['topLC'] = $myArraySubmenu[0];
        $this->content['title'] = "Manage::Add Project";
        $this->content['headCC'] = $myArraySubmenu[1];
        $this->content['bottomRC'] = "blank";
        $this->content['bottomCC1'] = "addKPI";
        $data['content'] = $this->content;
        $data['main_content'] = 'admin_index';
        $data['content'] = $this->content;
        $this->load->view('/ext/template', $data);
    }

    function addProject() {
        if ($this->session->userdata('user_id') != NULL) {
            if ($this->session->userdata('isAdmin') == "YES") {
                $this->form_validation->set_rules('employee[]', 'employee', 'required');
                $this->form_validation->set_rules('projectName', 'Project name', 'required');
                $this->form_validation->set_rules('projectDescrptn', 'Project Description', 'required');
                $this->form_validation->set_rules('questionnaire[]', 'Questionnaire', 'required');
                $myArrayMenu = $this->createTopNav(2);
                $this->content['leftNavbar'] = $myArrayMenu[0];
                $myArraySubmenu = $this->createSubMenu(2, 4);
                $this->content['topLC'] = $myArraySubmenu[0];
                $this->content['title'] = "Manage::Add Project";
                $this->content['headCC'] = $myArraySubmenu[1];
                $this->content['bottomRC'] = "blank";
                $this->content['bottomCC1'] = "addProject";
                $data['content'] = $this->content;
                $data['main_content'] = 'admin_index';
                if ($this->form_validation->run() == FALSE) {
                    $data['allDeptId'] = $this->view_qnaire_model->getDeptId();
                    $this->load->view('/ext/template', $data);
                } else {
                    //echo 'success';
                    //print_r($_POST);
                    $data['postVar'] = $_POST;
                    $this->content['bottomCC1'] = "success";
                    $data['content'] = $this->content;
                    $this->load->view('/ext/template', $data);
                }
            } else {
                $this->authenticationError("Administrator Login required");
            }
        } else {
            $this->authenticationError("Invalid Credentials");
        }
    }

    function getTeam($deptId) {
        $data['employee'] = $this->view_qnaire_model->getEmployee($deptId);
        // print_r($data);
        $this->load->view('getTeam_view', $data);
    }

    function viewProject() {
        //print_r($_POST);
    }

    function validate() {///this method validates recent_activities
        $this->form_validation->set_rules('qnaire', 'Questionnaire', 'required|callback_selected_check');
        $data=array();
        $qnaireReport=array();
        if (isset($_POST['qnaire'])) {
            $thisQnaire = $_POST['qnaire'];
            for ($i = 0; $i < count($thisQnaire); $i++) {
                $qnaireReport[$thisQnaire[$i]] = array();
                array_push($qnaireReport[$thisQnaire[$i]], $this->admin_report_model->getNumOfProjects($thisQnaire[$i]));
                array_push($qnaireReport[$thisQnaire[$i]], $this->admin_report_model->getNumOfEmployees($thisQnaire[$i]));
                array_push($qnaireReport[$thisQnaire[$i]], $this->admin_report_model->getQnaireStatusById(0, $thisQnaire[$i]));
                array_push($qnaireReport[$thisQnaire[$i]], $this->admin_report_model->getQnaireStatusById(1, $thisQnaire[$i]));
                array_push($qnaireReport[$thisQnaire[$i]], $this->admin_report_model->getQnaireStatusById(2, $thisQnaire[$i]));
                array_push($qnaireReport[$thisQnaire[$i]], $this->admin_report_model->getQnaireStatusById(3, $thisQnaire[$i]));
            }
        }
        $data['qnaireReport'] = $qnaireReport;
        $this->load->view('/ajax/recent_activities', $data);
    }

    function recent_activities() {
        $myArrayMenu = $this->createTopNav(4);
        $this->content['leftNavbar'] = $myArrayMenu[0];
        $myArraySubmenu = $this->createSubMenu(4, 0);
        $this->content['topLC'] = $myArraySubmenu[0];
        $this->content['title'] = "Notices :: Recent Activites";
        $this->content['headCC'] = $myArraySubmenu[1];
        $this->content['bottomRC'] = "blank";
        $this->content['bottomCC1'] = "activities";

        $data['unfilled'] = $this->admin_report_model->getQnaireStatus(0);
        $data['filled'] = $this->admin_report_model->getQnaireStatus(1);
        $data['reviewed'] = $this->admin_report_model->getQnaireStatus(2);
        $data['approved'] = $this->admin_report_model->getQnaireStatus(3);
        $data['content'] = $this->content;
        $data['main_content'] = 'admin_index';
        $data['content'] = $this->content;
        $this->load->view('/ext/template', $data);
    }

    function messages() {
        $myArrayMenu = $this->createTopNav(4);
        $this->content['leftNavbar'] = $myArrayMenu[0];
        $myArraySubmenu = $this->createSubMenu(4, 2);
        $this->content['topLC'] = $myArraySubmenu[0];
        $this->content['title'] = "Notice :: Messages";
        $this->content['headCC'] = $myArraySubmenu[1];
        $this->content['bottomRC'] = "blank";
        $this->content['bottomCC1'] = "messages";
        $data['content'] = $this->content;
        $data['main_content'] = 'admin_index';
        $data['content'] = $this->content;
        $this->load->view('/ext/template', $data);
    }

    function index() {
        if ($this->session->userdata('user_id') != NULL) {
            if ($this->session->userdata('isAdmin') == "YES") {
                $p = $this->uri->segment(3, 0);
                $a = $this->uri->segment(4, 0);
                if ($p == null

                    )$p = 0;
                if ($a == null

                    )$a = 0;
                $this->toggleView($p, $a);
                $data['content'] = $this->content;
                $data['main_content'] = 'admin_index';
                $this->load->view('/ext/template', $data);
            }else {
                $this->authenticationError("Administrator Login required");
            }
        } else {
            $this->authenticationError("Invalid Credentials");
        }
    }

    function logout() {
        $this->session->sess_destroy();
        redirect('http://localhost/ci2/index.php/login/');
    }

    function authenticationError() {
        $data['heading'] = "Invalid Credentials";
        $data['message'] = "Please Login to view this page, " . anchor('http://localhost/ci2/index.php/login/', 'Click here to login', 'title="Login"');
        $data['status_code'] = "500";
        show_error($data);
    }

    function toggleView($p, $a) {
        //$this->content['topRC']="adminTopRC";
        switch ($p) {
            case 1://Create Questionnaire
                $myArrayMenu = $this->createTopNav($p);
                $this->content['leftNavbar'] = $myArrayMenu[0];
                $this->content['bottomRC'] = "blank";
                if (!isset($a)

                    )$a = 0;
                $myArraySubmenu = $this->createSubMenu(1, $a * 2);
                $this->content['topLC'] = $myArraySubmenu[0];
                $this->content['headCC'] = $myArraySubmenu [1];
                switch ($a) {
                    case 1: //Add Heading
                        $this->content['bottomCC1'] = "add_headings";
                        $this->content['title'] = "Create::Add Heading";
                        break;
                    case 2:
                        $this->content['bottomCC1'] = "assignWeight";
                        $this->content['title'] = "Create::Assign Weight";
                        break;
                    case 3: //Select the Number of Questions For Headings
                        $this->content['bottomCC1'] = "selectQuestForHead";
                        $this->content['title'] = "Create::Select";
                        break;
                    case 4:
                        $this->content['bottomCC1'] = "addQuestions";
                        $this->content['title'] = "Create::Add Questions";
                        break;
                    case 5:
                        $this->content['bottomCC1'] = "preview";
                        $this->content['title'] = "Create::Preview";
                        break;
                    case 6: //Submit
                        $this->content['bottomCC1'] = "submit";
                        $this->content['title'] = "Create::Submit";
                        break;
                    default://Add Title &Sub heading //0
                        $this->content['bottomCC1'] = "add_title";
                        $this->content['title'] = "Create::Add Title";
                        break;
                }
                break;
            case 2:
                $myArrayMenu = $this->createTopNav($p);
                $this->content['leftNavbar'] = $myArrayMenu[0];
                if (!isset($a)

                    )$a = 0;
                $myArraySubmenu = $this->createSubMenu($p, $a * 2);
                $this->content['topLC'] = $myArraySubmenu[0];
                $this->content['headCC'] = $myArraySubmenu[1];
                $this->content['bottomRC'] = "blank";
                $this->content['bottomCC1'] = "showQnaire";
                $this->content['title'] = "Manage::View";
                break;
            case 3:
                redirect('http://localhost/ci2/index.php/admin_report/report_org/');
                break;
            case 4:
                redirect('http://localhost/ci2/index.php/admin/recent_activities/');
                break;
            default:
                //dashboard ---> this is the index page for administrator
                redirect('http://localhost/ci2/index.php/admin_report/dashboard_org/');

                break;
        }
    }

    function createTopNav($current) {
        $a = base_url() . 'index.php/admin/index/';
        $nav = array("Dashboard", base_url() . "index.php/admin_report/dashboard_org/",
            "Create", $a . "1/0", "Manage", $a . "2/0", "Report", $a . "3/0", "Notice", $a . "4/0");
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
            array("Add Title & Number of Sub-Headings", "$a" . "1/0", "Add Heading Title", "$a" . "1/1", "Assign Weight", "$a" . "1/1",
                "Add Number of Question", "$a" . "1/3", "Add Question(s)", "$a" . "1/4",
                "Preview", "$a" . "1/5", "Save and Exit", "$a" . "1/6"), array("View Questionnaire", "$a" . "2/0",
                "Edit Questionnaire", "$a" . "2/1", "Add Project", "http://localhost/ci2/index.php/admin/addProject",
                "Add Key Performance Indicators", "http://localhost/ci2/index.php/admin/addKPI"),
            array("By Organization", base_url() . "index.php/admin_report/report_org/",
                "By Department", base_url() . "index.php/admin_report/report_dept/"),
            array("Recent Activities", base_url() . "index.php/admin/recent_activities/",
                "Messages", base_url() . "index.php/admin/messages/"));

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

}

?>
