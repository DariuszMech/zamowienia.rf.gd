<?php

	session_start();

	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}
	
	ini_set("display_errors", 0);
	require_once 'connect.php';
	$polaczenie = mysqli_connect($host, $db_user, $db_password);;
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
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="style5.css" type="text/css" />
</head>
<body>
<main>
	
		
		
			<div class="contaiiner">
			
						<?php	
							echo "<nav class='sticky'><a href='opcje.php?id=$id' class='link'><div class='cofnij'>COFNIJ</div></a><a href='menu.php' class='link'><div class='cofnijmenu'>MENU</div></a></nav>";
						?>
						<header class="col-12">
							<div class="hdr" style="letter-spacing:0px">
								<?php	
									echo "LISTA&nbsp;ZAMÓWIEŃ";
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
						
						
	<div class="row" style="text-align:center">
	
<?php
echo<<<END

<div id="pasek">
	<label for="checkbox" class="col-12">
	<div class="sort"><div class="sortuj">SORTUJ</div></div>
	</label>
		<input type="checkbox" id="checkbox"/>
			<div id="stuffToShow">
				<form method="post">
				<div class="col-3" style="display:inline-block">
					Pokaż: 
				</div>
				<div class="col-8" style="display:inline-block">
					<select name="select1" class="sel">
						<option value="Niezakończone">Niezakończone</option>
						<option value="Zakończone">Zakończone</option>
                        <option value="Problem">Problem</option>
						<option value="Wszystkie">Wszystkie</option>
					</select>
				</div><br/><br/>
				
				<div class="col-3" style="display:inline-block">
					Sortuj po:
				</div>
				<div class="col-8" style="display:inline-block">
					<select name="select2" class="sel">						
						<option value="2 Data">2 Data</option>
						<option value="1 Data">1 Data</option>
						<option value="Numer zamówienia">Numer zamówienia</option>
					</select>
				</div><br/><br/>
				
				<div class="col-3" style="display:inline-block">
					Kolejność:
				</div>
				<div class="col-8" style="display:inline-block">
					<select name="select3" class="sel">
						<option value="Rosnąco">Rosnąco</option>
						<option value="Malejąco">Malejąco</option>
					</select>
				</div><br/><br/>
					<label class="col-12"><div class="sorsub">Zatwierdz<input type="submit" style="display:none"></div></label>
				</form>
			</div>

			
	<label for="checkbox2" class="col-12">
	<div class="sort2"><div class="sortuj">SZUKAJ ZAMÓWIENIA</div></div>
	</label>
		<input type="checkbox" id="checkbox2"/>
			<div id="stuffToShow2">
				<form method="post">				 
				<div class="col-12">
					Szukaj zamówienia o:
				</div>
				<div class="col-12">
					<select name="select4" class="sel2">
						<option value="Numerze">Numerze</option>
						<option value="Adresie">Adresie</option>
						<option value="Telefonie">Telefonie</option>
						<option value="Imieniu lub nazwisku">Imieniu lub nazwisku</option>
					</select>
				</div><br/>
					<input type="text" name="fraza" class="mar" placeholder="tutaj wpisz szukaną frazę"/>
					<label class="col-12"><div class="sorsub">Szukaj<input type="submit" style="display:none"></div></label>
				</form>
			</div>			
</div>

