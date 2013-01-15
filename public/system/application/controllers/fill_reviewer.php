<?php

class Fill_reviewer extends CI_Controller {

    public $data = array();
    public $responseColumn = 0;
    public $questions = array();
    public $content = array('leftNavbar' => '', 'rightTop' => '', 'rightContent' => '');
    public $error = FALSE;
    public $qnaireId = "";
    public $userId = "";
    public $isReviewer = TRUE;
    public $reviewee="";
    public $isApprovingOfficer = FALSE;

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('view_qnaire_model');
        $this->load->library('form_validation');
    }
    function index() {
        if ($this->session->userdata('pass') == TRUE) {
            $this->qnaireId = $this->session->userdata('qnaireId');
            $this->setVariables($this->qnaireId);
            $data = array();
            $i = 1;
            $screenno = 0;
            if (isset($_POST['screenno'])
                )$screenno = $_POST['screenno'];
            if ($screenno >= 1) {
                foreach ($this->data['headings'] as $rowHeads) {
                    if ($i == ($screenno)) {
                        $numOfQuestions = count($this->view_qnaire_model->get_qnaire_questions($rowHeads->id));
                        for ($j = 1; $j <= $numOfQuestions; $j++) {
                            $this->form_validation->set_rules('h' . ($i) . 'q' . $j, 'Radio button', 'trim|required');
                        }
                        $this->form_validation->set_rules('headComment' . ($i), 'Comment Field', 'trim|required');
                        break;
                    }
                    $i++;
                }
                if($screenno==(count($this->data['headings'])+1)){                    
                    $this->form_validation->set_rules('keyStrengths', 'Key Strength', 'trim|required');
                    $this->form_validation->set_rules('suggestns', 'Improvement Suggestion', 'trim|required');
                }
            }
            if (($screenno > 0 && $screenno <= ($this->data['MAXSCREEN'])+1) && $this->form_validation->run() == FALSE) {
                $_POST['screenno'] = ($screenno - 1);
                $this->error = TRUE;
            }
            $this->updateResponse();
            $this->data['html'] = $this->displayQuestion();
            $this->data['content'] = $this->content;
            $data = $this->data;
            $temp = $this->createLeftNav();
            $data['heading'] = $temp[1];
            $data['leftNav'] = $temp[0];
            $this->load->view('fill_view_qnaire_View', $data);
        } else {
            if ($this->checkPermission() == 0

                )$this->authenticationError('Invalid Credentials');
            else {
                $this->index();
            }
        }
    }
