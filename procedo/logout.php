<?php
	if(isset($_COOKIE['procedo']))
	{
		unset($_COOKIE['procedo']);
		setcookie('procedo', '', time() - 3600);
	}
	header("location: login.html");
?>