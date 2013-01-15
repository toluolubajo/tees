<?php
/////////////////////////////////////////////////////////////////////
$today = date_create();
$projectId = 0;
//$date = date_create_from_format('j-M-Y', $today);
$today = date_format($today, 'Y-m-d');
if (count($postVar['questionnaire']) > 0) {
    $projectId=$this->view_qnaire_model->insertProject($postVar['projectName'],$postVar['projectDescrptn']) ;
    for ($i = 0; $i < count($postVar['questionnaire']); $i++) {
        if (count($postVar['employee']) > 0) {
            for ($j = 0; $j < count($postVar['employee']); $j++) {                
                    $data = array(
                        'emp_qnaire_status' => 0,
                        'reviewer' => 0,
                        'emp_id' => $postVar['employee'][$j], //employee Id
                        'emp_mgr_id1' => 0,
                        'emp_mgr_id2' => 0,
                        'qnaire_rcomment' => "",
                        'qnaire_scomment' => "",
                        'qnaire_id' => $postVar['questionnaire'][$i],
                        'project_id' => $projectId,
                        'due_date' => $today //make sure you change this point by creating a page where
                            //project name, due data would be assigned to the created questionnaire's
                    );
                    $this->view_qnaire_model->insert_emp_qnaire($data);
                    //$this->db->insert('emp_qnaire', $data);
            }
        } else {
            echo '<h2 style="color:red">' . $title . ' appraisal form submission was unsuccessful</h2><br>';
            echo '<a href="">Click here to return to home page</a>';
            return;
        }
          echo '<h2>' . $title . ' appraisal form submission was successful</h2><br>';
    echo '<a href="localhost/ci2/index.php/admin/index">Click here to return to home page</a>';
    echo '<div style="height:70px"></div>';
    }
} else {
    echo '<h2 style="color:red">' . $title . ' appraisal form submission was unsuccessful</h2><br>';
    echo '<a href="">Click here to return to home page</a>';
    return;
}//print_r($postVar);
?>

