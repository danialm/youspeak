<?php

global $title;
global $navi;

?>

<!DOCTYPE html>

<html>

<head>
<title>YouSpeak - <?php echo $title; ?></title>
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
        <h1 style='color: #DDD'>&nbsp;&nbsp;YouSpeak</h1>
    </div><!-- heading -->
    <div id="navigation" class='ui-widget ui-widget-header'>
        <?php $navi->run(); ?>
    </div><!-- navigaton -->
    <div id="content">
