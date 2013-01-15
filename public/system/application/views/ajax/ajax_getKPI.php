<table class="myTable" style="width:500px;">
    <tr><th>#</th><th>id
        </th><th>Title</th><th colspan="2">Action</th></tr>
    <?php
    $KPI = $this->view_qnaire_model->getKPI();    
    $i=1;
    foreach ($KPI as $key => $value) {
        echo "<tr><td>$i</td><td>$key</td><td>$value</td><td class='edit'>Edit</td><td class='del'>Delete</td></tr>";
        ++$i;
    }
    ?>
</table>
<script type="text/javascript">
    $(document).ready(function(){
        $('.del').click(function(){
            id=$(this).parents().find('td:nth-child(2)').html();            
            response=confirm('Are you sure you want to delete');
            if(response){
                $.ajax({
                    url: 'http://localhost/ci2/index.php/ajaxController/deleteKPI/'+id,
                    type:'POST',
                    success:function(msg){
                        location.reload();
                    }
                });
                alert("Successfully deleted");
            }
        })       
            $('.edit').click(function(){
               id=$(this).parents().find('td:nth-child(2)').html();
               title=$(this).parents().find('td:nth-child(3)').html();
                response=confirm('Are you sure you want to edit');                
                if(response){
                    val=prompt('Enter Stuff',title);                    
                    if(val.length>0){
                    alert("You entered: "+val);
                    $.ajax({
                        url: 'http://localhost/ci2/index.php/ajaxController/updateKPI/'+id+"/"+val,
                        type:'POST',
                        success:function(msg){
                            location.reload();
                        }
                    });
                    alert("Successfully updated");
                }}
            })
        });
</script>