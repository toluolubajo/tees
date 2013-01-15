<script type="text/javascript">
            swfobject.embedSWF(
            "assets/swf/open-flash-chart.swf", "div_chart_1",
            "600", "500", "9.0.0", "expressInstall.swf",
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
        <?php
        // put your code here
        ?>
        <div id="div_chart_1"></div>