

<div class="nav noPrint" onmouseover="resizeFrame()" onmouseout="minimizeFrame()" style="align-content: center;">

	<ul class="select">
		<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
	</ul>

	<ul class="select" style="background-color: #419873;">
		<li><a href="#">Accueil &nbsp;&raquo;</a>
			<div class="select_sub">
				<ul class="sub">
					<li><a href="index.php" target="_top">Accueil</a></li>
				</ul>
				<ul class="sub">
					<li><a href="deconnect.php" target="_top">Déconnexion</a></li>
				</ul>
			</div>
	      </li>
	</ul>

	<ul class="select" style="background-color: #419873;">
		<li><a href="#">Agenda &nbsp;&raquo;</a>
			<div class="select_sub">
				<ul class="sub">
					<li><a href="agenda.php" target="_top">Liste des rendez-vous</a></li>
				</ul>
				<ul class="sub">
					<li><a href="rdv_repetes.php" target="_top">Rendez-vous par lots</a></li>
				</ul>
			</div>
		</li>
	</ul>

	<ul class="select" style="background-color: #419873;">
		<li><a href="#">Patients &nbsp;&raquo;</a>
			<div class="select_sub">
				<ul class="sub">
					<li><a href="liste.php" target="_top">Recherche de patient</a></li>
				</ul>
				<ul class="sub">
					<li><a href="nouveau_dossier.php?envoyer=Nouveau" target="_top">Nouveau dossier</a></li>
				</ul>
			</div>
		</li>
	</ul>

	<ul class="select" style="background-color: #419873;">
		<li><a href="#">Intervenants &nbsp;&raquo;</a>
			<div class="select_sub">
				<ul class="sub">
					<li><a href="correspondant.php" target="_top">Recherche d'intervenant</a></li>
				</ul>
				<ul class="sub">
					<li><a href="formulaire_correspondant.php?envoyer=Nouveau" target="_top">Nouvel intervenant</a></li>
				</ul>
			</div>
		</li>
	</ul>

	<ul class="select" style="background-color: #419873;">
		<li><a href="activite.php" target="_top" >Activit&eacute;</a></li>
	</ul>

	<ul class="select" style="background-color: #419873;">
		<li><a href="#">Medicaments &nbsp;&raquo;</a>
			<div class="select_sub">
				<ul class="sub">
					<li><a href="medicaments.php" target="_top">Fiche médicament</a></li>
				</ul>
				<ul class="sub">
					<li><a href="interactions.php" target="_top">Interactions</a></li>
				</ul>
			</div>
		</li>
	</ul>


 
	

	
</div>
<?php
	//recuperation des variables du menu horizontal
	$database = '';
	$espacepro = '';
	if (isset($_GET["database"]))
	  $database = $_GET["database"];
	if (isset($_GET["espacepro"]))
	  $espacepro = $_GET["espacepro"];

if ($database) {
	include("outils/myigsr.php");
	exit;
}
elseif ($espacepro) {
	include("outils/espacepro.php");
	exit;
}
?>
