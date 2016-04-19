<!DOCTYPE html>
<head>
<title>Richard Manly Adams, Jr.</title>

<link rel="stylesheet" type="text/css" href="css/style.css" />

<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
//echo "<script src=\"http://code.jquery.com/jquery-1.10.2.js\" type=\"text/javascript\"></script>";
//echo "<script src=\"http://code.jquery.com/ui/1.11.1/jquery-ui.js\" type=\"text/javascript\"></script>";
//echo "<link href=\"http://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css\" rel=\"stylesheet\" />";
      

require_once("dbinfo.php");


?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
$(document).ready(function(){

	$("#comm1").change(function(){
    	drawChart();
	})
	$("#comm2").change(function(){
    	drawChart();
	})
	$("#chapter").change(function(){
    	drawChart();
	})
	$("#charttype").change(function(){
    	//alert($("#commentaryid").val());
    	drawChart();
	})
})


google.load("visualization", "1", { packages: ["corechart"] });
        google.setOnLoadCallback(drawChart);

        function drawChart() {
           var commid=$("#commentaryid").val();
           var chapter=$("#chapter").val(); //if zero, then that's all chapters
           var comm1=$("#comm1").val();
           var comm2=$("#comm2").val();
           var charttype=$("#charttype").val();
            var jsonData = $.ajax({
                url: "getcomparecomms.php",
                dataType: "json",
                type: 'GET',
                data: { comm1: comm1, comm2: comm2, chapter: chapter} ,
                async: false
            }).responseText;

            
            var data = new google.visualization.DataTable(jsonData);
			
            var options = {
                title: 'Comparing Commentaries for Chapter ' . chapter ,
                height: 600,
                hAxis: {title: "Biblical Text"},
                vAxis: {title: "Percentage of All Words in Commentary"},
                colors: ['red', '#e8e3df', '#ec8f6e', '#f3b49f'],
                isStacked: true
            };
			if(charttype=="line") {
    			var chart = new google.visualization.LineChart(
                    document.getElementById('chart_div'));
            }
            else if(charttype=="bar") {
                var chart = new google.visualization.ColumnChart(
                    document.getElementById('chart_div'));
            }
            

            chart.draw(data, options);
}

</script>
<html>
<body> 
<div id='tabs'> 
<ul class="navtabs"> 
<li><a href='showcomms.php'>Look at Individual Commentaries</a></li> 
<li><a href='comparecomms.php' class="current">Compare 2 Commentaries</a></li> 

</ul> 
</div>
<table border="0">
<tbody>
<tr>
<td colspan="2">
<h2>Comparing Commentaries</h2>
</td>
</tr>
<tr>
<td>Select your first commentary: </td>
<td><select name="comm1" id="comm1">
<option value="0">Please select...</option>
<?php

$commselect="SELECT CommentaryID, Name, Author, PublicationYear FROM Commentary order by PublicationYear desc;";
$commstmt = $db->query($commselect);
while ($row = $commstmt->fetch(PDO::FETCH_ASSOC)) {
	echo "<option value=\"";
	echo $row["CommentaryID"];
	echo "\"";
//if($commentaryid==$row["CommentaryID"]) {echo " selected ";}
	echo ">";
	echo $row["Name"] . ", written by " . $row["Author"] . ", published in " . $row["PublicationYear"];
	echo "</option>";
}

?></select></td>

</tr>
<tr>
<td>Select the commentary you want to compare it with: </td>
<td><select name="comm2" id="comm2">
<option value="0">Please select...</option>
<?php

$commselect="SELECT CommentaryID, Name, Author, PublicationYear FROM Commentary order by PublicationYear desc;";
$commstmt = $db->query($commselect);
while ($row = $commstmt->fetch(PDO::FETCH_ASSOC)) {
	echo "<option value=\"";
	echo $row["CommentaryID"];
	echo "\"";
//if($commentaryid==$row["CommentaryID"]) {echo " selected ";}
	echo ">";
	echo $row["Name"] . ", written by " . $row["Author"] . ", published in " . $row["PublicationYear"];
	echo "</option>";
}

?></select></td>

</tr>
<tr>
<td>Which chapter in 1 Corinthians?</td>
<td>
<select name="chapter" id="chapter">
<option value=-1>Show Only Chapters</option>
<option value=0>Show All Verses for All Chapters</option>
<option value=1>Chapter 1</option>
<option value=2>Chapter 2</option>
<option value=3>Chapter 3</option>
<option value=4>Chapter 4</option>
<option value=5>Chapter 5</option>
<option value=6>Chapter 6</option>
<option value=7>Chapter 7</option>
<option value=8>Chapter 8</option>
<option value=9>Chapter 9</option>
<option value=10>Chapter 10</option>
<option value=11>Chapter 11</option>
<option value=12>Chapter 12</option>
<option value=13>Chapter 13</option>
<option value=14>Chapter 14</option>
<option value=15>Chapter 15</option>
<option value=16>Chapter 16</option>
</td></tr>
<tr>
<td>What kind of chart do you want?</td>
<td>
<select id="charttype" name="charttype">
<option value="bar" selected>Bar Chart</option>
<option value="line">Line Chart</option>
</select>
</td></tr>

</tbody>
</table>
<div id="chart_div"></div>
</body>
</html>