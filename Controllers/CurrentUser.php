<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
$response = $_SESSION['User'];
header('Content-Type: application/json', true, 200);
echo json_encode($response);
