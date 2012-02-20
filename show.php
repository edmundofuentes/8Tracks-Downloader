<?php
// based on: https://github.com/navinpai/8Tracks-Downloader
// by: mundofr http://github.com/mundofr
// feb 2012


//FIND PLAYLIST ID FROM PLAYLIST URL
$playlist= isset($_POST['playlist'])? $_POST['playlist'] : 0;
//echo $playlist;
$curl = curl_init($playlist);
curl_setopt($curl, CURLOPT_URL, $playlist);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$header = curl_exec($curl);
curl_close($curl);

list($discard,$actdat)=explode('mixes/',$header);
list($playlistid,$discard)=explode('/',$actdat);

//GENERATE NEW PLAYTOKEN
$api_key = isset($_POST['api_key'])? $_POST['api_key'] : 0; ### ADDED: API Key
$playtoken='http://8tracks.com/sets/new.json?api_key=' . $api_key;
$curl = curl_init($playtoken);
curl_setopt($curl, CURLOPT_URL,$playtoken);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$playid = curl_exec($curl);
curl_close($curl);

$obj = json_decode($playid,true);

 
//var_dump($obj);
$token=$obj['play_token'];

//GENERATE INITIAL PLAY LINK
$playurl= 'http://8tracks.com/sets/'.$token.'/play?mix_id='.$playlistid.'&format=jsonh&api_key=' . $api_key;

$songcurl = curl_init($playurl);
curl_setopt($songcurl, CURLOPT_URL,$playurl);
curl_setopt($songcurl, CURLOPT_RETURNTRANSFER, true);
$songdata = curl_exec($songcurl);
curl_close($songcurl);

$obj = json_decode($songdata,true);

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
 </div>
<div align="center"><br/><br/>';
 
$plid='http://8tracks.com/mixes/'.$playlistid.'.json?api_key=' . $api_key;
$albcurl = curl_init($plid);
curl_setopt($albcurl, CURLOPT_URL,$plid);
curl_setopt($albcurl, CURLOPT_RETURNTRANSFER, true);
$albdata = curl_exec($albcurl);
curl_close($albcurl);

$alb = json_decode($albdata,true);

echo '
<div class="playlistinfo" align="left">
    <form id="form" action="download.php" method="post">
            <input type="hidden" name="api_key" value="'. $api_key . '" />
            <input type="hidden" name="playlist" value="'. $playlist .'" />
    
        <span class="myimg" style="padding-right:15px;">
            <a href="http://8tracks.com'.$alb['mix']['path'].'"><img src="'.$alb['mix']['cover_urls']['sq133'].'"/></a>
            <br/><p><input type="submit" style="width:133px;!important" value="DOWNLOAD ALL" /></p>
        </span>
        <div class="title"><h3>'.$alb['mix']['name'].' by '. $alb['mix']['user']['login'] .'</h3></div><br/>
        <div class="desc"><h4>'.$alb['mix']['description'] . '</h4><br/></div>
        <div class="desc">
            <p>Right-click on the name of the song and select "Save As.." to download
            each song individually or click the button to download all the songs to disk.</p>
        </div>  
    </form>
</div>
<br/><br/><br/><br/>';

echo "\n\n\n";
echo '<div class="mytab"> <h3>Song List: </h3><br/><table border="1">';


//RECURSIVELY PLAY/DOWNLOAD SONGS
$at_end=false;
while(!$at_end)
{
$song=$obj['set']['track']['track_file_stream_url']; ### FIX: changed 'url' to 'track_file_stream_url'

echo '<tr><td><a href="'.$song.'">'.$obj['set']['track']['name'].'</a><br/>'.$obj['set']['track']['performer'].'</td></tr>';

//GET NEXT SONG
$playurl= 'http://8tracks.com/sets/'.$token.'/next?mix_id='.$playlistid.'&format=jsonh&api_key=' . $api_key;

$songcurl = curl_init($playurl);
curl_setopt($songcurl, CURLOPT_URL,$playurl);
curl_setopt($songcurl, CURLOPT_RETURNTRANSFER, true);
$songdata = curl_exec($songcurl);
curl_close($songcurl);

$obj = json_decode($songdata,true);

//CHECK IF AT END OF PLAYLIST
if($obj['set']['at_end'])
$at_end= true;
}

echo '</table></div>
</form>
</div>
</div>
</div>
</div>
</body>
</html> ';

?>