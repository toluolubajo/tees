<html>
    <?php session_start(); ?>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="description" content="#" />
        <meta name="keywords" content="#" />
        <meta name="author" content="#" />
        <base href="<?= $this->config->item('base_url') ?>" >
        <script type="text/javascript" src="assets/js/json2.js"></script>
        <script type="text/javascript" src="assets/js/swfobject.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/reset.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/style.css" media="screen,print" />
        <script type="text/javascript" src="<?php echo base_url(); ?>js/jQuery.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-ui.js"></script>
        <script type="text/javascript" src="jquery-dynamic-form.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>js/tiny_mce/tiny_mce.js"></script>
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/base/jquery.ui.tabs.css">                
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/base/demo.css">
        <script type="text/javascript" src="<?php echo base_url(); ?>js/ui/jquery.ui.core.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>js/ui/jquery.ui.widget.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>js/ui/jquery.ui.tabs.js"></script>
        <script type="text/javascript">
            tinyMCE.init({
                mode : "textareas",
                theme : "simple"
            });
        </script>
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
        <title><?php echo $title ?></title>
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
        $graphJSON1=""; ?>
                 var data_1 = <?php echo $graphJSON1; ?>;
        </script>
        <script type="text/javascript">
            $(function() {
                $( "#tabs" ).tabs({
                    collapsible: true
                });
            });
        </script>
    </head>
</head>
<body>
