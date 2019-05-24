<?php

$websocketPort = 8088;

// Check if the monitoring service is up
exec("ss -ltn | grep -i ':$websocketPort'", $out);

if(!empty($out)){
    // The service is up
    $data = ['server_status' => 'on', 'details' => 'It was already running.'];
}  else {
    // Try to start the service
    try {
        // Executes the launcher.php in a separate process
        // (we don't want launcher to block our process)
        popen("php launcher.php &", "r");

        // Give some time to finish the service booting
        sleep(1);

        // Checks again if the monitoring service is up
        exec("ss -ltn | grep -i ':$websocketPort'", $out);
        if(!empty($out)){
            // The service started successfully.
            $data = ['server_status' => 'on', 'details' => 'Just started.'];
        } else {
            // The service failed to start.
            $data = ['server_status' => 'off', 'details' => 'Failed to start.'];
        }

    } catch (Exception $ex) {
        // Something went wrong! The service is down.
        $data = ['server_status' => 'off', 'details' => $ex->getMessage()];
    }
}

// return the service status
header('Content-Type: application/json');
echo json_encode($data);
