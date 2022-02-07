<?php 

	session_start();
	
	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}

	if(isset($_POST['submit']))
	{
		$OK = true;
		$id = $_GET["id"];	

		ini_set("display_errors", 0);
		require_once 'connect.php';
		$polaczenie = mysqli_connect($host, $db_user, $db_password);
        //mysqli_query($polaczenie, "SET CHARSET utf8");
	    //mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
		mysqli_select_db($polaczenie, $db_name);

		$rezultat = $polaczenie->query(" SELECT * FROM zaproszenia WHERE zaproszenia.idnowegoczlonka='{$_SESSION["id"]}' and zaproszenia.idgrupy='$id' ");		
		if(!$rezultat) throw new Exception($polaczenie->error);			
		$ile_takich_wynikow = $rezultat->num_rows;
		if($ile_takich_wynikow==0)
		{
			$OK = false;
			header("Location: zaproszenia.php");
			exit();
		}
		
		if($OK == true)
		{
			$rezultat = mysqli_query($polaczenie, "INSERT INTO `grupowicze`(`idgr`, `idczlonkagr`) VALUES ('$id', '{$_SESSION["id"]}') " );		
			if(!$rezultat) throw new Exception($polaczenie->error);	
			$rezultat = mysqli_query($polaczenie, "DELETE FROM zaproszenia WHERE zaproszenia.idgrupy = '$id' and zaproszenia.idnowegoczlonka='{$_SESSION["id"]}'" );		
			if(!$rezultat) throw new Exception($polaczenie->error);		
		}	
	}
	else
	{
		header('Location: index.php');
	}
		
		
		$rezultat = mysqli_query($polaczenie, "SELECT grupa.nazwagrp FROM grupa WHERE  grupa.idgrp='$id' ");
		$row = mysqli_fetch_assoc($rezultat);
		$nazwag = $row['nazwagrp'];
		$_SESSION['powiadomienie']= "Właśnie zostałeś członkiem grupy $nazwag";
		
		header("Location: zaproszenia.php");
		die();
	

?>	
