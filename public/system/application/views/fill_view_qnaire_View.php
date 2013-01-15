<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="description" content="#" />
        <meta name="keywords" content="#" />
        <meta name="author" content="#" />        
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/style_1.css" media="screen,print" />
        <script type="text/javascript" src="<?php echo base_url(); ?>js/jQuery.js"></script>        
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
        <script type="text/javascript" src="<?php echo base_url(); ?>js/tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript">
            tinyMCE.init({
                mode : "textareas",
                theme : "simple"
            });
        </script>
        <title><?php echo $title ?></title>
    </head>
    <body>
        <div id="header">
            <div style="padding-right: 10px; padding-top: 10px; float : right">
                <p>
                    <?php echo "Welcome <i>" . $this->session->userdata('emp_name') . "</i>"; ?></p>
                <?php                
                if($this->session->userdata('isReviewer')=='YES'){                    
                    $this->load->model('view_qnaire_model');
                    if($this->session->userdata('reviewee')!=""){
                    echo "<p>Currently Reviewing <i>" .
                    $this->view_qnaire_model->get_empName($this->session->userdata('reviewee')) . "</i></p>";
                    }
                    echo '<p>'.anchor('http://localhost/ci2/index.php/emp/index/1', 'Logout').'</p>';
                }
                ?>
                </div>
                <h1 style="margin:0px; padding-top: 10px">Quality Control Information System</h1>
            </div>

            <div id="contentArea">

                <div id="topNav">
                    <div id="topNavRight">
                        <form action="" method="post">
                            <input name="searchword" maxlength="20"  class="inputbox" type="text" size="20" value="Search..." onblur="if (this.value=='')this.value='Search...';" onfocus="if (this.value=='Search...') this.value='';"/>
                            <input type="hidden" name="task" value="search"/>
                            <input type="hidden" name="option" value="com_search" />
                            <input type="hidden" name="Itemid" value="243" /></form>
                    </div>
                    <h2 style="text-align: center;color: white;font-size:large; margin-left: 50px;padding-top:5px;">
                        Fill/View Questionnaire</h2>
                </div>

                <div id="mainContent">
                    <!--            <div id="leftContent">                -->
                    <div id="leftContent">
                        <div id="topLeftNav">
<?php echo $leftNav ?>
                    </div>
                    <div id="bottomLeftNav">
                    </div>
                </div>

                <div id="rightContent">
                    <div id="rcHead">
<?php echo "<h2>" . $heading . "</h2>" ?>
                    </div>
                    <div id="rcContent">
                        <h4 style="color:red"><?php echo validation_errors(); ?></h4>
<?php echo $html; ?>
                    </div>
                </div>
            </div>
            <div id="footer">
                <div id="leftFooter">
                    <br>
                    <address style="padding:10px; font-size:1em; color:#454545;">
					University of Bradford, <br/>
					Bradford, <br/>
					West Yorkshire, <br/>
					BD7 1DP, <br/>
					UK<br/><br/>
					Tel: +44 (0) 1274 232323
                    </address> <br><br><br>
                    <ul style="margin-left:5px;padding:0px">
                        <li>&copy; University of Bradford 2011</li>
                        <li><a href="/about/web-accessibility/">Accessibility</a></li>
                        <li><a href="/foi/">Freedom of Information Act</a></li>
                    </ul>

                </div>
                <div id="centerFooter">

                </div>

                <div id="rightFooter"></div>
            </div><!--// end #footer //-->
        </div>

    </body>
</html>