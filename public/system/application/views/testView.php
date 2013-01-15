<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <base href="<?= $this->config->item('base_url') ?>" >
        <script type="text/javascript" src="assets/js/json2.js"></script>
        <script type="text/javascript" src="assets/js/swfobject.js"></script>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
         <script type="text/javascript">
            swfobject.embedSWF(
            "assets/swf/open-flash-chart.swf", "div_chart_1",
            "450", "300", "9.0.0", "expressInstall.swf",
            {"get-data":"get_data_1"} );
            function get_data_1()
            {
                return JSON.stringify(data_1);
            }
<?php if (isset($graphJSON)
        )$graphJSON1 = $graphJSON;
    else
        $graphJSON=""; ?>
                 var data_1 = <?php echo $graphJSON; ?>;
        </script>
           </head>
    <body>
        <?php
        // put your code here
        ?>
        <div id="div_chart_1"></div>
    </body>
</html>
