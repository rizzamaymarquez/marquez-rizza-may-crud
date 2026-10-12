<?php
//defined('PREVENT_DIRECT_SCRIPT_ACCESS') OR defined('ROOT_DIR') OR exit('No direct script access allowed');

class ProductController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->call->database();
        $this->call->model('ProductModel');
        $this->call->library('session');

        if (!$this->session->userdata('logged_in')) {
            if ($this->io->is_ajax()) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
                exit;
            }
            redirect('login');
        }
    }

    private function require_admin() {
        if (($this->session->userdata('role') ?? 'user') !== 'admin') {
            if ($this->io->is_ajax()) {
                echo json_encode(['status' => 'error', 'message' => 'Forbidden: admin access required']);
                exit;
            }
            redirect('products');
        }
    }

    // Load main view
    public function index() {
        $this->call->view('products/index');
    }

    // JSON Endpoint para sa table data
    public function get_all() {
        echo json_encode($this->ProductModel->get_all_products());
    }

    // Create via AJAX
    public function create() {
        $this->require_admin();
        $data = [
            'product_name' => $this->io->post('product_name'),
            'description'  => $this->io->post('description'),
            'price'        => $this->io->post('price'),
            'quantity'     => $this->io->post('quantity')
        ];
        $saved = $this->ProductModel->insert_product($data);
        echo json_encode($saved
            ? ['status' => 'success', 'message' => 'Product added successfully.']
            : ['status' => 'error', 'message' => 'Product was not saved.']);
    }

    // Update via AJAX
    public function edit($id) {
        $this->require_admin();
        $data = [
            'product_name' => $this->io->post('product_name'),
            'description'  => $this->io->post('description'),
            'price'        => $this->io->post('price'),
            'quantity'     => $this->io->post('quantity')
        ];
        $updated = $this->ProductModel->update_product($id, $data);
        echo json_encode($updated
            ? ['status' => 'success', 'message' => 'Product updated successfully.']
            : ['status' => 'error', 'message' => 'Product was not updated.']);
    }

    // Delete via AJAX
    public function delete($id) {
        $this->require_admin();
        $deleted = $this->ProductModel->delete_product($id);
        echo json_encode($deleted
            ? ['status' => 'success', 'message' => 'Product deleted successfully.']
            : ['status' => 'error', 'message' => 'Product was not deleted.']);
    }
}
?>