<?php

class AlumniRepository {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function verify($id) {
        $stmt = $this->conn->prepare(
            "UPDATE alumni 
             SET status_verifikasi='Verified' 
             WHERE id=?"
        );

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function reject($id) {
        $stmt = $this->conn->prepare(
            "UPDATE alumni 
             SET status_verifikasi='Rejected' 
             WHERE id=?"
        );

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM alumni 
             WHERE id=?"
        );

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}
?>
