<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Password.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
$User = new User();
$User->changePasswordAndMailAddress();
// if (json_decode(file_get_contents("php://input")) != null) {
//     if (!empty(json_decode(file_get_contents("php://input"))->mailAddress) && !empty(json_decode(file_get_contents("php://input"))->oldPassword) && !empty(json_decode(file_get_contents("php://input"))->newPassword) && !empty(json_decode(file_get_contents("php://input"))->confirmNewPassword) && json_decode(file_get_contents("php://input"))->mailAddress != $_SESSION['User']['mailAddress']) {
//         $User->changePasswordAndMailAddress();
//     } else if (!empty(json_decode(file_get_contents("php://input"))->mailAddress) && json_decode(file_get_contents("php://input"))->mailAddress != $_SESSION['User']['mailAddress']) {
//         $User->changeMailAddress();
//     } else if (!empty(json_decode(file_get_contents("php://input"))->oldPassword) && !empty(json_decode(file_get_contents("php://input"))->newPassword) && !empty(json_decode(file_get_contents("php://input"))->confirmNewPassword)) {
//         $User->changePassword();
//     } else {
//         $response = array(
//             "status" => 1,
//             "url" => "{$User->domain}/Users/Security/{$_SESSION['User']['username']}",
//             "message" => "The form must be completely filled!"
//         );
//         header('Content-Type: application/json', true, 300);
//         echo json_encode($response);
//     }
// } else {
//     $response = array(
//         "status" => 1,
//         "url" => "{$User->domain}/Users/Security/{$_SESSION['User']['username']}",
//         "message" => "The form must be completely filled!"
//     );
//     header('Content-Type: application/json', true, 300);
//     echo json_encode($response);
// }
