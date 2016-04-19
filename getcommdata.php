<?php 
//phpinfo();
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);      
require_once("dbinfo.php");


$commentaryid=isset($_GET["commentaryid"])?$_GET["commentaryid"]:0;
$showverses=isset($_GET["showverses"])?$_GET["showverses"]:9;
$showpercent=isset($_GET["showpercent"])?$_GET["showpercent"]:0;


if($showverses)
{
	$selectstmt="select Commentary.Author as Author, Commentary.TotalWordCount as TotalWordCount, Commentary.PublicationYear as Year, CONCAT(CommentaryVerse.ChapterNumber, ':', CommentaryVerse.VerseNumber) AS Text, CommentaryVerse.NumWords AS NumWords from CommentaryVerse INNER JOIN Commentary on CommentaryVerse.CommentaryID=Commentary.CommentaryID where CommentaryVerse.CommentaryID= :commid ";
	$vaxis="Verse Number";
}
else
{
	$selectstmt="select Commentary.Author as Author, Commentary.TotalWordCount as TotalWordCount, Commentary.PublicationYear as Year, CommentaryChapter.ChapterNumber AS Text, CommentaryChapter.NumWords from CommentaryChapter INNER JOIN Commentary on CommentaryChapter.CommentaryID=Commentary.CommentaryID where CommentaryChapter.CommentaryID= :commid ";
	$vaxis="Chapter Number";
}


$dbselect=$db->prepare($selectstmt);
$dbselect->bindParam(':commid', $commentaryid);
$dbselect->execute();



$rows = array();
//flag is not needed
$flag = true;
$table = array();
$table = array();

$rows=$dbselect->fetchAll(PDO::FETCH_ASSOC);
$commlabel='';

foreach($rows as $row) {
	$commlabel=$row['Author'] . ", " . $row['Year'];
}

$retval= "{\"cols\": [ {\"id\": \"\", \"label\": \"" . $vaxis . "\", \"type\": \"string\"}, {\"id\": \"\", \"label\": \"". $commlabel . "\", \"type\": \"number\"}],";





$row_num = 0;
$retval.=" \"rows\": [ ";
   foreach($rows as $row) {
   if($row_num>0) { $retval.=", ";}
   		if($showpercent) { $percent=number_format($row['NumWords']/$row['TotalWordCount']*100, 2);}
   		else {$percent=$row['NumWords'];}
      	$retval.= "{\"c\":[{\"v\": \"" . $row['Text'] ."\"}, {\"v\": " . $percent . ", \"f\": \"" . $percent . "% (" . $row['NumWords'] . " of " . $row['TotalWordCount'] . " total words).\"}]}";
    	$row_num++;

	}
    $row_num++;
    
    
$retval.= "]}";



$dbselect->closeCursor();
echo $retval;
?>