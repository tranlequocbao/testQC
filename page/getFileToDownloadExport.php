<?php 
	
include './_header.php';

return response([
	'code' => 200,
	'message' => 'Success',
	'data' => $_POST
]);