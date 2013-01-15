<?php

class Blog extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
    }

    function index() {
        $data['title'] = "My Blog Title";
        $data['heading'] = "My Blog Heading";
        $data['query'] = $this->db->get('entries');
         $this->load->model('blog_model');
         $data['counts']=$this->blog_model->getComments();
         $this->load->view('blog_view', $data);

    }

    function comments() {
        $data['title'] = "My Comments Title";
        $data['heading'] = "My Comments Heading";
        $this->db->where('entry_id', $this->uri->segment(3));
        $data['query'] = $this->db->get('comments');
        $this->load->view('comments_view', $data);
    }
    function comment_insert() {
        $this->db->insert('comments', $_POST);
        redirect('blog/comments/' . $_POST['entry_id']);
    }

}

?>
