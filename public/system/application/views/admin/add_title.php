<div>
    <?php
    $this->form_validation->set_rules('title', 'Title', 'required');
    if ($this->form_validation->run() == TRUE) {
        if (isset($_POST['title'])) {
            $_SESSION['title'] = $_POST['title'];
        }
        if (isset($_POST['headNum'])) {
            $_SESSION['headNum'] = ($_POST['headNum'] + 1);
        }
        redirect('admin/index/1/1');
    } else {
    ?>  
    <?php
        $attributes = array('class' => 'formLayout', 'name' => 'addTitle');
        echo form_open('admin/index/1/0', $attributes); ?>
    <?php echo "<h4 style='color:red'>" . validation_errors() . "</h4>"; ?>
        <div>
        <?php
        if (isset($_SESSION['title'])) {
            $title = $_SESSION['title'];
        } else {
            $title = 'Please Enter Title';
        }
        ?>
        <label for="title">Enter Questionnaire Title:<em class="required">(required)</em>
        </label><?php
        $data = array(
            'name' => 'title',
            'id' => 'title',
            'value' => set_value('title', $title),
            'maxlength' => '100',
            'style' => 'width:300px',
        );
        echo form_input($data);
        ?>
    </div>
    <br>
    <br>
    <div>
        <label for="headNum">How many key performance indicator do you wish to include:
            <em class="required">(required)</em></label>
        <?php
        $headNum1 = "";
        if (isset($_SESSION['headNum'])) {
            $headNum1 = $_SESSION['headNum'];
        } else {
            $headNum1 = 1;
        }
        //populateSelectBox($headNum1);
        echo form_dropdown('headNum', populate(), $headNum1);
        ?>

    </div>
    <div>
        <input type="Submit"  class="btn" value="Proceed to adding headings" style="margin-top: 30px; width: 200px;"/>
        <input type="hidden" name="add_title" value="yes">
    </div>

    <?php echo form_close();
    } ?>

</div>
<?php

    function populate() {
        $value = array();
        for ($i = 1; $i <= 30; $i++) {
            array_push($value, $i);
        }
        return $value;
    }
?>

