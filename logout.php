<?php
session_start();
include_once "csrf.php";
csrf_require_valid_post();
session_destroy();
header("Location: login.php");
exit;
