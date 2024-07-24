<?php

    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);


    function display_album_info() {
        if (isset($_GET['id']) && isset($_GET['token'])) {

            $curl = curl_init();
        
            $id = $_GET['id'];
            $access_token = $_GET['token'];
        
            $requestUrl = 'https://api.spotify.com/v1/albums/'.$id;
            curl_setopt($curl, CURLOPT_URL, $requestUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Authorization: Bearer '.$access_token,
            ));
        
        
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        
            if ($httpcode != 200) {
                echo '<br>There was an error displaying album info.<br>HTTP Code: '.$httpcode;
                if ($httpcode == 401) {
                    echo '<br>Your token may have expired. Please perform a refresh on the search results page to generate a new one.';
                }
                curl_close($curl);
            }
            else {
        
                #echo 'reading...<br>';
                $data = json_decode($response, true);


                $album_art = $data['images'][0]['url'];
                $artist_name = $data['artists'][0]['name'];
                $spotify_id = $data['id'];
                $release_title = $data['name'];
                $year = $data['release_date'];
                $album_type = $data['album_type'];
                $tracks = $data['tracks']['items'];
                $copyrights = $data['copyrights'];
                $label = $data['label'];
                $hd_art = $data['images'][0]['url'];


                echo '<title>'.$artist_name.' - '.$release_title.' - LblBrowser</title>';


                echo '<div class="music-container">';
                    display_album($artist_name, $release_title, $album_art, $year, $album_type, $spotify_id, $hd_art);
                echo '</div>';


                echo '<fieldset class="tracklist-container">';
                    echo '<legend>Tracklist</legend>';

                    if (isset($_COOKIE['discogs_tools']) && $_COOKIE['discogs_tools'] == true) {
                        echo '<div class="discogs-tools">';
                            echo '<button onclick="dashes_to_parantheses(this)" >Dashes → paranthases</button>';
                            echo '<button onclick="copy_mode(this)" >Copy mode<span class="what" title="Copy tracklist fields by clicking on them.">?</span></button>';
                        echo '</div>';
                    }

                    echo '<table class="tracklist">';
                    echo '<tbody>';
                        echo '<tr>';
                            echo '<th>Artist</th>';
                            echo '<th>Track</th>';
                            echo '<th>Duration</th>';
                            
                            if (isset($_COOKIE['show_ids']) && $_COOKIE['show_ids'] == true) {
                                echo '<th>Identifier</th>';
                            }

                            echo '<th>Preview</th>';
                        echo '</tr>';
                        
                        foreach ($tracks as $track) {
                            
                            $duration = date("i:s", intval($track['duration_ms'] / 1000) );
                            $preview_url = $track['preview_url'];
                            $track_id = $track['id'];

                            echo '<tr>';
                            
                                echo '<td class="track-artists copyable">';
                                    $track_artists = $track['artists'];
                                    $amount = 0;

                                    foreach ($track_artists as $track_artist) {
                                        if ($amount >= 1) {
                                            echo ', ';
                                        }
                                        echo $track_artist['name'];
                                        $amount++;
                                    }
                                echo '</td>';

                                echo '<td class="track-title copyable">'.$track['name'].'</td>';
                                echo '<td class="track-duration copyable">'.$duration.'</td>';


                                if (isset($_COOKIE['show_ids']) && $_COOKIE['show_ids'] == true) {
                                    curl_setopt($curl, CURLOPT_URL, 'https://api.spotify.com/v1/tracks/'.$track_id);
                                    
                                    $track_data = curl_exec($curl);
                                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                                    curl_close($curl);
                        
                                    if ($httpcode = 200) {
                                        
                                        $track_data = json_decode($track_data, true);
        
                                        $identifiers = $track_data['external_ids'];
                                        $gotten = array();
                                        
                                        if (isset($identifiers['isrc'])) {
                                            array_push($gotten, $identifiers['isrc']);
                                        }
                                        if (isset($identifiers['ean'])) {
                                            array_push($gotten, $identifiers['ean']);
                                        }
                                        if (isset($identifiers['upc'])) {
                                            array_push($gotten, $identifiers['upc']);
                                        }
        
                                        if (count($gotten) == 0) {
                                            echo '<td class="track-id copyable">none</td>';
                                        }
                                        else {
                                            foreach ($gotten as $identifier) {
                                                echo '<td class="track-id copyable">'.$identifier.'</td>';
                                            }
                                        }
                                    }
                                    else {
                                        echo '<td class="track-id copyable">none</td>';
                                    }
                                }
                                

                                echo '<td style="padding-right: unset !important">';
                                    echo '<div class="play-btn btn2" onclick="preview(\''.$preview_url.'\', this)" >';;
                                        echo '▶';
                                    echo '</div>';
                                echo '</td>';
                                

                            echo '</tr>';
                        }

                    echo '</tbody>';
                echo '</table>';
            echo '</fieldset>';

            echo '<div class="copyrights">';
                foreach ($copyrights as $copyright) {
                    echo '<a href="search.php?q='.$copyright['text'].'" >(' .$copyright['type']. ') ' .$copyright['text']. '</a>';
                }

                if (isset($label)) {
                    echo '<a href="search.php?q='.$label.'">'.$label.'</a>';
                }

            echo '</div>';

        
            }
    }


}





