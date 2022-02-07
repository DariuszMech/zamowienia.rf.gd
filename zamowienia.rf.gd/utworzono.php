<?php

	session_start();

	if(!isset($_SESSION['udanagrupa']))
	{
		header('Location: menu.php');
		exit();
	}
	else
	{
		unset($_SESSION['udanagrupa']);
	}
	
	//usuwamy zmienne pamiętajacych wartości wpisane do formularza
	if(isset($_SESSION['fr_nazwagrp'])) unset($_SESSION['fr_nazwagrp']);
	if(isset($_SESSION['fr_zdjeciegrp'])) unset($_SESSION['fr_zdjeciegrp']);
	
	//usuwanie błędów rejestracji
	if(isset($_SESSION['e_nazwagrp'])) unset($_SESSION['e_nazwagrp']);
	if(isset($_SESSION['e_zdjeciegrp'])) unset($_SESSION['e_zdjeciegrp']);
	
	
	$nazwagrp = $_GET['nazwagrp'];
	
	ini_set("display_errors", 0);
	require_once 'connect.php';
	$polaczenie = mysqli_connect($host, $db_user, $db_password);

	mysqli_select_db($polaczenie, $db_name);
	$rezultat = mysqli_query($polaczenie, "Select grupa.idgrp from grupa where grupa.nazwagrp=$nazwagrp ");
	$ile = mysqli_num_rows($rezultat);
	for ($i = 1; $i <= $ile; $i++) 
	{
		$row = mysqli_fetch_assoc($rezultat);
		$id = $row['idgrp'];
	}
	
	if($polaczenie->query("INSERT INTO grupowicze VALUES (NULL, '$id' ,' {$_SESSION["id"]}')"))
					{
						$_SESSION['powiadomienie']="Grupa $nazwagrp została utworzona!";
						header('Location: utworz.php');
						die();
					}
					else
					{
						throw new Exception($polaczenie->error);			
					}

?>

	
