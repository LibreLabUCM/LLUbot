# LLUbot
[Telegram @LLubot](https://telegram.me/llubot)

## Requirements
 * A web server SSL-encrypted with a public url (apache, nginx, caddy, etc....)
 * PHP
 * curl binding for php (`php5-curl` package in Ubuntu)
 * SQLite3 for php (`php-sqlite3` package in Ubuntu)

## Setup

 1. Clone this repository in the document root of the webserver, or any other folder inside it (anything accesible from a browser is valid):
   * https://example.com/index.php
   * https://example.com/llubot/index.php
   * https://example.com/somefolder/llubot/index.php
   
 2. Edit _config.php_:
   * Substitute _SECRETKEY_ with any random string
   * Substitute _SECRETTOKEN_ with the token provided by @BotFather
   * Set the variable _$botHook_ to the URL where the index.php is located.
   
       The URL should end in: '?key='.$_GET['key']
       
       If the URL to my index.php is: __https://SECRETURL/llubot/index.php__
       
       Then: __$botHook = 'https://SECRETURL/llubot/?key='.$_GET['key'];__
     
 3. Visit this URL in your browser to setup the webhook:
  
    https://SECRETURL/?key=SECRETKEY&setHook
    
    Replacing _SECRETKEY_ with the one choosen before and _SECRETURL_ with the URL prefix set in step 1. For example: _https://example.com/_ or _https://example.com/llubot/_.

Once the webhook is set, the bot should work. It is NOT neccesary to update again the webhook when the code is changed. Once the webhook is set, telegram will remember it.
