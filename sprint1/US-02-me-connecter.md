# US-02 — Me connecter

**Priorité :** P1 — Essentielle  
**Statut :** À faire  
**Prérequis :** [US-01 — Créer mon compte](US-01-creer-mon-compte.md) réalisée ; au moins un compte fictif créé pour les essais.  
**Références :** [Backlog](backlog.md), [cahier des charges](../documentation/cahier-des-charges.md) : F02, sections 3 et 8, recette R01.

## User story

- **En tant qu’** enseignant disposant d’un compte,
- **Je souhaite** me connecter avec mon adresse électronique et mon mot de passe,
- **Afin d’** accéder aux offres et aux fonctions réservées aux enseignants connectés.

## Ce qu’il faut réaliser

Créer un formulaire « Se connecter » avec deux champs obligatoires : **adresse électronique** et **mot de passe**. Masquer la saisie du mot de passe et ajouter le bouton « Se connecter ».

Après réussite, afficher l’espace connecté. En attendant la liste des offres de US-05, une page d’accueil affichant l’identité du compte suffit.

Une **session** permet de reconnaître l’enseignant entre plusieurs pages : il n’a pas à ressaisir son mot de passe à chaque fois. Une **page protégée** est une page réservée aux utilisateurs connectés.

## Règles à respecter

- La connexion utilise l’adresse et le mot de passe choisis à l’inscription. Le code commun n’est pas demandé.
- Pour une adresse inconnue ou un mauvais mot de passe, afficher le même message : « Adresse électronique ou mot de passe incorrect ».
- Sans session valide, refuser les pages et actions réservées, même si l’utilisateur les demande directement.
- Chaque session doit reconnaître le bon enseignant.

La création de compte relève de US-01 et la déconnexion de [US-03](US-03-me-deconnecter.md). La récupération de mot de passe et l’option « Se souvenir de moi » ne sont pas demandées. Les droits de modification des offres et participations seront précisés dans leurs US.

## Parcours attendu

1. L’enseignant ouvre « Se connecter » et saisit ses identifiants.
2. Il clique sur « Se connecter ».
3. Si les identifiants sont corrects, l’application affiche l’espace connecté.
4. Sinon, elle affiche un message d’erreur et permet un nouvel essai.

## Critères d’acceptation

| ID | Situation à tester | Résultat attendu |
| --- | --- | --- |
| CA01 | J’ouvre la page de connexion. | Les champs adresse électronique et mot de passe sont présents, avec un libellé et une indication qu’ils sont obligatoires. Le mot de passe est masqué. |
| CA02 | Je saisis l’adresse et le mot de passe d’un compte existant. | Je suis connecté à ce compte et j’accède à l’espace réservé aux enseignants. |
| CA03 | Je saisis une adresse inconnue, puis un mauvais mot de passe pour une adresse existante. | Je reste non connecté dans les deux cas et le même message « Adresse électronique ou mot de passe incorrect » apparaît. |
| CA04 | Je laisse un champ obligatoire vide. | La connexion est refusée et le champ à compléter est indiqué. |
| CA05 | Une fois connecté, je change de page protégée ou j’actualise la page. | L’application me reconnaît tant que ma session reste valide. Je n’ai pas à me reconnecter à chaque page. |
| CA06 | Sans session valide, je saisis directement l’adresse d’une page protégée. | Je dois me connecter ; aucune donnée réservée ne m’est transmise. |
| CA07 | Sans session valide, j’envoie directement une demande d’action réservée. | La demande est refusée et aucune modification n’est enregistrée. |
| CA08 | Marie et Paul se connectent dans deux navigateurs distincts. | Chaque navigateur reconnaît le bon compte : Marie retrouve son identité et Paul la sienne. |

## Exemple de démonstration

Avec des comptes fictifs créés dans US-01 :

1. Se connecter avec `marie@example.test` et son mot de passe : l’accueil protégé doit apparaître.
2. Actualiser la page : Marie doit rester reconnue.
3. Dans un autre navigateur non connecté, ouvrir directement cette page : la connexion doit être demandée.
4. Dans ce second navigateur, essayer un mauvais mot de passe puis une adresse inconnue : le message doit être identique.
5. Se connecter avec le compte de Paul dans ce navigateur : chacun doit retrouver sa propre identité.

L’US est terminée lorsque ses critères et les règles communes du backlog sont vérifiés. Vérifier à nouveau la protection des accès lorsque de nouvelles pages ou actions réservées sont ajoutées.

Les tâches et les essais sont détaillés dans la [fiche technique de US-02](equipe-dev-us-techniques/US-02-me-connecter-technique.md).
