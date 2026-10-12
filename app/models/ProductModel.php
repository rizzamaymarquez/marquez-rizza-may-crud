<?php
defined('PREVENT_DIRECT_SCRIPT_ACCESS') OR exit('No direct script access allowed');

class ProductModel extends Model {

    public function get_all_products() {
        return $this->db->table('products')->get_all();
    }

    public function get_product($id) {
        return $this->db->table('products')->where('id', $id)->get();
    }

    public function insert_product($data) {
        return $this->db->table('products')->insert($data);
    }

    public function update_product($id, $data) {
        return $this->db->table('products')->where('id', $id)->update($data);
    }

    public function delete_product($id) {
        return $this->db->table('products')->where('id', $id)->delete();
    }
}
?>