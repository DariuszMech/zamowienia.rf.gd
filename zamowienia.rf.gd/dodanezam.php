<?php

	session_start();
	
	ini_set("display_errors", 0);
	require_once 'connect.php';
	$polaczenie = mysqli_connect($host, $db_user, $db_password);
    //mysqli_query($polaczenie, "SET CHARSET utf8");
	//mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	mysqli_select_db($polaczenie, $db_name);

	$id = $_GET["id"];
	$idzam = $_GET["idzam"];

	//czy użytkownik należy do tej grupy?
	$rezultat = $polaczenie->query(" SELECT grupowicze.idgr FROM grupowicze WHERE grupowicze.idgr='$id' and grupowicze.idczlonkagr='{$_SESSION["id"]}' ");
				
	if(!$rezultat) throw new Exception($polaczenie->error);			
	$ile_takich_maili = $rezultat->num_rows;
	if($ile_takich_maili!=1)
	{
		header('Location: moje.php');
		exit();
	}
	
	if(!isset($_SESSION['udanezamowienie']))
	{
		header('Location: menu.php');
		exit();
	}
	else
	{
		unset($_SESSION['udanezamowienie']);
	}
	
	//usuwamy zmienne pamiętajacych wartości wpisane do formularza
	if(isset($_SESSION['fr_adres'])) unset($_SESSION['fr_adres']);
	if(isset($_SESSION['fr_telefon'])) unset($_SESSION['fr_telefon']);
	if(isset($_SESSION['fr_imienaz'])) unset($_SESSION['fr_imienaz']);
	if(isset($_SESSION['fr_opis'])) unset($_SESSION['fr_opis']);
	if(isset($_SESSION['fr_kwota'])) unset($_SESSION['fr_kwota']);
	if(isset($_SESSION['fr_notatki'])) unset($_SESSION['fr_notatki']);
	
	$_SESSION['powiadomienie'] ="Zamówienie o numerze $idzam zostało&nbsp;utworzone!";
	header("Location: lista.php?id=$id");
	die();

?>
	

