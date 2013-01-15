<script type="text/javascript">
    $(document).ready(function(){
        var deptId = $("#dept").val();
        $.ajax({url:"http://localhost/ci2/index.php/admin/ajax_getKPI/", success:function(result){
                $("#kpiTable").html(result);
            }});
        $("#dialog").dialog();
        $("#addKPI").submit(function(event) {
            //event.preventDefault();
            $.ajax({url:"http://localhost/ci2/index.php/admin/ajax_getKPI/", success:function(result){
                    $("#kpiTable").html(result);
                }});
        })

    });
</script>
<div id="kpiTable">
</div>
<br><br>

    <?php $attributes = array('class' => 'formLayout', 'name' => 'addKPI', 'id' => 'addKPI');
    echo form_open('', $attributes); ?>
    <label style="width:200px"><b>Please enter Key performance Indicator Name</b> </label>
    <?php
    $this->form_validation->set_rules('kpi', 'Key Performance Indicator', 'required');
    if ($this->form_validation->run() == TRUE) {
        $data = array(
            'title' => $_POST['kpi']);
        $this->db->insert('kpi', $data);
    }
    $kpi = "";
    if (isset($_POST['kpi'])
        )$kpi = $_POST['kpi'];
    $data = array(
        'name' => 'kpi',
        'id' => 'kpi',        
        'maxlength' => '100',
        'style' => 'width:300px',
    );
    echo form_input($data);
    echo "<div><label style=\"width:200px\">&nbsp</label>" . form_submit('submit', 'Submit') . "</div>";
    echo "<p style='color:red'>" . validation_errors() . "</p>";
    echo form_close();
    ?>

