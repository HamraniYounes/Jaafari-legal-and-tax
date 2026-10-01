<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

logout_admin();

// Nouvelle session propre pour le message éventuel
session_start();
redirect('../index.php');
