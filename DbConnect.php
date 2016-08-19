<?php

class DbConnect {

    private $_host = 'localhost';
    private $_dbname = 'a_task';
    private $_username = 'root';
    private $_password = '';
    private $_con;
    private $_stmt;

    public function __construct() {
        try {
            $this->_con = new PDO("mysql:host=$this->_host;dbname=$this->_dbname", $this->_username, $this->_password);
            $this->_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $e) {
            echo $e->getMessage();
            die;
        }
    }


    public function prepare($query) {
        $this->_stmt = $this->_con->prepare($query);
    }

    public function bindParam($param, $value) {
        $this->_stmt->bindParam($param, $value);
    }

    public function execute() {
        $this->_stmt->execute();
    }

    public function fetchOne() {
        $this->execute();
        return $this->_stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll() {
        $this->execute();
        return $this->_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find_user_by_id($id) {

        $this->prepare('SELECT * FROM users WHERE id = :id');
        $this->bindParam(':id', $id);
        return $this->fetchOne();
    }

    public function find_category_by_id($id) {

        $this->prepare('SELECT * FROM categories WHERE id = :id');
        $this->bindParam(':id', $id);
        return $this->fetchOne();
    }

    public function find_article_by_id($id) {

        $this->prepare('SELECT * FROM articles WHERE id = :id');
        $this->bindParam(':id', $id);
        return $this->fetchOne();
    }

}