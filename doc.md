ꓜꓳꓤꓷ

# ZORD

## génération de la documentation

cd ~/doc/
yuidoc -c zord_yuidoc.json --themedir ./theme_zord_yuidoc

cd ~/doc/
php apigen.phar generate

## ePub

Le nom du fichier de couverture doit être : frontcover.jpg

Et il doit avoir comme adresse :

	../appli/medias/ID_DU_FICHIER/frontcover.jpg


## Création d'un nouveau portail

/usr/bin/php -f /zord_adress/services/services.php newportal portal_name;

#### definition

	- PORTAL
	- TEICSSNAMEFILE
	- LOCALE

#### specific file

	− zord
		− admin
			− js
				TEI_PORTAL.js
		− appli
			− public
				− css
					− PORTAL
						frieze.css
						main.css
						main_small.css
						marker.css
						TEICSSNAMEFILE.css
						TEICSSNAMEFILE_print.css
						print.css
						search.css
						table.css
				− js
					− PORTAL
						book.js
						main.js
						search.js
						start.js
		− lib
			− locale
				− LOCALE
					− PORTAL
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
			− modules
				− admin
					Admin_TEI_PORTAL.php
			− profiles
				− PORTAL
					− epub
						cover.jpg
						def.php
						epub.css
						to.xsl
			− view
				− zord
					− PORTAL
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
		− lib
			− zord
				websites.php


## Infos droz

http://wikinum.droz.org/
http://wikiportails.droz.org/doku.php?id=archicol
http://wikiportails.droz.org/doku.php?id=cdcf


## git

Supprimer dossier lib/switcher


## Problèmes

- champ pour la langue : fre, lat ?? (FR-fr ou fr mais pas fre ; quelle norme ?)


## Dépendances
- base de données : MySQL Version 5.5 min encodage UTF-8
- langage côté serveur : PHP Version 5.3.10 min
- langage côté client : ECMAScript Version 5 (javaScript)
- Pages web : HTML5 + CSS3
- Moteur de recherche : Solr Version 4 min
- Création des ePub : TEI XSL Stylesheets (teitoepub) Version 7.28 min
	- Automatisation : Ant Apache Version 1.9.3 min
- Analyse RNG : Jing Version 20081028 min
- Analyse ePub : EpubCheck V3.0.1 min

- libPHP ?

## Docs

http://www.loc.gov/marc/marc-functional-analysis/tool.html

https://www.digitalocean.com/community/tutorials/how-to-install-solr-on-ubuntu-14-04
sudo service tomcat6 restart


http://blog.hemantthorat.com/php-solr-integration/#sthash.zZniYj5Z.dpbs

http://www.kingstonlabs.com/blog/how-to-install-solr-36-on-ubuntu-1204/

https://github.com/kcl-ddh/kiln/blob/master/webapps/ROOT/kiln/stylesheets/solr/tei-eats-to-solr.xsl


https://github.com/csupnig/Solr-PHP-Example/blob/master/index.php

## Indexation "cron"

	*/5 * * * * php -f /directory/zord/services/services.php indexation >> /directory/zord/log/indexation_$(/bin/date +\%Y\%m\%d).log 2>&1
