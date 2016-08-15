# Blue-API
This runs the cloud variable/list server in Blue. To set this up yourself, you will need the following things:
- A mysql database
- A php server
- A scratch modification built off of Scratch 2.0

## Setting up the server
This code is made to be simple to install and use, but it may need to be set up for your server's specific needs. To set up the mysqli database, open a text editor on the mysqli.php file. On the 14th line, you will find text that says `mysqli_connect('SERVER', 'USERNAME', 'PASSWORD', 'DATABASE');`. This is mostly self-explanatory: `SERVER` is the address of the mysql server (usually localhost). `USERNAME` and `PASSWORD` are the username and password in order to connect to the database. `DATABASE` is the name of the database. 

Now you need to set up the cloud.php file. If you place this in the root of your server's public_html directory, then you do not need to make any changes and you can skip to the section on adding cloud data to your scratch mod. However, if you are placing this file inside a directory, then it gets a little tricky. You need to create a new line after the 5th line, so that it looks like this:

```php
$dirs = explode('/', strtok($_SERVER['REQUEST_URI'], '?'));`

if ($dirs[2] == 'get') {
```
Then you need to add the following code in the new line you just created, replacing `n` with the number of subdirectories you are putting the cloud.php in:
```php
for ($i = 0; $i <= n; $i++) {
  unset($dirs[0]);
  $dirs = array_values($dirs);
} 
```

## Setting up the mod to communicate with the server
I cannot provide help with this part of the guide, mainly because I don't know how your server is set up or how your mod is made. I can, however, point you in the right direction:
- To generate the url to connect to the cloud correctly, you can use the following function implemented in Interpreter.as:
``` actionscript
public function createCloudUrl(argument1, argument2:* = null, argument3:* = null) {
    var baseUrl:String = 'http://your-path-to-cloud.php-here';
    var doneurl:String = baseUrl + '/' + (argument1.toString());
    if (argument2 == null) return doneurl;
    doneurl = doneurl + '/' + (argument2.toString());
    if (argument3 == null) return doneurl;
    return doneurl + '/' + (argument3.toString());
}
```
- To communicate with the server, use the following code:
``` actionscript
var request:URLRequest = new URLRequest(createCloudUrl(argument1, [argument2, argument3]));
var loader:URLLoader = new URLLoader();
loader.addEventListener(Event.COMPLETE, dataGet);
loader.dataFormat = URLLoaderDataFormat.TEXT;
loader.load(request);
function dataGet(event:Event):void {
event.target.data.toString(); // This is the data returned by the server
}
```
