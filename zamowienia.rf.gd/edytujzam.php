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
	
		$rezultat = mysqli_query($polaczenie, "SELECT grupa.nazwagrp, zamowienia.idzam, zamowienia.idgrpzam, zamowienia.adreszam, zamowienia.telefonzam, zamowienia.opiszam, zamowienia.notatkizam, zamowienia.imienazwiskozam, zamowienia.datadodaniazam, zamowienia.kwotazam, zamowienia.data1zam, zamowienia.data2zam  from grupa, grupowicze, zamowienia where zamowienia.idgrpzam='$id'  and zamowienia.idgrpzam = grupa.idgrp and grupowicze.idczlonkagr='{$_SESSION["id"]}' and grupowicze.idgr = grupa.idgrp and zamowienia.idzam = '$idzam'
		ORDER BY `zamowienia`.`data1zam` DESC");
		
			$row = mysqli_fetch_assoc($rezultat);
			$nazwagrp = $row['nazwagrp'];
			$idzam = $row['idzam'];
			$sadreszam = $row['adreszam'];
			$simienazwiskozam = $row['imienazwiskozam'];
			$stelefonzam = $row['telefonzam'];
			$sopiszam = $row['opiszam'];
			$snotatkizam = $row['notatkizam'];
			$skwotazam = $row['kwotazam'];
			$sdata1zam = $row['data1zam'];
			$sdata2zam = $row['data2zam'];
			$sdatadodania = $row['datadodaniazam'];
			
?>	
<?php	
	if(isset($_POST['submit']))
	{
		//udana walidacja? Załuzmy, że tak!
		$wszystko_OK=true;
		
		$adres = $_POST['adres'];
		$telefon = $_POST['telefon'];
		$imienaz = $_POST['imienaz'];
		$opis = $_POST['opis'];
		$kwota = $_POST['kwota'];
		$notatki = $_POST['notatki'];
		$datazam1 = $_POST['datazam1'];
		$datazam2 = $_POST['datazam2'];
		//Zapamiętaj wprowadzone dane
		$_SESSION['fr_adres'] = $adres;
		$_SESSION['fr_telefon'] = $telefon;
		$_SESSION['fr_imienaz'] = $imienaz;
		$_SESSION['fr_opis'] = $opis;
		$_SESSION['fr_kwota'] = $kwota;
		$_SESSION['fr_notatki'] = $notatki;
		$_SESSION['fr_datazam1'] = $datazam1;
		$_SESSION['fr_datazam2'] = $datazam2;
		
		
		$dataedycji=date('Y-m-d H:i:s');
		
		if( $datazam2 < $dataedycji)
		{
			$wszystko_OK=false;
			$_SESSION['e_data']="Nie można wybrać daty, która już minęła";
		}
		
		if($datazam1 > $datazam2)
		{
			$wszystko_OK=false;
			$_SESSION['e_data']="Pierwsza data nie może być późniejsza od drugiej";
		}
		
		if((empty($datazam1)) or (empty($datazam2)))
		{
			$wszystko_OK=false;
			$_SESSION['e_data']="Musisz podać zakres dat";
		}
			
		require_once 'connect.php';
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try
		{	
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if($polaczenie->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				
				if($wszystko_OK==true)
			{
				//Hurra, wszystkie testy zaliczone, dodajemy gracza do bazy
				$datadodania=date('Y-m-d H:i:s');
				
				
				$zdjeciaOK = true;
				//dodanie zdjęć do zamowienia				
				if(isset($_FILES['file'])) 
					{
						
						$fileCount = count($_FILES['file']['name']);
						
						for($i=0; $i<$fileCount; $i++)
						{ 
							$file = $_FILES['file'];
							//print_r($file);
							
							// file properities
							$file_name = $file['name'][$i];
							$file_tmp = $file['tmp_name'][$i];
							$file_size = $file['size'][$i];
							$file_error = $file['error'][$i];		
					//print_r($file_tmp);
					
							//work out the file extension_loaded
							$file_ext = explode('.', $file_name);
							$file_ext = strtolower(end($file_ext));
					//print_r($file_ext);
										
							$allowed = array ( 'jpg' , 'png');
							
							if (in_array($file_ext, $allowed))
							{
								if($file_error == 0)
								{
									if($file_size <=5097152)
									{		
										$file_name_new = uniqid('', true).'.'.$file_ext;
										$file_destination = 'zdjecia/'.$file_name_new;		
										//echo  $file_name_new;
													$sql = "INSERT INTO zdjeciachwilowe (lokalizacja, idz) VALUES ('$file_destination', '$idzam')";			 
													 if($polaczenie->query($sql) == true)
													 { 
														//echo " Succes!";
														/*$rezultat = mysqli_query($polaczenie, "Select zdjecia.idzdj from zdjecia WHERE zdjecia.lokalizacja='$file_destination' ");
														$ile = mysqli_num_rows($rezultat);
														for ($a= 1; $a <= $ile; $a++) 
														{
															$row = mysqli_fetch_assoc($rezultat);
															$idz = $row['idzdj'];
															//echo $idz;
														}*/
														move_uploaded_file($file_tmp, $file_destination);
														//echo "<img src='$file_destination' width='100px' height='100px'>";
													}
													 else
													{
														//echo " ERROR ";
														$zdjeciaOK = false;
														$_SESSION['problem']="Nie udało się połączyć z bazą";
													}	
									}
									else 
									{
										$zdjeciaOK = false;
										//echo "Zbyt duży plik";
										$_SESSION['problem']="Zbyt duży plik";
									}
								}	
							}
							else if(strlen($file_ext)<=0) 
							{
								if($fileCount ==1) //jeśli nie ma pliku lub jest plik bez rozszerzenia
								{
									$zdjeciaOK = true;
								}
								else
								{
									$zdjeciaOK = false;
									//echo "tutaj!!!!!!!!!";
								}										
							}
							else 
							{
								$zdjeciaOK = false;
								//echo "<br/>  Można wgrać jedynie pliki o rozszerzeniach 'jpg' lub 'png'";
								$_SESSION['problem']="Można wgrać jedynie pliki o rozszerzeniach 'jpg' lub 'png'";
							}
							//print_r($file_ext);
						}
						
					}
									
					if($zdjeciaOK == false)
					{
						//echo "KKKKKKKOOOOOOOOOONNNNNNNNIIIIIIEEEEECCCCCC";
						 
						 $rezultat = mysqli_query($polaczenie, "Select zdjeciachwilowe.lokalizacja from zdjeciachwilowe WHERE idz='$idzam' ");
														$ile = mysqli_num_rows($rezultat);
														for ($a= 1; $a <= $ile; $a++) 
														{
															$row = mysqli_fetch_assoc($rezultat);
															$lok = $row['lokalizacja'];
															 unlink($lok);
														}
						
						$rezultat = mysqli_query($polaczenie, "DELETE FROM zdjeciachwilowe WHERE idz = '$idzam' ");
						//echo "false";
					}
					else
					{	
						$_SESSION['udanezamowienie']=true;
						
						if($polaczenie->query("UPDATE `zamowienia` SET `adreszam`='$adres',`telefonzam`='$telefon',`imienazwiskozam`='$imienaz',`opiszam`='$opis',`notatkizam`='$notatki',`data1zam`='$datazam1',`data2zam`='$datazam2',`kwotazam`='$kwota', `dataedycji`='$dataedycji', `idedytujacego`='{$_SESSION["id"]}' WHERE zamowienia.idzam = '$idzam' and idgrpzam='$id' "))
						{ 
							$rezultat = mysqli_query($polaczenie, "Select zdjeciachwilowe.lokalizacja from zdjeciachwilowe WHERE idz='$idzam' ");
										$ile = mysqli_num_rows($rezultat);
										for ($a= 1; $a <= $ile; $a++) 
										{
											$row = mysqli_fetch_assoc($rezultat);
											$lok= $row['lokalizacja'];
											$sql = mysqli_query($polaczenie, "INSERT INTO zdjecia (lokalizacja, idz) VALUES ('$lok', '$idzam')");
											//$sql = "INSERT INTO zdjecia (lokalizacja, idz) VALUES ('$lok', '$idzam')";	
										}						
							$rezultat = mysqli_query($polaczenie, "DELETE FROM zdjeciachwilowe WHERE idz = '$idzam' ");
									
							
							//tutaj usunąć zdjęcia wybrane wcześniej
							$rezultat = mysqli_query($polaczenie, "SELECT `lokalizacja` FROM `zdjecia` WHERE zdjecia.idz='$idzam' ");
							$licznik = mysqli_num_rows($rezultat);
										for ($i = 1; $i <= $licznik; $i++) 
										{		
											$row = mysqli_fetch_assoc($rezultat);
											$lokalizacja = $row['lokalizacja'];											
											if(isset($_POST["k$i"]))
											{
												//echo "dupa";
												$usuwanie = mysqli_query($polaczenie, "DELETE FROM zdjecia WHERE lokalizacja='$lokalizacja' ");
												unlink($lokalizacja);	
												//echo "wszsko GIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIITTTTTTTTTTTTTTT";					
											}  		
										}							
						}
						else
						{	
							throw new Exception($polaczenie->error);	
						}
						
						
						header("Location: zamowienie.php?id=$id&idzam=$idzam");	
						//echo "true";
					}					
			}
				$polaczenie->close();
			}
		}
		catch(Exception $e)
		{
			echo '<span style="color: red;"> Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie </span>';
			echo '<br/> Informacja deweloperska: '.$e;		
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

	
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css">
 <script>
  ( function( factory ) {
    if ( typeof define === "function" && define.amd ) {
 
        // AMD. Register as an anonymous module.
        define( [ "../widgets/datepicker" ], factory );
    } else {
 
        // Browser globals
        factory( jQuery.datepicker );
    }
}( function( datepicker ) {
 
datepicker.regional.pl = {
    closeText: "Zamknij",
    prevText: "&#x3C;Poprzedni",
    nextText: "Następny&#x3E;",
    currentText: "Dziś",
    monthNames: [ "Styczeń","Luty","Marzec","Kwiecień","Maj","Czerwiec",
    "Lipiec","Sierpień","Wrzesień","Październik","Listopad","Grudzień" ],
    monthNamesShort: [ "Sty","Lu","Mar","Kw","Maj","Cze",
    "Lip","Sie","Wrz","Pa","Lis","Gru" ],
    dayNames: [ "Niedziela","Poniedziałek","Wtorek","Środa","Czwartek","Piątek","Sobota" ],
    dayNamesShort: [ "Nie","Pn","Wt","Śr","Czw","Pt","So" ],
    dayNamesMin: [ "N","Pn","Wt","Śr","Cz","Pt","So" ],
    weekHeader: "Tydz",
    dateFormat: "yy-mm-dd",
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.pl );
 
return datepicker.regional.pl;
 
} ) );
      $(function() {
        $( ".datepicker" ).datepicker();
      });
  </script>
  
</head>
<body>


	<main>
	
		
		
			<div class="contaiiner">
			
						<?php	
							echo "<nav class='sticky'><a href='zamowienie.php?id=$id&idzam=$idzam' class='link'><div class='cofnij'>COFNIJ</div></a><a href='menu.php' class='link'><div class='cofnijmenu'>MENU</div></a></nav>";
						?>
						<header class="col-12">
							<div class="hdr" style="letter-spacing:0px">
								EDYTUJ ZAMÓWIENIE<br/>
								<?php
									echo "nr: ".$idzam;
								?>
							</div>
						</header>
						<?php
							require_once 'powiadomienie.php';
						?>
						
						
	<div class="row" style="background-color:#08039e; text-align:center;">
				
				<form method="post" enctype="multipart/form-data" style="padding:0px; margin:0px">
				
				<div class="z11">
				Adres: <br/><textarea  class="slowoz"  name="adres" rows="3" />
						<?php
							if(isset($_SESSION['fr_adres']))
							{
								echo $_SESSION['fr_adres'];
								unset ($_SESSION['fr_adres']);
							}
							else{
								echo $sadreszam;
							}
						?></textarea></br>
				</div>

				<div class="z22">
				Telefon: <br/><textarea  class="slowoz"  name="telefon" rows="3"  />
						<?php
							if(isset($_SESSION['fr_telefon']))
							{
								echo $_SESSION['fr_telefon'];
								unset ($_SESSION['fr_telefon']);
							}
							else{
								echo $stelefonzam;
							}
						?></textarea></br>
				</div>

				<div class="z11">
				Imię i Nazwisko: <br/><textarea  class="slowoz"  name="imienaz" rows="3"  />
						<?php
							if(isset($_SESSION['fr_imienaz']))
							{
								echo $_SESSION['fr_imienaz'];
								unset ($_SESSION['fr_imienaz']);
							}
							else{
								echo $simienazwiskozam;
							}
						?></textarea></br>
				</div>
				
				<div class="z22">
				Opis zamówienia: <br/><textarea  class="slowoz"  name="opis" rows="8" style="text-align:left"/>
						<?php
							if(isset($_SESSION['fr_opis']))
							{
								echo $_SESSION['fr_opis'];
								unset ($_SESSION['fr_opis']);
							}
							else{
								echo $sopiszam ;
							}
						?></textarea></br>
				</div>
				
				<div class="z11">				
				<label  class="zal">
				Załącz zdjęcia: <br/>
				<input type='file' name='file[]' id='file' multiple class="polezdj"></br><p3 style="color:yellow; font-size:10px; font-weight:600;">Jeżeli przy zapisywaniu zamówienia wystąpi błąd, załączone&nbsp;zdjęcia&nbsp;zostaną&nbsp;usunięte!</p3></label>
				<?php
					if(isset($_SESSION['problem']))
					{
						echo '<div class="error">'.$_SESSION['problem'].'</div>';
						unset($_SESSION['problem']);		
					}
				?><br/>
				</div>
				
				
				<div class="z11">				
				<div class='usunzdj'>
				Usuń zdjęcia:</br>	
<?php
	ini_set("display_errors", 0);
	require_once 'connect.php';
	$polaczenie = mysqli_connect($host, $db_user, $db_password);
	mysqli_query($polaczenie, "SET CHARSET utf8");
	mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	mysqli_select_db($polaczenie, $db_name);
	
			$rezultat = mysqli_query($polaczenie, "SELECT `lokalizacja` FROM `zdjecia` WHERE zdjecia.idz='$idzam' ");
			$licznik = mysqli_num_rows($rezultat);

			for ($i = 1; $i <= $licznik; $i++) 
			{		
				$row = mysqli_fetch_assoc($rezultat);
				$lokalizacja = $row['lokalizacja'];
echo<<<END
				<label class="uz">
				  <input type="checkbox" name="k$i" style="float:left; width:20%; height:30px;"> <a href=$lokalizacja target='_blank'><div class="linkediv">Podgląd</div></a>
				  <img src=$lokalizacja class="uz" style="height:200px; width:98%; padding:5px 0px 0px 0px;"/>
				 </label>			  
END;
			}  
?>		

				</div>
				</div>
				
				
				
				
				<div class="z22">
				Kwota: <br/><textarea  class="slowoz" name="kwota"  rows="3"  />
						<?php
							if(isset($_SESSION['fr_kwota']))
							{
								echo $_SESSION['fr_kwota'];
								unset ($_SESSION['fr_kwota']);
							}
							else{
								echo $skwotazam;
							}
						?></textarea></br>
				</div>
						
				<div class="z11" style="padding:12px 0px 20px 0px;">
				<div style="padding:0px 0px 10px 0px;">Planowana data dostawy<br/>(&nbsp;zakres&nbsp;dni&nbsp;) [RRRR-MM-DD]<br/></div>
				<?php
					if(isset($_SESSION['e_data']))
					{
						echo '<div class="error">'.$_SESSION['e_data'].'</div>';
						unset($_SESSION['e_data']);		
					}
				?>
				<div class="dlewa">Od:</div><input type="text" class="datepicker" name="datazam1" placeholder="RRRR-MM-DD"
				value="<?php
							if(isset($_SESSION['fr_datazam1']))
							{
								echo $_SESSION['fr_datazam1'];
								unset ($_SESSION['fr_datazam1']);
							}
							else{
								echo $sdata1zam;
							}
						?>"><div class="dprawa">00:01</div><br/>
				<div class="dlewa">Do:</div><input type="text" class="datepicker" name="datazam2" placeholder="RRRR-MM-DD"
				value="<?php
							if(isset($_SESSION['fr_datazam2']))
							{
								echo $_SESSION['fr_datazam2'];
								unset ($_SESSION['fr_datazam2']);
							}
							else{
								echo $sdata2zam;
							}
						?>"><div class="dprawa">23:59</div>
				</div>
				
				<div class="z22">
				Notatki: <br/><textarea  class="slowoz"  name="notatki" rows="8"  />
						<?php
							if(isset($_SESSION['fr_notatki']))
							{
								echo $_SESSION['fr_notatki'];
								unset ($_SESSION['fr_notatki']);
							}
							else{
								echo $snotatkizam;
							}
						?></textarea></br>
				</div>
						
		<label class="dodajz">
		Zapisz
		<input type="submit" value="Edytuj zamówienie" name="submit" style="display:none"/>
		</label>
		
		</form>
				
					
							
								
	</div>			
	
			</div>
	
		
		
	</main>
	
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
	
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>

	<script src="bootstrap/js/bootstrap.min.js"></script>
	
</body>
</html>