# Critter, A Twitter like application written with [Laravel] in under 10 hours by [@msurguy]

Imagine Twitter is down again. It's dark outside, and how can you tell everyone about that? 
Critter to the rescue. Deploy your own Twitter with Laravel and any cloud service, in minutes! 

##What does this do ?   
It is a PHP/MySQL web application written in Laravel for the purpose of learning. 

The users can register, sign in, post critts, view critts and follow other users.

See working demo at http://demos.maxoffsky.com/critter

## How to get this thing running? 
You have few options:
To deploy in the cloud, use Pagodabox or PHPFog and watch my tutorials on [Udemy]
To deploy on the local machine use MAMP, WAMP or Apache/PHP 5.3/MySQL, edit your settings in the application/config folder according to Laravel documentation. Or again, watch my tutorials on [Udemy] 

The sql for the database structure is below:
```
CREATE TABLE `critts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `critt` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE `followers` (
  `user_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`following_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `users` (`id`, `username`, `password`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2a$08$iI.Vafp1s9CxDegDH2hA7uyaatGcVpYcR1tVE9VGDFEYgqf08teO.', 'Pete Adminovich', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

```
  
##Features: 
* [Laravel]
* [Bootstrapper Bundle]
* [Font Awesome]
* [jQuery char Counter]
* Controllers

## Contributing to This code
Contributions are encouraged and welcome. Submit pull requests or ask questions if something's not clear

### License
This code is open source and is under MIT license.

  [@msurguy]: http://twitter.com/msurguy
  [Laravel]: http://laravel.com
  [Bootstrapper Bundle]: http://bundles.laravel.com/bundle/bootstrapper
  [jQuery char Counter]: http://www.tomdeater.com
  [Font Awesome]: http://fortawesome.github.com/Font-Awesome/
  [Udemy]: http://www.udemy.com/develop-web-apps-with-laravel-php-framework/
