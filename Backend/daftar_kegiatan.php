<?php
// Titik masuk (route): pendaftaran kegiatan
require 'config.php';
require 'models/Profile.php';
require 'controllers/ActivityRegistrationController.php';

$controller = new ActivityRegistrationController(new Profile($conn));
$controller->register(input());
