<?php
$html = "";
$j = 1;
?>
<?php
$i = 1;
$k = 0;
$attributes = array('class' => 'qnaire');
echo form_fieldset('<p><b>' . $j . '. Submitted Questionnaires</b></p>', $attributes);
$tmpl = array('table_open' => '<table class="mytable">');
$this->table->set_template($tmpl);
$this->table->set_heading('#', 'Title', 'Id', 'Due Date', 'Reviewer Name',
        'Self Score(%)', 'Reviewer Score(%)', 'Appr. Mgr 1', 'Appr. Mgr 2', 'status', 'Action');
if (count($allQnaire) > 0) {
    foreach ($allQnaire as $row) {
        if ($row[6] != "Not filled") {
            $this->table->add_row($i++ . '.', $row[1], $row[0], $row[5], $row[7], $row[10], $row[11], $row[8], $row[9],
                    $row[6], anchor("http://localhost/ci2/index.php/view_qnaire/index/" . $row[0], "View"));
            ++$k;
        }
    }
} if ($k == 0) {
    $cell = array('data' => 'No available questionnaire submitted yet', 'class' => 'empty',
        'colspan' => 10);
    $this->table->add_row($cell);
}

echo $this->table->generate();
echo form_fieldset_close();




if ($this->session->userdata('isReviewer') == 'YES') {
    $k = 0;
    $tmpl = array('table_open' => '<table class="mytable">');
    $this->table->set_template($tmpl);
    $i = 1;
    echo form_fieldset('<p><b>' . ++$j . '. Questionnaires for your Review</b></p>', $attributes);

    $this->table->set_heading('#', 'Title', 'Id', 'Employee Name', 'Due Date', 'Action');
    if (count($reviewQnaire) > 0) {
        foreach ($reviewQnaire as $row) {
            if ($row[5] == "Filled") {
                $this->table->add_row($i . '.', $row[1], $row[0], $row[3], $row[4],
                        anchor("http://localhost/ci2/index.php/fill_reviewer/index/" .
                                $row[7] . "/" . $row[0] . "/" . $row[6], "Review"));
                $k++;
            }
        }
        if ($k == 0) {
            $cell = array('data' => 'No available questionnaire to review', 'class' => 'empty', 'colspan' => 6);
            $this->table->add_row($cell);
        }
        echo $this->table->generate();
    }
}
echo form_fieldset_close();


echo form_fieldset();
if ($this->session->userdata('isApprover') == 'YES') {
    $i = 1;
    $k = 0;
    echo form_fieldset('<p>' . ++$j . '. Questionnaires for your approval</p>', $attributes);
    $this->table->set_heading('#', 'Title', 'Id', 'Employee Name', 'Due Date', 'Action');
    foreach ($approveQnaire as $row) {
        $this->table->add_row($i . '.', $row[1], $row[0], $row[3], $row[4], '<a href="">Approve</a>');
    }
    echo $this->table->generate();
}
echo form_fieldset_close();
?>


<?php
$k = 0;
$tmpl = array('table_open' => '<table class="mytable">');
$this->table->set_template($tmpl);
$i = 1;
echo form_fieldset('<p><b>' . ++$j . '. Questionnaires Yet to be filled</b></p>', $attributes);

$this->table->set_heading('#', 'Title', 'Id', 'Employee Name', 'Project Name', 'Due Date', 'Action');
foreach ($allQnaire as $row) {
    if ($row[6] == "Not filled") {
        ++$k;
        $this->table->add_row($i . '.', $row[1], $row[0], $row[3], $row[4], $row[5],
                anchor("http://localhost/ci2/index.php/fill_view_qnaire/index/" . $row[2] . "/" . $row[0], "Fill"));
        ++$i;
    }
}
if ($k == 0) {
    $cell = array('data' => 'No available questionnaire to be filled', 'class' => 'empty',
        'colspan' => 10);
    $this->table->add_row($cell);
}echo $this->table->generate();
echo form_fieldset_close();
?>
<script type="text/javascript">
$
</script>

