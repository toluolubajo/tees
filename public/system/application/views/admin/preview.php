<?php
///p=1 a=4 ===>Preview
$numOfHeads = 0;
$numOfHeads = $_SESSION['headNum'];
?>
<div>
    <?php echo "<h4 style='color:red'>" . validation_errors() . "</h4>"; ?>
    <h2><span style="color:black">Title : </span><?php echo $_SESSION['title']; ?></h2>
    <?php
    $attributes = array('class' => 'formLayout', 'name' => 'preview');
    echo form_open('admin/index/1/6', $attributes);
    $html = "";
    for ($i = 1; $i <= $numOfHeads; ++$i) {
        echo "<h2>" . $_SESSION['head' . $i] . "</h2><ul>";
        for ($j = 1; $j <= $_SESSION['H' . $i . 'numofQ']; $j++) {
            $html .= ' <li>' . $_SESSION['H' . $i . 'Q' . $j] . '</li><br>';
        }$html.='</ul><hr>';
        echo $html;
        $html = "";
    }
    ?>
    <input type="Submit" id="btnAddhead" class="btn" value="Save and Exit" style="margin-top: 30px;"/>
    <input type="hidden" name="add_headings" value="yes">

    <?php form_close() ?>
</div>
