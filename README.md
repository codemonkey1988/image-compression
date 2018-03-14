# Image-compression
Adds image compression support to TYPO3.
 
Currently, there is only support for tinify.com but custom compressors an be added.
The images can be compressed on-the-fly when uploading them, or by a scheduler task.
Processsed images can only be compressed by the scheduler task because the compression
can take several seconds for each image. 

## Installation
This extension can be installed via composer only.

`compser require codemonkey1988/image-compression`

## Configuration for tinify.com
Add a tinify API key to the extension configuration in the extension manager.
To start the compression, setup the compression scheduler task.
