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
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css"/>  
</head>
<body>


	<main>
	
		
		
			<div class="contaiiner">
			
						<?php	
							echo "<nav class='sticky'><a href='menu.php' class='link'><div class='cofnij'>COFNIJ</div></a><a href='menu.php' class='link'><div class='cofnijmenu'>MENU</div></a></nav>";
						?>
						<header class="col-12">
                        <div class="hdr">MÓJ PROFIL</div>
						</header>
						<?php
							require_once 'powiadomienie.php';
						?>
						
						
				<div class="row">
						
							<div id="info">
					<?php
						echo"<div ><b>ID użytkownika:</b></div><div >".$_SESSION['id']."</div><br/> ";
						echo"<div ><b>Imię:</b></div><div >".$_SESSION['imie']."</div><br/> ";
						echo"<div ><b>Nazwisko:</b></div><div >".$_SESSION['nazwisko']."</div><br/>";	
						echo"<div ><b>E-mail:</b></div><div >".$_SESSION['email']."</div><br/>";
						echo"<div ><b>Telefon:</b></div><div >".$_SESSION['telefon']."</div><br/>";
					?>
							</div>
							
						<div class="opcje">
							
							<div class="col-6" style="display: inline-block;">
								<a href="edytuj.php" class="link">
									<div id="edytuj">
										<figure style="margin:0px;">
											Edytuj dane
										</figure>
									</div>
								</a>
							</div>

							<div class="col-6"  style="display: inline-block;">
								<label class="link"><div id="usun"> <figure style="margin:0px;">Usuń konto</figure>
								<form action="usun.php" method="post" onsubmit="if (!confirm('Czy na pewno chcesz usunąć konto?')) return false">
								<input type="submit" value="usuń konto" name="submit" style="display: none;"/>
								</form></div>
								</label>	
							</div>
								
							<div class="col-12"  style="display: inline-block;">
								<a href="logout.php" class="link">
									<div id="wyloguj">
										<figure style="margin:0px;">
											WYLOGUJ
										</figure>
									</div>
								</a>
							</div>
							
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