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
                        <div class="hdr">ZAPROSZENIA</div>
						</header>
						<?php
							require_once 'powiadomienie.php';
						?>
						
						
						<div class="row" style="padding: 40px 0px 0px 0px">
							
<?php
	ini_set("display_errors", 0);
	require_once 'connect.php';
	$polaczenie = mysqli_connect($host, $db_user, $db_password);
	//mysqli_query($polaczenie, "SET CHARSET utf8");
	//mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	mysqli_select_db($polaczenie, $db_name);
	
	
	$rezultat = mysqli_query($polaczenie, "SELECT zaproszenia.idgrupy , grupa.nazwagrp from zaproszenia, grupa where zaproszenia.idgrupy=grupa.idgrp and zaproszenia.idnowegoczlonka='{$_SESSION["id"]}' ");
	$ile = mysqli_num_rows($rezultat);
	for ($i = 1; $i <= $ile; $i++) 
	{		
		$row = mysqli_fetch_assoc($rezultat);
		$nazwagrupy= $row['nazwagrp'];
		$id= $row['idgrupy'];

echo<<<END

		<div class="kwadratg2">
			<a class="link2"><b>$nazwagrupy</b><br/>zaprasza Cię do swojej grupy<br/><br/></a>
		<label class="link3">
			<form action="akceptuj.php?id=$id" method="post">
			<input type="submit" value="Akceptuj" name="submit" class="zaps1" />
			</form>
		</label>	
		<label class="link3">
			<form action="odrzuc.php?id=$id" method="post">
			<input type="submit" value="Odrzuć" name="submit" class="zaps2" />
			</form>
		</label>		
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