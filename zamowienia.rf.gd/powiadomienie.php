<?php		

			if(isset($_SESSION['powiadomienie']))
			{
echo	"<div id='powiadomienie'>";
echo 	"<div class='leftp'>POWIADOMIENIE:</div></br></br>".$_SESSION['powiadomienie']."";
echo	"</div> ";
			unset($_SESSION['powiadomienie']);
			}			
?>