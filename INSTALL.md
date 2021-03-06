#Zoph Installation#

##Requirements##

See the REQUIREMENTS.md document.

##Creating the database##

###Create a database and import the tables###

```
$ mysql -u root -p -e "CREATE DATABASE zoph CHARACTER SET utf8 COLLATE utf8_general_ci"
$ mysql -u root -p zoph < sql/zoph.sql
```

###Create users for zoph###

I created two users: zoph_rw is used by the application and zoph_admin is used when I work directly in mysql so I don't
have to use root.

```
$ mysql -u root -p
mysql> grant select, insert, update, delete on zoph.* to zoph_rw@localhost identified by 'PASSWORD';
mysql> grant all on zoph.* to zoph_admin identified by 'PASSWORD';
```

##Create zoph.ini##
In Zoph 0.8.2 and later, you need to create a zoph.ini file, usually in 
/etc. zoph.ini is where you define database settings. A simple example:

```
[zoph]
db_host = "localhost"
db_name = "zoph"
db_user = "zoph_rw"
db_pass = "pass"
db_prefix = "zoph_"

php_location = /var/www/html/zoph
```

An example zoph.ini file, called zoph.ini.example is included in the cli directory.
See the man page for zoph.ini(5) or the Wikibooks documentation http://en.wikibooks.org/wiki/Zoph/Configuration for more details

##Install the templates##

###Pick a location to put Zoph###

Create a zoph/ directory off the doc root of your web server, or create a Virtual Host with a new doc root.

```
$ mkdir /var/www/html/zoph
```

###Copy the templates###
```
$ cp -r php/* /var/www/html/zoph/
```
##Configure the PHP templates##

Many configuration items can be set in php/config.inc.php file. For more information, see http://en.wikibooks.org/wiki/Zoph/Configuration.

##Install the CLI scripts##

###Check the path to PHP###

The CLI script points to /usr/bin/php.  If your PHP installation is in a different place, edit the first line of the script.

###Copy cli/zoph to /bin###
Or some other directory in your PATH.

###Install the man page###
Man pages for zoph and zoph.ini is in the cli/ directory. Copy these to the man1 and man5 directoies in your manpath, /usr/local/man/man1 and /usr/local/man/man5 for example.

##Test it##
Try hitting http://localhost/zoph/logon.php.  You should be presented with the logon screen.

You can log in with admin / admin. It is recommended to change this.

If you get a 404 error...
make sure the zoph/ folder and templates can be seen by the web server.

If you see a bunch of code...
make sure Apache is configured to handle PHP (see the REQUIREMENTS file)

If you see a MySQL access denied error...
make sure the db_user you specified in zoph.ini actually has access to the database.  If your database is not on localhost, you will need to grant permissions to zoph_rw@hostname for that host.
