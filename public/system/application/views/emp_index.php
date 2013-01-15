<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="description" content="#" >
        <meta name="keywords" content="#" >
        <meta name="author" content="#" >
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/reset.css" media="screen" >
       <base href="<?= $this->config->item('base_url') ?>" >
        <script type="text/javascript" src="assets/js/json2.js"></script>
        <script type="text/javascript" src="assets/js/swfobject.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/emp_style.css" media="screen,print" >
        <script type="text/javascript" src="<?php echo base_url(); ?>js/jQuery.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>js/tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript">
            tinyMCE.init({
                mode : "textareas",
                theme : "simple"
            });
        </script>
        <script type="text/javascript" src="jquery-dynamic-form.js"></script>
        <!--[if IE 6]>
        <style>
        #centerColumn {
        float:left;
        margin:0 10px 0 75px;
        padding:6px 6px 6px 10px;
        width:580px;
        font-size:.9em;
        color:#000000;
        border-left:1px dashed #cccccc;
        }
        </style>
        <![endif]-->    
        <title><?php echo $content['title']?></title>

        <script type="text/javascript">
            swfobject.embedSWF(
            "assets/swf/open-flash-chart.swf", "div_chart_1",
            "500", "300", "9.0.0", "expressInstall.swf",
            {"get-data":"get_data_1"} );
            function get_data_1()
            {               
                return JSON.stringify(data_1);
            }
            var data_1 = <?php echo $content['barData']; ?>;
        </script>
    </head>
    <body>
        
        <div id="header">            
            <h1>Quality Control Information System</h1>
            <div id="login"> <h4><?php echo anchor('http://localhost/ci2/index.php/emp/logout/', 'Logout', 'title="Logout"'); ?></h4></div>
        </div>
        <div id="contentArea">

            <div id="topNav">
                <div id="topNavLeft">
                    <?php echo $content['leftNavbar'] ?>
                </div>
                <div id="topNavRight">
                    <form action="" method="post">
                        <input name="searchword" maxlength="20"  class="inputbox" type="text" size="20" value="Search..." onblur="if (this.value=='')this.value='Search...';" onfocus="if (this.value=='Search...') this.value='';">
                        <input type="hidden" name="task" value="search">
                        <input type="hidden" name="option" value="com_search" >
                        <input type="hidden" name="Itemid" value="243" ></form>
                </div>
            </div>

            <div id="mainContent">
                <div id="leftContent">
                    <div id="LCheading">
                        <h2>
                            <?php echo $content['headLC'] ?></h2>
                        <hr>
                    </div>
                    <div>
                        <?php
                        if(!($content['leftContent']=='dashboard')){
                           $this->load->view('/emp/' . $content['leftContent'] . ".php");
                        }
                        if(($content['leftContent']=='dashboard')){
                            echo '<div id="div_chart_1"></div>';
                           $this->load->view('/emp/' . $content['leftContent'] . ".php");
                        }
                        //echo $content['barData'];
                       ?>                        
                        </div>
                    
                    </div>
                    <div id="rightContent">
                        <div id="rcTop">
                            <h2><b>Notices</b></h2><hr>
                        <?php $this->load->view('/emp/' . $content['rightTop'] . ".php") ?>
                    </div>

                </div>
            </div>            
        </div>
         <div id="footer"style="background-color:#E9E9E9;">
            <div id="leftFooter">
                <address style="padding:10px; font-size:1em; color:#454545;">
					University of Bradford, <br/>
					Bradford, <br/>
					West Yorkshire, <br/>
					BD7 1DP, <br/>
					UK<br/><br/>
					Tel: +44 (0) 1274 232323
                </address>
                <ul>
                    <li>&copy; University of Bradford 2011</li>
                    <li><a href="/about/web-accessibility/">Accessibility</a></li>
                    <li><a href="/foi/">Freedom of Information Act</a></li>
                </ul>


            </div>            
        </div><!--// end #footer //-->

    </body>
</html>
<script type="text/javascript" charset="utf-8">
    $('th').css('background','#999999');
    $('tr:odd').css('background','#cccccc');
</script>"