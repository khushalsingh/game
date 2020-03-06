<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends CI_Controller {

    public $cards = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Game_model');
    }

    public function index() {
        $this->load->view('game');
    }

    public function validate_cards() {
        if ($this->input->post()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('user_cards', 'Cards', 'trim|required');
            $this->form_validation->set_error_delimiters('', '<br />');
            if ($this->form_validation->run()) {
                $user_cards_array = explode(' ', $this->input->post('user_cards'));
                if (count($user_cards_array) > 0) {
                    foreach ($user_cards_array as $card) {
                        if (!in_array($card, $this->cards)) {
                            die('false');
                        }
                    }
                    die('true');
                }
            }
            die('false');
        }
    }

    public function play() {
        if ($this->input->post()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('user_name', 'Name', 'trim|required');
            $this->form_validation->set_rules('user_cards', 'Cards', 'trim|required');
            $this->form_validation->set_error_delimiters('', '<br />');
            if ($this->form_validation->run()) {
                $user_cards_array = explode(' ', $this->input->post('user_cards'));
                $generated_card_keys = array_rand($this->cards, count($user_cards_array));
                $generated_cards_array = [];
                foreach ($generated_card_keys as $generated_card_key) {
                    $generated_cards_array[] = $this->cards[$generated_card_key];
                }
                $user_score = 0;
                $generated_score = 0;
                foreach ($user_cards_array as $key => $card) {
                    if (array_keys($this->cards, $card) > array_keys($this->cards, $generated_cards_array[$key])) {
                        $user_score++;
                    }
                    if (array_keys($this->cards, $card) < array_keys($this->cards, $generated_cards_array[$key])) {
                        $generated_score++;
                    }
                }
                $user_won = ($user_score > $generated_score) ? '1' : '0';
                if ($this->Game_model->add(
                        [
                            'user_name' => $this->input->post('user_name'),
                            'user_score' => $user_score,
                            'hand_score' => $generated_score,
                            'user_won' => $user_won,
                            'created' => date('Y-m-d H:i:s')
                        ]
                    ) > 0) {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode(['errors' => '', 'generated_cards' => $generated_cards_array, 'user_won' => $user_won]));
                }
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['errors' => validation_errors(), 'generated_cards' => [], 'user_won' => '0']));
            }
            return;
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['errors' => '1', 'generated_cards' => [], 'user_won' => '0']));
    }

    public function score_card() {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(
                    $this->Game_model->get_score_card()
                )
        );
    }

}
