# 8tracks-Downloader
This is a simple playlist downloader for [8tracks.com](http://8tracks.com).

## About
[8tracks.com](http://8tracks.com) is by far the best music site I've ever used! The site UI is brilliant, the people are superb, and the music is pure awesomeness. 8tracks is internet radio, so ideally you're **NOT** supposed to be able to download songs from the site! Thankfully though, it can be done with a little hackery. ;-)

## Requirements
* A webserver running locally.   
I recommend WAMP for Windows and MAMP for Mac OS X because they are very simple and easy to set up.

## Installation
1. Download the script and unzip it.
2. Rename the extracted folder to "8tracks-downloader" and copy it into your web root folder.
3. Make sure that the "8tracks-downloader" folder's permissions are set to 0777.

## Instructions
1. Go to [http://localhost/8tracks-downloader](http://localhost/8tracks-downloader) in your web browser.
2. Type in the playlist's URL and your API Key (it must be valid), and click "Analyze".
3. You can choose to download one by one (right-click on the song title and choose "Save As..") or all (click the button "Download All).
4. Relax and let it do its magic.  
5. When it has finished it'll say "Done." and your songs will be waiting in the "Downloads" folder inside "8tracks-downloader". Sweet.

## Troubleshooting
* `Fatal error: Maximum execution time of XXX seconds exceeded`  
The thing with the script is that it doesn't stop running until the last song has been downloaded. PHP features a maximum execution time parameter, which is useful when you are running a traditional web page (you wouldn't want a single cycled script to take your server down), but in this case that's what is throwing an error.
Depending on various factors (such as your download speed, the number of songs in the playlist, etc..) the script can take up to 10 minutes or more to download a single playlist! My recommendation would be to set your execution time to 0, that way PHP interprets it as 'unlimited'.

* `Fatal error: Call to undefined function curl_init()`  
You might need to enable/install the cURL library. Try [this guide first](http://www.webtechquery.com/index.php/2010/03/fatal-error-call-to-undefined-function-curl_init-windows-and-linux-ubuntu/), it'll solve most problems related to cURL.

## Updates
By mundofr:  
* 8tracks recently changed it's API policies and you are now required to authenticate with a valid API key. Changed all the requests to the new API.  
* I also redesigned the script flow quite a bit, a new "analysis" page shows up with the playlist description and the song list, from there you can choose to download one-by-one or download all.  
* The command line functionality has been completely removed.

By navinpai:  
* Added a UI (?). All you have to do is input playlist URL in homepage and click download! :) Here's a peek at the simplified UI: http://brizzly.com/pic/4S3U   
* 8Tracks used to provide 64K m4a (still does infact), though their primary server now encodes into 48K ...This provides smaller filesize, but less quality. So have added support for 64K encoded downloads.  
* Updated UI: http://brizzly.com/pic/4S4Q (Just check HIGH QUALITY to download 64K m4a's)  
* Maintaining 64K encoding works out pretty costly for the 8Tracks team ( http://groups.google.com/group/8tracks-public-api/browse_thread/thread/14da42858b928b88# ) so I don't know how long they'll support it for! But while they do, enjoy! :)

## Thanks to
* Navinpai (https://github.com/navinpai) for creating the [original script](https://github.com/navinpai/8Tracks-Downloader).