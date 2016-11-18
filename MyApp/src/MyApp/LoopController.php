<?php

namespace MyApp;

/**
 * A demo wrapper class for a WAMP server based on Ratchet PHP
 *
 * The purpose of  this class is to allow us manage the starting up and
 * shuting down of the WAMP server through another PHP script.
 *
 * @author Alexandros Gougousis
 */
class LoopController {

    private $bindIp;
    private $bindPort;

    private $ioserver = null;
    private $wampServer = null;
    protected $loop;
    protected $timer = 'off';

    public function __construct($bindPort = 8088,$bindIp = '0.0.0.0') {
        $this->bindPort = $bindPort;
        $this->bindIp = $bindIp; // Binding to 0.0.0.0 means remotes can connect
    }

    /**
     * Creates and activates a WAMP server
     */
    public function startServer(){
        if(empty($this->ioserver)){

            // An event loop
            $this->loop  = \React\EventLoop\Factory::create();

            // An object that will handle the WampServer events through its methods
            $pusher = new Pusher(array($this,'stopServerCallback'));

            // Set up a WebSocket server to handle the websocket(for clients wanting real-time updates)
            $webSock = new \React\Socket\Server($this->loop);
            $webSock->listen($this->bindPort, $this->bindIp);
            // Set up a Wamp server object to handle subscriptions
            $this->wampServer = new \Ratchet\Wamp\WampServer(
                $pusher
            );

            // Set up an I/O server to handle the low level events (read/write) of a socket
            $this->ioserver = new \Ratchet\Server\IoServer(
                new \Ratchet\Http\HttpServer(
                    new \Ratchet\WebSocket\WsServer(
                        $this->wampServer
                    )
                ),
                $webSock,
                $this->loop
            );

            // $wamp = $this->wampServer;  Περιττό;

            // Add a timer to server's event loop
            $this->loop->addPeriodicTimer(30, function() use ($pusher) {
                $data = array(
                    'category' => 'newsTopic',
                    'about' => 'news',
                    'title'    => 'rock 2',
                    'subscribers'  => 'dummy data',
                    'when'     => date('H:i:s')
                );
                $message = json_encode($data);
                $pusher->onMessageToPush($message);
            });
            $this->ioserver->run();  // Equals to $loop->run();

        }

    }

    /**
     * The function we want to be executed when the last subscriber unsubscribes
     */
    public function stopServerCallback() {
        $this->loop->stop();
    }


}