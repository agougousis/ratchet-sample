<?php
    // Include the autoloader
    require dirname(__DIR__) . '/ratchet/vendor/autoload.php';

    use \MyApp\LoopController;

    // Create a Wamp server wrapper
    $loopController = new LoopController();
    // Start the server
    $loopController->startServer();


