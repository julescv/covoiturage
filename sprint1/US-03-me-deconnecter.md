# US-03 — Me déconnecter

**Priorité :** P1 — Essentielle  
**Statut :** À faire  
**Prérequis :** [US-02 — Me connecter](US-02-me-connecter.md) réalisée ; un compte fictif connecté pour les essais.  
**Références :** [Backlog](backlog.md), [cahier des charges](../documentation/cahier-des-charges.md) : F03, section 8, recette R01.

## User story

- **En tant qu’** enseignant connecté,
- **Je souhaite** me déconnecter,
- **Afin de** protéger l’accès à mon compte, notamment sur un ordinateur partagé.

## Ce qu’il faut réaliser

Ajouter un bouton « Se déconnecter » sur les pages de l’espace connecté. Il ferme la session puis affiche la page de connexion.

**Fermer la session** signifie rendre l’ancienne connexion inutilisable. L’enseignant doit saisir de nouveau son adresse et son mot de passe pour accéder aux pages réservées.

## Règles à respecter

- Le compte, les offres et les participations sont conservés.
- Après déconnexion, refuser les nouvelles demandes de pages ou d’actions réservées.
- Les autres onglets qui partagent la session sont concernés dès leur prochaine navigation, actualisation ou action. Une page déjà affichée peut rester visible jusque-là.
- Les sessions indépendantes sur d’autres appareils restent ouvertes.
- Fermer un onglet ne remplace pas l’action « Se déconnecter ».

## Parcours attendu

1. L’enseignant clique sur « Se déconnecter ».
2. L’application ferme la session et affiche la connexion.
3. Tout nouvel accès réservé demande une connexion.
4. L’enseignant peut se reconnecter et retrouver ses données.

## Critères d’acceptation

| ID | Situation à tester | Résultat attendu |
| --- | --- | --- |
| CA01 | Je consulte une page de l’espace connecté. | L’action « Se déconnecter » est accessible, sur ordinateur et téléphone, y compris au clavier. |
| CA02 | Je clique sur « Se déconnecter ». | Ma session devient inutilisable et la page de connexion apparaît. |
| CA03 | Après déconnexion, je saisis directement l’adresse d’une page protégée. | Je dois me connecter et je ne reçois aucune nouvelle donnée réservée. |
| CA04 | Après déconnexion, je renvoie une demande d’action réservée avec mon ancienne session. | La demande est refusée et aucune donnée n’est modifiée. |
| CA05 | J’avais ouvert deux onglets avec la même session. Je me déconnecte dans le premier, puis j’actualise le second ou j’y lance une action. | Le second onglet demande une connexion avant toute nouvelle lecture ou modification. |
| CA06 | Après déconnexion, j’utilise le bouton « Retour » du navigateur. | Mon ancienne session reste inutilisable. Une actualisation ou une action demande une connexion. Les pages réservées sont configurées pour éviter leur conservation en cache (copie gardée par le navigateur). |
| CA07 | Je me reconnecte avec mon adresse et mon mot de passe. | Une nouvelle session fonctionne. Mon compte et mes données ont été conservés. |
| CA08 | Je demande de nouveau la déconnexion alors que je suis déjà déconnecté. | Je reste déconnecté et je peux accéder à la page de connexion, sans erreur bloquante. |

## Exemple de démonstration

1. Se connecter avec un compte fictif et ouvrir l’accueil protégé dans deux onglets du même navigateur.
2. Se déconnecter dans le premier onglet : la page de connexion doit apparaître.
3. Actualiser le second onglet : une connexion doit être demandée.
4. Essayer de revenir à l’accueil protégé avec son adresse ou le bouton « Retour » : aucune session utilisable ne doit être retrouvée.
5. Se reconnecter : le compte et ses données doivent toujours être présents.

L’US est terminée lorsque ses critères et les règles communes du backlog sont vérifiés.

Les tâches et les essais sont détaillés dans la [fiche technique de US-03](equipe-dev-us-techniques/US-03-me-deconnecter-technique.md).
