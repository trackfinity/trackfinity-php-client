# trackfinity

## Usage

Steps to install the trackfinity application on your server. 


#### Install with composer

If you use composer you can simply add the package to your project using this command:
```CMD
composer require trackfinity/trackfinity-php-client
```

Otherwise if you would like to install the package your self, follow the steps in the next section.

#### Manual Install

First you will need to create an account at [trackfinity.com](https://www.trackfinity.com)
and create your first campaign. 

You will find a campaign key at the top of the settings page for that campaign.
You will need this key for the set up of the client side of the traffic filter application.

Next download a copy of the client side application and that can be found here: 

After extracting the zip file from the download. Move the unzipped folder "trackfinity" to the server where 
your site is hosted. You can place this folder in the root of your application.

After you have the files on your server. You will need to add the code below to the head of any php pages that you wish to
use the traffic filter on.

```PHP
    include </path/to/>'trackfinity.php';
    trackfinity::run('<your campaign key>', 'https://trafficfilter.com/api');
```

Please remember that you will need to replace ```</path/to/>trackfinity.php``` and ```<your campaign id>``` with the
correct path to the file on your server and the KEY from the campaign you created in the earlier steps.

## Conversion Tracking
After the basic set up above. You also have the option to set up conversion tracking. 
This will post back to traffic filter when a user has hit a conformation page.

To set this up...