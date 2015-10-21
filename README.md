# Scan
Multi-threaded web directory scanner. Uses a memory efficient brute-force method for generating paths.

## Usage
```
$scan = new Scan('SUPER-SECRET-SITE-WITH-UNKNOWN-FILES', 1, 3);
$scan->setSpecialPaths(array(
    "funny-cats.gif","naked-photo.jpg"
));
$scan->scan();
/* POSSIBLE OUTPUT
SUPER-SECRET-SITE-WITH-UNKNOWN-FILES/funny-cats.gif Found - Response Code: 301
SUPER-SECRET-SITE-WITH-UNKNOWN-FILES/naked-photo.jpg Found - Response Code: 200
SUPER-SECRET-SITE-WITH-UNKNOWN-FILES/aaa Found - Response Code: 200
SUPER-SECRET-SITE-WITH-UNKNOWN-FILES/abc Found - Response Code: 200
*/
```

## Contributing
Fork it, create a branch, commit your changes, push, request a pull. It's that easy :D

I accept and would be happy about all kinds of contribution, send me an e-mail & contact me on Twitter or just reach me on Github if you see some stuff which can be better or if you want to add something.

## License
GNU GENERAL PUBLIC LICENSE. Version 3, 29 June 2007. Look at LICENSE file for further information.