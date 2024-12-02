<?php
session_start();
session_destroy();
header('Location: ./index.php'); // Redirect to the root-level login page
exit;
