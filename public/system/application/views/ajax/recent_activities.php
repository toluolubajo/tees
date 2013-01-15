<?php

$k = 0;
$tmpl = array('table_open' => '<table class="mytable">');
$this->table->set_template($tmpl);
$this->table->set_heading('Id', 'Number of Projects', 'Number of Employees', 'Not filled', 'Filled', 'Reviewed', 'Approved', 'Action');
foreach ($qnaireReport as $key => $value) {
    ++$k;
        $this->table->add_row($key,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],"<a href=' '>Comment</a>");
}
if ($k == 0) {
    $cell = array('data' => 'No questionnaire selected yet', 'class' => 'empty', 'colspan' => 8);
    $this->table->add_row($cell);
}
echo $this->table->generate();
?>
