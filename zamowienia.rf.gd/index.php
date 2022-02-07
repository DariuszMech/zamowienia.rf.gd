<?php

	session_start();

	if(isset($_SESSION['zalogowany']) && ($_SESSION['zalogowany']==true))
	{
		header('Location: menu.php');
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
			
						<header class="col-12">
                        <div class="hdr" >PANEL LOGOWANIA</div>
						</header>
						<?php
					require_once 'powiadomienie.php';
						?>
						
						
					<div class="row" style="padding-bottom:400px">
						
						<div class="col-12">
								<form action="zaloguj.php" method="post">
									<div id="logowanie">
										<input type="text"  class="slowo" placeholder="login" name="login" style="margin-bottom:10px;"><br/>
										<input type="password"  class="slowo" placeholder="hasło" name="haslo">
									</div>
									
									
									<label style="width:100%">
										<div id="zaloguj">ZALOGUJ
											<input type="submit" value="ZALOGUJ" class="log">
										</div>
									</label>
								</form>
								
								<?php

									if(isset($_SESSION['blad']))
									{
										echo "<div class='error' >".$_SESSION['blad']."</div>";
										unset($_SESSION['blad']);
									}

								?>
						</div>	
								
						<div class="col-6">
								<a href="haslo.php" class="link">
									<div id="nph">
									<figure>
										Zapomniałeś </br>hasła?
									</figure>
									</div>
								</a>
						</div>
								
						<div class="col-6">
								<a href="zarejestruj.php" class="link">
									<div id="zarejestruj" >
									<figure>
										Zarejestruj
									</figure>
									</div>
								</a>
						</div>			
							
					</div>
								
			</div>			
	
	
		
		
	</main>
	
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
	
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>

	<script src="bootstrap/js/bootstrap.min.js"></script>

</html>