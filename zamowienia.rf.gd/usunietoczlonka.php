<?php 

	session_start();
	
	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}

	if(isset($_POST['usun']))
	{
		$OK = true;
		$us = $_POST["usun"];
		$_SESSION['fr_us'] = $us;
		$id = $_GET["id"];
		
		if($us==$_SESSION['id'] )
		{
			$wszystko_OK = false;
			$_SESSION['e_us']="Nie możesz usunąć siebie z grupy";
			header("Location: usunczlonka.php?id=$id");
			exit();
		}
		
		if((strlen($us)<=0))
		{
			$wszystko_OK = false;
			header("Location: usunczlonka.php?id=$id");
			exit();
		}
		
		if(ctype_digit($us)==false)
		{
			$OK = false;
			$_SESSION['e_us']="W podanym polu występują niedozwolone znaki";
			header("Location: usunczlonka.php?id=$id");
			exit();
		}
		
		ini_set("display_errors", 0);
		require_once 'connect.php';
		$polaczenie = mysqli_connect($host, $db_user, $db_password);
		//mysqli_query($polaczenie, "SET CHARSET utf8");
		//mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
		mysqli_select_db($polaczenie, $db_name);

		$rezultat = $polaczenie->query(" SELECT uzytkownicy.id FROM uzytkownicy WHERE uzytkownicy.id='$us' ");		
		if(!$rezultat) throw new Exception($polaczenie->error);			
		$ile_takich_wynikow = $rezultat->num_rows;
		if($ile_takich_wynikow==0)
		{
			$OK = false;
			$_SESSION['e_us']="Nie istnieje użytkownik o podanym ID";
			header("Location: usunczlonka.php?id=$id");
			exit();
		}
		
		$rezultat = $polaczenie->query(" SELECT * FROM grupowicze WHERE grupowicze.idczlonkagr='$us' and grupowicze.idgr='$id' ");		
		if(!$rezultat) throw new Exception($polaczenie->error);			
		$ile_takich_wynikow = $rezultat->num_rows;
		if($ile_takich_wynikow==0)
		{
			$OK = false;
			$_SESSION['e_us']="Użytkownik o podanym ID nie należy do twojej grupy";
			header("Location: usunczlonka.php?id=$id");
			exit();
		}		
		
		if($OK == true)
		{
			$rezult = mysqli_query($polaczenie, "DELETE FROM `grupowicze` WHERE grupowicze.idczlonkagr = '$us' and grupowicze.idgr='$id' " );		
			if($rezult) 
			{
				$rezultat = mysqli_query($polaczenie, "SELECT grupa.nazwagrp FROM grupa WHERE  grupa.idgrp='$id' ");
				$row = mysqli_fetch_assoc($rezultat);
				$nazwag = $row['nazwagrp'];
				$_SESSION['powiadomienie']="Użytkownik $us został wyrzucony z grupy $nazwag";
				
				if(isset($_SESSION['fr_us']))
				{
					unset ($_SESSION['fr_us']);
				}
				
				header("Location: usunczlonka.php?id=$id");
				die();
				
			}
			else
			{
				throw new Exception($polaczenie->error);	
			}	
		}
			
	}
	else
	{
		header('Location: moje.php');
		exit();
	}

?>	
