<?php
function update_user($update_data) {
	global $session_user_id;
	$update = array();
	array_walk($update_data, 'array_sanitize');

	foreach ($update_data as $field => $data) {
 		$update[] = '`' . $field . '` = \'' . $data . '\'';	
 	}

	mysql_query("UPDATE `users` SET " . implode(', ', $update) . " WHERE `userid` = $session_user_id") or die(mysql_error());
}

function change_password($userid, $password) {
	$userid = (int)$userid;
	$password = md5($password);

	mysql_query("UPDATE `users` SET `password` = '$password' WHERE `userid` = $userid");
}

function register_user($register_data) {
	array_walk($register_data, 'array_sanitize');
	$register_data['password'] = md5($register_data['password']); 
	
	$fields = '`' . implode('`, `', array_keys($register_data)) . '`';
	$data = '\'' . implode('\', \'', $register_data) . '\'';

	mysql_query("INSERT INTO `users` ($fields) VALUES ($data)");
}

function user_count() {
	return mysql_result(mysql_query("SELECT COUNT(`userid`) FROM `users` WHERE `active` = 0"), 0);
}

function user_data($userid) {
	$data = array();
	$userid = (int)$userid;

	$func_num_args = func_num_args();
	$func_get_args = func_get_args();

	if ($func_num_args > 1) {
		unset($func_get_args[0]);

		$fields = '`' . implode('`, `', $func_get_args) . '`';
		$data = mysql_fetch_assoc(mysql_query("SELECT $fields FROM `users` WHERE `userid` = $userid"));

		return $data;
	}
}

function logged_in() {
	return (isset($_SESSION['userid'])) ? true : false;
}

function user_exists($username) {
	$username = sanitize($username);
	return (mysql_result(mysql_query("SELECT COUNT(`userid`) FROM `users` WHERE `username` = '$username'"), 0) == 1) ? true : false;
}

// function email_exists($email) {
// 	$email = sanitize($email);
// 	return (mysql_result(mysql_query("SELECT COUNT(`user_id`) FROM `users` WHERE `email` = '$email'"), 0) == 1) ? true : false;
// }

function user_active($username) {
	$username = sanitize($username);
	return (mysql_result(mysql_query("SELECT COUNT(`userid`) FROM `users` WHERE `username` = '$username' AND  `active` = 0"), 0) == 1) ? true : false;
}

function user_id_from_username($username) {
	$username = sanitize($username);
	return mysql_result(mysql_query("SELECT `userid` FROM `users` WHERE `username` = '$username'"), 0, 'userid');
}

function login($username, $password) {
	$userid = user_id_from_username($username);

	$username = sanitize($username);
	$password = md5($password);

	return (mysql_result(mysql_query("SELECT COUNT(`userid`) FROM `users` WHERE `username` = '$username' AND `password` = '$password'"), 0) == 1) ? $userid : false;
}
?>