function display_album($artist, $title, $img, $year, $media_type, $spotify_id, $hd_art) {
    echo '<div class="music-details">';
        
        echo '<div class="left-side">';
            echo '<div class="album-bg noselect" style="background-image: url('.$img.')">';
                echo '<img class="album-fg" draggable=false src="img/album-fg.png">';
            echo '</div>';


            echo '<a class="dl-artwork" href="'.$hd_art.'" target="_blank" >Download Artwork</a>';


        echo '</div>';

        echo '<div class="right-side">';
            echo '<div>';
                echo '<span class="artist-name">'.$artist.'</span>';
                    echo '<div>';
                        echo '<span class="album-title"><b>'.$title.' </b></h4>';
                        echo '<span class="album-year">('.$year.')</h4>';
                    echo '</div>';
                echo '<span>'.$media_type.'</span>';
            echo '</div>';

            echo '<div style="display:flex; margin-top: 15px;">';
                echo '<div class="album-links" style="margin: unset;">';
                    echo '<a class="btn1 platform" target="_blank" id="view-on-spotify" href="https://open.spotify.com/album/'.$spotify_id.'">Spotify</a>';
                    echo '<a class="btn1 platform" target="_blank" id="view-on-apple" href="https://music.apple.com/us/search?term='.$artist.' - '.$title.'">Apple</a>';
                    echo '<a class="btn1 platform" target="_blank" id="view-on-deezer" href="https://deezer.com/search/'.$artist.' - '.$title.'">Deezer</a>';
                    echo '<div class="platforms-gap"></div>';
                    echo '<a class="btn1 platform" target="_blank" id="view-on-7d" href="https://us.7digital.com/search?q='.$artist.' - '.$title.'">7digital</a>';
                    echo '<a class="btn1 platform" target="_blank" id="view-on-bp" href="https://crates.co/search?q='.$artist.' - '.$title.'">Beatport</a>';
                    echo '<a class="btn1 platform" target="_blank" id="view-on-qb" href="https://qobuz.com/us-en/search?q='.$artist.' - '.$title.'">Qobuz</a>';
                    echo '<a class="btn1 platform" target="_blank" id="view-on-juno" href="https://www.junodownload.com/search/?q%5Ball%5D%5B%5D='.$artist.' - '.$title.'">Juno</a>';
                        echo '<div class="platforms-gap"></div>';
                    echo '<a class="btn1 platform" target="_blank" id="view-on-dc" href="https://discogs.com/search?q='.$artist.' - '.$title.'" title="Search on Discogs" >Discogs</a>';
                echo '</div>';
            echo '</div>';

        echo '</div>';
    echo '</div>';
}



?>


<html>



    <script>
        var copyMode = false;

        function copy_mode(button) {
            if (!copyMode) {
                copyMode = true;
                button.innerText = 'Copy mode [ON]';
            }
            else {
                copyMode = false;
                button.innerHTML = 'Copy mode<span class="what" title="Copy tracklist fields by clicking on them.">?</span>';
            }
        }

        setTimeout(() => {
            var items = document.getElementsByClassName('copyable');
            for (var i=0; i<items.length; i++) {
                items[i].style.cursor = 'copy';
                
                items[i].onclick = function() {
                    if (copyMode) {

                        navigator.clipboard.writeText(this.innerText);

                        this.style.color = 'limegreen';
                        setTimeout(() => {
                            this.style.color = 'white';
                        }, 250);
                    }
                }
            }
        }, 2500);

    </script>




    <script>
        var paranthesis_set = false;

        function dashes_to_parantheses(button) {
            var trackTitles = document.getElementsByClassName('track-title');
            if (!paranthesis_set) {
                    
                    paranthesis_set = true
                    button.innerText = 'Dashes ← Parantheses';

                    for (var i=0; i<trackTitles.length; i++) {
                        var title = trackTitles[i].innerText;
                        var split = title.split(' - ');

                        if (split[0] && split[1]) {
                            var newTitle = split[0] + ' (' + split[1] + ')';
                            trackTitles[i].innerText = newTitle;
                        }
                    }
                    
            }
            else {

                for (var i=0; i<trackTitles.length; i++) {
                    var title = trackTitles[i].innerText;
                    var split = title.split(' (');

                    if (split[0] && split[1]) {
                        var newTitle = split[0] + ' - ' + split[1];
                        newTitle = newTitle.substring(0, newTitle.length - 1);

                        trackTitles[i].innerText = newTitle;
                    }
                }

                paranthesis_set = false;
                button.innerText = 'Dashes → Parantheses';

            }
        }

    </script>



    <script>
        var audio = null;
        var playing = false;
        var current_play_button = null;

        function preview(url, button) {

            if (current_play_button) {
                current_play_button.innerText = '▶';
                current_play_button = button;
            }
            else {
                current_play_button = button;
            }

            if (playing) {
                playing = false;
                current_play_button.innerText = '▶';

                if (audio) {
                    audio.pause();
                    audio.currentTime = 0;
                }

            }
            else {

                if (audio) {
                    current_play_button.innerText = '◼';
                    audio.pause();
                    audio.currentTime = 0;
                }

                audio = document.createElement('audio');
                audio.preload = 'metadata';

                playing = true;
                audio.src = url;
                audio.play();

                current_play_button.innerText = '◼';


                audio.onplaying = function() {

                }


                audio.onended = function() {
                    playing = false;
                    current_play_button.innerText = '▶';
                }

            }

        }
    </script>

    <link rel="stylesheet" href="design.css">
    <body>
        <?php
            include('topbar.php');
            include('page_prefs.php');
        ?>

        <div class="page">
            <section class="page-content">
                <?php
                    display_album_info();
                ?>

            </section>
        </div>
    </body>
</html>