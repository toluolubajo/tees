<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Login</title>
        <link rel="stylesheet" href="<?php echo base_url();?>css/style2.css" type="text/css" media="screen"
              title="no title" charset="utf-8">
         <script src="<?php echo base_url(); ?>js/jQuery.js" type="text/javascript" charset="utf-8"></script>
    </head>
    <body>
 <div id="header">
        <h1>Quality Control Information System</h1>
    </div>
<div id="login_form">
    <h1>Login</h1>
    <?php
    $js='onblur="if(this.value==\'\')this.value=\'Username\';" onfocus="if(this.value==\'Username\') this.value=\'\';"';
    echo form_open('login/validate_credentials');
    echo form_input('username','Username',$js);
    $js2='onblur="if(this.value==\'\')this.value=\'Password\';" onfocus="if(this.value==\'Password\') this.value=\'\';"';
    echo form_password('password','Password',$js2);
    echo form_submit('submit','Login');
    echo anchor('login/signup','Create Account');
    ?>
    <?php echo validation_errors('<p class="error">');?>
    <?php  echo  '<br><p class="error">'.$error.'</p>';
     ?>
    
</div>
<?php $this->load->view('ext/tut_info');?>;
    </body>
</html>
