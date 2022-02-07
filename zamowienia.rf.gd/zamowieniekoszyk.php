<?php

	session_start();

	ini_set("display_errors", 0);
	require_once 'connect.php';
	$polaczenie = mysqli_connect($host, $db_user, $db_password);
	//mysqli_query($polaczenie, "SET CHARSET utf8");
	//mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	mysqli_select_db($polaczenie, $db_name);
		
	$id = $_GET["id"];
	$idzam = $_GET["idzam"];
	
	//czy użytkownik należy do tej grupy?
	$rezultat = $polaczenie->query(" SELECT grupowicze.idgr, zamowienia.idzam FROM grupowicze, zamowienia WHERE grupowicze.idgr='$id' and grupowicze.idczlonkagr='{$_SESSION["id"]}' and zamowienia.idzam='$idzam' and zamowienia.idgrpzam=grupowicze.idgr ");			
	if(!$rezultat) throw new Exception($polaczenie->error);
				
	$ile_takich_maili = $rezultat->num_rows;
	if($ile_takich_maili!=1)
	{
		header('Location: moje.php');
		exit();
	}
	
	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}
	
			$rezultat = mysqli_query($polaczenie, "SELECT grupa.nazwagrp, zamowienia.idzam, zamowienia.idgrpzam, zamowienia.adreszam, zamowienia.status,  zamowienia.statusopis, zamowienia.telefonzam, zamowienia.opiszam, zamowienia.notatkizam, zamowienia.imienazwiskozam, zamowienia.kwotazam, zamowienia.data1zam, zamowienia.data2zam , zamowienia.datadodaniazam, zamowienia.dataedycji from grupa, grupowicze, zamowienia, uzytkownicy where zamowienia.idgrpzam='$id'  and zamowienia.idgrpzam = grupa.idgrp and grupowicze.idczlonkagr='{$_SESSION["id"]}' and grupowicze.idgr = grupa.idgrp and zamowienia.idzam = '$idzam' and uzytkownicy.id=zamowienia.iddodajacego
			ORDER BY `zamowienia`.`data1zam` DESC");
		
			$row = mysqli_fetch_assoc($rezultat);
			$nazwagrp = $row['nazwagrp'];
			$idzam = $row['idzam'];
			$adreszam = $row['adreszam'];
			$imienazwiskozam = $row['imienazwiskozam'];
			$telefonzam = $row['telefonzam'];
			$opiszam = $row['opiszam'];
			$notatkizam = $row['notatkizam'];
			$kwotazam = $row['kwotazam'];
			$data1zam = $row['data1zam'];
			$data2zam = $row['data2zam'];
			$datadodaniazam = $row['datadodaniazam'];
			$dataedycji = $row['dataedycji'];
			$status = $row['status'];
			$statusopis = $row['statusopis'];
			
			$rezultat = mysqli_query($polaczenie, "SELECT zamowienia.iddodajacego, uzytkownicy.imie, uzytkownicy.nazwisko from grupa, grupowicze, zamowienia, uzytkownicy where zamowienia.idgrpzam='$id'  and zamowienia.idgrpzam = grupa.idgrp and grupowicze.idczlonkagr='{$_SESSION["id"]}' and grupowicze.idgr = grupa.idgrp and zamowienia.idzam = '$idzam' and uzytkownicy.id=zamowienia.iddodajacego");
			
			$row = mysqli_fetch_assoc($rezultat);
			$iddodajacego = $row['iddodajacego'];
			$imiedod = $row['imie'];
			$nazwiskodod = $row['nazwisko'];
						
			$rezultat = mysqli_query($polaczenie, "SELECT uzytkownicy.imie, uzytkownicy.nazwisko ,zamowienia.idedytujacego from grupa, grupowicze, zamowienia, uzytkownicy where zamowienia.idgrpzam='$id'  and zamowienia.idgrpzam = grupa.idgrp and grupowicze.idczlonkagr='{$_SESSION["id"]}' and grupowicze.idgr = grupa.idgrp and zamowienia.idzam = '$idzam' and uzytkownicy.id=zamowienia.idedytujacego");
			
			$row = mysqli_fetch_assoc($rezultat);
			$imieedyt = $row['imie'];
			$nazwiskoedyt = $row['nazwisko'];	
			$idedytujacego = $row['idedytujacego'];			
			
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
							echo "<nav class='sticky'><a href='koszyk.php?id=$id' class='link'><div class='cofnij'>COFNIJ</div></a><a href='menu.php' class='link'><div class='cofnijmenu'>MENU</div></a></nav>";
						?>
						<header class="col-12">
                        <div class="hdr" style="letter-spacing:0px">
						<?php	
							echo "<b>".$nazwagrp."</b><br/>Zamówienie nr: ".$idzam;
						?>
						</div>
						</header>
						<?php
							require_once 'powiadomienie.php';
						?>
						
						
				<div class="row" style="background-color:#02097d;; text-align:center;">
				
<?php	

	if(isset($_POST['koszyk']))
	{		
				$rezultat = $polaczenie->query(" DELETE FROM `koszyk` WHERE koszyk.idzam ='$idzam' and koszyk.idu='{$_SESSION["id"]}' ");	
				if($rezultat) 
				{
					$_SESSION['powiadomienie']="Usunięto z koszyka zamówienie nr: $idzam";
					header("Location: koszyk.php?id=$id");
					die();
				}
				else
				{
					throw new Exception($polaczenie->error);
					
				}		
	}
	
		
	if(isset($_POST['usunzamowienie']))
	{
		$rezultat = mysqli_query($polaczenie, "DELETE FROM `zamowienia` WHERE idzam = '$idzam' and idgrpzam='$id' ");
		
		$rezultat = mysqli_query($polaczenie, "Select zdjecia.lokalizacja from zdjecia WHERE idz='$idzam' ");
														$ile = mysqli_num_rows($rezultat);
														for ($a= 1; $a <= $ile; $a++) 
														{
															$row = mysqli_fetch_assoc($rezultat);
															$lok = $row['lokalizacja'];
															unlink($lok);
														}
						
		$rezultat = mysqli_query($polaczenie, "DELETE FROM zdjecia WHERE idz = '$idzam' ");

		$_SESSION['powiadomienie']="Zamówieni nr: $idzam zostało usunięte";
		header("Location: koszyk.php?id=$id");
		die();
		
	}

	if(isset($_POST['select4']))
			{
				$select4=$_POST['select4'];
				$_SESSION["fraza"]=$_POST['fraza'];	
				
					if($select4=="aktywne")
					{
						$s="UPDATE zamowienia SET status='aktywne' ,statusopis='{$_SESSION["fraza"]}' WHERE idzam='$idzam' ";
						echo" dupa1";
					}
					else if($select4=="skonczone")
					{
						$s="UPDATE zamowienia SET status='skonczone' ,statusopis='{$_SESSION["fraza"]}' WHERE idzam='$idzam' ";
						echo" dupa2";
					}
					else if($select4=="problem")
					{
						$s="UPDATE zamowienia SET status='problem' ,statusopis='{$_SESSION["fraza"]}' WHERE idzam='$idzam' ";
						echo" dupa3";
					}
					else if($select4=="reklamacja")
					{
						$s="UPDATE zamowienia SET status='reklamacja' ,statusopis='{$_SESSION["fraza"]}' WHERE idzam='$idzam' ";
						echo" dupa4";
					}
							
							
					if($polaczenie->query($s))
					{
						unset ($_SESSION["fraza"]);
						header("Location: zamowieniekoszyk.php?id=$id&idzam=$idzam");
					}
					else
					{	
						throw new Exception($polaczenie->error);	
					}
										
			}
		$datadzisiaj=date('Y-m-d');
		
		if($status=="skonczone"){
			$bc = "style='background-color: #10de13' ";
		}
		else if($status=="reklamacja"){
			$bc = "style='background-color: #6e695c'";
		}
		else if($status=="problem"){
			$bc = "style='background-color:#cbcf06 '";
		}
		else if($datadzisiaj > $data2zam){
			$bc = "style='background-color: #c93028'";
		} 
		else if($status=="aktywne" or $status=="brak"){
			$bc = "style='background-color: #1ab0b8 '";
		}
		
		
			
			
echo<<<END
		
			<div class="zs" $bc><div class="lefts" style="color:black;"> $status<div style="color:#b0ffba; float:left;">Status: &nbsp;</div>
									
			<label for="checkbox2" id="checkboxLabel2" style="float:right;"><div class="zmns">zmień status</div></label>
							
			</div>
			<div style="color:#b0ffba; float:left; font-weight:600;">Dodatkowy opis:</div></br>$statusopis</div>
			<input type="checkbox" id="checkbox2"/>
			<div id="stuffToShow2" style="float:left; font-size:18px; background-color:black;">
				<form method="post">				 
						
					<select name="select4" class="sel2" style="margin:5px 0px 15px 0px">
						<option value="problem">Problem</option>
						<option value="aktywne">Aktywne</option>
						<option value="skonczone">Zakończone</option>
						<option value="reklamacja">Reklamacja</option>
					</select>
				</br>
				Dodatkowy opis:					
					<textarea  class="slowoz" name="fraza"  rows="5"/>
						$statusopis
					</textarea>
					<label class="sorsub2">Zatwierdz<input type="submit" style="display:none"></label>
				</form>
			</div>
			
			<div class="z1"><div class="left2">adres zamowienia:<br/></div>$adreszam<br/><br/></div>
			<div class="z2"><div class="left2">klient:<br/></div> $imienazwiskozam <br/><br/></div>
			<div class="z1"><div class="left2">tel:<br/></div> $telefonzam<br/><br/></div>
			<div class="z2"><div class="left2">opis:<br/></div> $opiszam<br/><br/></div>
			<div class="z1"><div class="left2">notatki:<br/></div>$notatkizam<br/><br/></div>
			<div class="z2"><div class="left2">kwota:<br/></div> $kwotazam<br/><br/></div>
			<div class="z1d"><div class="left2">Termin:<br/></div><div class="left2d">Od:</div>$data1zam<div style="width:32px; height:20px; float:right;"></div><br/></div>
			<div class="z1d"><div class="left2d" style="margin-left:2px;">Do:</div>$data2zam<div style="width:32px; height:20px; float:right;"></div></div>
			<div class="z3"><div class="left22">zdjęcia:<br/></div>
END;

			$rezultat = mysqli_query($polaczenie, "SELECT `lokalizacja` FROM `zdjecia` WHERE zdjecia.idz='$idzam' ");
			$ile = mysqli_num_rows($rezultat);

			for ($i = 1; $i <= $ile; $i++) 
			{		
				$row = mysqli_fetch_assoc($rezultat);
				$lokalizacja = $row['lokalizacja'];
echo<<<END
				
				<a href=$lokalizacja target='_blank'> <img src=$lokalizacja style="max-width:90%;"/></a><br/><br/>
END;
			}
			
echo<<<END
			<div class="z1d"><div class="left2d" style="width:42%; text-align:left;">Dodano:</div><div class="dright">$datadodaniazam</div></div>
			<div class="z1d"><div class="left2d" style="width:35%; text-align:left;">Przez:</div><div class="dright">$imiedod $nazwiskodod</div></div>
			<div class="z1d"><div class="left2d" style="width:42%; text-align:left;">Edytowano:</div><div class="dright">$dataedycji</div></div>
			<div class="z1d" style="padding:5px 5px 15px 5px;"><div class="left2d" style="width:35%; text-align:left;">Przez:</div><div class="dright">$imieedyt $nazwiskoedyt</div></div>
			</div>		
END;
		
			
				$adreskosz = mysqli_query($polaczenie, "SELECT koszyk.id, koszyk.idzam, koszyk.idu, uzytkownicy.imie, uzytkownicy.nazwisko FROM koszyk, uzytkownicy WHERE koszyk.idzam='$idzam' and uzytkownicy.id=koszyk.idu ");
				$c = mysqli_num_rows($adreskosz);
				if($c>=1)
				{	
					echo "<div class='wk'><div class='left2dk'>W koszyku u:</div>";
					
					for ($v = 1; $v <= $c; $v++) 
					{		
						$row = mysqli_fetch_assoc($adreskosz);
						$imiekosz = $row['imie'];
						$nazwiskokosz = $row['nazwisko'];
echo<<<END
						<div class="fs">$imiekosz $nazwiskokosz </div>			
										
END;
					}			
					echo "</div>";
				}
				
echo<<<END
			<label class="link"><div class="ddkoszyka">Usuń z koszyka
			<form method="post">
			<input type="submit" name="koszyk" style="display: none;"/>
			</form></div>
			</label>
			
			<a href='edytujzamkoszyk.php?id=$id&idzam=$idzam' class='link'><div class='edytujzam'>Edytuj zamówienie</div></a>
			
			<label class="link"><div class="usunzamowienie">Usuń zamówienie
			<form method="post" onsubmit="if (!confirm('Czy na pewno chcesz usunąć zamówienie $idzam?')) return false">
			<input type="submit" value="usuń konto" name="usunzamowienie" style="display: none;"/>
			</form></div>
			</label>
END;
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