<?php

	session_start();

	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
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
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="style5.css" type="text/css" />
</head>
<body>

	<main>
	
		
		
			<div class="contaiiner">
			
						<?php	
							echo "<nav class='sticky'><a href='menu.php' class='link'><div class='cofnij'>COFNIJ</div></a><a href='menu.php' class='link'><div class='cofnijmenu'>MENU</div></a></nav>";
						?>
						<header class="col-12">
                        <div class="hdr">MOJE GRUPY</div>
						</header>
						<?php
							require_once 'powiadomienie.php';
						?>
						
						
						<div class="row" style="padding: 30px 0px 0px 0px">
							
<?php 
	ini_set("display_errors", 0);
	require_once 'connect.php';
	$polaczenie = mysqli_connect($host, $db_user, $db_password);
	//mysqli_query($polaczenie, "SET CHARSET utf8");
	//mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	mysqli_select_db($polaczenie, $db_name);

	$zapytanietxt = file_get_contents("zapytanie.txt");

	$rezultat = mysqli_query($polaczenie, "Select grupa.idgrp ,grupa.nazwagrp ,grupa.zdjeciegrp ,grupa.idszefagrp from grupa, grupowicze where grupowicze.idczlonkagr='{$_SESSION["id"]}'  and  grupa.idgrp=grupowicze.idgr");
	$ile = mysqli_num_rows($rezultat);

	for ($i = 1; $i <= $ile; $i++) 
	{
		
		$row = mysqli_fetch_assoc($rezultat);
		$nazwa = $row['nazwagrp'];
		$zdj = $row['zdjeciegrp'];
		$id = $row['idgrp'];

echo<<<END
<div class="col-12" style="float:left">
<a href="opcje.php?id=$id" class="link2">
		<div class="kwadratg3">
			$nazwa
		</div>
</a>
</div>
END;
	}
?>
								
						</div>			
	
			</div>
	
		
		
	</main>
	
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
	
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>

	<script src="bootstrap/js/bootstrap.min.js"></script>
	
</body>
</html>