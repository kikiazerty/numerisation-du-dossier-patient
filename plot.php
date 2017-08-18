<?php
?>
<!DOCTYPE HTML>
<html>
<head>
<title>
Courbe du patient <?php echo $_REQUEST['nom'].' '. $_REQUEST['prenom'] ?>- MedWebTux
</title>
<!-- <script src="http://canvasjs.com/assets/script/canvasjs.min.js"></script> -->
<script src="scripts/canvasjs.min.js"></script>
<script type="text/javascript">
window.onload = function () {
	var chart = new CanvasJS.Chart("chartContainer",
	{
		animationEnabled: true,
		zoomEnabled:true,
		title:{
			text: "<?php echo $_REQUEST['titre'].' de '.$_REQUEST['nom'].' '. $_REQUEST['prenom'] ?>"
		},
		data: [
		<?php
		$titles=$_REQUEST['titres'];
		$array_titles=explode ('|',$titles);
		$count_titles=count($array_titles);
		$dates=$_REQUEST['dates'];
		$array_dates=explode('|',$dates);
		$array_dates=array_filter($array_dates);
		$count_dates=count($array_dates);
		$number_loops=0;
		$indice=0;
		$values=$_REQUEST['values'];
		$array_values=explode('|',$values); //toutes les valeurs dans un tableau

		foreach ($array_titles AS $this_title) //les titres de courbes
		{ //php
		?>
		{ //javascript

			type: "line", //change type to bar, line, area, pie, etc
			showInLegend: true,    
                        legendText: "<?php echo $this_title ?>",
			dataPoints: [
			<?php
			$number_dates=1;
			$number_values=0;
			foreach ($array_dates AS $this_date)
			{
                          $indice=$number_loops+($number_values*$count_titles);
                          if (!empty(trim($array_values[$indice])))
                            echo "{ x: new Date(".substr($this_date,0,4).", ".substr($this_date,5,2).", ".substr($this_date,8,2)."), y: $array_values[$indice] }";
                          $number_dates++;
                          if ($number_dates<=$count_dates AND !empty(trim($array_values[$indice])))
                            echo ",
                            ";
                          $number_values++;
                        }
			?>
			]
                  }
		<?php
		$number_loops++;
		if ($number_loops<$count_titles)
                  echo ','; //virgule pour chaque tableau sauf pour le dernier
		} //php
		?>
		],
		legend: {
			cursor: "pointer",
			itemclick: function (e) {
				if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
					e.dataSeries.visible = false;
				} else {
					e.dataSeries.visible = true;
			}
			chart.render();
			}
		}
	});

	chart.render();
}
</script>
</head>
<body>
<div id="chartContainer" style="height: 300px; width: 100%;"></div>
</body>

</html>
