<?php

global $title;
global $navi;

?>

<!DOCTYPE html>

<html>

<head>
<title>YouSpeak - <?php echo $title; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<link href="scripts/main.css" rel="stylesheet" type="text/css" />
<link href="scripts/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" type="text/css" />
<script src="scripts/jquery-1.9.1.js"></script>
<script src="scripts/jquery-ui-1.10.3.custom.min.js"></script>
<script src="https://apis.google.com/js/client:platform.js" async defer></script>
<script src="scripts/script.js"></script>
<script>var NO_REWRITE = <?php echo NO_REWRITE?"true":"false"; ?>;</script>
</head>

<body>
<div id="container">
    <div id="heading">
        <img src="images/logo.png" alt="youspeak logo" /><h1><span class="orange">Y</span>ou<span class="orange">S</span>peak</h1>
    </div><!-- heading -->
    <div id="navigation" class='ui-widget ui-widget-header'>
        <?php $navi->run(); ?>
    </div><!-- navigaton -->
    <div id="content">
