<?php


//FIND PLAYLIST ID FROM PLAYLIST URL
$playlist=$_POST["playlist"];
//echo $playlist;
$curl = curl_init($playlist);
curl_setopt($curl, CURLOPT_URL, $playlist);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$header = curl_exec($curl);
curl_close($curl);

//HIGH QUALITY
if(isset($_POST["highq"])&&$_POST["highq"]=="Yes")
$highq='yes';
else
$highq='no';

list($discard,$actdat)=explode('mixes/',$header);
list($playlistid,$discard)=explode('/',$actdat);

//GENERATE NEW PLAYTOKEN
$playtoken='http://8tracks.com/sets/new.json';
$curl = curl_init($playtoken);
curl_setopt($curl, CURLOPT_URL,$playtoken);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$playid = curl_exec($curl);
curl_close($curl);

$obj = json_decode($playid,true);

//var_dump($obj);
$token=$obj['play_token'];

//GENERATE INITIAL PLAY LINK
$playurl= 'http://8tracks.com/sets/'.$token.'/play?mix_id='.$playlistid.'&format=jsonh';
//echo $playurl;

$songcurl = curl_init($playurl);
curl_setopt($songcurl, CURLOPT_URL,$playurl);
curl_setopt($songcurl, CURLOPT_RETURNTRANSFER, true);
$songdata = curl_exec($songcurl);
curl_close($songcurl);

$obj = json_decode($songdata,true);

if(isset($_POST["show"])&&$_POST["show"]=="Yes")
{
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Stay offline!" />

<title> 8Tracks Playlist Downloader</title>
<link rel="stylesheet" href="style/style.css" type="text/css"/>
</head>
<body>
<div id="page">
<div id="body">
<div id="header">
 <img src="style/header.jpg" border="0" align="centre"/>
 </div><div align="center"><br/><br/>';
 
$plid='http://8tracks.com/mixes/'.$playlistid.'.json';
$albcurl = curl_init($plid);
curl_setopt($albcurl, CURLOPT_URL,$plid);
curl_setopt($albcurl, CURLOPT_RETURNTRANSFER, true);
$albdata = curl_exec($albcurl);
curl_close($albcurl);

$alb = json_decode($albdata,true);

echo '<div class="title"><h3>'.$alb['mix']['name'].'</h3></div><br/><div class="desc"><h4>'.$alb['mix']['description'];
echo '</h4></div><br/><br/><div class="myimg"><a href="http://8tracks.com'.$alb['mix']['path'].'"><img src="'.$alb['mix']['cover_urls']['sq133'].'"/></a></div><br/><br/>';
echo '<div class="mytab"> <h3>Song List: </h3><br/><table border="1">';
}
else
{
//if (file_exists('songs/'.$playlistid.'.zip'))
//header( 'Location: songs/'.$playlistid.'.zip' );
//else
//{
//$zip = new ZipArchive();
//$zip->open($playlistid.'.zip', ZipArchive::CREATE);
//}
}  

$at_end='false';
//RECURSIVELY PLAY/DOWNLOAD SONGS
while($at_end=='false')
{
//RIGHT NOW 8Tracks provides 64K and 48K ENCODING... I OBVIOUSLY PREFER 64K ENCODING
//DUNNO HOW LONG THIS WILL LAST THOUGH... http://groups.google.com/group/8tracks-public-api/browse_thread/thread/14da42858b928b88#
if($highq == 'yes')
$song= str_replace("48k.v2.m4a","64k.m4a",$obj['set']['track']['url']);
else
$song=$obj['set']['track']['url'];

if(isset($_POST["show"])&&$_POST["show"]=="Yes")
{
echo '<tr><td><a href="'.$song.'">'.$obj['set']['track']['name'].'</a><br/>'.$obj['set']['track']['performer'].'</td></tr>';
}
else
{
$songfile = file_get_contents($song);
file_put_contents('songs/'.$obj['set']['track']['name'].'.m4a',$songfile);
//$zip->addFile($songfile,$obj['set']['track']['name'].'.m4a');
}
//GET NEXT SONG
$playurl= 'http://8tracks.com/sets/'.$token.'/next?mix_id='.$playlistid.'&format=jsonh';
//echo $playurl;

$songcurl = curl_init($playurl);
curl_setopt($songcurl, CURLOPT_URL,$playurl);
curl_setopt($songcurl, CURLOPT_RETURNTRANSFER, true);
$songdata = curl_exec($songcurl);
curl_close($songcurl);

$obj = json_decode($songdata,true);
//unlink('songs/'.$obj['set']['track']['name'].'.m4a');

//CHECK IF AT END OF PLAYLIST
if($obj['set']['at_end'])
$at_end= 'true';

}
if(isset($_POST["show"])&&$_POST["show"]=="Yes")
{
echo '</table></div>
</form>
</div>
</div>
</div>
</div>
</body>
</html> ';
}
else
{
	//$zip->close();
	 //header( 'Location: /tmp/'.$playlistid.'.zip' ) ;
}

?>