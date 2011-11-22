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

$at_end='false';
//RECURSIVELY PLAY/DOWNLOAD SONGS
while($at_end=='false')
{
echo $obj['set']['track']['name'].'<br/>';
$songfile = file_get_contents($obj['set']['track']['url']);
file_put_contents('songs/'.$obj['set']['track']['name'].'.m4a',$songfile);

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
}
?>

