<?php

namespace MyApp;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

/**
 * A class that implements the WAMP server functionality by implementing the
 * WampServerInterface.
 *
 * @author Alexandros Gougousis
 */
class Pusher implements WampServerInterface {

    protected $total_subscribers = 0;
    protected $stopCallback;

    public function __construct($stopServerCallback) {
        $this->stopCallback = $stopServerCallback;
    }

    /**
     * We don't call the callback function directly in case we want to do some
     * more actions before shutting down the service.
     */
    protected function close() {
        call_user_func($this->stopCallback);
    }


    /**
     * To be called by clients to subscribe for a topic
     *
     * @param ConnectionInterface $conn
     * @param Topic $topic
     */
    public function onSubscribe(ConnectionInterface $conn, $topic) {

        // Update the number of total subscribers
        $this->total_subscribers++;

        // Add the new subscriber to the list of this topic's subscribers
        $this->subscribedTopics[$topic->getId()] = $topic;

        // Inform the subscribers of this topic about the number of total subscribers
        $messageData = array(
            'about' => 'subscribers',
            'subscribers' => $this->total_subscribers,
            'when'     => date('H:i:s')
        );
        $topic->broadcast($messageData);

    }

    /**
     * To be called by clients to unsubscribe for a topic
     *
     * @param ConnectionInterface $conn
     * @param Topic $topic
     */
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {

        // Update the number of total subscribers
        $this->total_subscribers--;

        // Inform the subscribers of this topic about the number of total subscribers
        $messageData = array(
            'about' => 'subscribers',
            'subscribers' => $this->total_subscribers,
            'when'     => date('H:i:s')
        );
        $topic->broadcast($messageData);

    }

    /**
     * Executes when a client has initiated a connection
     *
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn) {
    }

    /**
     * Executes when a client has closed its connection
     *
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn) {

        foreach($this->subscribedTopics as $topic){
            if($topic->has($conn)){
                $topic->remove($conn);
                $this->onUnSubscribe($conn, $topic);
                break;
            }
        }

        if($this->total_subscribers == 0){
            $this->close();
        }

    }

    /**
     * Used when a client sends data
     *
     * @param ConnectionInterface $conn
     * @param string $id
     * @param Topic $topic
     * @param array $params
     */
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

    /**
     * This method will be called when a new message arrives through an established websocket.
     * So, it will be used by clients to publish messages to a topic.
     *
     * @param ConnectionInterface $conn
     * @param Topic $topic
     * @param string $payload
     * @param array $exclude
     * @param array $eligible
     */
    public function onPublish(ConnectionInterface $conn, $topic, $payload, array $exclude, array $eligible) {
        // Inform the subscribers of this topic about the published message
        $messageData = array(
            'about' => 'publishing',
            'message' => $payload,
            'when'     => date('H:i:s')
        );
        $topic->broadcast($messageData);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }

    /**
     * Publish a new message to a topic's subscribers. The topic name is
     * included in the message itself. In this application, we call this method
     * periodically through the periodic timer that we have added to the loop.
     *
     * @param string $message
     */
    public function onMessageToPush($message){

        $messageData = json_decode($message, true);

        // If the lookup topic object isn't set there is no one to publish to
        if (!array_key_exists($messageData['category'], $this->subscribedTopics)) {
            return;
        }

        $topic = $this->subscribedTopics[$messageData['category']];

        // re-send the data to all the clients subscribed to that topic
        $topic->broadcast($messageData);

    }

}
