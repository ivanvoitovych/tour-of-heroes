<?php

require __DIR__ . '/../vendor/autoload.php';

// include backend
include __DIR__ . '/../backend/endpoints.php';

// Viewi application here
include __DIR__ . '/../viewi-app/viewi.php';
Viewi\App::handle();
