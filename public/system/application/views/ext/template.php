<?php
$data['title']= $content['title'];
$this->load->view('/ext/header', $data);
?>

<?php $this->load->view($main_content, $content); ?>

<?php $this->load->view('/ext/footer'); ?>
