<div>
    <?php
     echo validation_errors();
    echo form_open('admin/addProject');
    $attributes = array('class' => 'group','style'=>'width:500px;');
    echo form_fieldset("Select Questionnaire",$attributes);
    $tmpl = array ( 'table_open'  => '<table class="mytable">' );
    $this->table->set_template($tmpl);
    $this->table->set_heading(array('#', 'id', 'Title', 'Action'));
    $query = $this->db->get('qnaire');
    $i = 1;
    foreach ($query->result() as $row) {
        $checkData = array( 'name' => 'questionnaire[]', 'value' => $row->id, 'style' => 'margin:5px; width:20px','checked' => set_checkbox('questionnaire[]', $row->id, FALSE));
        $this->table->add_row(array($i, $row->id, $row->title, form_checkbox($checkData)));
        $i++;
    }
    echo $this->table->generate();
    echo form_fieldset_close();
    $attributes = array('class' => 'group','style'=>'width:500px;');
    echo form_fieldset("Add Project Description",$attributes);
    echo "<div>";
    echo form_label('Project Name');
    echo form_input(array('name' => 'projectName','value'=>set_value('projectName')));
    
    echo "</div>";
    echo "<div>";
    echo form_label('Project Description');
    echo form_textarea(array('name' => 'projectDescrptn','value'=>set_value('projectDescrptn')));
    
    echo "</div>";
    echo form_fieldset_close();
    $attributes = array('class' => 'group','style'=>'width:500px;');
    echo form_fieldset("Add Project Team",$attributes);
    echo "<div>";
    echo form_label('Select Department Id'); 
    $js = 'id="dept"';
    echo form_dropdown('deptId',$allDeptId,"",$js);
    echo "</div>";
    echo "<div>";
        echo ('<h3  >Select Team members</h3>');
    echo "</div>";
    echo "<div id='ajaxDiv'>";
    echo "</div>";
    echo form_fieldset_close();
    echo form_submit(array('name'=>'submit','value'=>'Submit','class'=>'btn'));
    
    echo form_close();
    ?>
 
</div>
 <script type="text/javascript">
        $(document).ready(function(){
                 var deptId = $("#dept").val();                 
                $.ajax({url:"http://localhost/ci2/index.php/admin/getTeam/"+deptId, success:function(result){
                        $("#ajaxDiv").html(result);
                        $("#ajaxDiv").fadeOut(10);
                        $("#ajaxDiv").fadeIn(2000);
                    }});
                $("#dept").change(function() {
                    var deptId = $(this).val();
                    //alert( deptId );
                 $.ajax({url:"http://localhost/ci2/index.php/admin/getTeam/"+deptId, success:function(result){
                        $("#ajaxDiv").html(result);
                        $("#ajaxDiv").fadeOut(10);
                        $("#ajaxDiv").fadeIn(2000);
                    }})});
            });
    </script>
    <?php
    echo "<script type=\"text/javascript\" charset=\"utf-8\">
    $('tr:odd').css('background','#e3e3e3');    
    </script>";
    ?>