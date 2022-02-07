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
	$rezultat = mysqli_query($polaczenie, "SELECT grupa.nazwagrp FROM grupa WHERE  grupa.idgrp='$id' ");
	$row = mysqli_fetch_assoc($rezultat);
	$nazwag = $row['nazwagrp'];
	
	$rezultat = mysqli_query($polaczenie, "DELETE FROM `grupowicze` WHERE grupowicze.idgr = '$id'");
	$rezultat = mysqli_query($polaczenie, "DELETE FROM `zaproszenia` WHERE zaproszenia.idgrupy = '$id'");
	$rezultat = mysqli_query($polaczenie, "UPDATE `grupa` SET `nazwagrp`='x',`statusgrp`='pusta' WHERE idgrp='$id'");

	$_SESSION['powiadomienie']= "Grupa $nazwag została usunięta"; 
	header('Location: moje.php');
	die();
	
	?>
	
