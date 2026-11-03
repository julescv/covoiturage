# US-01 — Créer mon compte

**Priorité :** P1 — Essentielle  
**Statut :** Terminée  
**Prérequis :** [US-0-BIS — Créer la table des paramètres](US-0-BIS-configurer-le-code-inscription.md) réalisée ; code commun renseigné manuellement dans la base et communiqué aux enseignants.  
**Références :** [Backlog](backlog.md), [cahier des charges](../documentation/cahier-des-charges.md) : F01, sections 4.1 et 8, recette R01.

## User story

- **En tant qu’** enseignant du lycée,
- **Je souhaite** créer mon compte avec mes informations, dont mon numéro de téléphone, et le code fourni par le lycée,
- **Afin de** pouvoir me connecter à l’application de covoiturage.

## Ce qu’il faut réaliser

Depuis la page publique de démarrage « Bienvenue », l’enseignant accède au formulaire en cliquant sur le lien « Créer un compte » dans la navigation. Ce lien est accessible sans être connecté.

Créer un formulaire « Créer un compte » avec six champs obligatoires :

| Champ | Saisie attendue |
| --- | --- |
| Nom | Le nom de famille. |
| Prénom | Le prénom. |
| Adresse électronique | Une adresse valide, non utilisée par un autre compte. Elle servira à se connecter. |
| Numéro de téléphone | Le numéro de téléphone de l’enseignant. Le format attendu est indiqué près du champ. |
| Mot de passe | Un mot de passe personnel, masqué pendant la saisie. |
| Code d’inscription | Le code commun communiqué par le lycée. |

Ajouter le bouton « Créer mon compte ». Après réussite, afficher une confirmation et proposer la connexion. En cas d’erreur, indiquer ce qu’il faut corriger.

Le **code d’inscription est commun** aux enseignants ; le **mot de passe est personnel** et sert à se connecter.

## Règles à respecter

- Le code peut servir à créer plusieurs comptes, sans modification ni expiration.
- Une adresse électronique correspond à un seul compte.
- Le numéro de téléphone est obligatoire et enregistré avec le compte. Son format est vérifié ; un même numéro peut être utilisé par plusieurs comptes.
- Le compte est utilisable dès sa création, sans validation supplémentaire.
- L’enseignant doit ensuite se connecter avec son adresse et son mot de passe.
- Si le lycée change le code, seul le nouveau permet les inscriptions suivantes. Les comptes existants restent utilisables.

Le lycée transmet le code en dehors de l’application. La connexion relève de [US-02](US-02-me-connecter.md). Aucun écran d’administration, courriel ou traitement de mot de passe oublié n’est demandé.

## Parcours attendu

1. L’enseignant ouvre la page publique de démarrage « Bienvenue », sans être connecté.
2. Il clique sur le lien « Créer un compte » dans la navigation ; le formulaire avec les six champs s’affiche.
3. Il remplit les six champs et clique sur « Créer mon compte ».
4. L’application vérifie les saisies et le code.
5. Si tout est correct, elle crée le compte et propose de se connecter.
6. Sinon, elle explique l’erreur sans créer de compte.

## Critères d’acceptation

| ID | Situation à tester | Résultat attendu |
| --- | --- | --- |
| CA01 | Sans être connecté, j’ouvre la page publique de démarrage « Bienvenue » et clique sur « Créer un compte » dans la navigation. | Le formulaire d’inscription s’affiche. Les six champs, dont le numéro de téléphone, sont présents, avec un libellé et une indication qu’ils sont obligatoires. Le format de téléphone attendu est indiqué. Le mot de passe est masqué. Le code attendu n’est jamais affiché. |
| CA02 | Je renseigne des informations valides, dont mon numéro de téléphone, une adresse non utilisée et le bon code. | Mon compte est créé avec mon numéro de téléphone. Un message confirme la réussite et un lien me permet de me connecter. Je ne suis pas connecté automatiquement. |
| CA03 | Je laisse un champ vide, ou je saisis uniquement des espaces dans le nom, le prénom ou le numéro de téléphone. | Aucun compte n’est créé. Le champ à compléter est indiqué. |
| CA04 | Je saisis une adresse incorrecte, par exemple `marie.exemple`. | Aucun compte n’est créé. Un message demande une adresse électronique valide. |
| CA05 | Je saisis l’adresse d’un compte existant. | La création est refusée avec un message explicite. Même si deux demandes arrivent au même moment avec la même adresse, un seul compte peut être créé. |
| CA06 | Une erreur de saisie est détectée. | Le nom, le prénom, l’adresse et le numéro de téléphone restent renseignés pour être corrigés. Le mot de passe et le code doivent être ressaisis ; ils ne figurent pas dans le message d’erreur. |
| CA07 | Je ne saisis pas de code ou je saisis un mauvais code. | Aucun compte n’est créé. Le message indique « Code d’inscription obligatoire » ou « Code d’inscription incorrect », sans donner le bon code. |
| CA08 | Deux enseignants s’inscrivent avec le même code et deux adresses différentes. | Les deux comptes sont créés, même si les demandes arrivent au même moment. Le code reste utilisable. |
| CA09 | J’utilise un code enregistré depuis plusieurs mois. | L’inscription est acceptée si les autres informations sont valides : le code n’expire pas. |
| CA10 | Le code n’est pas correctement configuré ou l’application ne peut pas le lire. | Aucun compte n’est créé. Le message indique « Le service d’inscription est indisponible. Réessayez plus tard. » |
| CA11 | Un problème survient pendant l’enregistrement du compte. | L’application indique l’échec, sans annoncer de réussite ni conserver un compte incomplet. Le code reste utilisable pour un nouvel essai. |
| CA12 | Le code commun a été remplacé dans la base. | Le nouveau code permet de s’inscrire ; l’ancien est refusé. Les comptes déjà créés restent utilisables. |
| CA13 | Je me connecte avec le compte que je viens de créer, lorsque US-02 est disponible. | La connexion fonctionne avec mon adresse et mon mot de passe, sans demander le code d’inscription ni une autre validation. |
| CA14 | Je saisis un numéro de téléphone ne respectant pas le format indiqué, par exemple `abcdef`. | Aucun compte n’est créé. Un message demande un numéro de téléphone valide et rappelle le format attendu. |

## Exemple de démonstration

Avec des données fictives et un code de démonstration :

1. Créer le compte de Marie avec `marie@example.test`, le numéro fictif `06 00 00 00 01` et le bon code.
2. Créer le compte de Paul avec `paul@example.test`, le numéro fictif `06 00 00 00 02` et le même code : les deux inscriptions doivent réussir.
3. Essayer de créer un autre compte avec `marie@example.test` : l’inscription doit être refusée.
4. Essayer avec un mauvais code : aucun compte supplémentaire ne doit être créé.
5. Essayer avec un téléphone vide, puis `abcdef` : les deux inscriptions doivent être refusées et le champ à corriger indiqué.

L’US est terminée lorsque ses critères et les règles communes du backlog sont vérifiés. Le test de connexion CA13 est effectué dès que US-02 est disponible.

Les tâches et les essais sont détaillés dans la [fiche technique de US-01](equipe-dev-us-techniques/US-01-creer-mon-compte-technique.md).
