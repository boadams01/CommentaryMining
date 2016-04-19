
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

	$("#commentaryid").change(function(){
    	drawChart();
	})
	$("#showverses").change(function(){
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
           var showverses=$("#showverses").val();
           var charttype=$("#charttype").val();
           var showpercent=$("#showpercent").val();
            var jsonData = $.ajax({
                url: "getcommdata.php",
                dataType: "json",
                type: 'GET',
                data: { commentaryid: commid, showverses: showverses, showpercent: showpercent} ,
                async: false
            }).responseText;

            
            var data = new google.visualization.DataTable(jsonData);
			
            var options = {
                title: 'Word Count',
                height: 600,
                hAxis: {title: "Section of the Bible"},
                vAxis: {title: "Number of Words in Commentary"},
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
            else if(charttype=="pie") {
                var chart = new google.visualization.PieChart(
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
<h2>Individual Commentary Analysis</h2>
</td>
</tr>
<tr>
<td>Which commentary do you want to show?</td>
<td><select name="commentaryid" id="commentaryid">
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
<td>Group data by Biblical chapters or verses?</td>
<td>
<select name="showverses" id="showverses">
<option value=0>Chapters</option>
<option value=1>Verses</option>
</td></tr>
<tr>
<td>Show Unit Word Count or Percent of Total Word Count?</td>
<td>
<select name="showpercent" id="showpercent">
<option value=0>Unit Word Count</option>
<option value=1>Percent of Total Word Count</option>
</td></tr>
<tr>
<td>What kind of chart do you want?</td>
<td>
<select id="charttype" name="charttype">
<option value="pie">Pie Chart</option>
<option value="bar" selected>Bar Chart</option>
<option value="line">Line Chart</option>
</select>
</td></tr>

</tbody>
</table>
<div id="chart_div"></div>
