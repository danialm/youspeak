<?php
global $title;
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<title> <?php echo "YouSpeak - $title"; ?> </title>

<link rel="stylesheet" href="https://ajax.aspnetcdn.com/ajax/jquery.mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
<script src="scripts/script.js"></script>
<script>var NO_REWRITE = <?php echo NO_REWRITE?"true":"false"; ?>;</script>

<script>
$(document).bind("mobileinit", function()
{
    $.mobile.transitionFallbacks.flip = "none";
    $.mobile.transitionFallbacks.slideup = "none";
});
</script>

</head>

<body>

