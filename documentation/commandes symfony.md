# Installation de Symfony

Crée un nouveau projet Symfony nommé "oflix" en utilisant Composer:

```bash
composer create-project symfony/skeleton oflix ^6
```

Déplace tous les fichiers et répertoires à l'intérieur du répertoire "oflix" vers le répertoire actuel:

```bash
mv oflix/* oflix/.* .
```

Supprime le répertoire "oflix" vide:

```bash
rmdir oflix
```

Installer la gestion d'Apache

```bash
composer require symfony/apache-pack
```

Pour visuliser les routes existantes (éventuellement avec son controlleur)
```bash
bin/console debug:route --show-controllers
```

Installation du moteur de template TWIG, ceci nous ajoute un répertoir `templates` dans lequel on va retrouver tous les templates

```bash
composer require twig
```


Installation de la profile bar

```bash
composer require symfony/profiler-pack
```

Installation du debug-bundle

```bash
composer require debug-bundle
```

Installation du bundle maker

```bash
composer require --dev symfony/maker-bundle
```