function calculate_scores($answerString, $qnaireId) {
        //echo $this->view_qnaire_model->calculateScores($answerString, $qnaireId);
    }

    function set_session_var_from_database() {
        ///headComments[i]...headComments[numOfHeads], reviewers Comments,
        ///self comments
        ///answerString would be set based on either a reviewer or an ordinary employee
    }

    function checkPermission() {
        $j = 0;
        if ($this->session->userdata('user_id') != NULL) {
            $this->userId = $this->session->userdata('user_id');
            $this->qnaireId = $this->uri->segment(3, 0); //this is the questionnaire Id in the qnaire table
            $this->emp_qnaireId = $this->uri->segment(4, 0); //this is the employee questionnaire id in the emp_qnaire table
            $this->reviewee = $this->uri->segment(5, 0);            
            //echo "qnaire id in check permission is ".$this->qnaireId;
            $this->session->set_userdata('qnaireId', $this->qnaireId);
            $this->session->set_userdata('emp_qnaireId', $this->emp_qnaireId);
            $this->session->set_userdata('reviewee', $this->reviewee);
            $allQnaire = $this->view_qnaire_model->get_emp_qnaire($this->reviewee);
            if (count($allQnaire) > 0) {                
                foreach ($allQnaire as $row) {
                    echo "hi";
                    if ($row[7] == $this->userId) {
                        $j = 1;
                        $this->session->set_userdata('pass', TRUE);
                        break;
                    }
                }
            }
        }
        return $j;
    }

    function authenticationError($message) {
        $data['heading'] = $message;
        $data['message'] = "Please Login to view this page, " . anchor('http://localhost/ci2/index.php/login/', 'Click here to login', 'title="Login"');
        $data['status_code'] = "500";
        show_error($data);
    }
    function setVariables($qnaireId) {
        $this->data['title'] = $this->view_qnaire_model->qnaire_title($qnaireId);
        $this->data['qnaireId'] = $qnaireId;
        $this->data['headings'] = $this->view_qnaire_model->get_numOfHeads($qnaireId); ///problem area
        $this->data['num_of_questions'] = $this->view_qnaire_model->get_numOfQuestion($qnaireId);
        $this->data['response'] = str_repeat("0", count($this->data['num_of_questions']));                   // Number of data columns
        $this->data['screenno'] = 0;                                    // Current question for display
        $this->data['MAXSCREEN'] = count($this->data['headings']);
        $this->data['html'] = "";
    }

    function radiocell($radname, $colnum, $qval) {
        // Print out a radio button
        $html = "<td><input type=\"radio\" class=\"chkbox\" name=\"$radname\" value=\"$qval\"";
        if (substr($this->data['response'], $colnum, 1) == $qval) {///check this place
            $html.=" checked=\"checked\"";
        }
        $html.= "</td>";
        return $html;
    }

    function radio5($radname, $colnum, $qlabel) {
        // Print out a row of 5 radio buttons (as a six-cell table row)
        $html = "<tr><td class=\"srv_in\">${qlabel}</td>";
        $html.=$this->radiocell($radname, $colnum, 1);
        $html.=$this->radiocell($radname, $colnum, 2);
        $html.=$this->radiocell($radname, $colnum, 3);
        $html.=$this->radiocell($radname, $colnum, 4);
        $html.="<td class='noDisplay'> </td>";
        $html.=$this->radiocell($radname, $colnum, 9);
        $html.="</tr>";
        return $html;
    }

    function addresps($newval, $colnum) {
        // Change a single column response (radio button)
        // Reset the current value
        $this->data['response'] = substr_replace($this->data['response'], 0, $colnum, 1);
        //Update value if appropriate
        if (isset($_POST[$newval])) {
            $this->data['response'] = substr_replace($this->data['response'],
                            $_POST[$newval], $colnum, 1);
        }
    }

