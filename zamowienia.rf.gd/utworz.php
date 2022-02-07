<?php

	session_start();

	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}
	
	if(isset($_POST['nazwagrp']))
	{
	
		//udana walidacja? Załuzmy, że tak!
		$wszystko_OK=true;
		
		$idszefagrp = $_SESSION['id'];		
		
		if(isset($_FILES['nazwagrp'])) 
		{
			$file = $_FILES['zdjeciegrp'];
			//print_r($file);
			
			// file properities
			$file_name = $file['name'];
			$file_tmp = $file['tmp_name'];
			$file_size = $file['size'];
			$file_error = $file['error'];
			
			//work out the file extension_loaded
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));
						
			$allowed = array ( 'jpg' , 'png');
			
			if (in_array($file_ext, $allowed))
			{
				if($file_error == 0)
				{
					if($file_size <=5097152)
					{		
						$file_name_new = uniqid('', true).'.'.$file_ext;
						$file_destination = 'uploads/zdjeciegrp/'.$file_name_new;		
					}
					else 
					{
						$wszystko_OK = false;
						$_SESSION['e_zdjeciegrp']="Zbyt duży plik";
					}
				}
				
			}
			else if(strlen($file_ext)<=0) 
			{
				unset($_FILES['zdjeciegrp']);
			}
			else 
			{
				$wszystko_OK = false;
				$_SESSION['e_zdjeciegrp']="Można wgrać jedynie pliki o rozszerzeniach 'jpg' lub 'png'";
			}
			//print_r($file_ext);
		}
	
		$nazwagrp = $_POST['nazwagrp'];
		
		
		
		//sprawdzeniedlugosci nicka
		if((strlen($nazwagrp)<3) or (strlen($nazwagrp)>20))
		{
			$wszystko_OK = false;
			$_SESSION['e_nazwagrp']="Nazwa grupy musi posiadać od 3 do 20 znaków";
		}
	
	
		//Zapamiętaj wprowadzone dane
			$_SESSION['fr_nazwagrp'] = $nazwagrp;
			// $_SESSION['fr_zdjeciegrp'] = $file;
		
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
				
				//czy nick jest już zajęty?
				$rezultat = $polaczenie->query(" SELECT idgrp FROM grupa WHERE nazwagrp='$nazwagrp'");
				
				if(!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_nickow = $rezultat->num_rows;
				if($ile_takich_nickow>0)
				{
					$wszystko_OK = false;
				$_SESSION['e_nazwagrp']="Istnieje już grupa o takiej nazwie. Wybierz inną";
				}
				
				
				if($wszystko_OK==true)
			{
				//Hurra, wszystkie testy zaliczone, dodajemy gracza do bazy
				
				if($polaczenie->query("INSERT INTO grupa VALUES (NULL, '$nazwagrp', '$file_destination', '$idszefagrp', 'OK' )"))
				{
					
					if (move_uploaded_file($file_tmp, $file_destination)) 
							{
								$file_destination;
							}
							
					$_SESSION['udanagrupa']=true;
					header("Location: utworzono.php?nazwagrp='$nazwagrp'");	
					die();
				}
				else
				{
					throw new Exception($polaczenie->error);			
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
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css"/>  
</head>
<body>	
	

	
	
	<main>
	
		
		
			<div class="contaiiner">
			
						<?php	
							echo "<nav class='sticky'><a href='menu.php' class='link'><div class='cofnij'>COFNIJ</div></a><a href='menu.php' class='link'><div class='cofnijmenu'>MENU</div></a></nav>";
						?>
						<header class="col-12">
                        <div class="hdr">UTWÓRZ GRUPĘ</div>
						</header>
						<?php
							require_once 'powiadomienie.php';
						?>
						
						
				<div class="row">
						
								
							<div class="col-12"  style="display: inline-block;">
								<form   method="post" enctype="multipart/form-data">
									<!-- <div id="nowa"> -->
									<div id="utworz">
										<input type="text"  class="utworz"  name="nazwagrp" placeholder="nazwa grupy"
										value="<?php
														if(isset($_SESSION['fr_nazwagrp']))
														{
															echo $_SESSION['fr_nazwagrp'];
															unset ($_SESSION['fr_nazwagrp']);
														}
													?>"/></br>
													<?php
															if(isset($_SESSION['e_nazwagrp']))
															{
																echo '<div class="error">'.$_SESSION['e_nazwagrp'].'</div><br/>';
																unset($_SESSION['e_nazwagrp']);		
															}
													/*?>
										  <div id="logo">
											<input type="file" name="zdjeciegrp"  accept="image/*" 
											value="  <?php
															if(isset($_SESSION['fr_zdjeciegrp']))
															{
																echo $_SESSION['fr_zdjeciegrp'];
																unset ($_SESSION['fr_zdjeciegrp']);
															}
														?>"/><br/><br/>
														<?php
																if(isset($_SESSION['e_zdjeciegrp']))
																{
																	echo '<div class="error">'.$_SESSION['e_zdjeciegrp'].'</div>';
																	unset($_SESSION['e_zdjeciegrp']);		
																}*/
														?>
										<!--   </div>   
									</div> -->
									
											<input type="submit" class="utworzsubmit" value="Utwórz grupę"/>
									</div>				
								</form>
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