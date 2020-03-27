//__author__ Patrick Bollmann
//__email__ pbollman@mail.upb.de

<!DOCTYPE HTML>

<html>
<head>
        <meta charset="utf-8">
        <title>SmartSolar</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
    </head>
<body>

<?php
    header('Content-Type: text/html; charset=utf-8');
	
    $mysqli = new mysqli("localhost", "user", "pass", "solar");
	$mysqli->set_charset("utf8");

    if ($mysqli->connect_errno) {
        die("Verbindung fehlgeschlagen: " . $mysqli->connect_error);
    }
	echo "<h1>SmartSolar</h1>";
	//Plot minutes
		$sql = "SELECT date, wh FROM history WHERE timestamp >10000";
		$result = $mysqli->query($sql);
		$power = "[";
		$date = "[";
		while ($row = $result->fetch_assoc()):
			$row["wh"] = $row["wh"]/1000;
			$power = $power.$row["wh"].",";
			$date = $date."'".$row["date"]."',";
		endwhile;
		$power = $power."]";
		$date = $date."]";
	//Plot daily
		$sql = "SELECT date(date), SUM(wh) as power FROM history WHERE timestamp >10000 GROUP BY date(date)";
		$result = $mysqli->query($sql);
		$powerday = "[";
		$dateday = "[";
		while ($row = $result->fetch_assoc()):
			$row["power"] = $row["power"]/1000;
			$powerday = $powerday.$row["power"].",";
			$dateday = $dateday."'".$row["date(date)"]."',";
		endwhile;
		$powerday = $powerday."]";
		$dateday = $dateday."]";
	//Plot monthly
		$sql = "SELECT year(date), month(date), SUM(wh) as power FROM history GROUP BY year(date), month(date)";
		$result = $mysqli->query($sql);
		$powermonth = "[";
		$datemonth = "[";
		while ($row = $result->fetch_assoc()):
			 $row["power"] = $row["power"]/1000;
			 $powermonth = $powermonth.$row["power"].",";
			 $datemonth = $datemonth."'".$row["year(date)"]."-".$row["month(date)"]."',";
		endwhile;
		$powermonth = $powermonth."]";
		$datemonth = $datemonth."]";
	//Plot yearly
		$sql = "SELECT year(date), SUM(wh) as power FROM history GROUP BY year(date)";
		$result = $mysqli->query($sql);
		$poweryear = "[";
		$dateyear = "[";
		while ($row = $result->fetch_assoc()):
			 $row["power"] = $row["power"]/1000;
			 $poweryear = $poweryear.$row["power"].",";
			 $dateyear = $dateyear."'".$row["year(date)"]."',";
		endwhile;
		$poweryear = $poweryear."]";
		$dateyear = $dateyear."]";
	//Plot sum
		$sql = "SELECT year(date), month(date), SUM(wh) as power FROM history GROUP BY year(date), month(date)";
		$result = $mysqli->query($sql);
		$powersum = "[";
		$datesum = "[";
		$lastsum =0;
		while ($row = $result->fetch_assoc()):
			 $row["power"] = $row["power"]/1000;
			 $lastsum = $lastsum+$row["power"];
			 $powersum = $powersum.$lastsum.",";
			 $datesum = $datesum."'".$row["year(date)"]."-".$row["month(date)"]."',";
		endwhile;
		$powersum = $powersum."]";
		$datesum = $datesum."]";
	$mysqli->close();
		?>



<div id='monthly'><!-- Plotly chart will be drawn inside this DIV --></div>
<br>
<div id='precise'><!-- Plotly chart will be drawn inside this DIV --></div>
<br>
<div id='yearly'><!-- Plotly chart will be drawn inside this DIV --></div>
<br>
<div id='sum'><!-- Plotly chart will be drawn inside this DIV --></div>

