ITA Organiser
=============

Webpage for the organisation of homework etc...

Apache RewriteRule in .htaccess

	RewriteEngine On
	RewriteRule ^(.*)$ /php/main.php?url=$1 [QSA]
	
Nginx Conig File

	server {
		listen 80;

		root /WebPath/ITA_Organiser;
		index index.html index.htm index.php;

		server_name localname.com;

		location / {
			try_files $uri $uri/ /index.html;
			rewrite ^/(.*)$ /php/main.php?url=$1;
		}
	
		location ~ /php/main\.php {
			fastcgi_pass unix:/var/run/php5-fpm.sock;
			fastcgi_index index.php;
			include fastcgi_params;
		}

	}
