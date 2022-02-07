<?php

	session_start();

	if(!isset($_POST['wemail']))
	{
			header('Location: haslo.php');
			exit();
	}		

	require_once "connect.php";

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
	
	if($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else
	{
			
		$email = $_POST['wemail'];
		$wemail = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if((filter_var($email, FILTER_VALIDATE_EMAIL)==false) or ($wemail!=$email))
		{
			$_SESSION['blad'] = '<span style = "color:red">Podaj poprawny adres email</span>';
			header('Location: haslo.php');
		}
		else
		{
		//$wemail = htmlspecialchars(stripslashes(strip_tags(trim($_POST["wemail"]))), ENT_QUOTES);
		
		$rezultat = mysqli_query($polaczenie, "SELECT * FROM uzytkownicy WHERE email='$wemail'");
		$ilu_userow = $rezultat->num_rows;
			if($ilu_userow==1)
			{   
                $wiersz = $rezultat->fetch_assoc();
				
				$nowykod = uniqid('', true);
				$a = $wiersz['login'];
				$b = $nowykod;
                $imie = $wiersz['imie'];
                $nazwisko = $wiersz['nazwisko'];
				$za_2_dni = time() + 172800;
				$data = date("Y-m-d H-i-s", $za_2_dni); // Wyświetli datę 'za 2 dni'

				$rezultat = mysqli_query($polaczenie, "UPDATE uzytkownicy SET kod_resetu='$nowykod', data_kodu='$data' WHERE uzytkownicy.email='$wemail'");
 
                $to = $wemail;
                $subject = "ZMIANA HASŁA - zamówienia.rf.gd";
                $msg = "<h2 style='text-align:center'>Poniżej znajduje się link, który pozwoli Ci ustawić nowe hasło dla konta <b>$a</b> na stronie www.zamowienia.rf.gd </br></br> <a href=\"http://zamowienia.rf.gd/nowehaslo.php?a=$a&b=$b\">LINK</a> </br></br> Link będzie aktywny do momentu zmiany hasła, ale nie dłużej niż 2 dni</h2>"; 


//error_reporting(E_ALL);
//ini_set('display_errors', true);
 
require('phpmailer/PHPMailerAutoload.php');
$mail = new PHPMailer();
$mail->PluginDir = "phpmailer/";
$mail->From = "xxx";
$mail->FromName = "zamowienia.rf.gd";
$mail->SMTPAuth = "xxx";
$mail->SMTPSecure = "ssl";
$mail->Host = "smtp.gmail.com";
$mail->Mailer = "smtp";
$mail->Username = "xxx";
$mail->Password = "xxx";
$mail->SMTPAuth = true;
$mail->Port = 465;
$mail->CharSet  = 'UTF-8';
$mail->Subject = $subject;
$mail->Body = $msg;

$mail->IsHTML(true);
$mail->AddAddress($to, "$imie $nazwisko");
if($mail->Send())
   {                      
        $_SESSION['powiadomienie'] ="Link resetujący hasło został wysłany na podany email";
         header('Location: haslo.php');
     }     
else
    {
        $_SESSION['powiadomienie'] ="Wystąpił nieoczekiwany błąd. Spróbuj ponownie pózniej". //$mail->ErrorInfo;
        header('Location: haslo.php');
    }
$mail->ClearAddresses();
$mail->ClearAttachments();

			}
			else
			{
				$_SESSION['blad'] = '<span style = "color:red">Nie znaleziono użytkownika o takim emailu</span>';
				header('Location: haslo.php');
			}
		}
		$polaczenie->close();
	}

?>