<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Game_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function add($game_array) {
        if ($this->db->insert('scores', $game_array)) {
            return $this->db->insert_id();
        }
        return 0;
    }

    public function get_score_card() {
        return $this->db->select('user_name, SUM(user_won) AS user_hands_won')->group_by('user_name')->get('scores')->result_array();
    }

}
