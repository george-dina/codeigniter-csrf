<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Security extends CI_Security {

    public $session;
    protected $CI;
    protected $_csrf_token_name;
    protected $_csrf_cookie_name;
    protected $_real_csrf_token_name;
    protected $_csrf_protection;
    protected $_real_csrf_protection;

    public function __construct()
    {
        parent::__construct();
        $this->CI =& get_instance();

        $this->_csrf_token_name = $this->CI->security->get_csrf_token_name();
        $this->_csrf_protection = config_item('csrf_protection');
        $this->_csrf_cookie_name = config_item('csrf_cookie_name');
        $this->_real_csrf_protection = config_item('real_csrf_protection');
        $this->_real_csrf_token_name = 'kept_' . $this->_csrf_token_name;
    }

    public function csrf_verify()
    {
        if ($this->_real_csrf_protection) {

            if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
                return $this->csrf_match_cookie();
            }

            if (!$this->csrf_token_match()) {
                $this->csrf_show_error();
            }

            // invalidate after the first request
            $this->invalidate_server_side_token();
        }
    }


    protected function csrf_token_match()
    {
        // Retrieve the server-side token from session
        $server_side_token = $this->CI->session->userdata('csrf_token');

        // Compare it with the submitted token
        // return $server_side_token === $input->post($this->_csrf_token_name);
        return $server_side_token === $_POST[$this->_real_csrf_token_name];
    }


    /**
     * Get the previous hash set by Security class
     * right after initialization and store it in the session. The session
     * will be reset after each POST request.
     * @return
     */
    public function csrf_match_cookie()
    {
        $token = $this->CI->security->get_csrf_hash();
        $this->CI->session->set_userdata('csrf_token', $token);
    }


    private function invalidate_server_side_token()
    {
        $this->CI->session->unset_userdata('csrf_token');
    }


    /**
     * Replicate some of the random string functionality in order
     * not to load ahead of of the time helper section
     * @param  integer $len
     * @return string
     */
    protected function random_string($len = 8)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
    }
}
