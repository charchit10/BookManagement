<?php
session_start();

//remove all session variables
session_unset();

//destroy
session_destroy();

// Redirect back to login.php
header("Location: ../login.php");
exit;
