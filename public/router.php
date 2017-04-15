<?php

// router.php
if ($_SERVER["SCRIPT_FILENAME"] != 'public/router.php' && file_exists($_SERVER["SCRIPT_FILENAME"])) {
    return false;
}
require __DIR__ . '/index.php';
