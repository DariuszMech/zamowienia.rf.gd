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
		
	$id = $_GET["id"];
	
	//czy użytkownik należy do tej grupy?
	$rezultat = $polaczenie->query(" SELECT grupowicze.idgr FROM grupowicze WHERE grupowicze.idgr='$id' and grupowicze.idczlonkagr='{$_SESSION["id"]}' ");
				
	if(!$rezultat) throw new Exception($polaczenie->error);
				
	$ile_takich_maili = $rezultat->num_rows;
	if($ile_takich_maili!=1)
	{
		header('Location: moje.php');
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
							echo "<nav class='sticky'><a href='zarzadzaj.php?id=$id' class='link'><div class='cofnij'>COFNIJ</div></a><a href='menu.php' class='link'><div class='cofnijmenu'>MENU</div></a></nav>";
						?>
						<header class="col-12">
                        <div class="hdr" style="letter-spacing:0px">
								<?php	
									echo "LISTA&nbsp;CZŁONKÓW";
									$rezultat = mysqli_query($polaczenie, "SELECT grupa.nazwagrp FROM grupa WHERE  grupa.idgrp='$id' ");
									$row = mysqli_fetch_assoc($rezultat);
									$nazwag = $row['nazwagrp'];
									echo "<br/><b>".$nazwag."</b>";	
								?>
						</div>
						</header>
						<?php
							require_once 'powiadomienie.php';
						?>
						
						
						<div class="row" style="padding: 30px 30px 0px 30px">
							
<?php 
	$rezultat = mysqli_query($polaczenie, "Select uzytkownicy.imie, uzytkownicy.nazwisko, uzytkownicy.email, uzytkownicy.telefon, grupowicze.idczlonkagr, grupowicze.indeks  from uzytkownicy, grupowicze where grupowicze.idgr='$id' and grupowicze.idczlonkagr=uzytkownicy.id group by grupowicze.indeks ASC");
	$ile = mysqli_num_rows($rezultat);
	for ($i = 1; $i <= $ile; $i++) 
	{		
		$row = mysqli_fetch_assoc($rezultat);
		$imie = $row['imie'];
		$nazwisko = $row['nazwisko'];
		$email = $row['email'];
		$telefon = $row['telefon'];
		$idczlonkagr = $row['idczlonkagr'];
		$indeks = $row['indeks'];

echo<<<END
<div class="col-12">
		<div class="listaczlonkow">
			<p1 style="font-size:22px; font-weight:600;">$imie $nazwisko</p1><br/>
			<div class="left"><b>email:</b></div> <div class="right">$email</div><br/><div style="clear:both"></div>
			<div class="left"><b>telefon: </b></div><div class="right">$telefon </div><div style="clear:both"></div>
			<div class="left"><b>ID: </b></div><div class="right">$idczlonkagr</div>
		</div>
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