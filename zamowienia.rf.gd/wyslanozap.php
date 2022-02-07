<?php 

	session_start();
	
	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}

	if(isset($_POST['zap']))
	{
		$OK = true;
		$zap = $_POST["zap"];
		$_SESSION['fr_zap'] = $zap;
		$id = $_GET["id"];
		
		if((strlen($zap)<=0))
		{
			$wszystko_OK = false;
			header("Location: dodajczlonka.php?id=$id");
			exit();
		}
		
		if(ctype_digit($zap)==false)
		{
			$OK = false;
			$_SESSION['e_zap']="W podanym polu występują niedozwolone znaki";
			header("Location: dodajczlonka.php?id=$id");
			exit();
		}
		
		ini_set("display_errors", 0);
		require_once 'connect.php';
		$polaczenie = mysqli_connect($host, $db_user, $db_password);
		//mysqli_query($polaczenie, "SET CHARSET utf8");
		//mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
		mysqli_select_db($polaczenie, $db_name);

		$rezultat = $polaczenie->query(" SELECT uzytkownicy.id FROM uzytkownicy WHERE uzytkownicy.id='$zap' ");		
		if(!$rezultat) throw new Exception($polaczenie->error);			
		$ile_takich_wynikow = $rezultat->num_rows;
		if($ile_takich_wynikow==0)
		{
			$OK = false;
			$_SESSION['e_zap']="Nie istnieje użytkownik o podanym ID";
			header("Location: dodajczlonka.php?id=$id");
			exit();
		}
		
		$rezultat = $polaczenie->query(" SELECT * FROM grupowicze WHERE grupowicze.idczlonkagr='$zap' and grupowicze.idgr='$id' ");		
		if(!$rezultat) throw new Exception($polaczenie->error);			
		$ile_takich_wynikow = $rezultat->num_rows;
		if($ile_takich_wynikow!=0)
		{
			$OK = false;
			$_SESSION['e_zap']="Użytkownik o podanym ID jest już członkiem grupy";
			header("Location: dodajczlonka.php?id=$id");
			exit();
		}
		
		$rezultat = $polaczenie->query(" SELECT * FROM zaproszenia WHERE zaproszenia.idgrupy='$id' and zaproszenia.idnowegoczlonka='$zap' ");		
		if(!$rezultat) throw new Exception($polaczenie->error);		
		$ile_takich_wynikow = $rezultat->num_rows;
		if($ile_takich_wynikow!=0)
		{
			$OK = false;
			$_SESSION['e_zap']="Zaproszenie zostało wysłane już wcześniej";
			header("Location: dodajczlonka.php?id=$id");
			exit();
		}
		
		
		if($OK == true)
		{
			$rezultat = $polaczenie->query(" INSERT INTO `zaproszenia`(`idgrupy`, `idnowegoczlonka`) VALUES ('$id','$zap') ");		
			if($rezultat) 
			{
				$rezultat = mysqli_query($polaczenie, "SELECT grupa.nazwagrp FROM grupa WHERE  grupa.idgrp='$id' ");
				$row = mysqli_fetch_assoc($rezultat);
				$nazwag = $row['nazwagrp'];
				$_SESSION['powiadomienie']= "Wysłano zaproszenie dołączenia do grupy $nazwag ";
				
				if(isset($_SESSION['fr_zap']))
				{
					unset ($_SESSION['fr_zap']);
				}
				
				header("Location: dodajczlonka.php?id=$id");
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
