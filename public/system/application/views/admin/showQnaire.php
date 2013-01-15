
<?php
echo "<div id=showQnaire>";
$this->load->library('table');
 echo form_open('admin/addProject');
     $tmpl = array ( 'table_open'  => '<table class="mytable">' );
    $this->table->set_template($tmpl);
$this->table->set_heading(array('#', 'id', 'Title','Date Created','Action'));
$query = $this->db->get('qnaire');
$i=1;
foreach($query->result() as $row){
    $this->table->add_row(array($i,$row->id,$row->title,$row->date_created,
        form_submit(array('class'=>'btn','name'=>'add'.$row->id,'value'=>'Add Project'))." ".form_submit(array('class'=>'btn','name'=>'view'.$row->id,'value'=>'View Project'))));
    $i++;
}
echo $this->table->generate();
echo form_close();
echo "</div>";
echo "<script type=\"text/javascript\" charset=\"utf-8\">
    $('tr:odd').css('background','#e3e3e3');    
    </script>";

?>
