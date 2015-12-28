ꓜꓳꓤꓷ

# ZORD

## génération de la documentation

cd ~/doc/
yuidoc -c zord_yuidoc.json --themedir ./theme_zord_yuidoc

cd ~/doc/
php apigen.phar generate

#### definition

	- PORTAL
	- TEICSSNAMEFILE
	- LOCALE

#### specific file

	− zord
		− admin
			− js
				TEI_PORTAL.js
				TEITOHTML_PORTAL.js
		− appli
			− public
				− css
					− PORTAL
						frieze.css
						main.css
						main_small.css
						marker.css
						print.css
						search.css
						table.css
						TEICSSNAMEFILE.css
						TEICSSNAMEFILE_print.css
				− js
					− PORTAL
						admin.js
						book.js
						main.js
						search.js
						start.js
		− lib
			− locale
				− LOCALE
					− PORTAL
						admin.json
						adminconnect.json
						book.json
						categories.json
						connexion.json
						dialog.json
						footer.json
						header.json
						marker.json
						navigation.json
						notices.json
						search.json
						start_books.json
						subscription.json
			− modules
				− admin
					Admin_TEI_PORTAL.php
			− profiles
				− PORTAL
					− epub
						cover.jpg
						epub.css
			− view
				− zord
					− PORTAL
						admin.php
						adminconnect.php
						book.php
						citations.php
						connexion.php
						dialog.php
						footer.php
						header.php
						marker.php
						navigation.php
						notices.php
						search.php
						start.php
						start_books.php
						subscription.php
						subscription_portal.php
						− pages
							− LOCALE
								xxx.php
			− zord
				− PORTAL
					PORTAL.php
					categories.json
					csl_style.php

#### var in file

	− zord
		− config
			config_portals.php

## Dépendances
- base de données : MySQL Version 5.5 min encodage UTF-8
- langage côté serveur : PHP Version 5.3.10 min
- langage côté client : ECMAScript Version 5 (javaScript)
- Pages web : HTML5 + CSS3
- Moteur de recherche : Solr Version 4 min
- Analyse RNG : Jing Version 20081028 min
- Analyse ePub : EpubCheck V3.0.1 min
- Débugage : extension Chrome https://craig.is/writing/chrome-logger

- PHP extensions :
	- Core
	- ctype
	- date
	- dom
	- filter
	- hash
	- json
	- libxml
	- mbstring
	- pcre
	- PDO
	- pdo_mysql
	- Phar
	- session
	- SimpleXML
	- solr
	- SPL
	- standard
	- tokenizer
	- xsl
	- xml
	- zip

## Indexation "cron"

	*/5 * * * * php -f /directory/zord/services/services.php indexation >> /directory/zord/log/indexation_$(/bin/date +\%Y\%m\%d).log 2>&1
