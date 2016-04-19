<!--#passthrough
// <![CDATA[

<script type="mce-text/javascript">
$(document).ready(function(){
    $("#startdate").datepicker();
    $("#enddate").datepicker();

$("#chartselect").change(function(){
    drawChart();
})
})


google.load("visualization", "1", { packages: ["corechart"] });
        google.setOnLoadCallback(drawChart);

        function drawChart() {
            
            var jsonData = $.ajax({
                url: "getpatronvisits.php",
                dataType: "json",
                async: false
            }).responseText;

            //var obj = window.JSON.stringify(jsonData);
            var data = new google.visualization.DataTable(jsonData);
            //var data = google.visualization.arrayToDataTable(obj);

            var options = {
                title: 'Number of Library Visits',
                colors: ['#000000', '#e8e3df', '#ec8f6e', '#f3b49f']
            };
if(document.getElementById('chartselect').value=='pie') {
            var chart = new google.visualization.PieChart(
                        document.getElementById('chart_div'));
}
else {
    var chart = new google.visualization.BarChart(
                        document.getElementById('chart_div'));
}
            chart.draw(data, options);
        }

</script>
//]]
#passthrough-->