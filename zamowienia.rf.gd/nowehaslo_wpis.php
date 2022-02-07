<?php

	session_start();
	
	if(isset($_SESSION['zalogowany']) && ($_SESSION['zalogowany']==true))
	{
		header('Location: menu.php');
		exit();
	}

	ini_set("display_errors", 0);
	require_once 'connect.php';
	$polaczenie = mysqli_connect($host, $db_user, $db_password);
	mysqli_query($polaczenie, "SET CHARSET utf8");
	mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	mysqli_select_db($polaczenie, $db_name);
		
	$a = $_GET["a"];
	$b = $_GET["b"];
	
	//czy użytkownik należy do tej grupy?
	$rezultat = $polaczenie->query(" SELECT uzytkownicy.data_kodu  FROM uzytkownicy WHERE uzytkownicy.login='$a' and uzytkownicy.kod_resetu='$b' ");			
	if(!$rezultat) throw new Exception($polaczenie->error);
				
	$ile_takich_maili = $rezultat->num_rows;
	if($ile_takich_maili!=1)
	{
		$_SESSION['blad'] = '<span style = "color:red">Podany link jest nieprawidłowy lub już wygasł</span>';
		header('Location: index.php');
		exit();
	}
	else
	{
			$row = mysqli_fetch_assoc($rezultat);
			$data_kodu = $row['data_kodu'];
			$data_dzisiaj = date("Y-m-d H:i:s");
			if($data_kodu < $data_dzisiaj)
			{
				$rezultat = $polaczenie->query("UPDATE `uzytkownicy` SET `kod_resetu`='-',`data_kodu`='0000.00.00 00:00:00' WHERE uzytkownicy.data_kodu='$data_kodu'");
				
				$_SESSION['blad'] = '<span style = "color:red">Podany link jest nieprawidłowy lub już wygasł</span>';
				header('Location: index.php');
				exit();
			}
			else
			{
				echo "heja";
				
				//sprawdz poprawnośc hasła
				$haslo1 = $_POST['nhaslo1'];
				$haslo2 = $_POST['nhaslo2'];
				
				if((strlen($haslo1)<8) or (strlen($haslo1)>20))
				{
					$_SESSION['blad'] = '<span style = "color:red">Haslo musi posiadać od 8 do 20 znaków</span>';
					header("Location: nowehaslo.php?a=$a&b=$b");
					exit();
				}
				else if($haslo1!=$haslo2)
				{
					$_SESSION['blad'] = '<span style = "color:red">Podane hasła nie są identyczne</span>';
					header("Location: nowehaslo.php?a=$a&b=$b");
					exit();
				}
				else
				{
					$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
					
					if($rezultat = $polaczenie->query("UPDATE `uzytkownicy` SET `haslo`='$haslo_hash' WHERE uzytkownicy.login='$a' "))
					{
						$rezultat = $polaczenie->query("UPDATE `uzytkownicy` SET `kod_resetu`='-',`data_kodu`='0000.00.00 00:00:00' WHERE uzytkownicy.data_kodu='$data_kodu'");
						
						$_SESSION['powiadomienie'] ="Nowe hasło zostało poprawnie ustawione";
						
						header('Location: index.php');
					}
				}
				
			}
	}
?>