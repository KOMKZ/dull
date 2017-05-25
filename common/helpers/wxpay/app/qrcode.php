<?php
require "../src/helpers/phpqrcode.php";
$url = urldecode($_GET["data"]);
\QRcode::png($url);
