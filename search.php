

<?php
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
    include('page_prefs.php');


    $credentials = array(
        'client_id' => '',
        'client_secret' => ''
    );


    require('../lblbrowser-creds.php');
    if (isset($client_id)) {
        $credentials['client_id'] = $client_id;
    }
    if (isset($client_secret)) {
        $credentials['client_secret'] = $client_secret;
    }


    if (isset($_COOKIE['client_id']) && isset($_COOKIE['client_secret'])) {
        $credentials['client_id'] = $_COOKIE['client_id'];
        $credentials['client_secret'] = $_COOKIE['client_secret'];
    }


    $one_year = time() + (365 * 24 * 60 * 60);


    if (!isset($_COOKIE['search-history'])) {
        $array = array();
        setcookie('search-history', json_encode($array), $one_year);
    }
    if (isset($_COOKIE['search-history']) && isset($_GET['q'])) {
        
        if (isset($_COOKIE['save_search_results']) && $_COOKIE['save_search_results'] == true) {
            $items = json_decode($_COOKIE['search-history']);

            if (!in_array($_GET['q'], $items)) {
                array_push($items, $_GET['q']);
            }
            setcookie('search-history', json_encode($items), $one_year);
        }

    }



?>


<html>

    <link rel="stylesheet" href="design.css">



    <body>
        <?php
            include('topbar.php');
            include('page_prefs.php');
        ?>


        <script>
            function changeUrlParam(param, value) {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set(param, value);
                window.location.search = urlParams;
            }

            function deleteUrlParam(param) {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.delete(param);
                window.location.search = urlParams;
            }
        </script>


        <div class="page">

            <aside class="side-panel">

                <span class="sidepanel-cat-title">Applied filters</span>
                <fieldset class="sidepanel-form-container">
                    <?php
                        if (!isset($_GET['artist']) && !isset($_GET['year'])) {
                            echo 'none';
                        }
                        else {
                            if (isset($_GET['artist'])) {
                                echo '<span><b>Artist:</b> '.$_GET['artist'].'</span> <a onclick="deleteUrlParam(\'artist\')" style="margin-left: 5px;" class="link">Remove</a>';
                            }
                            if (isset($_GET['artist']) && isset($_GET['year'])) {
                                echo '<br>';
                            }
                            if (isset($_GET['year'])) {
                                echo '<span><b>Year:</b> '.$_GET['year'].'</span> <a onclick="deleteUrlParam(\'year\')" style="margin-left: 5px;" class="link">Remove</a>';
                            }
                        }
                    ?>
                </fieldset>


                <span class="sidepanel-cat-title">Filters</span>
                <fieldset class="sidepanel-form-container">

                    <!-- filter by year stuff. -->
                    <label for="year">Year: </label>
                    <input type="number" min="1" max="4" name="year" value="" onkeypress="tb_change_url_param(event, 'year', this.value)">

                    <br><label for="custom-artist-field">By artist: </label>
                    <input name="custom-artist-field" onkeypress="tb_change_url_param(event, 'artist', this.value)" type="text">
                </fieldset>

                <span class="sidepanel-cat-title">Featured artists</span>
                <fieldset class="sidepanel-form-container">
                    <div class="filterby-artist-list"></div>
                </fieldset>


                <script>
                    function tb_change_url_param(e, param, query) {
                        if (e.keyCode == 13) {
                            changeUrlParam(param, query)
                        }
                    }
                </script>

                


                <script>

                    var first_artist = '';
                    var artists = [];

                    function create_featured_artist_entry(artist_name) {

                        if (!artists.includes(artist_name)) {

                            if (first_artist == '') {
                                first_artist = artist_name;
                                document.getElementsByName('custom-artist-field')[0].placeholder = 'e.g. ' + first_artist;
                            }

                            artists.push(artist_name);

                            var list = document.getElementsByClassName('filterby-artist-list')[0]
                            var item = document.createElement('a');
                            item.className = 'featured-artist link';
                            item.innerText = artist_name;
                            item.onclick = function(){changeUrlParam('artist', artist_name);}
                            
                            list.appendChild(item);
                        }
                    }
                </script>


            </aside>

            <section class="page-content">
                <?php
                    
                    $page_num = 1;
                    if (isset($_GET['page'])) {
                        if (is_numeric($_GET['page']) && $_GET['page'] >1 && $_GET['page'] < 10000) {
                            $page_num = $_GET['page'];
                        }
                    }

                    $with_artist_name = '';
                    if (isset($_GET['artist'])) {
                        $with_artist_name = ' by <b>'.$_GET['artist'].'</b>';
                    }

                    $from_year = '';
                    if (isset($_GET['year']) && is_numeric($_GET['year'])) {
                        $from_year = ' from <b>'.$_GET['year'].'</b>';
                    }


                    if (!isset($_GET['q'])) {
                        echo '<h4>You have not provided a search term.</h4>';
                    }
                    else {  
                        echo '<div class="results-area">';     
                            
                            $curl = curl_init();
                            $access_token = get_spotify_token($curl);
                            
                            if ($access_token) {
                                $results = get_spotify_results($_GET['q'], $access_token, $curl);

                                if (count($results) == 0) {
                                    echo '<title>LblBrowser</title>';
                                    echo '<span>Sorry. No matches were found under <b>'.$_GET['q'].'</b>.</span>';
                                }
                                else {

                                    echo '<title>'.$_GET['q'].' - LblBrowser</title>';

                                    echo '<span>Showing results for music'.$from_year.' under <b>'.$_GET['q'].'</b>'.$with_artist_name.'. Page <b>'.$page_num.'</b>.</span>';
                                    foreach ($results as $result) {
                                        echo '<script>create_featured_artist_entry("'.$result['artist'].'");</script>';
                                        display_result_item($result['artist'], $result['title'], $result['image'], $result['year'], $result['media_type'], $result['spotify_id'], $access_token);
                                    }
                                }

                            }

                        echo '</div>';

                        $page_num = get_page_num();
                        createPageCtrls($page_num);
                    }

                ?>
            </section>
        </div>
    </body>


    <?php

        function display_result_item($artist, $title, $img, $year, $media_type, $spotify_id, $token) {
            echo '<div class="music-result-item">';
                
                echo '<div class="left-side">';
                    echo '<div class="album-bg noselect" style="background-image: url('.$img.')">';
                        echo '<img class="album-fg" draggable=false src="img/album-fg.png">';
                    echo '</div>';
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
                        echo '<a class="btn1" href="view.php?id='.$spotify_id.'&token='.$token.'" >Details</a>';
                        echo '<div class="platforms-gap"></div>';
                        echo '<a class="btn1 platform" target="_blank" id="view-on-spotify" href="https://open.spotify.com/album/'.$spotify_id.'">Spotify</a>';
                        echo '<a class="btn1 platform" target="_blank" id="view-on-apple" href="https://music.apple.com/us/search?term='.urlencode($artist.' - '.$title).'">Apple</a>';
                        echo '<a class="btn1 platform" target="_blank" id="view-on-deezer" href="https://deezer.com/search/'.$artist.' - '.$title.'">Deezer</a>';
                        echo '<div class="platforms-gap"></div>';
                        echo '<a class="btn1 platform" target="_blank" id="view-on-7d" href="https://us.7digital.com/search?q='.urlencode($artist.' - '.$title).'">7digital</a>';
                        echo '<a class="btn1 platform" target="_blank" id="view-on-bp" href="https://crates.co/search?q='.urlencode($artist.' - '.$title).'">Beatport</a>';
                        echo '<a class="btn1 platform" target="_blank" id="view-on-qb" href="https://qobuz.com/us-en/search?q='.urlencode($artist.' - '.$title).'">Qobuz</a>';
                        echo '<a class="btn1 platform" target="_blank" id="view-on-juno" href="https://www.junodownload.com/search/?q%5Ball%5D%5B%5D='.urlencode($artist.' - '.$title).'">Juno</a>';
                            echo '<div class="platforms-gap"></div>';
                        echo '<a class="btn1 platform" target="_blank" id="view-on-dc" href="https://discogs.com/search?q='.urlencode($artist.' - '.$title).'" title="Search on Discogs" >Discogs</a>';
                    echo '</div>';
                echo '</div>';
                    
                echo '</div>';
            echo '</div>';
        }


        function createPageCtrls($page_num) {

            if (!isset($page_num) || $page_num <= 0) {
                $page_num = 1;
            }

            echo '<div class="page-ctrls">';

                $start = $page_num;
                $max = $start + 5;

                if ($start > 1) {
                    $start = $start - 1;
                }


                while ($start <= $max) {
                    if ($start == $page_num) {
                        echo '<a class="btn2" id="curr-page" href="javascript:changeUrlParam(\'page\', '.$start.')">'.$start.'</a>';
                    }
                    else {
                        echo '<a class="btn2" href="javascript:changeUrlParam(\'page\', '.$start.')">'.$start.'</a>';
                    }
                    $start += 1;
                }
            echo '</div>';
        }


        function get_page_num() {
            if (isset($_GET['page'])) {
                if (is_numeric($_GET['page'])) {
                    return $_GET['page'];
                }
            }
        }


        function get_spotify_token($curl) {

            global $credentials;

            #echo '<br>Requesting access token via client id and secret...<br>';

            $client_id = $credentials['client_id'];
            $client_secret = $credentials['client_secret'];
            $client_id_and_secret = $client_id.':'.$client_secret;
            
            #clear
            $credentials['client_id'] = '';
            $credentials['client_secret'] = '';
            
    
            $urlRequestToken = 'https://accounts.spotify.com/api/token';
            
            curl_setopt($curl, CURLOPT_URL, $urlRequestToken);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Authorization: Basic '.base64_encode($client_id_and_secret),
                'Accept: application/json'
            ));
    
            $requestParams = array(
                'grant_type'=>'client_credentials'
            );
    
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestParams));
    
              
            $result = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);


            if ($httpcode != 200) {
                echo '<br>Could not retrieve a Spotify access token. Please make sure that your client credentials are correct.';
            }
            else {

                $result = json_decode($result, JSON_PRETTY_PRINT);
                $access_token = $result['access_token'];
    
                #clear
                $client_id = '';
                $client_secret = '';
                $client_id_and_secret = '';
    
                
                if (isset($access_token)) {
                    return $access_token;
                }
                else {
                    return false;
                }

            }

        }


        function get_spotify_results($searchQuery, $access_token, $curl) {

            #sanitize
            $searchQuery = strip_tags($searchQuery);


            $searchQueryInfo = array(
                'q' => 'label:'.$searchQuery,
                'type' => 'album',
                'limit' => 10,
                'offset' => 0
            );


            #replace spaces with dashes for search query
            $searchQueryInfo['q'] = str_replace(' ', '-', $searchQueryInfo['q']);


            if (isset($_GET['year']) && is_numeric($_GET['year'])) {
                $searchQueryInfo['q'] = $searchQueryInfo['q'] . ' year:' .$_GET['year'];
            }


            #append artist if provided
            if (isset($_GET['artist'])) {
                $artist = strip_tags($_GET['artist']);
                $artist = str_replace(' ', '-', $artist);
                $searchQueryInfo['q'] = $searchQueryInfo['q'] . ' artist:' .$artist;
            }


            # go to results from an offset.
            if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] >= 0 && $_GET['page'] < 10000) {
                $searchQueryInfo['offset'] = ($_GET['page'] - 1) * 10;
                #echo 'offset: ' . $searchQueryInfo['offset'] . '<br>';
            }


            $requestUrl = 'https://api.spotify.com/v1/search?'.http_build_query($searchQueryInfo);
            curl_setopt($curl, CURLOPT_URL, $requestUrl);
            curl_setopt($curl, CURLOPT_POST, 0);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Authorization: Bearer '.$access_token,
            ));

            $result = curl_exec($curl);
            curl_close($curl);


            $searchResults = json_decode($result, true);
            $gotten_albums = array();

            
            $albums = $searchResults['albums']['items'];

            foreach ($albums as $album) {

                $album_art = $album['images'][0]['url'];
                $artist_name = $album['artists'][0]['name'];
                $spotify_id = $album['id'];
                $release_title = $album['name'];
                $year = $album['release_date'];
                $album_type = $album['album_type'];


                $album_item = array(
                    'artist' => $artist_name,
                    'title' => $release_title,
                    'media_type' => $album_type,
                    'year' => $year,
                    'spotify_id' => $spotify_id,
                    'image' => $album_art,
                );

                array_push($gotten_albums, $album_item);
            }

            return $gotten_albums;
        }

    ?>


</html>