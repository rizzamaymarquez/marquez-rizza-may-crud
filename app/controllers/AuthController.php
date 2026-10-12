<?php
defined('PREVENT_DIRECT_SCRIPT_ACCESS') OR exit('No direct script access allowed');

class AuthController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->call->database();
        $this->call->model('AuthModel');
        $this->call->library('session');
    }

    // Show Login Form
    public function login() {
        if ($this->session->userdata('logged_in')) {
            redirect('products');
        }
        $this->call->view('auth/login');
    }

    // Process Login Submission
    public function authenticate() {
        $username = $this->io->post('username');
        $password = $this->io->post('password');

        $user = $this->AuthModel->get_user_by_username($username);

        // Supports plain text (admin123) and hashed passwords
        if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
            $this->session->set_userdata([
                'user_id'   => $user['id'],
                'username'  => $user['username'],
                'role'      => $user['role'] ?? 'user',
                'logged_in' => TRUE
            ]);
            redirect('products');
        } else {
            $this->session->set_flashdata('error', 'Invalid username or password!');
            redirect('login');
        }
    }

    // Logout Action
    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }
}
?>