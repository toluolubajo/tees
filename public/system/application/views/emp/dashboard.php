<?php
$this->load->model('view_qnaire_model');
$i = 1;
$j = 1;
$k = 0;
echo "<div id='summary'>";
$attributes = array('class' => 'qnaire');
echo form_fieldset('<b>' . $j . '.Summary of Submitted Questionnaire</b>', $attributes);
$tmpl = array('table_open' => '<table class="mytable">');
$this->table->set_template($tmpl);
$cell1 = array('data' => 'Lowest Score(%)', 'class' => '',
    'colspan' => 2);
$cell2 = array('data' => 'Highest Score(%)', 'class' => '',
    'colspan' => 2);
$cell3 = array('data' => 'Comments', 'class' => '',
    'colspan' => 2);
$this->table->set_heading('#', 'Id','Project Name',$cell1, $cell2,
        $cell3, 'Action');
$this->table->add_row('', '', '', 'Self', 'Reviewer', 'Self', 'Reviewer', 'Self', 'Reviewer', '');
foreach ($qnaireData as $key => $value) {
    $this->table->add_row(++$k, $key, $value[0], 
            $this->view_qnaire_model->getHeadTitle($maxLow[$key]['min_self']),
            $this->view_qnaire_model->getHeadTitle($maxLow[$key]['min_reviewer']),
            $this->view_qnaire_model->getHeadTitle($maxLow[$key]['max_self']),
            $this->view_qnaire_model->getHeadTitle($maxLow[$key]['max_reviewer']),
            $value[3], $value[4], '<a href="">Comment</a>');
}

if ($k == 0) {
    $cell = array('data' => 'No questionnaire submitted yet', 'class' => 'empty',
        'colspan' => 11);
    $this->table->add_row($cell);
}
echo $this->table->generate();
echo form_fieldset_close();
echo "</div>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";



?>