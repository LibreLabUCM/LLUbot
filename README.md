# LLUbot
http://telegram.me/llubot


## Setup

 * Set up a web server with a public url, and with php support (apache, nginx, caddy, etc....)
 * Clone this repository in the document root of the webserver, or any other folder inside it (anything accesible from a browser is valid)
   * https://example.com/index.php
   * https://example.com/llubot/index.php
   * https://example.com/somefolder/llubot/index.php
 * Edit _config.php_:
   * Substitute _SECRETKEY_ with any random string
   * Substitute _SECRETTOKEN_ with the token provided by @BotFather
   * Set the variable _$botHook_ to the URL where the index.php is located.
   
       The URL should end in: '?key='.$_GET['key']
       
       If the URL to my index.php is: __https://SECRETURL/llubot/index.php__
       
       Then: __$botHook = 'https://SECRETURL/llubot/?key='.$_GET['key'];__
     
 * Visit this URL in your browser:
  
    https://SECRETURL/llubot/?key=SECRETKEY&setHook
    
    Replacing _SECRETKEY_ with the one choosen before.

 * Once the webhook is set, the bot should work. It is NOT neccesary to update again the webhook when the code is changed. Once the webhook is set, telegram will remember it.
