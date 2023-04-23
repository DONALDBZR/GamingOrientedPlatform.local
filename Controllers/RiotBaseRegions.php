<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Regions.php";
$baseRegions = array_keys(Regions::baseUniformResourceLocators);
echo json_encode($baseRegions);
