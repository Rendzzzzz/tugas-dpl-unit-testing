<?php

require_once 'Database.php';
require_once 'AlumniRepository.php';

require_once 'ActionInterface.php';
require_once 'VerifyAction.php';
require_once 'RejectAction.php';
require_once 'DeleteAction.php';

$db = new Database();
$conn = $db->connect();

$repository = new AlumniRepository($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = $_POST['id'];
    $action = $_POST['action'];

    $actions = [
        'verify' => new VerifyAction($repository),
        'reject' => new RejectAction($repository),
        'delete' => new DeleteAction($repository)
    ];

    if (isset($actions[$action])) {
        $actions[$action]->execute($id);

        echo "Aksi berhasil";
    }
}
?>