END;

	$datadzisiaj=date('Y-m-d');
	//&nbsp;  - spacja

	$select1=$_POST['select1'];
	$select2=$_POST['select2'];
	$select3=$_POST['select3'];
	$select4=$_POST['select4'];
	
	$pokaz  = file_get_contents("txt/niezakonczone.txt");
	
	if($select1=="Wszystkie")
	{
		$pokaz  = '';
	}
    else if($select1=="Problem")
	{
		$pokaz  = file_get_contents("txt/problem.txt");
	}
	else if($select1=="Zakończone")
	{
		$pokaz  = file_get_contents("txt/zakonczone.txt");
	}
	
	
	
	if($select2=="Numer zamówienia")
	{
		$order  = file_get_contents("txt/Numer zamówienia.txt");
	}
	else if($select2=="1 Data")
	{
		$order  = file_get_contents("txt/1 Data.txt");
	}
	else
	{
		$order  = file_get_contents("txt/2 Data.txt");
	}
	
	
	if($select3=="Malejąco")
	{
		$jak  = file_get_contents("txt/Malejąco.txt");
	}
	else{
		$jak = file_get_contents("txt/Rosnąco.txt");
	}
	
	$s ="SELECT grupa.nazwagrp, zamowienia.idzam, zamowienia.idgrpzam, zamowienia.adreszam, zamowienia.imienazwiskozam, zamowienia.kwotazam, zamowienia.data1zam, zamowienia.data2zam, zamowienia.status from grupa, grupowicze, zamowienia where zamowienia.idgrpzam='$id'  $pokaz and zamowienia.idgrpzam = grupa.idgrp and grupowicze.idczlonkagr='{$_SESSION["id"]}' and grupowicze.idgr = grupa.idgrp 
	ORDER BY zamowienia .$order $jak";
	
	$_SESSION["fraza"]=$_POST['fraza'];		
	
	if($select4=="Numerze")
	{
		$s="SELECT grupa.nazwagrp, zamowienia.idzam, zamowienia.idgrpzam, zamowienia.adreszam, zamowienia.imienazwiskozam, zamowienia.kwotazam, zamowienia.data1zam, zamowienia.data2zam, zamowienia.status from grupa, grupowicze, zamowienia where zamowienia.idgrpzam='$id'   and zamowienia.idgrpzam = grupa.idgrp and grupowicze.idczlonkagr='{$_SESSION["id"]}' and grupowicze.idgr = grupa.idgrp  and zamowienia.idzam Like '%{$_SESSION["fraza"]}%'
		ORDER BY zamowienia .$order $jak";
	}
	else if($select4=="Adresie")
	{
		$s="SELECT grupa.nazwagrp, zamowienia.idzam, zamowienia.idgrpzam, zamowienia.adreszam, zamowienia.imienazwiskozam, zamowienia.kwotazam, zamowienia.data1zam, zamowienia.data2zam, zamowienia.status from grupa, grupowicze, zamowienia where zamowienia.idgrpzam='$id'   and zamowienia.idgrpzam = grupa.idgrp and grupowicze.idczlonkagr='{$_SESSION["id"]}' and grupowicze.idgr = grupa.idgrp  and zamowienia.adreszam Like '%{$_SESSION["fraza"]}%'
		ORDER BY zamowienia .$order $jak";
	}
	else if($select4=="Telefonie")
	{
		$s="SELECT grupa.nazwagrp, zamowienia.idzam, zamowienia.idgrpzam, zamowienia.adreszam, zamowienia.imienazwiskozam, zamowienia.kwotazam, zamowienia.data1zam, zamowienia.data2zam, zamowienia.status from grupa, grupowicze, zamowienia where zamowienia.idgrpzam='$id'   and zamowienia.idgrpzam = grupa.idgrp and grupowicze.idczlonkagr='{$_SESSION["id"]}' and grupowicze.idgr = grupa.idgrp  and zamowienia.telefonzam Like '%{$_SESSION["fraza"]}%'
		ORDER BY zamowienia .$order $jak";
	}
	else if($select4=="Imieniu lub nazwisku")
	{
		$s="SELECT grupa.nazwagrp, zamowienia.idzam, zamowienia.idgrpzam, zamowienia.adreszam, zamowienia.imienazwiskozam, zamowienia.kwotazam, zamowienia.data1zam, zamowienia.data2zam, zamowienia.status from grupa, grupowicze, zamowienia where zamowienia.idgrpzam='$id'   and zamowienia.idgrpzam = grupa.idgrp and grupowicze.idczlonkagr='{$_SESSION["id"]}' and grupowicze.idgr = grupa.idgrp  and zamowienia.imienazwiskozam Like '%{$_SESSION["fraza"]}%'
		ORDER BY zamowienia .$order $jak";
	}
	
	
	$rezultat = mysqli_query($polaczenie, $s);
	unset ($_SESSION["fraza"]);
	$ile = mysqli_num_rows($rezultat);

	for ($i = 1; $i <= $ile; $i++) 
	{		

		$row = mysqli_fetch_assoc($rezultat);
		$idzam = $row['idzam'];
		$adreszam = $row['adreszam'];
		$imienazwiskozam = $row['imienazwiskozam'];
		$kwotazam = $row['kwotazam'];
		$data1zam = $row['data1zam'];
		$data2zam = $row['data2zam'];
		$status = $row['status'];
		
	if($status=="skonczone"){
			$bc1="class='skonczone1'";
			$bc2="class='skonczone2'";
		}
		else if($status=="reklamacja"){
			$bc1 = "class='reklamacja1'";
			$bc2 = "class='reklamacja2'";
		}
        else if($status=="problem"){
			$bc1 = "class='problem1'";
			$bc2 = "class='problem2'";
		}
		else if($datadzisiaj > $data2zam){
			$bc1 = "class='po_terminie1'";
			$bc2 = "class='po_terminie2'";
		}
		else if($status=="aktywne" or $status=="brak"){
			$bc1 = "class='aktywne1'";
			$bc2 = "class='aktywne2'";
		}
		
		
		$adreskosz = mysqli_query($polaczenie, "SELECT `id`, `idzam`, `idu` FROM `koszyk` WHERE koszyk.idzam='$idzam' ");
		$c = mysqli_num_rows($adreskosz);
		if($c>=1)
		{	
			$kolor = "style='background-color:orange;'";	
		}
		else
		{
			$kolor ='';
		}
		
echo<<<END
<div class="col-12">
<a href="zamowienie.php?id=$id&idzam=$idzam" class="link2" >
		<div $bc1>
			nr: $idzam<br/>
			<div class="adr" $kolor>$adreszam</div>
			$data1zam ______________
			$data2zam
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