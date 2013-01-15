<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Graph extends CI_Controller{
    public $scores=array();
    public $headings=array();
    function __construct()
	{
        parent::__construct();
        $this->load->helper('url_helper');
        $this->load->model('view_qnaire_model');
	}
    function setData($answer,$qnaireId){
        $heads=array();
        $headings = $this->view_qnaire_model->get_qnaire_headings($qnaireId);
        foreach($headings as $row){
           // echo $row->title."<br>";
            array_push($heads,$row->title);
        }
        $this->headings=$heads;
        $this->scores=$this->view_qnaire_model->calculateScores($answer,$qnaireId);

    }
    function index(){        
        $this->setData('22233', 98);       
         $this->show_chart('bar');
    }
    public function show_chart($chart_type)
    {
        // available chart types
        $chart_types = array('line', 'bar', 'pie', 'area', 'scatter');

        // build links for display
        $links = array();
        foreach ($chart_types as $type)
        {
            $links[$type] = site_url('graph/show_chart/' . $type);
        }

        // clean chart type and build view variables
        $chart_type = strtolower(trim($chart_type));
        if (in_array($chart_type, $chart_types))
        {
            $data = array(
                            'chart_height'  => 400,
                            'chart_width'   => '80%',
                            'data_url'      => site_url('graph/get_data_'
                                                         . $chart_type),
                            'page_title'    => ucwords('OFC2 Plugin - '
                                                        . $chart_type
                                                        . ' chart'),
                            'links'         => $links
                         );

            $this->load->view('graph_view', $data);
        }
        else
        {
            show_error('Bad chart type, try: ' . implode(', ', $chart_types));
        }
    }

    /**
     * Generates data for OFC2 bar chart in json format
     *
     * @return void
     */
    public function get_data_bar()
    {
        $scores=$this->scores=$this->view_qnaire_model->calculateScores('22233',98);
        //print_r($scores);
        $this->load->helpers('ofc2');
        $title = new title( date("D M d Y") );
        $bar = new bar();
        $bar->set_values($scores);
        $chart = new open_flash_chart();
        $chart->set_title( $title );
        $chart->add_element( $bar );
        $x = new x_axis();
        
        
        $chart->set_x_axis( $x );

        $y = new x_axis();
        $y->set_range( 0, 100 );
        $y->set_steps( 10 );
        $chart->add_y_axis( $y );
        echo $chart->toPrettyString();
    }

    /**
     * Generates data for OFC2 line chart in json format
     *
     * @return void
     */
    public function get_data_line()
    {
        $this->load->helpers('ofc2');

        $data_1 = array();
        $data_2 = array();
        $data_3 = array();

        for( $i=0; $i<6.2; $i+=0.2 )
        {
        $data_1[] = (sin($i) * 1.9) + 7;
        $data_2[] = (sin($i) * 1.9) + 10;
        $data_3[] = (sin($i) * 1.9) + 4;
        }

        $title = new title( date("D M d Y") );

        $d = new hollow_dot();
        $d->size(5)->halo_size(0)->colour('#3D5C56');

        $line_1 = new line();
        $line_1->set_default_dot_style($d);
        $line_1->set_values( $data_1 );
        $line_1->set_width( 2 );
        $line_1->set_colour( '#3D5C56' );

        $d = new hollow_dot();
        $d->size(4)->halo_size(1)->colour('#668053');

        $line_2 = new line();
        $line_2->set_values( $data_2 );
        $line_2->set_default_dot_style($d);
        $line_2->set_width( 1 );
        $line_2->set_colour( '#668053' );

        $d = new hollow_dot();
        $d->size(4)->halo_size(1)->colour('#C25030');

        $line_3 = new line();
        $line_3->set_values( $data_3 );
        $line_3->set_default_dot_style($d);
        $line_3->set_width( 6 );
        $line_3->set_colour( '#C25030' );

        $y = new y_axis();
        $y->set_range( 0, 15, 5 );


        $chart = new open_flash_chart();
        $chart->set_title( $title );
        $chart->add_element( $line_1 );
        $chart->add_element( $line_2 );
        $chart->add_element( $line_3 );
        $chart->set_y_axis( $y );

        echo $chart->toPrettyString();
    }

    /**
     * Generates data for OFC2 pie chart in json format
     *
     * @return void
     */
    public function get_data_pie()
    {
        $scores=$this->scores=$this->view_qnaire_model->calculateScores('22233',98);
        $this->load->helpers('ofc2');
        $title = new title( 'Pork Pie, Mmmmm' );
        $pie = new pie();
        $pie->set_alpha(0.6);
        $pie->set_start_angle( 35 );
        $pie->add_animation( new pie_fade() );
        $pie->set_tooltip( '#val# of #total#<br>#percent# of 100%' );
        $pie->set_colours( array('#1C9E05','#FF368D') );
        $pie->set_values( array(2,3,4,new pie_value(6.5, "hello (6.5)")) );
        //$pie->set_values($scores);
        $chart = new open_flash_chart();
        $chart->set_title( $title );
        $chart->add_element( $pie );
        $chart->x_axis = null;
        echo $chart->toPrettyString();
    }

    /**
     * Generates data for OFC2 area chart in json format
     *
     * @return void
     */
    public function get_data_area()
    {
        $this->load->helpers('ofc2');

        $data = array();

        for( $i=0; $i<6.2; $i+=0.2 )
        {
            $tmp = sin($i) * 1.9;
            $data[] = $tmp;
        }
        

        $chart = new open_flash_chart();
        $chart->set_title( new title( 'Area Chart' ) );

        //
        // Make our area chart:
        //
        $area = new area();
        // set the circle line width:
        $area->set_width( 2 );
        $area->set_default_dot_style( new dot() );
        $area->set_colour( '#838A96' );
        $area->set_fill_colour( '#E01B49' );
        $area->set_fill_alpha( 0.7 );
        $area->set_values( $data );

        // add the area object to the chart:
        $chart->add_element( $area );

        $y_axis = new y_axis();
        $y_axis->set_range( -2, 2, 2 );
        $y_axis->labels = null;
        $y_axis->set_offset( false );

        $x_axis = new x_axis();
        $x_axis->labels = $data;
        $x_axis->set_steps( 2 );

        $x_labels = new x_axis_labels();
        $x_labels->set_steps( 4 );
        $x_labels->set_vertical();
        // Add the X Axis Labels to the X Axis
        $x_axis->set_labels( $x_labels );

        $chart->add_y_axis( $y_axis );
        $chart->x_axis = $x_axis;

        echo $chart->toPrettyString();
    }

    /**
     * Generates data for OFC2 scatter chart in json format
     *
     * @return void
     */
    public function get_data_scatter()
    {
        $this->load->helpers('ofc2');

        $chart = new open_flash_chart();

        $title = new title( date("D M d Y") );
        $chart->set_title( $title );

        $s = new scatter_line( '#DB1750', 3 );
        $def = new hollow_dot();
        $def->size(3)->halo_size(2);
        $s->set_default_dot_style( $def );
        $v = array();

        $x = 0.0;
        $y = 0;
        do
        {
            $v[] = new scatter_value( $x, $y );

            // move up or down in Y axis:
            $y += rand(-20, 20)/10;
            if( $y > 10 )
                $y = 10;

            if( $y < -10 )
                $y = -10;

            $x += rand(5, 15)/10;
        }
        while( $x < 25 );

        $s->set_values( $v );
        $chart->add_element( $s );

        $x = new x_axis();
        $x->set_range( 0, 25 );
        $chart->set_x_axis( $x );

        $y = new x_axis();
        $y->set_range( -10, 10 );
        $chart->add_y_axis( $y );


        echo $chart->toPrettyString();
    }
}
?>
