<?php

	session_start();
	
	if(isset($_SESSION['zalogowany']) && ($_SESSION['zalogowany']==true))
	{
		header('Location: menu.php');
		exit();
	}

	ini_set("display_errors", 0);
	require_once 'connect.php';
	$polaczenie = mysqli_connect($host, $db_user, $db_password);
	mysqli_query($polaczenie, "SET CHARSET utf8");
	mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	mysqli_select_db($polaczenie, $db_name);
		
	$a = $_GET["a"];
	$b = $_GET["b"];
	
	//czy użytkownik należy do tej grupy?
	$rezultat = $polaczenie->query(" SELECT uzytkownicy.data_kodu  FROM uzytkownicy WHERE uzytkownicy.login='$a' and uzytkownicy.kod_resetu='$b' ");			
	if(!$rezultat) throw new Exception($polaczenie->error);
				
	$ile_takich_maili = $rezultat->num_rows;
	if($ile_takich_maili!=1)
	{
		$_SESSION['blad'] = '<span style = "color:red">Podany link jest nieprawidłowy lub już wygasł</span>';
		header('Location: index.php');
		exit();
	}
	else
	{
			$row = mysqli_fetch_assoc($rezultat);
			$data_kodu = $row['data_kodu'];
			$data_dzisiaj = date("Y-m-d H:i:s");
			if($data_kodu < $data_dzisiaj)
			{
				$rezultat = $polaczenie->query("UPDATE `uzytkownicy` SET `kod_resetu`='-',`data_kodu`='0000.00.00 00:00:00' WHERE uzytkownicy.data_kodu='$data_kodu'");
				
				$_SESSION['blad'] = '<span style = "color:red">Podany link jest nieprawidłowy lub już wygasł</span>';
				header('Location: index.php');
				exit();
			}
			else
			{
				//echo "wszystko git";
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
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css"/>  
</head>
<body>

<main>
	
		
		
			<div class="contaiiner">
			<?php	
							echo "<nav class='sticky'><div class='cofnij' style='opacity:0'>COFNIJ</div><a href='index.php' class='link'><div class='cofnijmenu' >MENU</div></a></nav>";
						?>
						<header class="col-12">
                        <div class="hdr">NOWE&nbspHASŁO</div>
						</header>
						<?php
					require_once 'powiadomienie.php';
						?>
						
						
					<div class="row" style="padding-bottom:400px">
						
						<div class="col-12">
								<?php 
echo<<<END
								<div id="inforeset" style="background-color:#f7aa1b">Ustaw nowe hasło dla użytkownika $a</div>
								<form action='nowehaslo_wpis.php?a=$a&b=$b' method='post'>
									<div id="logowanie" style="margin-top:10px;">
										<input type="password"  class="slowo" placeholder="nowe hasło" name="nhaslo1" style="margin-bottom:10px;"><br/>
										<input type="password"  class="slowo" placeholder="powtórz hasło" name="nhaslo2" style="margin-bottom:10px;"><br/>
									</div>
									
									
									<label style="width:100%">
										<div id="zaloguj">Zatwierdz zmiany
											<input type="submit" value="ZALOGUJ" class="log">
										</div>
									</label>
								</form>
								
END;
									if(isset($_SESSION['blad']))
									{
										echo "<div class='error' >".$_SESSION['blad']."</div>";
										unset($_SESSION['blad']);
									}

								?>
						</div>	
										
							
					</div>
								
			</div>			
	
	
		
		
	</main>
	
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
	
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>

	<script src="bootstrap/js/bootstrap.min.js"></script>

</html>