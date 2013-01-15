
<?php
//
// This is the MODEL section:
//

$this->load->helpers('ofc2');
$this->load->helper('url_helper');
$title = new title(date("D M d Y"));

$bar = new bar();
$bar->set_values(array(9, 8, 7, 6, 5, 4, 3, 2, 1));

$chart_1 = new open_flash_chart();
$chart_1->set_title($title);
$chart_1->add_element($bar);

//$data_1 = $chart_1->toPrettyString();
// generate some random data
srand((double) microtime() * 1000000);

$tmp = array();
for ($i = 0; $i < 9; $i++)
    $tmp[] = rand(1, 10);

$bar_2 = new bar();
$bar_2->set_values($tmp);

$chart_2 = new open_flash_chart();
$chart_2->set_title(new title("Chart 2"));
$chart_2->add_element($bar_2);

//$data_2 = $chart_2->toPrettyString();
//
// This is the VIEW section:
//
$title = new title('Consolidated Performance');
$pie = new pie();
$pie->set_alpha(0.6);
$pie->set_start_angle(35);
$pie->add_animation(new pie_fade());
$pie->set_tooltip('#val# of #total#<br>#percent# of 100%');
$pie->set_colours(array('#ff0033', '#ffff33', '#66ff66'));
$pie->set_values(array(2, 3, 4, new pie_value(6.5, "hello (6.5)")));
$chart_3 = new open_flash_chart();
$chart_3->set_title($title);
$chart_3->add_element($pie);
$chart_3->set_bg_colour('#eeeeee');
$chart_3->x_axis = null;
//$data_3 = $chart_3->toPrettyString();
?>

<html>
    <head>

        <base href="<?= $this->config->item('base_url') ?>" />
        <script type="text/javascript" src="assets/js/json2.js"></script>
        <script type="text/javascript" src="assets/js/swfobject.js"></script>
        <script type="text/javascript">
            swfobject.embedSWF(
            "assets/swf/open-flash-chart.swf", "div_chart_1",
            "300", "300", "9.0.0", "expressInstall.swf",
            {"get-data":"get_data_1"} );

            swfobject.embedSWF(
            "assets/swf/open-flash-chart.swf", "div_chart_2",
            "450", "300", "9.0.0", "expressInstall.swf",
            {"get-data":"get_data_2"} )

            swfobject.embedSWF(
            "assets/swf/open-flash-chart.swf", "div_chart_3",
            "450", "300", "9.0.0", "expressInstall.swf",
            {"get-data":"get_data_3"} )



            function ofc_ready()
            {
                alert('ofc_ready');
            }


            function get_data_1()
            {
                alert( 'reading data 1' );
                return JSON.stringify(data_1);
            }

            function get_data_2()
            {
                alert( 'reading data 2' );
                alert(JSON.stringify(data_2));
                return JSON.stringify(data_2);
            }
            function get_data_3()
            {
                alert( 'reading data 3' );
                alert(JSON.stringify(data_3));
                return JSON.stringify(data_3);
            }


            var data_1 = <?php echo $chart_1->toPrettyString(); ?>;
            var data_2 = <?php echo $chart_2->toPrettyString(); ?>;
            var data_3 = <?php echo $chart_3->toPrettyString(); ?>;

        </script>


    </head>
    <body>
        <p>Open Flash Chart</p>


        <div id="div_chart_1"></div>
        <div id="div_chart_2"></div>
        <div id="div_chart_3"></div>

        <br>
        <a href="javascript:load_1()">display data_1</a> || <a href="javascript:load_2()">display data_2</a>
        <p>
            Don't forget to 'view source' to see how the Javascript JSON data is loaded.
        </p>
    </body>
</html>