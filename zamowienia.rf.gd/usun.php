<?php 

	session_start();
	
	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}
	
	ini_set("display_errors", 0);
	require_once 'connect.php';
	$polaczenie = mysqli_connect($host, $db_user, $db_password);
	//mysqli_query($polaczenie, "SET CHARSET utf8");
	//mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	mysqli_select_db($polaczenie, $db_name);

	$rezultat = mysqli_query($polaczenie, "DELETE FROM `uzytkownicy` WHERE uzytkownicy.id = '{$_SESSION["id"]}'");
	$rezultat = mysqli_query($polaczenie, "DELETE FROM `zaproszenia` WHERE zaproszenia.idnowegoczlonka = '{$_SESSION["id"]}'");
	$rezultat = mysqli_query($polaczenie, "DELETE FROM `grupowicze` WHERE grupowicze.idczlonkagr = '{$_SESSION["id"]}'");	
	$rezultat = mysqli_query($polaczenie, "DELETE zaproszenia from zaproszenia, grupa where grupa.idszefagrp='{$_SESSION["id"]}' and zaproszenia.idgrupy=grupa.idgrp");	
	
	$rezultat = mysqli_query($polaczenie, "SELECT `idgrp` FROM `grupa` WHERE grupa.idszefagrp = '{$_SESSION["id"]}' ");
	$ile = mysqli_num_rows($rezultat);
	for ($i = 1; $i <= $ile; $i++) 
	{		
		$row = mysqli_fetch_assoc($rezultat);
		$idgrp = $row['idgrp'];
		$nowy = mysqli_query($polaczenie, "DELETE FROM grupowicze WHERE  grupowicze.idgr='$idgrp' ");
		$nowy2 = mysqli_query($polaczenie, "UPDATE `grupa` SET `statusgrp`='pusta'  WHERE grupa.idgrp='$idgrp'   ");
	}
	
	$_SESSION['powiadomienie'] ="Twoje konto zostało usunięte";
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
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css"/>  
</head>
<body>	
	
	<main>
	
		
		
		<div class="contaiiner">
						<header class="col-12">
                        <div class="hdr" style="letter-spacing:0px">
								POWIADOMIENIE
						</div>
						</header>
						<?php
							require_once 'powiadomienie.php';
						?>
						
						
			<div class="row">
						
								
				<div class="col-12" style="text-align:center; padding:200px 0px 0px 0px;">		
<a href="index.php" class="linkk" style="font-size:20px; font-weight:600;">STRONA GŁÓWNA</a>
				</div>
					
			</div>
			
		</div>
		
		
	</main>
	
	
	
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
	
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>

	<script src="bootstrap/js/bootstrap.min.js"></script>
	
</body>
</html>

<?php

session_unset();

?>