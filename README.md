# ratchet-sample
Example of building a WAMP server with PHP Ratchet library

This example is intended for those who are struggling with the poor documentation of Ratchet library. Apart from its documentation, Ratchet, and by so ReactPHP, is a very nice tool to build network services and real-time applications with PHP and Websockets.

### About this app:

This repository contains a sample application that demonstrates how to setup a WAMP server using the Ratchet PHP library. Some important implementation details of our server are: 

* We want to start the WAMP server as soon as the first subscriber arrives. That means that the WAMP server is client-initiated.
* We want to stop the WAMP server when the last subscriber unsubscribes.
* We want the server the keep track of the number of total subscribers.

The 3 main tasks that we want our server to implement are:

* Every time a new subscriber subscribes or unsubscribes, we inform all the subscribers about the new total subscribers number.
* We want the WAMP server to execute a specific action/method every 30 seconds. In this application the action just sends a random text message to the subscribers of a specific topic (the only topic we use in this example).
* Any message posted by any subscriber is send to all other subscribers of the topic.


### Libraries used:

* Ratchet, PHP library for setting up a websocket server (http://socketo.me/) 
* Autobahn.js , Javascript library for WAMP clients (http://autobahn.ws/js/)

_Note_: I have included the Autobahn.js library in the repository not only to make it easier for you but also because my original example was built with an older version that was not using React and it makes it easier for me to build my example. ( the newer version can be found here: http://autobahn.ws/js/)


### Installation steps

A yml configuration file for docker-compose has been added to help you setup the application in a few seconds. If you are not going to use Docker, you need to make the following changes:

1) Change the hostname (wampHostname) and the port (wampPort) of the server that hosts the WAMP and in the /client.html 
2) Change the name of the application directory ($websocketPort) in  /launcher.php
3) Change the port used by websockets ($websocketPort) in /enable_wamp_server.php
4) Change the default port that will be used for websockets ($bindPort)  in LoopController.php
5) If your server is not Linux-based, you may need to modify the bash commands used in /enable_wamp_server.php


### How to test
	
* IF you are using Docker, start the PHP container by:  "docker-compose up"	
* Open http://your_hostname/client.html  (or http://localhost/client.html , if you are using Docker) from a browser, while you have opened the developer tools supported by this browser 
* Check out the messages printed in the developer tools'console.
* Open the same URL in another browser and check the console of both browsers.

### Example (I am using ratchet.gr as hostname)

We open Firefox at http://localhost/client.html . The client initiates WAMP server's boot up and subscribes. The WAMP server immediately broadcasts the number of total subscribers (only this client for the moment). You can see below a screenshot of Firebug console panel a few seconds after the initial HTTP request. 

![s1](https://user-images.githubusercontent.com/5471589/58316463-e56b3e80-7e13-11e9-9024-61011b48d11c.png)

After one minute, two dummy messages have been sent to subscribers of 'newsTopic' topic by the timer that we have attached to the event loop.

![s2](https://user-images.githubusercontent.com/5471589/58316477-f0be6a00-7e13-11e9-9304-320c9daa5c7c.png)

We open Chrome at http://localhost/client.html , too. The client finds that WAMP server is already up and subscribes immediately. The console panel of both Chrome (developer tools) and Firefox is displayed below. You can see that both clients have been informed about the new total number of subscribers. A new dummy message have arrived, too, in both clients since both of them are subscribed to the 'newsTopic' topic. 

![s3](https://user-images.githubusercontent.com/5471589/58316489-f5831e00-7e13-11e9-8149-834a2904ae8e.png)

Now, from Firefox input box, we publish a message to the "newsTopic" channel and we see the message propagated to both browser windows.
![s4](https://user-images.githubusercontent.com/5471589/58316504-fae06880-7e13-11e9-8f96-b24baa130a03.png)

We do the same from the Chrome input box.

![s5](https://user-images.githubusercontent.com/5471589/58316519-016ee000-7e14-11e9-9633-9dcfc20339cc.png)

If you log in to the server you can see the service run on 8088 port (the default port for this example). If you close both browser windows you wil see that the service is not running anymore.
