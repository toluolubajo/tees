        <base href="<?= $this->config->item('base_url') ?>" />
        <script type="text/javascript" src="assets/js/swfobject.js"></script>
<h1><?= $page_title ?></h1>
<script type="text/javascript">
    swfobject.embedSWF(
    "assets/swf/open-flash-chart.swf", "test_chart",
    "<?= $chart_width ?>", "<?= $chart_height ?>",
    "9.0.0", "expressInstall.swf",
    {"data-file":"<?= urlencode($data_url) ?>"}
);
</script>
<div id="test_chart" style=""></div>
