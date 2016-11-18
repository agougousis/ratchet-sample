<?php

// Check if the monitoring service is up
exec("ss -ltn | grep -i ':8088'",$out);

if(!empty($out)){
    // The service is up
    $data = array('server_status' => 'on');
}  else {
    // Try to start the service
    try {

        // Executes the launcher.php in a separate process
        // (we don't want launcher to block our process)
        popen("php launcher.php &","r");

        // Give some time to finish the service booting
        sleep(1);

        // Checks again if the monitoring service is up
        exec("ss -ltn | grep -i ':8088'",$out);
        if(!empty($out)){
            // The service started successfully.
            $data = array('server_status' => 'on');
        } else {
            // The service failed to start.
            $data = array('server_status' => 'off');
        }


    } catch (Exception $ex) {
        // Something went wrong! The service is down.
        $data = array(
            'server_status' => 'off',
            'message'       => $ex->getMessage()
            );
    }

}

// return the service status
header('Content-Type: application/json');
echo json_encode($data);