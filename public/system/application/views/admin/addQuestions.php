<?php
if (isset($_SESSION['headNum'])) {
    $numOfHeads = 0;
    $numOfHeads = $_SESSION['headNum'];
    for ($i = 1; $i <= $numOfHeads; ++$i) {
        if (isset($_POST['H' . $i . 'numofQ'])

            )$_SESSION['H' . $i . 'numofQ'] = ($_POST['H' . $i . 'numofQ'] + 1);
    }
    for ($i = 1; $i <= $numOfHeads; ++$i) {
        for ($j = 1; $j <= $_SESSION['H' . $i . 'numofQ']; $j++) {
            $this->form_validation->set_rules('H' . $i . 'Q' . $j, 'All text area', 'required');
        }
    }
    
    if ($this->form_validation->run() == TRUE) {
        for ($i = 1; $i <= $numOfHeads; ++$i) {
            for ($j = 1; $j <= $_SESSION['H' . $i . 'numofQ']; $j++) {
                if (isset($_POST['H' . $i . 'Q' . $j])

                    )$_SESSION['H' . $i . 'Q' . $j] = $_POST['H' . $i . 'Q' . $j];
            }
        }
        redirect('admin/index/1/5');
    }
    
else {
?>
    <h2><span style="color:black">Title : </span><?php echo $_SESSION['title']; ?></h2>
<?php
echo "<h4 style='color:red'>" . validation_errors() . "</h4>";
    $attributes = array('class' => 'formLayout', 'name' => 'addQuestions');
    echo form_open('admin/index/1/4', $attributes);
    $head = "";
    for ($i = 1; $i <= $numOfHeads; ++$i) {
        $head.='<br><br><label class="heading"><h3>' . $_SESSION["head" . $i] . '</h3>';
        //echo $_SESSION['H' . $i . 'numofQ'];
        for ($j = 1; $j <= $_SESSION['H' . $i . 'numofQ']; ++$j) {
            if (isset($_SESSION['H' . $i . 'Q' . $j])

                )$k = $_SESSION['H' . $i . 'Q' . $j];else
                $k="";
            $entry = array(
                'name' => 'H' . $i . 'Q' . $j,
                'value' => $k,
                'rows' => '4',
                'cols' => '30'
            );
            $head.='<div><label class="question">Question' . $j . ' :- </label>' . form_textarea($entry) . '</div>';
        }
    }
    echo $head;
?>
    <input type="Submit" id="btnAddhead" class="btn" value="Preview"/>
    <input type="hidden" name="add_headings" value="yes">
<?php form_close(); ?>
    <br/>
    <br/>
    <br/>
    <br/>

<?php } }else{
    redirect('admin/index/1/4');
}
    ?>



