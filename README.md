# Taskit

This is a highly flexible task management system.  It already has a lot of
features and is quite usable despite being a fairly early state.

You can try Taskit out yourself at http://www.mytaskit.net

Originally this project was to be a commercial venture, but now I feel that the
community could complete the project and benefit from the project much more if
it weren't commercial.

# Help Us

This project has a few missing features and a few things that need some
attention here and there.  If you like the project and want to help, please do!

http://www.mytaskit.net is using the same code you can find here.

## Profit

At this point, I would much rather someone make use of my code than make money
off of it.  If mytaskit.net winds up with enough traffic it may require
something to subsidise it, but it's my intention to keep it as free as possible
for as long as possible.

# Installation

Taskit requires PHP 5 and Yii (1.1.12 works fine).  You also need an SQL
database system.  We use PostgreSQL for ours.  There's PostgreSQL specific
features in the included sql scripts.

There's no real reason it couldn't work with MySQL or another system, but I
haven't tried it yet.

When you clone the taskit git repo, it will have an htdocs directory inside it
ready for using with your website.  The default configuration assums that yii
will be checked out in the root of the git repository.

I like to create a symlink to the specific yii versioned directory named simply
"yii".  This is the directory that taskit expects to find yii.  If you would
like to change this you can edit htdocs/index.html and
htdocs/protected/yiic.php.

If using PostgreSQL you must have a user created, for example:

	createuser -P -D -E -S -R -U postgres taskit

This command will create a new user named taskit who can use their own
databases, but is not a superuser, cannot create new databases, or other users.

Next, create the database:

	createdb -U postgres -O taskit taskit

This will create the database as postgres, but the database will be owned by
taskit, and the database will be named taskit.

Next, create the tables:

	psql -U taskit taskit -f taskit.sql

Next, configure your taskit.  Open the following file in your favorite editor:

	taskit/htdocs/protected/config/core.php

Feel free to change the name near the top, configure the database section (look
for connectionString) and enter required credentials.  Finally, check out the
'params' section at the bottom and change this to suit your needs.

Finally, if using Apache (recommended for now), then you'll need the following
added to your configuration, modified for your needs:

	<VirtualHost *:80>
		DocumentRoot "/srv/domains/localhost/taskit/htdocs"
		ServerName taskit.localhost

		<Directory "/srv/domains/localhost/taskit/htdocs">
			Options Indexes FollowSymLinks
			AllowOverride all
			Require all granted  # This is apache 2.4 syntax
			<IfModule mod_rewrite.c>
				RewriteEngine on

				RewriteCond %{REQUEST_FILENAME} !-f
				RewriteCond %{REQUEST_FILENAME} !-d
				RewriteRule . index.php
			</IfModule>
		</Directory>
	</VirtualHost>

Unfortunately, Taskit currently must be in the root of a virtual host to
function correctly.  With any luck, this will be fixed soon.  Restart apache and
you're good to go.

If you want to use something other than apache, make sure that something
equivelent to the above rewrite rules works, and the htdocs/protected directory
is not accessible to the public.

Yii has some guidse for getting it running with other web servers on their
website.

## Create Initial Users

Users can be created on the command line using the yiic tool.  To create your
first user enter:

		htdocs/protected/yiic user create --username=<user> --email=<email>

This will let you know if your database configuration has worked, and also
create your first user account.  The password will be generated randomly, and
displayed on the screen.

You should now be able to login to Taskit.
