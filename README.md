# ratchet-sample
Example of building a WAMP server with PHP Ratchet library

This example is intended for those who are struggling with the poor documentation of Ratchet library. Apart from its documentation, Ratchet, and by so ReactPHP, is a very nice tool to build network services and real-time applications with PHP and Websockets.

### About this app:

This repository contains a sample application that demonstrates how to setup a WAMP server using the Ratchet PHP library. The implemented scenario is the following:

* We want to start the WAMP server as soon as the first subscriber arrives. That means that the WAMP server is client-initiated. 
* We want to shut down the WAMP server when the last subscriber unsubscribes.
* We want the server the keep track of the number of total subscribers.
* Every time a new subscriber subscribes or unsubscribes, we inform all the subscribers about the new total subscribers number.
* We want the WAMP server to execute a specific action/method every 30 seconds. In this application the action just sends a random text message to the subscribers of a specific topic.

### Libraries used:

* Ratchet, PHP library for setting up a websocket server (http://socketo.me/) 
* Autobahn.js , Javascript library for WAMP clients (http://autobahn.ws/js/)

### Installation steps
	
1) Change WAMP server port in enable_wamp_server.php to the one you want
2) 	Change the WAMP server port,IP and hostname in client.html
3)	Change 'ratchet' to the name of application's directory in launcher.php 

### How to test
	
* Open http://your_hostname/client.html from a browser, while you have opened the developer tools supported by this browser 
* Check out the messages printed in the developer tools'console.
* Open the same URL in another browser and check out its console, too

### Example (I am using ratchet.gr as hostname)

We open Firefox at http://ratchet.gr/client.html . The client initiates WAMP server's boot up and subscribes. The WAMP server immediately broadcasts the number of total subscribers (only this client for the moment). You can see below a screenshot of Firebug console panel a few seconds after the initial HTTP request. 

![pin1](https://cloud.githubusercontent.com/assets/5471589/20449665/6d4879ec-adf3-11e6-9b17-abb82e1ec753.png)

After one minute, two dummy messages have been sent to subscribers of 'newsTopic' topic by the timer that we have attached to the event loop.

![pin2](https://cloud.githubusercontent.com/assets/5471589/20449669/75e106be-adf3-11e6-972c-d612b0898d1a.png)

We open Chrome at http://ratchet.gr/client.html , too. The client finds that WAMP server is already up and subscribes immediately. The console panel of both Chrome (developer tools) and Firefox is displayed below. You can see that both clients have been informed about the new total number of subscribers. A new dummy message have arrived, too, in both clients since both of them are subscribed to the 'newsTopic' topic. 

![pin3](https://cloud.githubusercontent.com/assets/5471589/20449681/8449c128-adf3-11e6-9df8-ce554f22c342.png)

![pin4](https://cloud.githubusercontent.com/assets/5471589/20449686/8dd6dc9e-adf3-11e6-8d0e-eaacefd3ed00.png)

If you log in to the server you can see the service run on 8088 port (the default port for this example). If you close both browser windows you wil see that the service is not running anymore.
