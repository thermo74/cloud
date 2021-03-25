# cloud
Créer une fichier .env avec vos valeurs, si vous ne changer pas le docker-compose vous pouvez garder le même lien de database.

Pour lancer le projet faites les commandes suivantes :  
symfony composer install  
yarn i  
docker-compose up  
symfony serve  
symfony console doctrine:migration:migrate  
symfony console doctrine:fixtures:load  

Regarder dans les fixtures pour avoir les identifiants par défaut.
