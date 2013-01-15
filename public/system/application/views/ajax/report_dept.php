<?php
$tmpl = array('table_open' => '<table class="mytable">');
$this->table->set_template($tmpl);
$cell2 = array('data' => '<b>Reviewer</b>', 'class' => 'head2',
    'colspan' => 2);
$cell3 = array('data' => '<b>Self</b>', 'class' => 'head2',
    'colspan' => 2);
$cell4 = array('data' => '', 'class' => 'head2');
$this->table->set_heading('Performance Indicator', 'Min Score(%)', 'Max Score(%)', 'Min Score(%)', 'Max Score(%)',
        'Achievable Score(%)', 'Average(%)', 'Action');
$i=0;
$this->table->add_row($cell4, $cell3, $cell2, $cell4, $cell4, $cell4);
if (isset($heading)) {
    foreach ($max_self as $key => $value) {
        $this->table->add_row("<b>".$heading[$i]."</b>", $min_self[$key], $max_self[$key], $min_reviewer[$key],
                $max_reviewer[$key], $max_head_val[$i], '', '<a href="">Comment</a>');
        ++$i;
    }

} else {
    $cell = array('data' => 'No available questionnaire submitted yet', 'class' => 'empty',
        'colspan' => 6);
    $this->table->add_row($cell);
}
echo $this->table->generate();
?>
<script type="text/javascript" charset="utf-8">    
    $('tr:odd').css('background','#e3e3e3');
    $('.head2').css('background','#cbcfd4');    
    $("tr td:first-child").css('background','#cbcfd4');
    $("tr td:last-child").css('background','#cbcfd4');     
</script>
