<div>      <table class="mytable">
        <thead>
            <tr>
                <th>#</th><th>id</th><th>Name</th><th>sex</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            if (!(count($employee) > 0)) {
                echo "<tr><td colspan='5'><b>No employee found for the selected depeartment</b></td></tr>";
            } else {
                foreach ($employee as $row) {
                    $checkData = array('name' => 'employee[]', 'style' => 'margin:5px; width:20px',
                        'value' => $row[0], 'checked' => set_checkbox('employee[]', $row[0], FALSE));
                    echo"<tr>
                <td>$i</td><td>$row[0]</td><td>" . $row[1] . " " . $row[2] . " " . $row[3] . "</td><td>$row[4]</td>
    <td>" . form_checkbox($checkData) . "</td>
            </tr>";
                    $i++;
                }
            }
            ?>
        </tbody>
    </table> 
</div>
<?php
            echo "<script type=\"text/javascript\" charset=\"utf-8\">
    $('tr:odd').css('background','#e3e3e3');    
    </script>";
?>