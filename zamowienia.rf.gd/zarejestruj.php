<?php

	session_start();
	
	if(isset($_SESSION['zalogowany']) && ($_SESSION['zalogowany']==true))
	{
		header('Location: menu.php');
		exit();
	}
	
	if(isset($_POST['email']))
	{
		//udana walidacja? Załuzmy, że tak!
		$wszystko_OK=true;
		
		$imie = $_POST['imie'];
		
		if(empty($imie))
		{
			$wszystko_OK = false;
			$_SESSION['e_imie']="Pole ''imię'' nie może być puste";
		}
		
		$nazwisko= $_POST['nazwisko'];
		if(empty($nazwisko))
		{
			$wszystko_OK = false;
			$_SESSION['e_nazwisko']="Pole ''nazwisko'' nie może być puste";
		}
		//sprawdz poprawnosc nickanem'a
		$login = $_POST['login'];
		
		//sprawdzeniedlugosci nicka
		if((strlen($login)<3) or (strlen($login)>20))
		{
			$wszystko_OK = false;
			$_SESSION['e_login']="Login musi posiadać od 3 do 20 znaków";
		}	
		
		if(ctype_alnum($login)==false)
		{
			$wszystko_OK = false;
			$_SESSION['e_login']="Login może składać sie tylko z liter i cyfr (bez polskich znaków)";
		}
		
		$telefon = $_POST['telefon'];
		if(ctype_digit($telefon)==false)
		{
			$wszystko_OK = false;
			$_SESSION['e_telefon']="W podanym polu występują niedozwolone znaki";
		}
		
		//sprawdz poprawnośc adresu email
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) or ($emailB!=$email))
		{
			$wszystko_OK = false;
			$_SESSION['e_email']="Podaj poprawny adres email";
		}
		
		//sprawdz poprawnośc hasła
		$haslo1 = $_POST['haslo1'];
		$haslo2 = $_POST['haslo2'];
		
		if((strlen($haslo1)<8) or (strlen($haslo1)>20))
		{
			$wszystko_OK = false;
			$_SESSION['e_haslo']="Haslo musi posiadać od 8 do 20 znaków";
		}
		
		if($haslo1!=$haslo2)
		{
			$wszystko_OK=false;
			$_SESSION['e_haslo']="Podane hasła nie są identyczne";
		}
		
		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
		
		//czy zaakceptowwano regulamin?
		//if(!isset($_POST['regulamin']))
		//{
		//	$wszystko_OK = false;
		//	$_SESSION['e_regulamin']="Potwierdz akceptacje regulaminu";
		//}
		
		
		//Bot or not
		$sekret = "6LcI-RAaAAAAAKxyjvVA7LJS_uZHIiyBTcmOrocy";
		
		$sprawdz = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$sekret.'&response='.$_POST['g-recaptcha-response']);
		
		$odpowiedz = json_decode($sprawdz);
		
		if($odpowiedz->success==false)
			{
			$wszystko_OK = false;
			$_SESSION['e_bot']="Potwierdz, że nie jesteś botem";
		}
		
		//Zapamiętaj wprowadzone dane
		$_SESSION['fr_login'] = $login;
		$_SESSION['fr_email'] = $email;
		$_SESSION['fr_haslo1'] = $haslo1;
		$_SESSION['fr_haslo2'] = $haslo2;
		$_SESSION['fr_telefon'] = $telefon;
		$_SESSION['fr_imie'] = $imie;
		$_SESSION['fr_nazwisko'] = $nazwisko;
		
		//if(isset($_POST['regulamin'])) $_SESSION['fr_regulamin'] = true;
		
		require_once 'connect.php';
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try
		{	
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if($polaczenie->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				//czy email jest już istnieje?
				$rezultat = $polaczenie->query(" SELECT id FROM uzytkownicy WHERE email='$email'");
				
				if(!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili>0)
				{
					$wszystko_OK = false;
				$_SESSION['e_email']="Istnieje już konto przypisane do tego adresu email";
				}
				
				//czy nick jest już zajęty?
				$rezultat = $polaczenie->query(" SELECT id FROM uzytkownicy WHERE login='$login'");
				
				if(!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_nickow = $rezultat->num_rows;
				if($ile_takich_nickow>0)
				{
					$wszystko_OK = false;
				$_SESSION['e_login']="Istnieje już uzytkownik o takim loginie. Wybierz inny";
				}
				
				
				if($wszystko_OK==true)
			{
				//Hurra, wszystkie testy zaliczone, dodajemy gracza do bazy
				
				if($polaczenie->query("INSERT INTO uzytkownicy VALUES (NULL, '$imie', '$nazwisko','$email','$telefon','$login','$haslo_hash')"))
				{
					$_SESSION['udanarejestracja']=true;
					header('Location: witamy.php');						
				}
				else
				{
					throw new Exception($polaczenie->error);			
				}
			}
			
				$polaczenie->close();
			}
		}
		catch(Exception $e)
		{
			echo '<span style="color: red;"> Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie </span>';
			echo '<br/> Informacja deweloperska: '.$e;		
		}
	}
	
	
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8"/>
	 <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ZAMÓWIENIA</title>
	<meta name="description" content="magazyn aplikacja"/>
	<meta name="keywords" content="magazyn,aplikacja"/>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="style5.css" type="text/css" />
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<style>
		.error
		{
			color:red;
			margin-top: 0px;
			margin:bottom 20px;
		}
		
	</style>	
