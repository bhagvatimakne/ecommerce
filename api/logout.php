<?php
include "../config/session.php";
session_destroy();
header("Location: ../public/login.php");
exit;
