<?php
include 'aut/ASEngine/AS.php';
if ( app('login')->isLoggedIn()) {
	$user = app('current_user');
	echo '<table><tbody>';
	echo '<tr><td>'.$user->first_name.'</td></tr>';
	echo '<tr><td>'.$user->role.'</td></tr>';
	echo '<tr><td><button id="mfl_logout">Logout</button></td></tr>';	
	echo '</tbody></table>';	
} else {
	echo '<table><tbody>';
	echo '<tr><td>Acesso para área restrita</td></tr>';
	echo '<tr><td>Só para utentes cadastrados</td></tr>';
	echo '<tr><td><button id="mfl_login">Login</button></td></tr>';	
	echo '</tbody></table>';
}
/*
Username: admin
Password: admin123
*/
?>
