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
			
						<header class="col-12" style="font-size:250%;">
                        <div class="hdr" style="padding:5% 0%">MENU</div>
						</header>
						<?php
					require_once 'powiadomienie.php';
						?>
						
						
						<div class="row">
						
							<div class="klocki">
								
								<div class="col-6">
								<a href="moje.php" class="link">
								<div class="kwadrat" style="padding: 50px 5px 41px 5px;">
									<figure>
										
											Moje grupy</br><i class="icon-grupy" style="font-size:200%;"></i>
									
									</figure>
								</div>
								</a>
								</div>
								
								<div class="col-6">
								<a href="profil.php" class="link">
								<div class="kwadrat">
									<figure>
										
											Mój profil</br><i class="icon-profil" style="font-size:200%;"></i>
										
									</figure>
								</div>
								</a>
								</div>
								
								<div class="col-6">
								<a href="utworz.php" class="link">
								<div class="kwadrat">
									<figure>		
										
											Utwórz grupę</br><i class="icon-utworz" style="font-size:200%;"></i>
										
									</figure>
								</div>
								</a>
								</div>
								
								<div class="col-6">
								<a href="zaproszenia.php" class="link">
								<div class="kwadrat">
									<figure>	
										
											Zaproszenia</br><i class="icon-zaproszenia" style="font-size:200%;"></i>
										
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