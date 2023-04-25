<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Preserve_post extends CI_Config {

    protected $_real_csrf_protection;
    protected $_csrf_token_name;

    public function __construct()
    {
        parent::__construct();

        $this->_real_csrf_protection = config_item('real_csrf_protection');
        $this->_csrf_token_name = config_item('csrf_token_name');
        $this->_csrf_protection = config_item('csrf_protection');
    }

    public function keep_csrf_fields()
    {
        if ($this->_csrf_protection && $this->_real_csrf_protection) {
            if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
                if (array_key_exists($this->_csrf_token_name, $_POST)) {
                    $_POST['kept_' . $this->_csrf_token_name] = $_POST[$this->_csrf_token_name];
                }
            }
        }
    }
}
