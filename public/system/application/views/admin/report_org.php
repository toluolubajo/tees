<?php
$j = 1;
$i = 1;
$k = 0;
echo "<h4 style='color:red'>" . validation_errors() . "</h4>";
$attributes = array('class' => 'myfieldset  ');
$formAtr = array('id' => 'myForm', 'name' => 'myForm');
echo form_fieldset($j . '. Select Questionnaire', $attributes);
echo form_open('', $formAtr);
$tmpl = array('table_open' => '<table class="mytable">');
$this->table->set_template($tmpl);
$this->table->set_heading(array('#', 'id', 'Title', 'Date Created', 'Check'));
$query = $this->db->get('qnaire');
$i = 1;
foreach ($query->result() as $row) {
    $html = '<input type="checkbox" id="chkbox" name="qnaire[]" ' . "value='" . $row->id . "'" . set_checkbox('qnaire[]', $row->id) . "/>";
    $this->table->add_row(array($i, $row->id, $row->title, $row->date_created, $html));
    $i++;
}
echo $this->table->generate();
echo $this->table->clear();
echo "<div>";
echo '<input id="submit" type="submit" style="width:70px" id="submit"/>';
echo "</div>";
echo form_fieldset_close();
echo form_close();

echo "<br><br>";
$tmpl = array('table_open' => '<table class="mytable">');
$this->table->set_template($tmpl);
$i = 1;
echo form_fieldset(++$j . '. Performance Chart', $attributes);
echo "<script type=\"text/javascript\" charset=\"utf-8\">
    $('tr:odd').css('background','#e3e3e3');    
    </script>";

echo '<div style="margin-left:10px" id="div_chart_1"></div>';

echo form_fieldset_close();
?>
