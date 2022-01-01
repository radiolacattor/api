<?php 

function printResult($result_follow, $result_set, $result_location, $count){
	while (($row = $count->fetch_assoc()) !=false) {
		$totalC[] = $row;
	};

	$users = array();

	while (($row = $result_set->fetch_assoc()) !=false) {
		$user[] = $row;
	};

	while (($row = $result_follow->fetch_assoc()) !=false) {
		$bool = array('followed' => boolval($row['followed']), 'id' => $row['id']);
		$users[] = $bool;
	};

	while (($row = $result_location->fetch_assoc()) !=false) {
		$photos = array('small' => $row['small'], 'id' => $row['key_users'], 'large'=>$row['large']);
		$photo_u[] = $photos;
	};

	$res = array();
	for ($i=0; $i < count($users); $i++) { 
		$state[] = array('small'=>$photo_u[$i]['small'], 'large'=>$photo_u[$i]['large']);
		$res[] = array_merge($users[$i], $user[$i], array('photos'=>$state[$i]));
	};
	$array_count = count($totalC);
	// $page = ceil($array_count / $limit); 
	$final_res = array('items' => $res, 'totalCount' => $array_count);
	echo json_encode($final_res);
};

function getPosts($connect, $num, $limitNum)
{
	$limit = $limitNum;
	$pages = ($num - 1) * $limitNum;

	$count = $connect->query("SELECT * FROM `users`");

	$result_set = $connect->query("SELECT id, uniqueUrlName, name, status FROM `users`, `photos` 
		WHERE users.photos_id = photos.key_users LIMIT $pages,$limit");

	$result_follow = $connect->query("SELECT followed, id FROM `users`, `photos` WHERE users.photos_id = photos.key_users LIMIT $pages,$limit");

	$result_location  = $connect->query("SELECT small, key_users, large	FROM `photos`, `users` WHERE users.photos_id = photos.key_users 
		LIMIT $pages,$limit"); 

	printResult($result_follow, $result_set, $result_location, $count);

	$connect->close ();
};

function getPost($connect, $id){
	$post = $connect->query("SELECT * FROM `users` WHERE `id` = '$id'");

	$count = $connect->query("SELECT * FROM `users`");

	if (mysqli_num_rows($post) === 0) {
		http_response_code(404);
		$response = array('Status' => false, 'Message' => 'Post not found');
		echo json_encode($response);
	}else{
		$result_set = $connect->query("SELECT id, uniqueUrlName, name, status FROM `users`, `photos` 
			WHERE users.photos_id = photos.key_users AND users.id = '$id'");

		$result_follow = $connect->query("SELECT followed, id FROM `users`, `photos` WHERE users.photos_id = photos.key_users AND users.id = '$id'");

		$result_location  = $connect->query("SELECT small, key_users, large	FROM `photos`, `users` WHERE users.photos_id = photos.key_users AND users.id = '$id'"); 

		printResult($result_follow, $result_set, $result_location, $count);

		$connect->close ();
	}
};

function addPost($connect, $data)
{
	$followed = intval($data['followed']);
	$name = $data['name'];
	$status = $data['status'];
	$photosid = intval($data['photosid']);
	$url = $data['url'];

	mysqli_query($connect, "INSERT INTO `users` VALUES(null, '$url', $followed, '$name', '$status', $photosid)");
	http_response_code(201);
	$response = array("status" => true, "post_id" => mysqli_insert_id($connect));
	echo json_encode($response);
}

function updatePost($connect, $id, $data)
{
	$followed = intval($data['followed']);
	$name = $data['name'];
	$status = $data['status'];
	$photosid = intval($data['photosid']);
	$url = $data['url'];

	mysqli_query($connect, "UPDATE `users` 
		SET `uniqueUrlName` = '$url', `followed` = $followed, `name` = '$name', `status` = '$status', `photos_id` = $photosid 
		WHERE `users`.`id` = '$id'");

	http_response_code(200);
	$response = array('Status' => true, 'Message' => 'Post has updated');
	echo json_encode($response);
}
function deletePost($connect, $id)
{
	mysqli_query($connect, "DELETE FROM `users` WHERE `users`.`id` = '$id'");

	http_response_code(200);
	$response = array('Status' => true, 'Message' => 'Post has deleted');
	echo json_encode($response);
}