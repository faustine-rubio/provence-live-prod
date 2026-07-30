# Thème enfant Provence Live Prod

Ce dossier est le thème enfant d'Astra. Les mises à jour d'Astra ne supprimeront pas les personnalisations placées ici.

## Mise en service

1. Dans **Apparence > Thèmes**, activez « Provence Live Prod ».
2. Dans **Apparence > Personnaliser > Constructeur d'en-tête**, modifiez le bouton « Réservez maintenant » : son libellé conseillé est « Nous contacter » et son lien doit pointer vers la page Contact.
3. La page « Demande de catalogue » est créée automatiquement dans l'administration WordPress. Elle contient le formulaire de demande de catalogue.

4. Dans **Apparence > Menus**, ajoutez cette page au menu principal.
5. Dans le constructeur de pied de page Astra, remplacez le logo AVDC par le logo PLP et renseignez les vrais liens sociaux, l'e-mail et le téléphone. Aucun faux lien n'est ajouté par le thème.

## Mise en forme des blocs Spectra

Dans chaque bloc concerné : **Avancé > Classe(s) CSS supplémentaire(s)**, ajoutez la classe correspondante :

- `plp-hero` : image principale / hero ;
- `plp-services` : section Nos services ;
- `plp-trust` : section Ils nous ont fait confiance (à placer en bas de l'accueil) ;
- `plp-strengths` : section Nos points forts ;
- `plp-contact` : section Contact ;
- `plp-about-stats` : conteneur des chiffres-clés de la page À propos.

Les classes ne changent pas le contenu : elles appliquent une présentation cohérente, aérée et adaptée au mobile.

## Formulaire Catalogue

Le formulaire demande le prénom, le nom et l'e-mail. Une demande est envoyée à l'adresse définie dans **Réglages > Général > Adresse e-mail d'administration**. L'équipe PLP envoie ensuite le lien du catalogue manuellement.

Avant publication, ajoutez une mention de cette finalité dans votre politique de confidentialité et vérifiez la réception du premier e-mail de test.
