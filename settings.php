
<?php
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
?>


<html>
    <link rel="stylesheet" href="design.css">
    <body>
        <?php
            include('topbar.php');
            include('page_prefs.php');
            $one_year = time() + ( 365 * 24 * 60 * 60);
        ?>

        <div class="page">
            <section class="page-content">

                <?php

                    if (isset($_POST['action']) && $_POST['action'] == 'save-creds') {
                        if (isset($_POST['client-id'])) {
                            setcookie('client_id', $_POST['client-id'], $one_year);
                        }
                        if (isset($_POST['client-secret'])) {
                            setcookie('client_secret', $_POST['client-secret'], $one_year);
                        }
                        echo 'Settings were saved. Please <a href="javascript:window.location = \'settings.php\' ">refresh the page.</a>';
                    }


                    if (isset($_POST['action']) && $_POST['action'] == 'save-width') {
                        if (isset($_POST['full-width'])) {
                            setcookie('full_width', true, $one_year);
                        }
                        else {
                            setcookie('full_width', false, $one_year);
                        }
                        echo 'Settings were saved. Please <a href="javascript:window.location = \'settings.php\' ">refresh the page.</a>';
                    }


                    if (isset($_POST['action']) && $_POST['action'] == 'save-id-display') {
                        if (isset($_POST['show-ids'])) {
                            setcookie('show_ids', true, $one_year);
                        }
                        else {
                            setcookie('show_ids', false, $one_year);
                        }
                        echo 'Settings were saved. Please <a href="javascript:window.location = \'settings.php\' ">refresh the page.</a>';
                    }


                    if (isset($_POST['action']) && $_POST['action'] == 'save-search-res') {
                        if (isset($_POST['save-search-results'])) {
                            setcookie('save_search_results', true, $one_year); 
                        }
                        else {
                            setcookie('save_search_results', false, $one_year); 
                        }
                        echo 'Settings were saved. Please <a href="javascript:window.location = \'settings.php\' ">refresh the page.</a>';
                    }


                    if (isset($_POST['clear-search-history']) && $_POST['clear-search-history'] == 1) {
                        setcookie('search-history', '', time() - 3600, '/');
                        echo 'Cleared search history.';
                    }

                    if (isset($_GET['error'])) {
                        if ($_GET['error'] == 'missing-creds') {
                            echo 'Please provide your spotify client credentials below.';
                        }
                    }

                ?>

                <form method="POST" action="settings.php">
                    <input name="action" type="hidden" value="save-width">
                    <fieldset>
                        <legend>Interface</legend>
                        <label for="full-width">Full width page</label>
                        <input type="checkbox" name="full-width">
                        <input type="submit" class="btn2" style="width:unset;height:unset;display:unset;" value="save">
                    </fieldset>
                </form>


                <form method="POST" action="settings.php">
                    <input name="action" type="hidden" value="save-id-display">
                    <fieldset>
                            <legend>Tracklist</legend>
                            <label for="show-ids">Show track barcodes/identifiers</label>
                            <input type="checkbox" name="show-ids">
                            <input type="submit" class="btn2" style="width:unset;height:unset;display:unset;" value="save">
                        </fieldset>
                </form>

                <form method="POST" action="settings.php">
                <input name="action" type="hidden" value="save-creds">
                    <fieldset>
                        <legend>Custom client credentials (Will be used instead of ones set by server)</legend>
                        <br>
                        <label for="client-id">Client ID: </label>
                        <input id="c-id" name="client-id" type="password">

                        <label for="client-secret">Client Secret: </label>
                        <input id="c-secret" name="client-secret" type="password">

                        <input type="submit" class="btn2" style="width:unset;height:unset;display:unset;" value="save">
                    </fieldset>
                </form>

                <fieldset>
                    <legend>Search History</legend>
                    <form method="POST" action="settings.php">
                        <input name="action" type="hidden" value="save-search-res">

                        <label for="save-search-results">Save search results</label>
                        <input type="checkbox" name="save-search-results">
                        <input type="submit" class="btn2" style="width:unset;height:unset;display:unset;" value="save">
                    </form>

                    <form method="POST" action="settings.php">
                        <input type="hidden" name="clear-search-history" value="1">
                        <input type="submit" class="btn2" style="width:unset;height:unset;display:unset;" value="Clear search history">
                    </form>

                </fieldset>


                <?php
                    if (isset($_COOKIE['client_id'])) {
                        echo '<script>document.getElementById("c-id").value = "'.$_COOKIE['client_id'].'"</script>';
                    }
                    if (isset($_COOKIE['client_secret'])) {
                        echo '<script>document.getElementById("c-secret").value = "'.$_COOKIE['client_secret'].'"</script>';
                    }
                    if (isset($_COOKIE['full_width'])) {
                        echo '<script>document.getElementsByName("full-width")[0].checked = true</script>';
                    }
                    if (isset($_COOKIE['show_ids']) && $_COOKIE['show_ids'] == true) {
                        echo '<script>document.getElementsByName("show-ids")[0].checked = true</script>';
                    }
                    if (isset($_COOKIE['save_search_results']) && $_COOKIE['save_search_results'] == true) {
                        echo '<script>document.getElementsByName("save-search-results")[0].checked = true</script>';
                    }
                ?>

            </section>
        </div>

    </body>
</html>