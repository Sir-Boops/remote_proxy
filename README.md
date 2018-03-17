# Remote proxy

Proxy all remote content via the roundcube server

To install clone the repo into your roundcube plugins dir
Edit the required args in config.inc.php
````
CREATE USER rcube_proxy;
CREATE DATABASE rcube_proxy OWNER rcube_proxy;
````
Then logout and back in and you're good!
