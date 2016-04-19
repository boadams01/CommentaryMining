<?php 
//phpinfo();
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);      
require_once("dbinfo.php");


$comm1=isset($_GET["comm1"])?$_GET["comm1"]:0;
$comm2=isset($_GET["comm2"])?$_GET["comm2"]:0;
$chapter=isset($_GET["chapter"])?$_GET["chapter"]:9;

if($chapter==-1) { //if we just want chapters, and not verses
	$selectstmt="select Commentary1.Author as Author1, Commentary2.Author as Author2, Commentary1.PublicationYear as Year1, Commentary2.PublicationYear as Year2, Commentary1.TotalWordCount as Total1, Commentary2.TotalWordCount as Total2, a.ChapterNumber as ChapterNumber, a.ChapterNumber as VerseNumber, a.NumWords as Comm1Words, b.NumWords as Comm2Words from CommentaryChapter a INNER JOIN CommentaryChapter b on a.ChapterNumber=b.ChapterNumber and a.ChapterNumber=b.ChapterNumber INNER JOIN Commentary Commentary1 on a.CommentaryID=Commentary1.CommentaryID INNER JOIN Commentary Commentary2 on b.CommentaryID=Commentary2.CommentaryID where a.CommentaryID=:comm1 and b.CommentaryID=:comm2;";
}
else {



//Long SQL Statement returns
// Words in 1/Words in 2/VerseNumber/NumWords1/NumWords2
	$selectstmt="select Commentary1.Author as Author1, Commentary2.Author as Author2, Commentary1.PublicationYear as Year1, Commentary2.PublicationYear as Year2, Commentary1.TotalWordCount as Total1, Commentary2.TotalWordCount as Total2, a.ChapterNumber as ChapterNumber, a.VerseNumber as VerseNumber, a.NumWords as Comm1Words, b.NumWords as Comm2Words from CommentaryVerse a INNER JOIN CommentaryVerse b on a.VerseNumber=b.VerseNumber and a.ChapterNumber=b.ChapterNumber INNER JOIN Commentary Commentary1 on a.CommentaryID=Commentary1.CommentaryID INNER JOIN Commentary Commentary2 on b.CommentaryID=Commentary2.CommentaryID where a.CommentaryID=:comm1 and b.CommentaryID=:comm2 ";
	if ($chapter>0) {
		$selectstmt.=" and a.ChapterNumber=:chapter";
	}
	$selectstmt.=";";
}

$dbselect=$db->prepare($selectstmt);
$dbselect->bindParam(':comm1', $comm1);
$dbselect->bindParam(':comm2', $comm2);
if($chapter>0){
	$dbselect->bindParam(':chapter', $chapter);
}
$dbselect->execute();



$rows = array();
//flag is not needed
$flag = true;
$table = array();
$table = array();




$rows=$dbselect->fetchAll(PDO::FETCH_ASSOC);
$comm1label='';
$comm2label='';
foreach($rows as $row) {
$comm1label=$row['Author1'] . ", " . $row['Year1'];
$comm2label=$row['Author2'] . ", " . $row['Year2'];

}
$retval= "{\"cols\": [ {\"id\": \"\", \"label\": \"Verse Number\", \"type\": \"string\"}, {\"id\": \"\", \"label\": \"" . $comm1label ."\", \"type\": \"number\"}, {\"id\": \"\", \"label\": \"" . $comm2label . "\", \"type\": \"number\"}],";


$row_num = 0;
$percent1=0;
$percent2=0;
$retval.=" \"rows\": [ ";
   foreach($rows as $row) {
   if($row_num>0) { $retval.=", ";}
   		$percent1=number_format($row['Comm1Words']/$row['Total1']*100, 2);
   		$percent2=number_format($row['Comm2Words']/$row['Total2']*100, 2);
      	//$retval.= "{\"c\":[{\"v\": \"" . $row['VerseNumber'] ."\"}, {\"v\": " . $row['Comm1Words'] . ", \"f\": \"" . $row['Comm2Words']."\"}, {\"v\": " . $row['Comm2Words'] . ", \"f\": \"" . $row['Comm2Words']."\"}]}";
    	$retval.= "{\"c\":[{\"v\": \"" . $row['ChapterNumber']. ":" . $row['VerseNumber'] ."\"}, {\"v\": " . $percent1 . ", \"f\": \"" . $percent1 . "% (" . $row['Comm1Words'] . " of " . $row['Total1'] . " total words)\"}, {\"v\": " . $percent2 . ", \"f\": \"" . $percent2 . "% (" . $row['Comm2Words'] . " of " . $row['Total2'] . " total words)\"}]}";
    	
    	$row_num++;

	}
    $row_num++;
    
    
$retval.= "]}";



$dbselect->closeCursor();
echo $retval;
?>