<!-- Latest compiled and minified plotly.js JavaScript -->
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script>
	var yearis = {
	  x: <?php echo $dateyear; ?>,
	  y: <?php echo $poweryear; ?>,
	  type: 'bar',
	  name: 'Ertrag Jährlich'
	};
	var yearplan = {
	  x: [2012,2013,2014,2015,2016,2017,2018,2019,2020],
	  y: [10000,10000,10000,10000,10000,10000,10000,10000,10000],
	  type: 'scatter',
	  name: 'Ertrag Jährlich Soll'
	};
	var Ertrag = {
	  x: <?php echo $date; ?>,
	  y: <?php echo $power; ?>,
	  type: 'scatter',
	  name: 'Ertrag Stündlich'
	};
	
	var Tag = {
	  x: <?php echo $dateday; ?>,
	  y: <?php echo $powerday; ?>,
	  type: 'scatter',
	  name: 'Ertrag Täglich'

	};
	var Monat = {
	  x: <?php echo $datemonth; ?>,
	  y: <?php echo $powermonth; ?>,
	  type: 'bar',
	  name: 'Ertrag Monatlich'
	};
	var MonatSoll = {
	  x: ['2012-11','2012-12','2013-01','2013-02','2013-03','2013-04','2013-05','2013-06','2013-07','2013-08','2013-09','2013-10','2013-11','2013-12','2014-01','2014-02','2014-03','2014-04','2014-05','2014-06','2014-07','2014-08','2014-09','2014-10','2014-11','2014-12','2015-01','2015-02','2015-03','2015-04','2015-05','2015-06','2015-07','2015-08','2015-09','2015-10','2015-11','2015-12','2016-01','2016-02','2016-03','2016-04','2016-05','2016-06','2016-07','2016-08','2016-09','2016-10','2016-11','2016-12','2017-01','2017-02','2017-03','2017-04','2017-05','2017-06','2017-07','2017-08','2017-09','2017-10','2017-11','2017-12','2018-01','2018-02','2018-03','2018-04','2018-05','2018-06','2018-07','2018-08','2018-09','2018-10','2018-11','2018-12','2019-01','2019-02','2019-03','2019-04','2019-05','2019-06','2019-07','2019-08','2019-09','2019-10','2019-11','2019-12','2020-01','2020-02','2020-03'],
	  y: [400,200,200,400,800,1000,1200,1250,1500,1250,1000,800,400,200,200,400,800,1000,1200,1250,1500,1250,1000,800,400,200,200,400,800,1000,1200,1250,1500,1250,1000,800,400,200,200,400,800,1000,1200,1250,1500,1250,1000,800,400,200,200,400,800,1000,1200,1250,1500,1250,1000,800,400,200,200,400,800,1000,1200,1250,1500,1250,1000,800,400,200,200,400,800,1000,1200,1250,1500,1250,1000,800,400,200,200,400,800],
	  type: 'trace',
	  name: 'Soll Ertrag Monatlich'
	};
	var sumis = {
	  x: <?php echo $datesum; ?>,
	  y: <?php echo $powersum; ?>,
	  type: 'scatter',
	  name: 'Ertrag Monatlich Summiert'
	};
	var sumplanned = {
	  x: [2013,2014,2015,2016,2017,2018,2019,2020,2021],
	  y: [465,10000,20000,30000,40000,50000,60000,70000,80000],
	  type: 'scatter',
	  name: 'Ertrag Monatlich Summiert Plan'
	};

	var dataprecise = [Ertrag, Tag];
	var layout = {
		title:'Erträge Täglich',
		xaxis: {
			autorange: true, 
			domain: [0, 1], 
			range: ['2020-01-01', '2020-04-01'], 
			rangeslider: {range: ['2020-01-01', '2020-02-15']}, 
			title: '<- Datum ->', 
			type: 'date'
		  },
		};
	var datamonthly = [Monat, MonatSoll];
	var layout2 = {
		title:'Erträge Monatlich',
		xaxis: {
			autorange: true, 
			domain: [0, 1], 
			range: ['2019-01-01', '2020-04-01'], 
			rangeslider: {range: ['2017-01-03', '2017-02-15']}, 
			title: '<- Datum ->', 
			type: 'date'
		  }, 
		};
	var datayearly = [yearis, yearplan];
	var layoutyear = {
		title:'Erträge Jährlich',
		xaxis: {
			autorange: true, 
			domain: [0, 1], 
			range: ['2019-01-01', '2020-04-01'], 
			rangeslider: {range: ['2017-01-03', '2017-02-15']}, 
			title: '<- Datum ->', 
			type: 'date'
		  }, 
		};
	var datasum = [sumis, sumplanned];
	var layoutsum = {
		title:'Summiert',
		xaxis: {
			autorange: true, 
			domain: [0, 1], 
			range: ['2019-01-01', '2020-04-01'], 
			rangeslider: {range: ['2017-01-03', '2017-02-15']}, 
			title: '<- Datum ->', 
			type: 'date'
		  }, 
		};


	Plotly.newPlot('precise', dataprecise, layout);
	Plotly.newPlot('monthly', datamonthly, layout2);
	Plotly.newPlot('yearly', datayearly, layoutyear);
	Plotly.newPlot('sum', datasum, layoutsum);
</script>
</body>
</html>
