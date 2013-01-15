<script  type="text/javascript">
    $().ready(function(){ 
        $('#submit').click(function () {
            $('#loading').show();
        })
        $("#myForm").submit(function(event) {
            event.preventDefault();
            $.post("http://localhost/ci2/index.php/admin_report/ajax_report_dept/", $("#myForm").serialize(),
            function(data) {
                $('#report').html(data)
            })})});
</script>
<?php
$j = 1;
$i = 1;
$k = 0;
$attributes = array('class' => 'myfieldset');
$formAtr = array('id' => 'myForm', 'name' => 'myForm');
echo form_fieldset($j . '. Select Questionnaire', $attributes);
$tmpl = array('table_open' => '<table class="mytable">');
$this->table->set_template($tmpl);
echo form_open('', $formAtr);
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
echo form_fieldset_close();
echo "<br><br>";
$tmpl = array('table_open' => '<table class="mytable">');
$this->table->set_template($tmpl);
$i = 1;
echo form_fieldset(++$j . '. Select Department', $attributes);
echo "<div>";
$js = 'id="dept"';
$allDeptId = $this->view_qnaire_model->getDeptId();
$allDeptId = fixArray($allDeptId);
echo form_dropdown('deptId', $allDeptId, "", $js);
echo '<input id="submit" type="submit" style="width:70px" id="submit"/>';
echo "</div>";

echo '<br>';
echo form_fieldset_close();

echo form_close();
echo "<br><br>";
$tmpl = array('table_open' => '<table class="mytable">');
$this->table->set_template($tmpl);
$i = 1;
echo form_fieldset(++$j . '. Result', $attributes);

echo "<div id='report'>";
$this->table->set_heading('#', 'Dept', 'Scores', 'Performance Indicator 1', 'Comments');
if ($k == 0) {
    $cell = array('data' => 'No questionnaire selected yet', 'class' => 'empty', 'colspan' => 5);
    $this->table->add_row($cell);
}
echo $this->table->generate();
echo "</div>";

echo form_fieldset_close();

function fixArray($data) { ///input to the graph
    ///the input to this function is in this form array(0=>array(1==>2),1=>array(4=>5))
    $myData = array();
    for ($i = 0; $i < count($data); $i++) {
        foreach ($data[$i] as $key => $value) {
            $myData[$key] = $value;
        }
    }
    return $myData;
}
echo "<script type=\"text/javascript\" charset=\"utf-8\">
    $('tr:odd').css('background','#e3e3e3');
    </script>";
?>
