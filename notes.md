Fait :

- Changement des libélés :
	- source → sources
	- nosource → études
	- bibliographie → bibliographies


- représentation des tableaux de départ (home)
	-> s'appyuer sur les catégorie ensuite date

- Partie "admin/publication" case nouveauté
- Partie "home" entête du tableau les nouveautés
	style : couleur distincte + label nouveauté

- file d'ariane dans la navigation

- flèches “chapitre suivant” “Chapitre précédent” (sans texte, juste un infobulle)

- soulignement du chapitre cliqué

- onglet Bibliographie à la recherche

- nettoyage tei (p,l,item)
	example <p>(backr)
				<pb />

- nettoyage tei <p>\s+Bla => <p>Bla

- contenu note dans l'attribute title

- citation : Enregistrer la citation
									Non Oui
							Ajouter une note

- position scroll sur toc sur élément selected

- 16537 gloss en prosse
	- tei:gloss -> prosse
	- tei:l tei:gloss -> vers (position absolute 2 colonnes)

- les citations finir ajout du numéro de la page pour l'ensemble des styles

- fenetre help + wait

- test si plus de 500 occurences bloqué l'affichage + alertes ou navigation avec numéro de page
- boite d'attente après la recherche
- bug sur les notes (indexation en fin de page)

- augmenter la plage de recherche (avant/après) des occurences

- générer un epub de test et de travail

- check recherche dans <l> "[" → numéro de page

- <l> quand retour à la ligne
	ex : dsfdsf df sdf sdfsdf sdfsd
			[qsdqsd qsd

- mise à jour admin importation des sources (bug firefox)

- marcxml distinguer les nouveautés

- ordre des documents dans les résultats d'une recherche
	ex : 9782600009256 search : serres

- problème tri dans les tableaux sur "titre"

- les citations ajouter bouton "ajouter une note"

- counter / rapport 5

- recherche filtre index/biblio/glossaire

- check braket update ereg ^\[

---------------------------------------

- pool ! import TEI par case à cocher pour chaque portail gestion en une source unique !

- utiliser le chargement des tableaux php de start_book.php pour la recherche

- recherche multi-portail

- après la connexion revenir à la page précédente !
	Passer par sesssionStorage la redirection faite par js
	le retour serveur étant une confimration de la connexion avant redirection
	(délai d'attente avec redirection)

- probleme creferencing EncodeTEI.php ligne 70

- ajouter envoie d'emails nouveautés !

- tei/js différencier/rassembler notes lettre/notes chiffre par pages (no !!!)

- liseuse bge avec fichiers dans /var/www/bge/Archives_Tronchin.zip

I 9782600031660
II 9782600000130
III 9782600004275
