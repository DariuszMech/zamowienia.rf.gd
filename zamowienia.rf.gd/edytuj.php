<?php
	session_start();

	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}
	
	if(isset($_POST['nimie']))
	{
		//udana walidacja? Załuzmy, że tak!
		$wszystko_OK=true;
		
		$nimie = $_POST['nimie'];
		$nnazwisko = $_POST['nnazwisko'];
		
		if(empty($nimie))
		{
			$wszystko_OK = false;
			$_SESSION['e_nimie']="Pole ''imię'' nie może być puste";
		}
		
		if(empty($nnazwisko))
		{
			$wszystko_OK = false;
			$_SESSION['e_nnazwisko']="Pole ''nazwisko'' nie może być puste";
		}
		
		
		$nlogin = $_POST['nlogin'];
		
		//sprawdzeniedlugosci nicka
		if((strlen($nlogin)<3) or (strlen($nlogin)>20))
		{
			$wszystko_OK = false;
			$_SESSION['e_nlogin']="Login musi posiadać od 3 do 20 znaków";
		}	
		
		if(ctype_alnum($nlogin)==false)
		{
			$wszystko_OK = false;
			$_SESSION['e_nlogin']="Login może składać sie tylko z liter i cyfr (bez polskich znaków)";
		}
		
		$ntelefon = $_POST['ntelefon'];
		if(ctype_digit($ntelefon)==false)
		{
			$wszystko_OK = false;
			$_SESSION['e_ntelefon']="W podanym polu występują niedozwolone znaki";
		}
		
		//sprawdz poprawnośc adresu email
		$nemail = $_POST['nemail'];
		$nemailB = filter_var($nemail, FILTER_SANITIZE_EMAIL);
		
		if((filter_var($nemailB, FILTER_VALIDATE_EMAIL)==false) or ($nemailB!=$nemail))
		{
			$wszystko_OK = false;
			$_SESSION['e_nemail']="Podaj poprawny adres email";
		}
		
		
		//sprawdz poprawnośc hasła
		$shaslo = $_POST['shaslo'];
		$nhaslo1 = $_POST['nhaslo1'];
		$nhaslo2 = $_POST['nhaslo2'];
		$haslo_OK = true;
		$haslo_puste = true;
		
		$nhaslo_hash = password_hash($nhaslo1, PASSWORD_DEFAULT);
		$shaslo_hash = password_hash($shaslo, PASSWORD_DEFAULT);
		
	
		if(empty($shaslo) and empty($nhaslo1) and empty($nhaslo2))
		{
			$haslo_OK=true;
			$haslo_puste = true;
			//unset($_SESSION['e_nhaslo']);
			//unset($_SESSION['e_shaslo']);
		}
		else
		{
			$haslo_puste = false;
			
			if((strlen($nhaslo1)<8) or (strlen($nhaslo1)>20))
			{
				$haslo_OK = false;
				$_SESSION['e_nhaslo']="Haslo musi posiadać od 8 do 20 znaków";
			}
						
			if($nhaslo1!=$nhaslo2)
			{
				$haslo_OK=false;
				$_SESSION['e_nhaslo']="Podane nowe hasła nie są identyczne";
			}
		
			
			
			
			require_once "connect.php";

			$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
			
			if($polaczenie->connect_errno!=0)
			{
				echo "Error: ".$polaczenie->connect_errno;
			}
			else
			{
					
				$login = $_SESSION['login'];
				$haslo = $_SESSION['haslo'];
			
				$login = htmlentities($login, ENT_QUOTES, "UTF-8");
				if($rezultat = @$polaczenie->query(
				sprintf("SELECT * FROM uzytkownicy WHERE id='{$_SESSION["id"]}'",
				mysqli_real_escape_string($polaczenie, $id))))
				{	
					$ilu_userow = $rezultat->num_rows;
					
					if($ilu_userow>0)
					{
						$wiersz = $rezultat->fetch_assoc();
						
						if(!password_verify($shaslo, $wiersz['haslo']))
						{
							$haslo_OK=false;
							$_SESSION['e_shaslo']="Podane stare hasło nie jest poprawne";	
						}
						
						if(password_verify($nhaslo1, $wiersz['haslo']))
						{
							$haslo_OK=false;
							$_SESSION['e_nhaslo']="Podane nowe hasło jest takie same jak stare";
						}
					}
					else
					{
						$_SESSION['blad'] = '<span style = "color:red"> Nieprawidłowy login lub hasło!</span>';
						header('Location: index.php');
					}
				}
				
			}
			
		}
		
		
		
		//Zapamiętaj wprowadzone dane
		$_SESSION['fr_nimie'] = $nimie;
		$_SESSION['fr_nnazwisko'] = $nnazwisko;
		$_SESSION['fr_nlogin'] = $nlogin;
		$_SESSION['fr_nemail'] = $nemail;
		$_SESSION['fr_ntelefon'] = $ntelefon;
		
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
				$rezultat = $polaczenie->query(" SELECT id FROM uzytkownicy WHERE email='$nemail'");
				
				if(!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili>0)
				{
					//czy wpisujesz tego samego maila?
					$rezultat = $polaczenie->query(" SELECT id FROM uzytkownicy WHERE email='$nemail' and id='{$_SESSION["id"]}'");
					
					if(!$rezultat) throw new Exception($polaczenie->error);
					
					$mojemail = $rezultat->num_rows;
					if($mojemail>0)
					{
						//$_SESSION['e_nemail']="Wpisałeś tego samego maila";
					}
					else
					{
						$wszystko_OK = false;
						$_SESSION['e_nemail']="Istnieje już konto przypisane do tego adresu email";	
					}	
				}
				
				//czy login jest już zajęty?
				$rezultat = $polaczenie->query(" SELECT id FROM uzytkownicy WHERE login='$nlogin'");
				
				if(!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_nickow = $rezultat->num_rows;
				if($ile_takich_nickow>0)
				{
					//czy wpisujesz tego samego maila?
					$rezultat = $polaczenie->query(" SELECT id FROM uzytkownicy WHERE login='$nlogin' and id='{$_SESSION["id"]}'");
					
					if(!$rezultat) throw new Exception($polaczenie->error);
					
					$mojlogin = $rezultat->num_rows;
					if($mojlogin>0)
					{
						//$_SESSION['e_nlogin']="Wpisałeś ten sam login";
					}
					else
					{			
						$wszystko_OK = false;
						$_SESSION['e_nlogin']="Istnieje już uzytkownik o takim loginie. Wybierz inny";
					}
				}
				
				//czy telefon jest już zajęty?
				$rezultat = $polaczenie->query(" SELECT id FROM uzytkownicy WHERE telefon='$ntelefon'");
				
				if(!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_tele = $rezultat->num_rows;
				if($ile_takich_tele>0)
				{
					
					//czy wpisujesz tego samego maila?
					$rezultat = $polaczenie->query(" SELECT id FROM uzytkownicy WHERE telefon='$ntelefon' and id='{$_SESSION["id"]}'");
					
					if(!$rezultat) throw new Exception($polaczenie->error);
					
					$mojtelefon = $rezultat->num_rows;
					if($mojtelefon>0)
					{
						// $_SESSION['e_ntelefon']="Wpisałeś ten sam numer telefonu";
					}
					else
					{
						$wszystko_OK = false;
						$_SESSION['e_ntelefon']="Istnieje już konto przypisane do tego numeru telefonu".$ntelefon.". Podaj inny lub pomiń";
					}
				}
				
				 /**/
				 
				if($nnazwisko==$_SESSION['nazwisko'] and $nimie==$_SESSION['imie'] and $nlogin==$_SESSION['login'] and $nemail==$_SESSION['email'] and $ntelefon==$_SESSION['telefon'] and ($haslo_puste==true ))
				{
					$wszystko_OK = false;
					unset($_SESSION['e_nlogin']);
					unset($_SESSION['e_nemail']);
					unset($_SESSION['e_ntelefon']);
					$_SESSION['e_bezzmian']="Nowe dane są takie same jak stare";
				}
				
				
				
				
					if(($wszystko_OK==true and $haslo_puste==true) or ( $wszystko_OK==true and  $haslo_OK == true and $haslo_puste == false  ))
				{
					//Hurra, wszystkie testy zaliczone, dodajemy gracza do bazy
					
					if($polaczenie->query("UPDATE `uzytkownicy` SET `imie`='$nimie', `nazwisko`='$nnazwisko' , `telefon`='$ntelefon',`email`='$nemail',`login`='$nlogin' WHERE uzytkownicy.id='{$_SESSION["id"]}'"))
					{
						$_SESSION['imie'] = $nimie;
						$_SESSION['nazwisko'] = $nnazwisko;
						$_SESSION['telefon'] = $ntelefon;
						$_SESSION['email'] = $nemail;
						$_SESSION['login'] = $nlogin;
						
						if($haslo_OK == true and $haslo_puste == false)
						{
							if($polaczenie->query("UPDATE `uzytkownicy` SET `haslo`='$nhaslo_hash' WHERE uzytkownicy.id='{$_SESSION["id"]}'"))
							{
								$_SESSION['haslo'] = $nhaslo1;
							}
							else
							{
								throw new Exception($polaczenie->error);			
							}
							
						}

						$_SESSION['powiadomienie']='Udało Ci się zmienić dane';
						
						header('Location: profil.php');	
						die();
						
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
</head>
<body>

	<main>
	
		
		
			<div class="contaiiner">
			
						<?php	
							echo "<nav class='sticky'><a href='profil.php' class='link'><div class='cofnij'>COFNIJ</div></a><a href='menu.php' class='link'><div class='cofnijmenu'>MENU</div></a></nav>";
						?>
						<header class="col-12">
                        <div class="hdr">EDYTUJ DANE</div>
						</header>
						<?php
							require_once 'powiadomienie.php';
						?>
						
						
						<div class="row" style="padding: 20px 0px 0px 0px">
									
<form method="post">
		      
			<div class="daneedytuj" style="margin-bottom:20px">
			
			Imię:<br/> <input type="text"  class="slowoe"  name="nimie"
			value="<?php
							if(isset($_SESSION['fr_nimie']))
							{
								echo $_SESSION['fr_nimie'];
								unset ($_SESSION['fr_nimie']);
							}
							else
							{
								echo $_SESSION['imie'];
							}
						?>"/></br>
<?php
		if(isset($_SESSION['e_nimie']))
		{
			echo '<div class="error">'.$_SESSION['e_nimie'].'</div>';
			unset($_SESSION['e_nimie']);		
		}
?>

			Nazwisko:<br/> <input type="text"  class="slowoe"  name="nnazwisko"
			value="<?php
							if(isset($_SESSION['fr_nnazwisko']))
							{
								echo $_SESSION['fr_nnazwisko'];
								unset ($_SESSION['fr_nnazwisko']);
							}
							else
							{
								echo $_SESSION['nazwisko'];
							}
						?>"/></br>
<?php
		if(isset($_SESSION['e_nnazwisko']))
		{
			echo '<div class="error">'.$_SESSION['e_nnazwisko'].'</div>';
			unset($_SESSION['e_nnazwisko']);		
		}
?>



			Login: <br/><input type="text"  class="slowoe" name="nlogin" 
			value="<?php
							if(isset($_SESSION['fr_nlogin']))
							{
								echo $_SESSION['fr_nlogin'];
								unset ($_SESSION['fr_nlogin']);
							}
							else
							{
								echo $_SESSION['login'];
							}
						?>"/><br/>
<?php
		if(isset($_SESSION['e_nlogin']))
		{
			echo '<div class="error">'.$_SESSION['e_nlogin'].'</div>';
			unset($_SESSION['e_nlogin']);		
		}
?>

			Email: <br/><input type="text"  class="slowoe" name="nemail" 
			value="<?php
							if(isset($_SESSION['fr_nemail']))
							{
								echo $_SESSION['fr_nemail'];
								unset ($_SESSION['fr_nemail']);
							}
							else
							{
								echo $_SESSION['email'];
							}
						?>"/><br/>
<?php
		if(isset($_SESSION['e_nemail']))
		{
			echo '<div class="error">'.$_SESSION['e_nemail'].'</div>';
			unset($_SESSION['e_nemail']);		
		}
?>
			Telefon: <br/><input type="text"  class="slowoe" name="ntelefon" 
			value="<?php
							if(isset($_SESSION['fr_ntelefon']))
							{
								echo $_SESSION['fr_ntelefon'];
								unset ($_SESSION['fr_ntelefon']);
							}
							else
							{
								echo $_SESSION['telefon'];
							}
						?>"/><br/>
<?php
		if(isset($_SESSION['e_ntelefon']))
		{
			echo '<div class="error">'.$_SESSION['e_ntelefon'].'</div>';
			unset($_SESSION['e_ntelefon']);		
		}
?>		</div>

			<div class="daneedytuj" style="margin-bottom:20px;">
			Stare hasło: <br/> <input type="password"  class="slowoe" name="shaslo"/><br/>
<?php
		if(isset($_SESSION['e_shaslo']))
		{
			echo '<div class="error">'.$_SESSION['e_shaslo'].'</div>';
			unset($_SESSION['e_shaslo']);		
		}
?>

			Nowe hasło: <br/> <input type="password"  class="slowoe" name="nhaslo1"/><br/>
<?php
		if(isset($_SESSION['e_nhaslo']))
		{
			echo '<div class="error">'.$_SESSION['e_nhaslo'].'</div>';
			unset($_SESSION['e_nhaslo']);		
		}
?>
			Powtórz nowe hasło: <br/><input type="password"  class="slowoe" name="nhaslo2"/><br/>
						
			</div>  



<?php
		if(isset($_SESSION['e_bezzmian']))
		{
			echo '<div class="error">'.$_SESSION['e_bezzmian'].'</div><br/>';
			unset($_SESSION['e_bezzmian']);		
		}
?>
					
		 
		
		<input type="submit" class="edytujsubmit" value="Zapisz zmiany"/>
		
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