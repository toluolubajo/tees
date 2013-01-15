<?php
$numOfHeads = $_SESSION['headNum'];
for ($i = 1; $i <= $numOfHeads; ++$i) {
    if (isset($_POST['head' . $i])) {
        $_SESSION['head' . $i] = $_POST['head' . $i];
    }
}
?>

<h2><span style="color:black">Title : </span><?php echo $_SESSION['title']; ?></h2>

<?php
$attributes = array('class' => 'formLayout', 'name' => 'addQuestNumForHead');
echo form_open('admin/index/1/4', $attributes);
?>

<?php
$head = "";
$k = "";
for ($i = 1; $i <= $numOfHeads; ++$i) {
    if (isset($_SESSION['H' . $i . 'numofQ'])
        )$k = $_SESSION['H' . $i . 'numofQ'];
    $head = $head . '<div><label>' . $_SESSION['head' . $i] . '</label>' . form_dropdown('H'.$i.'numofQ', populate(), $k) . '</div>';      
}
  echo $head;
?>    
<div>
    <input type="Submit" class="btn" value="Proceed to adding Questions" style="margin-top: 30px;"/>
    <input type="hidden" name="add_headings" value="yes">
</div>
<?php
echo form_close();

function populate() {
    $value = array();
    for ($i = 1; $i < 30; $i++) {
        array_push($value, $i);
    }
    return $value;
}
?>


