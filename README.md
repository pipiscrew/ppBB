# ppBB

A lightweight PHP forum system using PDO sqlite. Inspired by [Xeoncross/forumfive](https://github.com/Xeoncross/forumfive) sadly the persona website doesnt exist anymore. Here is a compact forum version, supports only one user :)

* 4 database tables
* 12 PHP files
* 41kb

Database file and structure if doesnt exist will be created on first sign in, see the login.php code.


![alt text](https://user-images.githubusercontent.com/3852762/40888929-df5441e4-675e-11e8-8826-50c172523485.jpg "Screenshot")
<br>
![alt text](https://user-images.githubusercontent.com/3852762/44483966-bfc6c400-a64c-11e8-98c1-eadef8433184.png "Screenshot2")
<br>
rev2
* list topic date DESC
* add&edit button on forum list
* forum can declared 'private', displayed only on 'logged in user' (new field cat_private at categories table)


rev3
* proof on random integer on url parameters + a child cant be accessible when any of the parents (subforums) is private
* SortOrder inputbox on forums
* subforums implemented (new field cat_parent_id at categories table)
* breadcrumb

rev4
* fix malfunction on save/update reply/topic
* make hyperlink the last forum on breadcrumb

rev5
* migrate the [calendar] (https://github.com/pipiscrew/fullcalendar-php-example)

rev6
* (calendar) add 'goto date' + timeline! view all events vertically! (greets to [Tiki Wiki CMS](https://tiki.org) for the CSS!)

rev7
* 'move topic' to another category - button added to view_topic
* view_topic, on replies the image now has id and link (allowing copy link and share the specific reply)


<br><br>
This project uses the following 3rd-party dependencies :
* [bootstrap](https://getbootstrap.com/)
* [summernote](https://github.com/summernote/summernote/) with [jQuery](https://github.com/jquery/jquery)
* [rmodal](https://github.com/zewish/rmodal.js)
* [fullcalendar](https://fullcalendar.io/)  

you may also like https://github.com/handylulu/RiteCMS by @handylulu




<br><br>
Copyright (c) 2018 [PipisCrew](http://pipiscrew.com)

Licensed under the [MIT license](http://www.opensource.org/licenses/mit-license.php).
