<?php
// Titik masuk (route): lihat dan simpan profil
require 'config.php';
require 'models/User.php';
require 'models/Profile.php';
require 'controllers/ProfileController.php';

$controller = new ProfileController(new Profile($conn), new User($conn));
$controller->handle($_SERVER['REQUEST_METHOD'], input());
