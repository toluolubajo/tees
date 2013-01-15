<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="description" content="#" />
        <meta name="keywords" content="#" />
        <meta name="author" content="#" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/view_qnaire.css" media="screen,print" />
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
        <title> <?php echo $emp_qnaire_data[2]; ?></title>
    </head>
    <body>
        <div id="header">            
            <h1 style="margin:0px; padding-top: 10px">Quality Control Information System</h1>
        </div>

        <div id="contentArea">

            <div id="topNav">
                <div id="topNavRight">
                    <form action="" method="post">
                        <input name="searchword" maxlength="20"  class="inputbox" type="text" size="20"
                               value="Search..." onblur="if (this.value=='')this.value='Search...';" onfocus="if (this.value=='Search...') this.value='';"/>
                        <input type="hidden" name="task" value="search"/>
                        <input type="hidden" name="option" value="com_search" />
                        <input type="hidden" name="Itemid" value="243" /></form>
                </div>
                <div id="topNavLeft">
                    <h2 style=" padding-left: 350px; color: white;font-size:large;">
                        Preview Questionnaire</h2>
                </div>
            </div>

            <div id="mainContent">                
                <div id="heading">
                    <h3> <?php echo $emp_qnaire_data[0]; ?><em><?php echo " - " . $emp_qnaire_data[2]." (" .$emp_qnaire_data[10]."%)" ?></em></h3><p></p>
                </div>
                <div id="content">
                    <?php
                    echo form_open();
                    $i = 0;
                    $selfAnswerArray = toArray($emp_qnaire_data[11]);
                    $reviewerAnswerArray = toArray($emp_qnaire_data[12]);
                    echo '<table class="mytable">';
                    echo '<tr><th>#</th><th>Questions</th><th>Reviewer Score</th><th>Self Score</th></tr> ';
                    $heads = array();
                    foreach ($headings as $row) {
                        array_push($heads, $row->title);
                    }
                    for ($j = 0; $j < count($questions); ++$j) {
                        if(isset($reviewerAnswerArray[$i]))$rev_Array_str=$reviewerAnswerArray[$i];
                        else{$rev_Array_str="";}
                        echo '<tr class="heading"><td class="align" colspan="2"><b>' . $heads[$j] . ' <b></td><td>('.$headScores[$j][0]. '%)</td><td>('.$headScores[$j][1]. '%)</td></tr> ';
                        for ($k = 0; $k < count($questions[$j]); ++$k) {
                            echo '<tr><td>' . ($k + 1) . '</td><td>' . $questions[$j][$k] . '</td>
                                <td class="score">' . getScore($rev_Array_str) . '</td><td class="score">' . getScore($selfAnswerArray[$i]) . '</td></tr> ';
                            $i++;
                        }
                    }
                    echo "</table>";
                    $attributes = array('class' => 'summary');
                    echo form_fieldset('<b>Summary<b>',$attributes);
                    echo "<h4>Key Strengths demonstrated</h4>";
                    echo "<p>".$emp_qnaire_data[13]."</p>";
                    
                        echo "<h4>Suggestions on how to achieve improved performance</h4>";
                    echo "<p>".$emp_qnaire_data[14]."</p>";
                    
                        echo "<h4>Your Comments</h4>";
                    echo "<p>".$emp_qnaire_data[15]."</p>";
                    
                    echo form_fieldset_close();
                    $js = 'onClick="window.close()"';
                    ?>
                    <div style="margin-left:450px">
                        <?php echo form_button('exit', 'Exit', $js) ?>
                    </div>
                    <br>
                </div>

                <?php
                        ;
                        echo form_close();
                ?>

                        <script type="text/javascript" charset="utf-8">
                            $('tr:even').css('background','#e3e3e3');
                            $('th').css('background','#eeeeee');
                            $('.heading').css('background','#abc6dd');
                        </script>
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
                            </address><br>
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

<?php

                        function getScore($val) {
                            $myVal = "";
                            switch ($val) {
                                case 1:$myVal = "EE";
                                    break;
                                case 2:$myVal = "ME";
                                    break;
                                case 3:$myVal = "MSE";
                                    break;
                                case 4:$myVal = "NME";
                                    break;
                                default:
                                    $myVal = "NA";
                            }
                            return $myVal;
                        }

                        function toArray($string) {
                            $str = $string;
                            $chars = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
//print_r($chars);
                            return $chars;
                        }
?>