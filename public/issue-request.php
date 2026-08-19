<?php
session_start();
// This page is now disabled to prevent students from self-issuing books.
// All issuance must be done through the Admin Panel.

header('Location: view-books.php?error=self_issue_disabled');
exit;
?>