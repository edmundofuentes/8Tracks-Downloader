<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Stay offline!" />

<title>Downloading 8Tracks Playlist...</title>
<link rel="stylesheet" href="style/style.css" type="text/css"/>
</head>
<body>
<div id="page">
    <div id="body">
        <div id="header">
            <img src="style/header.jpg" border="0" align="centre"/>
        </div>
        <div align="center"><br/><br/>
            <div class="desc">
                <!-- TRICK BROWSER TO SHOW PAGE BEFORE LOADING...
                Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                Maecenas aliquam leo a nulla volutpat sit amet egestas nunc tincidunt.
                Pellentesque sed risus ipsum. Morbi tellus eros, sagittis consectetur tristique vitae,
                imperdiet at magna. Maecenas dapibus cursus scelerisque.
                Nunc eget urna at purus volutpat venenatis hendrerit in quam.
                Aliquam pulvinar libero eget tortor scelerisque sit amet dictum leo accumsan.
                Integer a quam lorem. Suspendisse potenti.
                Suspendisse fermentum nunc ut augue suscipit a convallis leo euismod.
                Praesent mollis facilisis magna vel adipiscing.
                Cum sociis natoque penatibus et magnis dis parturient montes,
                nascetur ridiculus mus. Curabitur blandit bibendum metus,
                quis imperdiet ligula eleifend eget.
                -->

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

// PLAYLIST INFO
$plid='http://8tracks.com/mixes/'.$playlistid.'.json?api_key=' . $api_key;
$albcurl = curl_init($plid);
curl_setopt($albcurl, CURLOPT_URL,$plid);
curl_setopt($albcurl, CURLOPT_RETURNTRANSFER, true);
$albdata = curl_exec($albcurl);
curl_close($albcurl);

$alb = json_decode($albdata,true);

$playlist_name = $alb['mix']['name'];

// CHECK AND CREATE THE DOWNLOADS FOLDER
$thisdir = getcwd();
if (!file_exists($thisdir."/downloads")){
    if(mkdir( $thisdir . "/downloads" , 0777 )){
        echo "<p>Created 'Downloads' folder in script directory.</p>\n";
    } else {
        die('Failed to create Downloads folder..');
    }
}

// CHECK AND CREATE THE PLAYLIST FOLDER
$playlist_name = preg_replace("/[a-zA-Z0-9\s]/", "_", $playlist_name); // fixed by reddit user elmes3
if (!file_exists($thisdir."/downloads/" . $playlist_name)){
    if(mkdir( $thisdir . "/downloads/" . $playlist_name , 0777 )){
        echo "<p>Created folder '" . $playlist_name . "' inside 'Downloads' directory.</p>\n";
    } else {
        die('Failed to create Playlist folder..');
    }
}

echo "<n>Starting download..</p>\n";
flush();

//RECURSIVELY PLAY/DOWNLOAD SONGS
$at_end=false;
$song_number=1;
while(!$at_end)
{
$song=$obj['set']['track']['track_file_stream_url']; ### FIX: changed 'url' to 'track_file_stream_url'

### ADDED: Check before if the song has already been downloaded
$file = $thisdir . '/downloads/' . $playlist_name. '/' . $song_number . ' - ' . $obj['set']['track']['performer']. ' - ' .$obj['set']['track']['name'].'.m4a';

if (file_exists($file)){
    echo '<p>Skipping song ' . $song_number . '. File "' . $file . '" already exists in directory.';
    flush();
} else {
    ### ADDED: downloading status.
    echo '<p>Downloading: ' . $obj['set']['track']['name'] . ' - ' . $obj['set']['track']['performer'] . ' from: ' . $song . "</p>\n";
    flush();
            
    $songfile = file_get_contents($song);
    file_put_contents($file,$songfile);
}

//GET NEXT SONG
$playurl= 'http://8tracks.com/sets/'.$token.'/next?mix_id='.$playlistid.'&format=jsonh&api_key=' . $api_key;

$songcurl = curl_init($playurl);
curl_setopt($songcurl, CURLOPT_URL,$playurl);
curl_setopt($songcurl, CURLOPT_RETURNTRANSFER, true);
$songdata = curl_exec($songcurl);
curl_close($songcurl);

$obj = json_decode($songdata,true);

$song_number += 1;

//CHECK IF AT END OF PLAYLIST
if($obj['set']['at_end'])
$at_end= true;

}

echo "<p>Done.</p>\n";

?>

            </div>
        </div>
    </div>
</div>
</body></html>