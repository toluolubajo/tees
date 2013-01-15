<?php
$today = strip_malcodes(date("y.m.d"));
$title = strip_malcodes($_SESSION['title']);
$qnaireId = 0;
///////////////////Insert questionnaire title and todays date into qnaire database - start
$data = array(
    'title' => $title,
    'date_created' => $today,
);
$this->db->insert('qnaire', $data);
////////////////////////////////////////////////////end
//////////////////select the questionnaire ID previously submitted - start
$q1 = "SELECT id FROM qnaire WHERE title='$title' AND date_created='$today'";
$query = $this->db->query($q1);
foreach ($query->result() as $row) {
    $qnaireId = $row->id;
}

/////////////////////////////////////////////////////////////////// end




///////////////////////////Insert the the questionnaire headings into the database using the questionnaire ID - start
for ($i = 1; $i <= $_SESSION['headNum']; ++$i) {
    $head = $_SESSION['head' . $i];
    $headWeight = (int) ($_SESSION['headWeight' . $i]+1);
    $q3 = "INSERT INTO qnaire_head(qnaire_id,title,score) VALUES('$qnaireId','$head','$headWeight')";
    $affected_rows = $this->db->query($q3);
}
//////////////////////////////////////////////////////////////////////  end
////////////obtain the Id's of the just inserted headings, in order to use it to submit the questions into the database
////////////and also submit the questions appropriately using the headings ID
$q1 = "SELECT id FROM qnaire_head WHERE qnaire_id='$qnaireId'";
$query = $this->db->query($q1);
$i = 1;
foreach ($query->result() as $row) {
    $headingId = $row->id;
    for ($j = 1; $j <= $_SESSION['H' . $i . 'numofQ']; ++$j) {
        //echo "SESSION H" . $i . "Q" . $j . "=" . $_SESSION['H' . $i . 'Q' . $j];
        //echo "<br>";
        $question = $_SESSION['H' . $i . 'Q' . $j];
        $q3 = "INSERT INTO question(head_id,question,questionType) VALUES('$headingId','$question','2')";
        $affected_rows = $this->db->query($q3);
    }
    ////insert into the headingScores Table
    $weight1=$_SESSION['head'.$i.'Weight1'];$weight2=($_SESSION['head'.$i.'Weight2']);
    $weight3=$_SESSION['head'.$i.'Weight3'];$weight4=($_SESSION['head'.$i.'Weight4']);
    $q4 = "INSERT INTO headingscores(id,NME,MSE,ME,EE) VALUES('$headingId','$weight1','$weight2','$weight3','$weight4')";
    $affected_rows = $this->db->query($q4);
    $i++;
}///////////////////////////////////////////////////////////////////

echo '<h2>' . $title . ' appraisal form submission was successful</h2><br>';
    echo '<a href="localhost/ci2/index.php/index">Click here to return to home page</a>';
    echo '<div style="height:70px"></div>';
session_unset();

function strip_malcodes($string) {
    return strip_tags(stripslashes($string));
}

function toArray($string) {
    $str = $string;
    $chars = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
//print_r($chars);
    return $chars;
}

?>
