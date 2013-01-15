<?php

class AjaxController extends CI_Controller {

    function deleteKPI($id) {
        echo $id;
        $this->db->delete('kpi', array('id' => $id));
    }

    function updateKPI($id, $title) {
        $title=rawurldecode($title);
        $data = array(
            'title' => $title
        );
        $this->db->where('id', $id);
        $this->db->update('kpi', $data);
        echo $id . " " . $title;
    }

    function index() {
        echo "hello world";
    }

}

?>
