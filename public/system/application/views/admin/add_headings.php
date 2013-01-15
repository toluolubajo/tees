<?php
if (isset($_SESSION['headNum'])) {
    $numOfHeads = 0;
    $numOfHeads = $_SESSION['headNum'];
    $validate = "";
    $error = "";
    $heads = array();
    if (isset($_POST['error'])
        )$error = $_POST['error'];
    for ($i = 1; $i <= $numOfHeads; ++$i) { ///collect all the post head weight variables
        if (isset($_POST['head' . $i])) {
            array_push($heads, $_POST['head' . $i]);
        }
    }
    $headCount = count($heads);
    if ($headCount > 0) { ///now check each value with the rest of the value in the array
        //to be sure they are not the same
        for ($i = 0; $i <$headCount; ++$i) {
            $temp = $heads[$i];
            for ($j = 0; $j < $headCount; ++$j) {
                if ($temp == $heads[$j] AND $j != $i){
                    echo "j is ".$j." while i is ".$i.'<br>';
                    echo "temp is ".$temp." while headweight is ".$heads[$j].'<br>';
                    $error = "You must choose distinct Performance Indicators";
                }
            }
        }
    }
    if ($error == "no") {
        for ($i = 1; $i <= $numOfHeads; ++$i) {
            if (isset($_POST['headWeight' . $i])) {
                $_SESSION['headWeight' . $i] = $_POST['headWeight' . $i];
            }
        }
        for ($i = 1; $i <= $numOfHeads; ++$i) {
            if (isset($_POST['head' . $i])) {
                $_SESSION['head' . $i] = $_POST['head' . $i];
            }
        }
        redirect('admin/index/1/2');
    } else {
?>
    <div>
<?php
        echo "<h4 style='color:red'>" . $error . "</h4>";
        $attributes = array('class' => 'formLayout', 'name' => 'addHeadings');
        echo form_open('admin/index/1/1', $attributes); ?>
    <h2><span style="color:black">Title : </span><?php echo $_SESSION['title']; ?></h2>
    <div>
        <?php
        $head = "";
        $this->table->set_heading("", "Name", "Weight");
        for ($i = 1; $i <= $numOfHeads; ++$i) {
            if (isset($_SESSION['head' . $i])

                )$k = $_SESSION['head' . $i];else
                $k="";
            if (isset($_SESSION['headWeight' . $i])

                )$j = $_SESSION['headWeight' . $i];else
                $j="";
            $kpi = $this->view_qnaire_model->getKPI();

            $this->table->add_row('Performance Indicator :    ' . $i,
                    form_dropdown('head' . $i, $kpi, $k),
                    form_dropdown('headWeight' . $i, populate(), $j)
            );
        }
        echo $this->table->generate();
        ?>
    </div>
    <div>
        <input type="hidden" name="error" value="no">
        <input type="Submit" class="btn" value="Proceed to adding Questions"/>
        <input type="hidden" name="add_headings" value="yes">
    </div>
    <?php } ?>
    <?php
}

function populate() {
    $value = array();
    for ($i = 1; $i < 101; $i++) {
        array_push($value, $i);
    }
    return $value;
}

echo form_close()
    ?>

</div>
