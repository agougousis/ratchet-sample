<?php
    $appDirectoryName = 'html';

    // Decides whether we are going to use an encrypted websocket connection or not.
    // Keep in mind that this example uses the included self-signed certificate and
    // the browser will throw an ERR_CERT_AUTHORITY_INVALID error and won't let you
    // establish a secure connection with an untrusted certificate. However, there are
    // a few ways to get away with it, for the sake of this example. (1) You can start
    // Chrome with an option that makes it ignore certificate errors (for Windows:
    // "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" --ignore-certificate-errors )
    // There should not be any other windows/tabs open when you execute this command.
    // (2) You can use these certificate for another HTTP endpoint and let the browser
    // give you the ability to accept the untrusted certificate.
    $useSecure = false;

    // Include the autoloader
    require dirname(__DIR__) . "/$appDirectoryName/vendor/autoload.php";

    use MyApp\LoopController;
    use MyApp\SecureLoopController;

    // Create a Wamp server wrapper
    if ($useSecure) {
        $loopController = new SecureLoopController();
    } else {
        $loopController = new LoopController();
    }

    // Start the server
    $loopController->startServer();


