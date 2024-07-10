<?php
    if (isset($_COOKIE['full_width']) && $_COOKIE['full_width'] == true) {
        echo '<script>document.getElementsByTagName("body")[0].style.maxWidth = "unset"</script>';
    }
?>