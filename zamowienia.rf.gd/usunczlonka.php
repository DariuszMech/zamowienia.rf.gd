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
	$ile_takich_wynikow = $rezultat->num_rows;
	if($ile_takich_wynikow!=1)
	{
		header('Location: moje.php');
		exit();
	}
	
	//czy użytkownik jest szefem tej grupy?
	$rezultat = $polaczenie->query(" SELECT grupa.idszefagrp FROM grupa WHERE grupa.idgrp='$id' and grupa.idszefagrp='{$_SESSION["id"]}'  ");			
	if(!$rezultat) throw new Exception($polaczenie->error);			
	$ile_takich_wynikow = $rezultat->num_rows;
	if($ile_takich_wynikow==0)
	{
		header("Location: zarzadzaj.php?id=$id");
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
							echo "<nav class='sticky'><a href='zarzadzajszef.php?id=$id' class='link'><div class='cofnij'>COFNIJ</div></a><a href='menu.php' class='link'><div class='cofnijmenu'>MENU</div></a></nav>";
						?>
						<header class="col-12">
                        <div class="hdr" style="letter-spacing:0px">
								<?php	
									echo "USUŃ&nbsp;CZŁONKA";
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
						
						
			<div class="row">
						
								
				<div class="col-12" style="text-align:center;">		
<?php
echo<<<END
			<form action="usunietoczlonka.php?id=$id" method="post" >
END;
?>
			<input type="text"  name="usun" class="du" placeholder="WPISZ ID UŻYTKOWNIKA" 
			 value="<?php if(isset($_SESSION['fr_us']))
							{
								echo $_SESSION['fr_us'];
								unset ($_SESSION['fr_us']);
							}
							?>">
<?php							
			if(isset($_SESSION['e_us']))
			{
				echo '<div class="error">'.$_SESSION['e_us'].'</div>';
				unset($_SESSION['e_us']);		
			}
?>
<?php
echo<<<END
			<br/>			
			<input type="submit" class="zcz" style="background-color:red; color:white;" value="usuń członka z grupy" name="submit" />
			</form>	
END;
?>
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