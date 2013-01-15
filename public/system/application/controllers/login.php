<?php

class Login extends CI_Controller {

    public $data = array('main_content' => '', 'error' => '');

    function __construct() {
        parent::__construct();
        $this->load->library('session');
    }

    function index() {
        $this->data['main_content'] = 'login_form';
        $this->load->view('login_form', $this->data);
    }

    function validate_credentials() {
        global $data;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->data['main_content'] = 'login_form';
            $this->load->view('login_form', $this->data);
        } else {
            $this->load->model('membership_model');
            $user = $this->membership_model->validate();
            if (count($user) > 1) {
                $this->session->set_userdata('user_id', $user['id']);
                $this->session->set_userdata('isAdmin', $user['isAdmin']);
                $this->session->set_userdata('isReviewer', $user['isReviewer']);
                $this->session->set_userdata('isApprover', $user['isApprover']);
                $data = array('username' => $this->input->post('username'), 'is_logged_in' => true);
                $this->session->set_userdata($data);
                echo "<script type=text/javascript>alert(" . $user['isAdmin'] . ")</script>";
                if ($user['isAdmin'] == "YES")
                    redirect('admin');
                else {
                    redirect('emp');
                }
            } else {
                $this->data['error'] = "Wrong Username and Password";
                $this->data['main_content'] = 'login_form';
                $this->load->view('login_form', $this->data);
            }
        }
    }

    function signup() {
        $this->data['main_content'] = 'signup_form';
        $this->load->view('signup_form', $this->data);
    }

    function create_member() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('first_name', 'Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
        $this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->data['main_content'] = 'signup_form';
            $this->load->view('signup_form', $this->data);
        } else {
            $this->load->model('membership_model');
            if ($query = $this->membership_model->create_member()) {
                $this->data['main_content'] = 'signup_successful';
                $this->load->view('signup_successful', $this->data);
            } else {
                $this->data['main_content'] = 'signup_form';
                //$data['error']='Could not create an account';
                $this->load->view('signup_form', $this->data);
            }
        }
    }

}

?>
