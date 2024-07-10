


<?php
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
?>


<html>
    <link rel="stylesheet" href="design.css">
    
    <title>LblBrowser</title>
    <body>
        <?php
            include('topbar.php');
            include('page_prefs.php');
        ?>

        <div class="page">
            <section class="page-content">

                <img class="logo" src="img/logo.png">

                <h1>Welcome</h1>
                
                <div class="about">
                    <p><b>LblBrowser</b> is a free PHP application that lets you browse through digital music labels that are distributed by Spotify without
                        the need to use the Spotify web player. It can be hosted on a server or be ran locally on your computer.</p>

                    <p>In order to use this application, you'll need to provide a Spotify client ID and secret. You can obtain these
                        credentials by starting a new project through the <a href="https://developer.spotify.com/dashboard">Spotify developers dashboard</a>.
                    </p>

                    <p>After receiving these credentials, follow the instructions on the <a href="https://github.com/splatert/lblbrowser">github page</a> to finish setting up the application.</p>
                    </p>
                </div>

                <h4>Features</h4>
                <li>You're not using the Spotify web player for one.</li>
                <li>Simple, neat interface. Does not use any web frameworks.</li>
                <li>Keep track of where you're at on the search results page. Especially when browsing big labels.</li>
                <li>Quickly add filters with convenience.</li>
                <li>Download artwork of your favorite singles and albums.</li>
                <li>Easily Select and copy items from the tracklist.</li>


                <div class="screenshots-info"> <h4>Screenshots</h4><p>(Click on them to view their full size.)</p></div>
                <a href="img/screenshots/screen1.png" target="_blank"><img class="screenshot" src="img/screenshots/screen1.png"></a>
                <a href="img/screenshots/screen2.png" target="_blank"><img class="screenshot" src="img/screenshots/screen2.png"></a>

                
            </section>
        </div>

    </body>
</html>