</head>
<body>

	<main>
	
		
		
			<div class="contaiiner">
			
						<?php	
							echo "<nav class='sticky'><a href='index.php' class='link'><div class='cofnij'>COFNIJ</div></a><div class='cofnijmenu' style='opacity:0'>MENU</div></nav>";
						?>
						<header class="col-12">
                        <div class="hdr">REJESTRACJA</div>
						</header>
						<?php
							require_once 'powiadomienie.php';
						?>
						
						
						<div class="row" style="padding: 10% 0px 0px 0px">
									

		<form method="post">
		    <div class="dane">   
			
			<input type="text"  class="slowoe"  name="imie" placeholder="Imię" 
			value="<?php
							if(isset($_SESSION['fr_imie']))
							{
								echo $_SESSION['fr_imie'];
								unset ($_SESSION['fr_imie']);
							}
						?>"/></br>
							<?php
		if(isset($_SESSION['e_imie']))
		{
			echo '<div class="error">'.$_SESSION['e_imie'].'</div>';
			unset($_SESSION['e_imie']);		
		}
?>


			<input type="text"  class="slowoe" name="nazwisko" placeholder="Nazwisko"
			value="<?php
							if(isset($_SESSION['fr_nazwisko']))
							{
								echo $_SESSION['fr_nazwisko'];
								unset ($_SESSION['fr_nazwisko']);
							}
						?>"/><br/>
							<?php
		if(isset($_SESSION['e_nazwisko']))
		{
			echo '<div class="error">'.$_SESSION['e_nazwisko'].'</div>';
			unset($_SESSION['e_nazwisko']);		
		}
?>
						
			<input type="text"  class="slowoe" name="email" placeholder="e-mail"
			value="<?php
							if(isset($_SESSION['fr_email']))
							{
								echo $_SESSION['fr_email'];
								unset ($_SESSION['fr_email']);
							}
						?>"/><br/>
			<?php
		if(isset($_SESSION['e_email']))
		{
			echo '<div class="error">'.$_SESSION['e_email'].'</div>';
			unset($_SESSION['e_email']);		
		}
?>
			<input type="text"  class="slowoe" name="telefon" placeholder="telefon"
			value="<?php
							if(isset($_SESSION['fr_telefon']))
							{
								echo $_SESSION['fr_telefon'];
								unset ($_SESSION['fr_telefon']);
							}
						?>"/><br/>
			<?php
		if(isset($_SESSION['e_telefon']))
		{
			echo '<div class="error">'.$_SESSION['e_telefon'].'</div>';
			unset($_SESSION['e_telefon']);		
		}
?>
			<input type="text"  class="slowoe" name="login" placeholder="login"
			value="<?php
							if(isset($_SESSION['fr_login']))
							{
								echo $_SESSION['fr_login'];
								unset ($_SESSION['fr_login']);
							}
						?>"/><br/>
			<?php
		if(isset($_SESSION['e_login']))
		{
			echo '<div class="error">'.$_SESSION['e_login'].'</div>';
			unset($_SESSION['e_login']);		
		}
?>
			<input type="password"  class="slowoe" name="haslo1" placeholder="hasło"
			value="<?php
							if(isset($_SESSION['fr_haslo1']))
							{
								echo $_SESSION['fr_haslo1'];
								unset ($_SESSION['fr_haslo1']);
							}
						?>"/><br/>
			<?php
		if(isset($_SESSION['e_haslo']))
		{
			echo '<div class="error">'.$_SESSION['e_haslo'].'</div>';
			unset($_SESSION['e_haslo']);		
		}
?>
			<input type="password"  class="slowoe" name="haslo2" placeholder="powtórz hasło"
			value="<?php
							if(isset($_SESSION['fr_haslo2']))
							{
								echo $_SESSION['fr_haslo2'];
								unset ($_SESSION['fr_haslo2']);
							}
						?>"/><br/>
			
<?php
/*		<label>
			<input type="checkbox" name="regulamin"  
			<?php
				if(isset($_SESSION['fr_regulamin']))
				{
					echo "checked";
					unset($_SESSION['fr_regulamin']);
				}	
			?> />Akceptuję regulamin
		</label>
			<?php
		if(isset($_SESSION['e_regulamin']))
		{
			echo '<div class="error">'.$_SESSION['e_regulamin'].'</div>';
			unset($_SESSION['e_regulamin']);		
		}
*/
?>


		<div class="g-recaptcha" style="margin-bottom:10px; margin-top:5px" data-sitekey="6LcI-RAaAAAAAO_DGxiNhr1L6hXmFFtTLF5MW5uQ"></div>
			<?php
		if(isset($_SESSION['e_bot']))
		{
			echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
			unset($_SESSION['e_bot']);		
		}
?>

		<input type="submit" class="zar" value="Zarejestruj się"/>
			
		</div>   
		
		
		
		</form>
										
						</div>			
	
			</div>
	
		
		
	</main>
	
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
	
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>

	<script src="bootstrap/js/bootstrap.min.js"></script>
	
</body>
</html>