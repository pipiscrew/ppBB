# ppBB

A lightweight PHP forum system using PDO sqlite. Inspired by [Xeoncross/forumfive](https://github.com/Xeoncross/forumfive) sadly the persona website doesnt exist anymore. Here is a compact forum version, supports only one user :)

* 4 database tables
* 12 PHP files
* 30kb

Database file and structure if doesnt exist will be created on first sign in, see the login.php code.


![alt text](https://user-images.githubusercontent.com/3852762/40888929-df5441e4-675e-11e8-8826-50c172523485.jpg "Screenshot")


rev2
*list topic date DESC

+add&edit button on forum list

+forum can declared 'private', displayed only on 'logged in user' (new field cat_private at categories table)


<br><br>
This project uses the following 3rd-party dependencies :
* [bootstrap](https://getbootstrap.com/)
* [summernote](https://github.com/summernote/summernote/) with [jQuery](https://github.com/jquery/jquery)
* [rmodal](https://github.com/zewish/rmodal.js)

<br><br>
Copyright (c) 2018 [PipisCrew](http://pipiscrew.com)

Licensed under the [MIT license](http://www.opensource.org/licenses/mit-license.php).
