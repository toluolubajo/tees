<script  type="text/javascript">
    $().ready(function(){
        $('#submit').click(function () {
            $('#loading').show();
        })
        $("#myForm").submit(function(event) {
            event.preventDefault();
            $.post("http://localhost/ci2/index.php/admin/validate/", $("#myForm").serialize(),
            function(data) {
                $('#report').html(data)
            })})});
</script>
<?php
$j = 1;
$i = 1;
$k = 0;
$tmpl = array('table_open' => '<table class="mytable">');
$this->table->set_template($tmpl);
$attributes = array('class' => 'myfieldset');
$formAtr = array('id' => 'myForm', 'name' => 'myForm');
echo form_fieldset($j . '. Select Questionnaire', $attributes);
echo form_open('admin/validate/', $formAtr);
$this->table->set_heading(array('#', 'id', 'Title', 'Date Created', 'Check'));
$query = $this->db->get('qnaire');
$i = 1;
foreach ($query->result() as $row) {
    $html = '<input type="checkbox" id="chkbox" name="qnaire[]" ' . "value='" . $row->id . "'" . set_checkbox('qnaire[]', $row->id) . "/>";
    $this->table->add_row(array($i, $row->id, $row->title, $row->date_created, $html));
    $i++;
}
echo $this->table->generate();
$btnData = array('class' => 'btn', 'name'=>'submit','value'=>'View');
echo form_submit($btnData);
echo form_close();
echo form_fieldset_close();
$this->table->clear();


echo "<br><br>";
$i = 1;
echo form_fieldset(++$j . '. Result', $attributes);
echo "<div id='report'>";
$tmpl = array('table_open' => '<table class="mytable">');
$this->table->set_template($tmpl);
$this->table->set_heading('Number of Projects', 'Number of Employess','Not filled','Filled', 'Reviewed', 'Approved','Submitted', 'Comment');
if ($k == 0) {
    $cell = array('data' => 'No questionnaire selected yet', 'class' => 'empty', 'colspan' => 8);
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

echo "<br>";
echo form_fieldset("General Activities",$attributes);
echo "<p>There is a total of <a href=''>" . $unfilled . "</a> unfilled questionnaires</p>";
echo "<p>There is a total of <a href=''>" . $filled . "</a> filled Questionnaires</p>";
echo "<p>There is a total of <a href=''>" . $reviewed . "</a> reviewed Questionnaires</p>";
echo "<p>There is a total of <a href=''>" . $approved . "</a> approved questionnaires</p>";
echo "<p>There is a total of <a href=''>" . $approved . "</a> submitted questionnaires</p>";
echo form_fieldset_close();
echo "<script type=\"text/javascript\" charset=\"utf-8\">
    $('tr:odd').css('background','#e3e3e3');    
    </script>";


?>
