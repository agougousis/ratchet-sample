<?php
    $appDirectoryName = 'html';

    // Include the autoloader
    require dirname(__DIR__) . "/$appDirectoryName/vendor/autoload.php";

    use MyApp\LoopController;

    // Create a Wamp server wrapper
    $loopController = new LoopController();
    // Start the server
    $loopController->startServer();


