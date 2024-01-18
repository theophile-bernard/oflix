### oflix

**Répertoires**

* bin : contient l'outil console qui permet d'effectuer les tâches de routine pour créer ou gérer un projet.
* config : contient les fichiers de configuration.
* public : contient le fichier index de l'application.
* src : contient les controleurs, le Kernel mais aussi les entités etc.
* var : contient les cache et les logs.
* vendor : contient les classes des Bundles installés comme http-foundation.

**Fichier**

* .env : fichier de configuration de Symfony (ex : BDD)
* composer.json : fichier de configuration de Composer.
* composer.lock : fichier de lock de Composer.
* symfony.lock : fichier de lock de Symfony.

**Sous-répertoires**

* bin :
    * console : outil console.
* config :
    * bundles.php : fichier de configuration des bundles.
    * packages : contient les fichiers de configuration des packages
    * routes : contient les fichiers de routes.
    * routes.yaml : définition des routes en yaml
* public :
    * index.php : fichier d'entrée de l'application.
* src :
    * Controller : contient les contrôleurs de l'application.
    * Kernel.php : classe Kernel de Symfony.
* vendor :
    * autoload.php : fichier d'autoload de Composer.
    * bin : contient les binaires des bundles installés.
    * composer : contient les fichiers de Composer.
    * psr : contient les fichiers de la norme PSR.
    * symfony : contient les classes de Symfony.
