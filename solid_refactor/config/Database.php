<?php

class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db   = "alumni_db";

    public function connect() {
        $conn = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->db
        );

        if ($conn->connect_error) {
            die("Koneksi gagal");
        }

        return $conn;
    }
}
?>
