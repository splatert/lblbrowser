



<div class="topbar">
    <a href="index.php"><div class="logo-title"><b>Lbl</b>Browser</div></a>
    <div class="topbar-search">
        <form method="GET" action="search.php" class="topbar-search-container">

            <script>
                function sh(btn) {
                    var history = document.getElementsByClassName('search-history')[0];
                    if (history.style.display == 'none') {
                        history.style.display = 'block';
                        btn.value = "▲";
                    }
                    else {
                        history.style.display = 'none';
                        btn.value = "▼";
                    }
                }
            </script>

            <input type="text" name="q" autocomplete="off" placeholder="Enter label title">
            <input type="button" onclick="sh(this)" value="▼">
            <div class="search-history" style="display: none;">
                <p>Search History</p>
                <?php
                    if (isset($_COOKIE['search-history'])) {
                        
                        $past_searches = json_decode($_COOKIE['search-history']);
                        $past_searches = array_reverse($past_searches);

                        foreach ($past_searches as $search) {
                            echo '<a onclick="window.location.href=\'search.php?q=\'+this.innerText;">'.$search.'</a>';
                        }
                    }
                ?>
            </div>
        </form>
    </div>
    <div class="topbar-links">
        <a href="https://github.com/splatert/lblbrowser" class="topbar-link">Project Link</a>
        <a href="settings.php" class="topbar-link">Settings</a>
    </div>
</div>