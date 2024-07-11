
<img src="https://github.com/splatert/lblbrowser/assets/82643571/ab4ea7a8-5307-48f6-877d-e07b0e443196" height="75">

# lblbrowser
PHP application to browse music labels on Spotify


LblBrowser is a free PHP application that lets you browse through digital music labels that are distributed by Spotify without the need to use the Spotify web player. It can be hosted on a server or be ran locally on your computer.


## features

- You're not using the Spotify web player for one.
- Simple, neat interface. Does not use any web frameworks.
- Keep track of where you're at on the search results page. Especially when browsing big labels.
- Quickly add filters with convenience.
- Download artwork of your favorite singles and albums.
- Retrieve barcodes and identifers of album tracks.
- Select and copy data from a tracklist without having to inspect their HTML elements.


## Setup

In order to use this application, you'll need to provide a Spotify ``client ID`` and ``secret``. You can obtain these credentials by starting a new project through the [Spotify developer dashboard](https://developer.spotify.com/dashboard).
After receiving those credentials, follow the instructions below.

1. Create a PHP file one level above the application's root directory. Give either ``root`` or ``www-data`` access to this file and make them file owners. Do not allow users to access it in any way. Call it ``lblbrowser-creds.php``.
2. Add the following to the file. Replace the values with your credentials.
```
<?php
	$client_id = 'client id';
	$client_secret = 'client secret';
?>
```  
4. Test out the application by performing a search. Enter a name of a label then hit enter.
5. If you are able to retrieve song results, then your application is ready. Make sure that users cannot access the ``lblbrowser-creds.php`` file you've made.
6. Enjoy!


## screenshots

<img src="https://github.com/splatert/lblbrowser/assets/82643571/c7f3f75e-0fd9-43f9-81a9-e384b763e95f" height="350">
<img src="https://github.com/splatert/lblbrowser/assets/82643571/dc07510f-fbab-411f-a642-b8416ebd54f8" height="350">
