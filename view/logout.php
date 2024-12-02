<?php
session_start();
session_destroy();
header('Location: MoveMeNow/index.php');
exit;