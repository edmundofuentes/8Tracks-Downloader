<?php


//FIND PLAYLIST ID FROM PLAYLIST URL
$playlist=$_POST["playlist"];
//echo $playlist;
$curl = curl_init($playlist);
curl_setopt($curl, CURLOPT_URL, $playlist);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$header = curl_exec($curl);
curl_close($curl);

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

echo $alb['mix']['name'].'<br/>'.$alb['mix']['description'];
echo '<br/><br/><a href="http://8tracks.com'.$alb['mix']['path'].'"><img src="'.$alb['mix']['cover_urls']['sq133'].'"/></a><br/><br/>';
echo '<table border="1">';
}

$at_end='false';
//RECURSIVELY PLAY/DOWNLOAD SONGS
while($at_end=='false')
{
if(isset($_POST["show"])&&$_POST["show"]=="Yes")
{
echo '<tr><td><a href="'.$obj['set']['track']['url'].'">'.$obj['set']['track']['name'].'</a></td></tr>';
}
else
{
$songfile = file_get_contents($obj['set']['track']['url']);
file_put_contents('songs/'.$obj['set']['track']['name'].'.m4a',$songfile);
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
</body>
</html> ';
}

?>

