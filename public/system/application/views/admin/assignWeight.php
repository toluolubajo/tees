<?php
if (isset($_SESSION['headNum'])) {
    $numOfHeads = 0;
    $numOfHeads = $_SESSION['headNum'];
    for ($i = 1; $i <= $numOfHeads; ++$i) {
        $weight1='head' . $i . "Weight1";
        $weight2='head' . $i . "Weight2";
        $weight3='head' . $i . "Weight3";
        $weight4='head' . $i . "Weight4";
        $this->form_validation->set_rules($weight1, 'Weight', 'required|callback_total_check');
        $this->form_validation->set_rules($weight2, 'Weight', 'required|callback_total_check');
        $this->form_validation->set_rules($weight3, 'Weight', 'required|callback_total_check');
        $this->form_validation->set_rules($weight4, 'Weight', 'required|callback_total_check');
    }
    if ($this->form_validation->run() == TRUE) {
        for ($i = 1; $i <= $numOfHeads; ++$i) {
            if (isset($_POST['head' . $i . "Weight1"])

                )$_SESSION['head' . $i . "Weight1"] = ($_POST['head' . $i . "Weight1"] );
            if (isset($_POST['head' . $i . "Weight2"])

                )$_SESSION['head' . $i . "Weight2"] = ($_POST['head' . $i . "Weight2"]);
            if (isset($_POST['head' . $i . "Weight3"])

                )$_SESSION['head' . $i . "Weight3"] = ($_POST['head' . $i . "Weight3"]);
            if (isset($_POST['head' . $i . "Weight4"])

                )$_SESSION['head' . $i . "Weight4"] = ($_POST['head' . $i . "Weight4"]);
        }redirect('admin/index/1/3');
    } else {
?>
    <div>
<?php
echo "<h4 style='color:red'>" . validation_errors() . "</h4>";
        $attributes = array('class' => 'formLayout', 'name' => 'addWeight');
        echo form_open('admin/index/1/2', $attributes); ?>
    <h2><span style="color:black">Title : </span><?php echo $_SESSION['title']; ?></h2>
    <div>
        <?php
        $head = "";
        $k1 = 0;
        $k2 = 0;
        $k3 = 0;
        $k4 = 0;
        $this->table->set_heading("Performance Indicator", "NME", "MSE", "ME", "EE");
        for ($i = 1; $i <= $numOfHeads; ++$i) {
            if (isset($_SESSION['head' . $i . "Weight1"])

                )$k1 = $_SESSION['head' . $i . "Weight1"];
            if (isset($_SESSION['head' . $i . "Weight2"])

                )$k2 = $_SESSION['head' . $i . "Weight2"];
            if (isset($_SESSION['head' . $i . "Weight3"])

                )$k3 = $_SESSION['head' . $i . "Weight3"];
            $this->table->add_row($_SESSION['head' . $i],
                    form_dropdown('head' . $i . "Weight1", populate($_SESSION['headWeight' . $i]), $k1),
                    form_dropdown('head' . $i . "Weight2", populate($_SESSION['headWeight' . $i]), $k2),
                    form_dropdown('head' . $i . "Weight3", populate($_SESSION['headWeight' . $i]), $k3),
                    form_dropdown('head' . $i . "Weight4", populate($_SESSION['headWeight' . $i]), $_SESSION['headWeight' . $i]));
        }
        echo $this->table->generate();
        ?>
    </div>
    <div>
        <input type="Submit" class="btn" value="Proceed to adding Questions"/>
        <input type="hidden" name="add_headings" value="yes">
    </div>
<?php
        echo form_close()
?>

        </div>
<?php
    }
}else {
    redirect('admin/index/1/1');
}

function total_check($str) { ////validation function
    if ($str == '1') {
        $this->form_validation->set_message('total_check', 'This value can\'t be 1 ' );
        return FALSE;
    } else {
        return TRUE;
    }
}

function populate($end) {
    $value = array();
    for ($i = 1; $i <= ($end + 1); $i++) {
        array_push($value, $i);
    }
    return $value;
}
?>

