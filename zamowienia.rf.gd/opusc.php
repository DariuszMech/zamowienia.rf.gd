<?php 

	session_start();
	
	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}

	ini_set("display_errors", 0);
	require_once 'connect.php';
	$polaczenie = mysqli_connect($host, $db_user, $db_password);
	//mysqli_query($polaczenie, "SET CHARSET utf8");
	//mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	mysqli_select_db($polaczenie, $db_name);

	$id = $_GET["id"];
	$rezultat = mysqli_query($polaczenie, "DELETE FROM `grupowicze` WHERE grupowicze.idczlonkagr = '{$_SESSION["id"]}' and grupowicze.idgr='$id' " );
			
	$rezultat = mysqli_query($polaczenie, "SELECT grupa.nazwagrp FROM grupa WHERE  grupa.idgrp='$id' ");
	$row = mysqli_fetch_assoc($rezultat);
	$nazwag = $row['nazwagrp'];
	$_SESSION['powiadomienie']= "Nie należysz już do członków grupy $nazwag";
	header('Location: moje.php');
	die();
		
?>
