<?php

	session_start();

	if(!isset($_SESSION['udanarejestracja']))
	{
		header('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['udanarejestracja']);
	}
	
	//usuwamy zmienne pamiętajacych wartości wpisane do formularza
	if(isset($_SESSION['fr_login'])) unset($_SESSION['fr_login']);
	if(isset($_SESSION['fr_telefon'])) unset($_SESSION['fr_telefon']);
	if(isset($_SESSION['fr_imie'])) unset($_SESSION['fr_imie']);
	if(isset($_SESSION['fr_nazwisko'])) unset($_SESSION['fr_nazwisko']);
	if(isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
	if(isset($_SESSION['fr_haslo1'])) unset($_SESSION['fr_haslo1']);
	if(isset($_SESSION['fr_haslo2'])) unset($_SESSION['fr_haslo2']);
	if(isset($_SESSION['fr_regulamin'])) unset($_SESSION['fr_regulamin']);
	
	//usuwanie błędów rejestracji
	if(isset($_SESSION['e_login'])) unset($_SESSION['e_login']);
	if(isset($_SESSION['e_telefon'])) unset($_SESSION['e_telefon']);
	if(isset($_SESSION['e_imie'])) unset($_SESSION['e_imie']);
	if(isset($_SESSION['e_nazwisko'])) unset($_SESSION['e_nazwisko']);
	if(isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if(isset($_SESSION['e_haslo'])) unset($_SESSION['e_haslo']);
	if(isset($_SESSION['e_regulamin'])) unset($_SESSION['e_regulamin']);
	if(isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);
	
	$_SESSION['powiadomienie']="Dziękujemy za rejestracje w serwisie! Zaloguj&nbsp;się&nbsp;na&nbsp;swoje&nbsp;konto";
	
	header('Location: index.php');
	die();
	
?>


	
	
