<?php

class Login_Model extends Model {

    function __construct() {
        parent::__construct();
        echo '<p>Login model</p>';
        //session_start();
    }

    public function run() {
        $db_stmnt = $this->db->prepare(
                "SELECT id FROM users"
                . " WHERE login = :login"
                . " AND password = MD5(:password)");

        $db_stmnt->execute(array(
            ':login' => $_POST['login'],
            ':password' => $_POST['password'])
        );

        if (count($db_stmnt->fetchAll()) > 0) {
            Session::set('loggedIn', true);
            Session::set('user', $_POST['login']);
            header('location: ' . URL . 'login');
            echo "<p>LOGGED IN</p>";
        } else {
            header('location: ' . URL . 'login');
            echo "<p>REJECTED</p>";
        }
    }

    public function logout() {
        Session::destroy();
        header('location: ' . URL . 'login');
    }
    
    public function doa() {
        echo "You've been denied access, please login to continue."; 
    }

}