// Update response set based on incoming question number
    function updateResponse() {
        if (isset($_POST['response'])) {
            //echo "response in update is " . $_POST['response'] . "<br>";
            //echo "error is ".$this->error."<br>";
            $this->data['response'] = $_POST['response'];
        }
        if (isset($_POST['screenno'])) {
            $this->data['screenno'] = $_POST['screenno'];
        }
        if ($this->error == FALSE) {
            $numOfHeads = count($this->data['headings']);
            if ($this->data['screenno'] <= ($numOfHeads + 1) AND $this->data['screenno'] >= 0) {
                $i = 1;
                foreach ($this->data['headings'] as $rowHeads) {
                    if ($i == $this->data['screenno']) {
                        $numOfQuestions = count($this->view_qnaire_model->get_qnaire_questions($rowHeads->id));
                        if (isset($_POST['answerStart'])

                            )$previousHeadNumOfQtn = $_POST['answerStart'];
                        else {
                            $previousHeadNumOfQtn = 0;
                        };
                        for ($j = 1, $k = $previousHeadNumOfQtn; $j <= $numOfQuestions; $j++) {
                            $this->addresps('h' . $i . 'q' . $j, $k);
                            $k++;
                        }
                    }
                    $i++;
                }
            }
        }
        $this->increDecre();
    }

    function increDecre() {
        if (isset($_POST['next'])) {
            $this->data['screenno']++;
        }
        if (isset($_POST['back'])) {
            $this->data['screenno']--;
        }
    }

    function displayQuestion() {
        $tempHtml = "";
        $html = "";
        $html .= "<p><table cellpadding='2' cellspacing='10' class='mytable'>
    <tr><td class=\"srv_wl\"></td>
    <td class=\"srv_wr\"><div class=\"title\"><h2 style='color:black'>{$this->data['title']}</h2></div>
    </td></tr>
    </table></p>" . form_open('fill_reviewer/index') . "
    <input type=\"hidden\" name=\"screenno\" value=\"" . $this->data['screenno'] . "\">" .
                "<input type=\"hidden\" name=\"response\" value=\"" . $this->data['response'] . "\">";
        if ($this->data['screenno'] <1) { ////////////////////////////////////problem area            
                $html .= $this->startHtml();
        } else {
            $numOfHeads = count($this->data['headings']);
            if ($this->data['screenno'] <= ($numOfHeads) && $this->data['screenno'] >= 1) {   //enters if screenno is
               
                $answerStart = 0;
                $k = 0;
                $j = 1;
                foreach ($this->data['headings'] as $rowHeads) {////this point obtain the questions and put it
                    //in the question array
                    $val = "";
                    if ($j == $this->data['screenno']) {
                        ///assign a session variable to the incoming post variable
                        if (isset($_POST['headComment' . ($j - 1)])) {
                            $this->session->set_userdata('headComment' . ($j - 1), $_POST['headComment' . ($j - 1)]);
                        }/////ends
                        ///set the value of the textarea to the session variable set ends
                        if ($this->session->userdata('headComment' . ($j)) != NULL)
                            $val = $this->session->userdata('headComment' . ($j));
                        $data = array(
                            'name' => "headComment" . ($j), ///the textarea default data
                            'id' => 'username',
                            'value' => $val,
                            'rows' => '10',
                            'cols' => '10',
                            'style' => 'width:50%',
                        );
                        $tempHtml = "<h2>Reviewer's Comment?</h2><br>" . form_textarea($data); ///temporarily holds the html to display the text area
                        //for comments
                        $this->questions = $this->view_qnaire_model->get_qnaire_questions($rowHeads->id);
                        break;
                    }
                    $j++;
                }
                $prevCount = 0;
                if (isset($_POST['next']) && $this->data['screenno'] > 1) {
                    $answerStart = (int) $_POST['answerFinish'];
                    if (isset($_POST['count'])

                        )$prevCount = $_POST['count'];
                }
                if (isset($_POST['back'])) {
                    if ($this->data['screenno'] == $this->data['MAXSCREEN']) {
                        $answerStart = ((int) $_POST['answerFinish'] - (int) $_POST['count']);
                    } else {
                        $answerStart = ((int) $_POST['answerStart'] - (int) $_POST['count']) + 1;
                    }
                }

                $html .= "<input type=\"hidden\" name=\"prevNumOfQtn\" value=" . $prevCount . ">";
                if ($this->error == TRUE) {///this section set the post variables on an error on a page
                    $answerStart = $_POST['answerStart'];
                }
                $k = $answerStart;

                $html .= "<p><br/><table cellpadding='2' cellspacing='10' class='mytable'>";
                $html.="<tr><th></th><th>SEE</th> <th>EE</th> <th>ME</th> <th>NME</th> <th class='noDisplay'></th> <th>NA</th> </tr>";

                for ($i = 1; $i <= count($this->questions); ++$i) {

                    $html.=$this->radio5('h' . $j . 'q' . $i, $k++, $this->questions[($i - 1)]);
                }
                $html .= "<input type=\"hidden\" name=\"count\" value=" . count($this->questions) . ">";
                $html.="</table></p>";
                $html .= "<input type=\"hidden\" name=\"answerStart\" value=" . $answerStart . ">";
                $html.="<input type=\"hidden\" name=\"answerFinish\" value=" . $k . ">";
            } else {
                if ($this->data['screenno'] == $this->data['MAXSCREEN'] + 1) {
                    $j = ((int) $this->data['MAXSCREEN']);
                    $html .= "<input type=\"hidden\" name=\"count\" value=" . $_POST['count'] . ">";
                    $html .= "<p><table cellpadding='2' cellspacing='10' class='mytable'>";
                    $html .= "<input type=\"hidden\" name=\"prevNumOfQtn\" value=" . $_POST['prevNumOfQtn'] . ">";
                    $html .= "<input type=\"hidden\" name=\"answerStart\" value=" . $_POST['answerStart'] . ">";
                    $html.="<input type=\"hidden\" name=\"answerFinish\" value=" . $_POST['answerFinish'] . "></table>";
                    if (isset($_POST['headComment' . ($j)])) {
                        $this->session->set_userdata('headComment' . ($j), $_POST['headComment' . ($j )]);
                    }

                    ////start-4///////this part would display the text area for the reviewer to enter the key strngths
                    ////and the suggestions for improvement, and if the user login as a reviewee, it displays only the text
                    ///area to enter comments
                    $val = "";

                    if ($this->session->userdata('keyStrengths') != NULL)
                        $val = $this->session->userdata('keyStrengths');
                    $data = array(
                        'name' => 'keyStrengths', ///the textarea default data
                        'value' => $val,
                        'rows' => '10',
                        'cols' => '10',
                        'style' => 'width:50%',
                    );
                    $html .= "<div><h3>1. Summarise his/her key strength as demonstrated for this
                review period</h3>";
                    $html.=form_textarea($data);
                    $val1 = "";
                    if ($this->session->userdata('suggestns') != NULL)
                        $val1 = $this->session->userdata('suggestns');
                    $data1 = array(
                        'name' => 'suggestns', ///the textarea default data
                        'value' => $val1,
                        'rows' => '10',
                        'cols' => '10',
                        'style' => 'width:50%',
                    );
                    $html .= "</div><div><h3>2. Suggestions on how to achieve improved performance</h3>";
                    $html.=form_textarea($data1);

                    $html .= "<br></div><h4 style='color:black'>Click Back to review your responses or Next to
                            submit them.</h4>";
                    $val = "";
                    if ($this->session->userdata('qnaireCmnts') != NULL)
                        $val = $this->session->userdata('qnaireCmnts');                             

                    /////////////////////////////////////////////////////////////////////ends-4
                }
                if ($this->data['screenno'] == $this->data['MAXSCREEN'] + 2) {
                    //print_r($_POST);
                    
                    $html .= "The responses were coded as: " . $this->data['response'];
                    $percdone = ($this->data['screenno'] - 1) * 100 / ($this->data['MAXSCREEN'] + 1);
                    $html.="<br><td class='noDisplay'>" . $percdone . "% complete.</td>";                   
                    if (isset($_POST['suggestns'])) {
                        $this->session->set_userdata('suggestns', $_POST['suggestns']);
                    }
                    if (isset($_POST['keyStrengths'])) {
                        $this->session->set_userdata('keyStrengths', $_POST['keyStrengths']);
                    }
                    $head_comments = array();
                    for ($i = 1; $i <= count($this->data['headings']); $i++) {
                        array_push($head_comments, $this->session->userdata('headComment' . $i));
                    }
                    $qnaireId = $this->session->userdata('qnaireId');
                    $emp_qnaireId = $this->session->userdata('emp_qnaireId');
                    $qnaireCmnt = '';
                    ////ensure you fix this point to allow
                    ///for the submission of the post variables of qnaireCmnts, suggestns and keyStrengths
                    ///based on either the employee is a reviewer, approving officer or just an ordinary employee
                    $qnaireCmnts = "";
                    $keyStrengths = $this->session->userdata('keyStrengths');
                    $suggestns = $this->session->userdata('suggestns');
                    $reviewerName = "";
                    $approveName1 = "";
                    $approveName2 = "";
//                    echo     $emp_qnaireId." ".$this->data['response']." ".$qnaireId." ".
//                           '2'." ".$head_comments." ".'2'." keystrngths is".$keyStrengths." suggestions is ".$suggestns."<br>";
//
//                    print_r($head_comments);
                    $this->view_qnaire_model->update_Qnaire($reviewerName, $approveName1, $approveName2, $emp_qnaireId, $this->data['response'], $qnaireId,
                            $qnaireCmnts, 2, $head_comments, 2, $keyStrengths, $suggestns);

                    //$this->session->sess_destroy();
                }
            }
        }
        $html.=$tempHtml;
        $html.=$this->displayButton();
        return $html;
    }

    function startHtml() {
        $html = "<h1><b>Basis For Evaluation</b></h1>
<br>
<ul>
    <li>
        <h2>Significantly Exceeded Expectation (SEE)</h2>
        <p>Continuous exceptional performance coupled with outstanding professional and personal attributes which provide
            the basis for rapid future development. Performance consistently exceeds what is expected of an individual at this level
            He/She is already achieving expectations of an individual at the level above</p>
    </li><li>
        <h2>Exceeded Expectation (EE)</h2><p>High quality performance demonstrating very good personal attributes which provide the basis
            for strong future development</p>
    </li><li>
        <h2>Met Expectations (ME)</h2><p>This rating indicates the individual is meeting expectations for performance at his/her level of
            experience. This implies high quality work because high quality performance is built into our expectation. It indicates an ability
            to make progress within the Firm and the individual has the potential to progress to the next promotion level.</p>
    </li> <li>
        <h2>Not Met Expectations (NME)</h2><p>Performance is below what is expected of an individual at this level of
            experience and the individual demonstrates significant weakness in key skill areas which must be overcome in order to fulfil
            his/her responsibilities</p>
    </li><li>
        <h2>Not Applicable</h2><p>If the individual did not have the opportunity to demonstrate behaviour described in the evaluation scaled
            or you did not observe, you should omit the affected section</p>
    </li></ul>
<br>
        <h4>NB: The reason for selecting SEE, EE, MSE and NME must be stated in the comments section of this form</h4>
        ";
        $html .= "<input type=\"hidden\" name=\"prevNumOfQtn\" value=\"0\">";
        $html .= "<input type=\"hidden\" name=\"count\" value=\"0\">";
        $html .= "<input type=\"hidden\" name=\"prevCount\" value=\"0\">";

        return $html;
    }

    function displayButton() {
        $html = "";
        if ($this->data['screenno'] < $this->data['MAXSCREEN'] + 2) {
            $html.= "<p><table class='noDisplay'><tr>";
            if ($this->data['screenno'] > 0) {
                // Don't display "Back" or percentage complete on first screen
                $html.="<td class='noDisplay'><input  class=\"btn\" type=\"submit\" name=\"back\" value=\"<< Back\">
            </td>";
                $percdone = ($this->data['screenno'] - 1) * 100 / ($this->data['MAXSCREEN'] + 1);
                $html.="<td class='noDisplay'>" . $percdone . "% complete.</td>";
            }
            $html.= "<td class='noDisplay'><input  class=\"btn\" type=\"submit\" name=\"next\" value=\"Next >>\">
        </td>\n</tr></table><br/>";
            $html.= "<script type=\"text/javascript\" charset=\"utf-8\">
    $('tr:odd').css('background','#e3e3e3');
    </script>";
// Close everything up
            $html.= form_close();
//foot();

            return $html;
        }
    }

    function createLeftNav() {
        $a = base_url() . 'index.php/fill_reviewer/';
        $nav = array("Start", $a);

        $this->data['headings'] = $this->view_qnaire_model->get_numOfHeads($this->qnaireId);
        foreach ($this->data['headings'] as $rowHeads) {
            array_push($nav, $rowHeads->title);
            array_push($nav, $a);
        }
        $topnav = "<ul>";
        $heading = "Thank You";
        for ($i = 0, $j = 1; $i < (sizeof($nav) / 2); $i++, $j++) {
            if ($i == $this->data['screenno']) {
                $heading = $nav[($i + $j - 1)];
                if ($i == (sizeof($nav) / 2) - 1) {
                    $topnav.='<li id=lastLeftNav>' . '<a href="' . $nav[($i + $j)] . '" class="currentNav">' . $nav[($i + $j - 1)] . '</a> ' . '</li>';
                } else {
                    $topnav.='<li>' . '<a href="' . $nav[($i + $j)] . '" class="currentNav">' . $nav[($i + $j - 1)] . '</a> ' . '</li>';
                }
            } else {

                if ($this->data['screenno'] >= (sizeof($nav) / 2)) {
                    if ($i == (sizeof($nav) / 2) - 1) {
                        $topnav.='<li id=lastLeftNav>' . '<a href="' . $nav[($i + $j)] . '">' . $nav[($i + $j - 1)] . '</a> ' . '</li>';
                    } else {
                        $topnav.='<li>' . '<a href="' . $nav[($i + $j)] . '">' . $nav[($i + $j - 1)] . '</a> ' . '</li>';
                    }
                } else {
                    if ($i == (sizeof($nav) / 2) - 1) {
                        $topnav.='<li id=lastLeftNav>' . '<a href="' . $nav[($i + $j)] . '">' . $nav[($i + $j - 1)] . '</a> ' . '</li>';
                    } else {
                        $topnav.='<li>' . '<a href="' . $nav[($i + $j)] . '">' . $nav[($i + $j - 1)] . '</a> ' . '</li>';
                    }
                }
            }
        }
        $topnav.="</ul>";
        return $myArray = array($topnav, $heading);
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

