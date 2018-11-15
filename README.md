# SlimCMS
Lightweight CMS(CMF) based on: php framework slim 3, laravel eloquent, symfony event dispatcher, Twig templater and other libraries.

The fast creation of a new website(blog, news, ecommerce, etc).

[![Latest Unstable Version](https://poser.pugx.org/andrey900/slimcms/v/unstable)](https://packagist.org/packages/andrey900/slimcms)
[![Total Downloads](https://poser.pugx.org/andrey900/slimcms/downloads)](https://packagist.org/packages/andrey900/slimcms)
[![License](https://poser.pugx.org/andrey900/slimcms/license)](https://packagist.org/packages/andrey900/slimcms)

### Screenshots
| Sign In       | Users page    | Column config  |
| ------------- |:-------------:| --------------:|
| ![alt tag](http://ipic.su/img/img7/fs/ScreenShot2016-03-26at13.1458989450.png) | ![alt tag](http://ipic.su/img/img7/fs/ScreenShot2016-03-26at13.1458989486.png) | ![alt tag](http://ipic.su/img/img7/fs/ScreenShot2016-03-26at13.1458989510.png) |

| Project use additional library | Implemented modules |
|---|---|
| Slim v3 | Frendly admin panel(based on template: SB-admin v2) |
| Slim Twig Templater v2 | Create visual page, and create route |
| Slim Flash | Create sections(categories) and hierarchical sections(categories) |
| Monolog - save log in file or DB(mysql, sqlite) | Many types show field from admin panel(hidden, checkbox, select, wysiwyg html, text) |
| Slim http cache(don't use this time) | Admin panel count show items in table(your settings for every page type) |
| Slim CSRF protection | Admin panel table pagination(your settings for every page type) |
| Portable DB sqlite | User customize show field and sortable fields from tables(your settings for every page type) |
| Illuminate database v5.2 | Options system |
| Illuminate pagination v5.2 | Auth system |
| **Supported versions of php:** | Logging system |
| php: ^5.5 | Create new module |
| php: ^7.0 | Installer module |

### Installation:

      git clone https://github.com/andrey900/SlimCMS.git
      cd SlimCMS
      php composer install
or

       mkdir ~/slimcms && cd ~/slimcms
       composer create-project -s dev andrey900/slimcms .
       mkdir cache && chmod a+w cache && mkdir log && chmod a+w log
       php -S 127.0.0.1:8080 -t public/
       open browser url: http://127.0.0.1:8080
 
 After install create folder: **cache, log**. Set permittion from write this folders.

Enter admin panel:
 - url: /auth/login
 - login*: admin
 - password: admin

*if use email for login: admin@admin.net

## If You Need Help
If you have problems using or install system, please write in new issue or email(andrey@avgz.net), and I will try to help you.

If you are interested in this system, **place a star** )))

If the project attains **more than 50 stars**, the official website of the documentation will be created.


## License
The SlimCMS platform is free software distributed under the terms of the [MIT license](http://opensource.org/licenses/MIT).

## Donations
Bitcoin address for donation: 18ERsiXpvrkGMwcvLmCNVBrfJwmM8hqurY

### Social Links
[Official facebook](https://www.facebook.com/groups/997922036987106/)
