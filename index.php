<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header('Content-type: json/application');

require 'connect.php';
require 'functions.php';

$method = $_SERVER ['REQUEST_METHOD'];

$q = $_GET['q'];
$params = explode('/', $q);

$type = $params[0];
$id = $params[1];

$page = explode('/', $q);

$limit = $page[1];
$pages = $page[0];
$limitNum = preg_replace('/[^0-9]/', '', $page[1]);
$num = preg_replace('/[^0-9]/', '', $page[0]); 

if ($method === 'GET') {
	if ($type === 'posts') {
		if (isset($id)) {
			getPost($connect, $id);
		}
	}
	elseif($pages === 'page' . $num){
		if (isset($limit)) {
			getPosts($connect, $num, $limitNum);
		}
		else{
			getPosts($connect, $num, 5);	
		}
	}
}elseif ($method === 'POST') {
	if ($type === 'posts') {
		addPost($connect, $_POST);
	}
}elseif ($method === 'PATCH') {
	if ($type = 'posts') {
		if (isset($id)) {
			$data = file_get_contents('php://input');
			$data = json_decode($data, true);
			updatePost($connect, $id, $data);
		}
	}
}elseif ($method === 'DELETE') {
	if ($type = 'posts') {
		if (isset($id)) {
			deletePost($connect, $id);
		}
	}
}

?>