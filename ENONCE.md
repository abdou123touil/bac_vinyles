# TP de synthèse — Le Bac à Vinyles 🎵

> Un TP pour réutiliser **tout ce qu'on a vu depuis le début** : HTML/CSS, JavaScript, jQuery & AJAX, PHP, formulaires GET et POST, AltoRouter, les données dans des fichiers PHP, l'inscription, la connexion et la session.
> Le projet est organisé **exactement comme SneakStock** : si tu sais refaire SneakStock, tu sais faire ce TP.

---

## Le contexte

Un disquaire, **Le Bac à Vinyles**, veut sa boutique en ligne. Toutes les pages sont déjà intégrées en HTML/CSS, mais elles sont **statiques** : les disques sont écrits « en dur ». Ton travail consiste à rendre le site **vivant** :

- afficher les vinyles depuis le fichier `data/vinyles.php` ;
- filtrer le catalogue ;
- ajouter des disques au panier ;
- créer un compte, se connecter ;
- donner un espace d'administration à l'admin.

| | |
|---|---|
| **Durée conseillée** | 2 à 3 jours |
| **Rendu** | Le dossier du projet zippé : `NOM-Prenom-bac-a-vinyles.zip` |
| **Règle** | Tu ne modifies pas le CSS. Tu peux t'aider de tes cours et de ton projet SneakStock |

---

## 0. Prise en main

### Lancer le projet

Depuis le dossier du projet :

```bash
php -S localhost:8000 index.php
```

Puis ouvre <http://localhost:8000> : la page d'accueil s'affiche. Pour l'instant, **toutes les autres pages font une 404**, c'est normal.

### L'organisation des fichiers (la même que SneakStock)

```
bac-a-vinyles/
├── index.php              ← les routes + une fonction par route
├── AltoRouter.php         ← la librairie de routage (ne pas toucher)
├── data/
│   ├── vinyles.php        ← la "table" des 12 vinyles
│   └── utilisateurs.php   ← la "table" des utilisateurs
└── views/
    ├── navbar.php         ← le menu (inclus dans toutes les pages)
    ├── footer.php         ← le pied de page
    ├── accueil.php
    ├── catalogue.php
    ├── vinyle.php         ← la page d'UN vinyle
    ├── panier.php
    ├── connexion.php
    ├── inscription.php
    ├── admin.php
    └── 404.php
```

Dans `index.php`, il y a déjà la route `/`, les fonctions `accueil()` et `erreur()`, et la fonction `ecrireTable()` que tu connais.

### Un vinyle dans `data/vinyles.php`

```php
[
    'id' => 1,
    'titre' => 'Kind of Blue',
    'artiste' => 'Miles Davis',
    'annee' => 1959,
    'genre' => 'jazz',          // rock, jazz, soul, hip-hop ou electro
    'prix' => 25,
    'stock' => 6,               // 0 = épuisé
    'couleur' => '#2b5f8a',     // la couleur de la pochette
    'description' => "Le disque de jazz le plus vendu de l'histoire...",
],
```

### Les comptes déjà créés

| Rôle | E-mail | Mot de passe |
|---|---|---|
| admin | `admin@bacavinyles.fr` | `Vinyle2026` |
| user | `lea@exemple.fr` | `Client2026` |

> ⚠️ Les mots de passe sont enregistrés **hachés** (ils commencent par `$2y$`), comme ceux créés par ton inscription SneakStock.

### ⚠️ Le piège du `require_once`

`require_once` ne charge un fichier qu'**une seule fois** par page. La 2e fois, il renvoie `true` au lieu du tableau. Donc dans chaque fonction, tu fais **un seul** `require_once(FICHIER_VINYLES)` et tu gardes le résultat dans une variable.

---

## Mission 1 — Les routes et les pages

**Notions :** AltoRouter (`map`), fonctions, `require_once`.

1. Dans `index.php`, ajoute une route `GET` et sa fonction pour chaque page :

| Route | Fonction | Vue affichée |
|---|---|---|
| `/catalogue` | `catalogue()` | `views/catalogue.php` |
| `/vinyle/[i:id]` | `vinyle($id)` | `views/vinyle.php` |
| `/connexion` | `login()` | `views/connexion.php` |
| `/inscription` | `register()` | `views/inscription.php` |
| `/panier` | `panier()` | `views/panier.php` |
| `/admin` | `admin()` | `views/admin.php` |

2. Vérifie que **tous les liens du menu** fonctionnent.

**✅ Résultat attendu**

- Chaque lien du menu ouvre la bonne page (encore statique).
- `/vinyle/1`, `/vinyle/6` et `/vinyle/50` affichent tous la même page (*Abbey Road*). On corrigera ça à la mission 4.
- `/nimportequoi` affiche la page 404.

---

## Mission 2 — L'accueil dynamique

**Notions :** `require_once` d'une table, `count()`, `foreach`, boucle `for`, `if / elseif / else`, `<?= ?>`.

Dans `accueil()` :

1. charge les vinyles : `$vinyles = require_once(FICHIER_VINYLES);`
2. calcule `$totalStock`, la somme des `stock` de tous les vinyles (avec un `foreach`).

Dans `views/accueil.php` :

1. le chiffre « références » = le nombre de vinyles (`count`) ;
2. le chiffre « disques en stock » = `$totalStock` ;
3. « La sélection du moment » affiche les **4 premiers** vinyles du tableau, avec une boucle `for ($i = 0; $i < 4; $i++)`. Supprime les 4 cartes écrites en dur pour n'en garder qu'**une** dans la boucle.

Chaque carte affiche :
- la pochette : remplace la couleur dans `style="--couleur: ..."` ;
- le genre, l'année, le titre, l'artiste, le prix ;
- un lien vers `/vinyle/<id>`.

Le **badge** de stock suit cette règle (les 3 classes CSS existent déjà) :

| Stock | Badge |
|---|---|
| 0 | `<span class="badge badge--epuise">Épuisé</span>` |
| 1 ou 2 | `<span class="badge badge--bientot">Plus que 2 !</span>` (avec le vrai nombre) |
| 3 et plus | `<span class="badge badge--ok">En stock</span>` |

**✅ Résultat attendu**

- Les chiffres : **12** références et **49** disques en stock.
- La sélection : *Kind of Blue*, *Rumours*, *Random Access Memories* (« Plus que 2 ! ») et *What's Going On* (« Épuisé »).
- Dans `data/vinyles.php`, change le stock de *Kind of Blue* à 0 puis recharge : son badge devient « Épuisé » et le total passe à 43. Remets 6 ensuite.

---

## Mission 3 — Le catalogue et les filtres GET

**Notions :** `foreach`, formulaire `method="get"`, `$_GET`, `isset`, conditions, ternaire.

### 3.1 Afficher tous les vinyles

Comme pour l'accueil, mais avec un `foreach` sur **tous** les vinyles. Le texte « X vinyle(s) trouvé(s) » affiche le bon nombre.

### 3.2 Filtrer par genre

Le formulaire du haut de la page envoie `genre` en **GET** (regarde l'URL après avoir cliqué sur « Filtrer » : `/catalogue?genre=jazz`).

Dans `catalogue()` :

1. récupère le genre choisi dans `$genre` (`""` s'il n'existe pas : utilise `isset`) ;
2. crée un tableau vide `$resultats` ;
3. parcours les vinyles. Si aucun genre n'est choisi, **ou** si le genre du vinyle est le genre choisi, ajoute-le : `$resultats[] = $vinyle;`.

La vue affiche alors `$resultats` au lieu de tous les vinyles.

Après le filtrage, le menu déroulant doit **rester sur le genre choisi**. Ajoute `selected` sur la bonne option, comme dans ta navbar SneakStock :

```php
<option value="jazz" <?= $genre == 'jazz' ? 'selected' : '' ?>>Jazz</option>
```

S'il n'y a aucun résultat, affiche : `<p class="vide">Aucun vinyle ne correspond à ta recherche.</p>`.

### 3.3 ⭐ La recherche

Le champ `recherche` doit garder seulement les vinyles dont le **titre ou l'artiste** contient le texte tapé, sans tenir compte des majuscules. Le champ doit garder le texte tapé après l'envoi.

> 💡 `stripos($texte, $morceau)` renvoie la position de `$morceau` dans `$texte`, ou `false` s'il n'y est pas (sans tenir compte des majuscules).
> Exemple : `stripos("Daft Punk", "punk")` renvoie `5`, et `stripos("Nas", "punk")` renvoie `false`.
> Pour chercher dans le titre **et** l'artiste : `stripos($vinyle['titre'] . " " . $vinyle['artiste'], $recherche)`.

**✅ Résultat attendu**

| URL | Résultat |
|---|---|
| `/catalogue` | 12 vinyles |
| `/catalogue?genre=electro` | 3 : Random Access Memories, Discovery, Homework — et « Électro » reste sélectionné |
| `/catalogue?recherche=daft` | 3 (les mêmes) |
| `/catalogue?recherche=DAFT` | 3 aussi |
| `/catalogue?genre=jazz&recherche=miles` | 1 : Kind of Blue |
| `/catalogue?recherche=zzz` | 0 et le message « Aucun vinyle… » |

---

## Mission 4 — La page d'un vinyle

**Notions :** paramètre de route `[i:id]`, `foreach`, `if`, `http_response_code(404)`.

Dans `vinyle($id)` :

1. charge les vinyles ;
2. cherche avec un `foreach` le vinyle dont l'`id` est égal à `$id`, et garde-le dans `$vinyle` (mets `$vinyle = null;` avant la boucle) ;
3. si aucun vinyle n'a été trouvé (`$vinyle == null`) : code 404, `erreur()`, puis `return;` ;
4. sinon, affiche la vue.

> ⚠️ Dans SneakStock, on faisait `$produits[$id]`, qui utilise la **position** dans le tableau (0, 1, 2…) et pas l'**id** du produit. Ici on cherche vraiment l'id : c'est plus sûr, surtout quand on ajoutera des vinyles.

Dans `views/vinyle.php`, rends dynamiques :
- le titre de l'onglet ;
- la pochette ;
- genre, année, titre, artiste, prix ;
- le badge, avec la même règle qu'en mission 2 ;
- la description ;
- le `value` du champ caché `id` ;
- `data-stock` et `data-prix`, qui serviront au JavaScript.

Si le stock vaut 0, le formulaire « Ajouter au panier » est remplacé par :
`<p class="info">Ce disque est épuisé. Repasse bientôt !</p>`

**✅ Résultat attendu**

- `/vinyle/3` : *Random Access Memories*, Daft Punk, 40 €, « Plus que 2 ! ».
- `/vinyle/4` : *What's Going On*, « Épuisé », pas de formulaire.
- `/vinyle/99` : la page 404.
- Les cartes de l'accueil et du catalogue mènent à la bonne fiche.

---

## Mission 5 — JavaScript

**Notions :** `querySelector`, `addEventListener`, `dataset`, variables, conditions, tableaux (`sort`), `textContent`, `preventDefault`.

Le JavaScript s'écrit dans une balise `<script>` à la fin de la vue concernée, comme dans SneakStock.

### 5.1 Trier le catalogue (`views/catalogue.php`)

Le menu `#tri` permet de trier les cartes **sans recharger la page** :
- « Prix croissant » ;
- « Prix décroissant » ;
- « par défaut », qui remet l'ordre d'origine.

Chaque carte doit porter son prix : ajoute `data-prix="<?= $vinyle['prix'] ?>"` sur la balise `<a class="carte">`.

> 💡 C'est le même principe que le tri de la page catalogue de SneakStock : `Array.from(...)`, `sort(...)` avec `Number(a.dataset.prix)`, puis `grille.append(carte)` pour remettre chaque carte dans l'ordre.

### 5.2 Le compteur de quantité (`views/vinyle.php`)

Comme le compteur vu en cours :
- `+` augmente la quantité, **sans dépasser le stock** (`data-stock`) ;
- `−` la diminue, **sans descendre sous 1** ;
- `#compteur` affiche la quantité ;
- le champ caché `#quantite` reçoit la même valeur (c'est lui qui part au serveur) ;
- `#total` affiche `prix × quantité` suivi de « € ».

> ⚠️ Si le disque est épuisé, le formulaire n'existe pas : vérifie que `.quantite` existe avant d'ajouter les écouteurs, sinon tu auras une erreur dans la console.

### 5.3 Vérifier l'inscription (`views/inscription.php`)

Au `submit` du formulaire `#form-inscription`, si le mot de passe et la confirmation sont différents :
- on bloque l'envoi (`event.preventDefault()`) ;
- on affiche « Les deux mots de passe sont différents. » dans `#erreur-js` (il est caché au départ avec `display: none`).

**✅ Résultat attendu**

- Catalogue, prix croissant : le premier est *A Love Supreme* (23 €). Prix décroissant : *Random Access Memories* (40 €). Par défaut : *Kind of Blue* revient en premier.
- `/vinyle/6` (*Abbey Road*, 32 €, stock 3) : 5 clics sur `+` → **3**, total **96 €**. 5 clics sur `−` → **1**.
- Inscription avec deux mots de passe différents : le message rouge apparaît et la page ne se recharge pas.
- **Aucune erreur** dans la console (F12), sur aucune page.

---

## Mission 6 — jQuery et AJAX

**Notions :** `$(...)`, `.on('click')`, effets (`slideToggle`, `fadeIn`), `.text()`, `.attr()`, `.css()`, `$.getJSON`, `.done()` / `.fail()`.

Ajoute jQuery dans les pages qui en ont besoin, avant ton script :

```html
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
```

### 6.1 La description (`views/vinyle.php`)

Le paragraphe `#description` est caché. Un clic sur le bouton `#btn-description` l'affiche ou le cache avec `slideToggle`.

### 6.2 Un vinyle au hasard (`views/accueil.php`)

Pour que jQuery puisse récupérer les vinyles, le serveur doit les envoyer en **JSON**. Ajoute cette route et cette fonction **(code fourni)** :

```php
$router->map('GET', '/api/vinyles', 'api_vinyles');
```

```php
function api_vinyles() {
    $vinyles = require_once(FICHIER_VINYLES);
    header('Content-Type: application/json');
    echo json_encode($vinyles);   // transforme le tableau PHP en JSON
}
```

Ouvre <http://localhost:8000/api/vinyles> : tu vois tes 12 vinyles au format JSON, comme le fichier `sneakers.json` du cours jQuery.

Ensuite, au clic sur `#btn-hasard` :

1. `$.getJSON('/api/vinyles')` ;
2. dans `.done(...)`, choisis un vinyle au hasard dans le tableau reçu (`Math.random()`) ;
3. remplis :
   - `#hasard-titre` avec le titre ;
   - `#hasard-artiste` avec « Artiste · Année » ;
   - `#hasard-prix` avec le prix suivi de « € » ;
   - la couleur de `#hasard-pochette` (`.css('background', ...)`) ;
   - le lien `#hasard-lien` vers `/vinyle/<id>` (`.attr('href', ...)`) ;
4. fais apparaître `#resultat-hasard` en fondu (`.hide().fadeIn(400)`) ;
5. le bouton affiche « Un autre ! » ;
6. dans `.fail(...)`, affiche « Impossible de charger les vinyles. » dans `#erreur-hasard`.

**✅ Résultat attendu** — chaque clic affiche un disque différent (ou pas, c'est le hasard !). Le lien mène à la bonne fiche. Si tu arrêtes le serveur et que tu cliques, le message d'erreur apparaît.

---

## Mission 7 — L'inscription

**Notions :** formulaire `POST`, `$_POST`, conditions, `strlen`, `foreach`, `password_hash`, `ecrireTable`, `header('Location: ...')`.

1. Ajoute la route `POST /register` → `register_user()`. Le formulaire de `views/inscription.php` envoie `name`, `email` et `password`.
2. Dans `register_user()`, vérifie **dans cet ordre**. À la première erreur, redirige vers l'inscription avec un paramètre GET, puis `return;` :

| Vérification | Redirection |
|---|---|
| Un des 3 champs est vide | `/inscription?erreur=champs` |
| Le mot de passe fait moins de 8 caractères (`strlen`) | `/inscription?erreur=mdp` |
| L'e-mail est déjà dans `utilisateurs.php` (`foreach`) | `/inscription?erreur=email` |

3. Si tout est bon : ajoute l'utilisateur avec `id`, `nom`, `email`, `mot_de_passe` **haché** (`password_hash`), `role` = `'user'` et `date_inscription`. Enregistre avec `ecrireTable(FICHIER_UTILISATEURS, $users)`, puis redirige vers `/connexion?inscrit=1`.
4. Dans `views/inscription.php`, si `$_GET['erreur']` existe, affiche le bon message :

| `erreur` | Message (`<p class="flash flash--erreur">…</p>`) |
|---|---|
| `champs` | Tous les champs sont obligatoires. |
| `mdp` | Le mot de passe doit faire au moins 8 caractères. |
| `email` | Cette adresse e-mail est déjà utilisée. |

5. Dans `views/connexion.php`, si `$_GET['inscrit']` existe : `<p class="flash flash--succes">Ton compte est créé, tu peux te connecter.</p>`

> 💡 Pour tester une erreur sans que le navigateur bloque l'envoi, retire temporairement `required` ou `type="email"` avec F12.

**✅ Résultat attendu**

- Inscription avec `lea@exemple.fr` → « Cette adresse e-mail est déjà utilisée. ».
- Mot de passe `court` → le message sur les 8 caractères.
- Inscription valide → page de connexion avec le message vert, et un 3e utilisateur dans `data/utilisateurs.php` dont le mot de passe commence par `$2y$`.

---

## Mission 8 — Connexion, session, déconnexion

**Notions :** `POST`, `foreach`, `password_verify`, `$_SESSION`, `isset`, `session_unset`, redirections.

1. Route `POST /auth` → `authentification()`. Le formulaire envoie `email` et `password`.
2. Parcours les utilisateurs. Si l'e-mail correspond **et** que le mot de passe est bon :
   - enregistre dans la session : `is_authenticated` (`true`), `user_email`, `user_nom` et `user_role` ;
   - redirige vers `/admin` si le rôle est `admin`, sinon vers `/catalogue` ;
   - `return;`
3. Si aucun utilisateur ne correspond : redirige vers `/connexion?erreur=1`. La vue affiche alors « E-mail ou mot de passe incorrect. ».

> ⚠️ **Le mot de passe enregistré est haché** : `$_POST['password'] == $user['mot_de_passe']` ne marchera jamais.
> 💡 `password_verify($motDePasseTapé, $hash)` renvoie `true` si le mot de passe tapé correspond au hash, `false` sinon. C'est la fonction « partenaire » de `password_hash`.

4. Route `GET /deconnexion` → `deconnexion()` : vide la session (`session_unset()`) et redirige vers l'accueil.
5. Le menu (`views/navbar.php`) s'adapte, comme dans SneakStock :
   - **visiteur** : « Connexion » et « Créer un compte » ;
   - **connecté** : `<span class="bonjour">Bonjour Prénom Nom</span>` et un lien « Déconnexion » ;
   - **admin** : en plus, un lien « Admin » vers `/admin`.

**✅ Résultat attendu**

| Action | Résultat |
|---|---|
| `lea@exemple.fr` / `faux` | retour sur la connexion + message rouge |
| `lea@exemple.fr` / `Client2026` | arrivée sur le catalogue, menu « Bonjour Léa Martin » + « Déconnexion », **pas** de lien Admin |
| Le compte que tu as créé à la mission 7 | ça marche aussi |
| `admin@bacavinyles.fr` / `Vinyle2026` | arrivée sur `/admin`, lien « Admin » dans le menu |
| Clic sur « Déconnexion » | retour à l'accueil, le menu affiche « Connexion » |

---

## Mission 9 — Le panier

**Notions :** formulaire `POST`, `$_SESSION` (un tableau dans la session), boucles imbriquées, calculs, `unset`.

Le panier est un tableau rangé dans `$_SESSION['panier']`. Chaque ligne est un petit tableau : `['id' => 6, 'quantite' => 2]`.

1. Route `POST /panier/ajouter` → `panier_ajouter()`. Elle reçoit `id` et `quantite` (le formulaire de la page vinyle). Elle ajoute une ligne au panier puis redirige vers `/panier` :

```php
$_SESSION['panier'][] = ['id' => $_POST['id'], 'quantite' => $_POST['quantite']];
```

2. Route `GET /panier` → `panier()`. Pour chaque ligne du panier, retrouve le vinyle correspondant (une boucle **dans** une boucle). Prépare un tableau `$lignes` (titre, artiste, prix, quantité, sous-total) et calcule `$total`. Si le panier n'existe pas encore (`isset`), on part d'un tableau vide.
3. Dans `views/panier.php` :
   - un `<tr>` par ligne ;
   - si le total est **≥ 60 €**, la livraison est « Offerte » ;
   - sinon, la livraison coûte **5 €**, le total l'inclut et on affiche « Plus que X € pour la livraison offerte. » ;
   - panier vide : « Ton panier est vide. ».
4. Route `POST /panier/vider` → `panier_vider()` : `unset($_SESSION['panier'])` puis redirection vers `/panier`.
5. Dans `views/navbar.php`, la pastille du panier affiche le nombre de lignes du panier (0 s'il n'existe pas).

**✅ Résultat attendu**

| Action | Résultat |
|---|---|
| 2 × *Kind of Blue* puis 1 × *Abbey Road* | lignes à 50 € et 32 €, total **82 €**, livraison **Offerte**, pastille **2** |
| Vider, puis 1 × *Back to Black* | 26 € + 5 € = **31 €**, « Plus que 34 € pour la livraison offerte. » |
| « Vider le panier » | « Ton panier est vide. », pastille 0 |

---

## Mission 10 — L'administration

**Notions :** session et rôles, `!`, `||`, `foreach`, formulaire `POST`, `ecrireTable`.

### 10.1 Protéger la page

Au début de `admin()` : si l'utilisateur **n'est pas connecté**, **ou** si son rôle n'est pas `admin`, redirige vers `/connexion` puis `return;`.

```php
if (!isset($_SESSION['is_authenticated']) || $_SESSION['user_role'] != 'admin') {
```

### 10.2 Les tableaux

`views/admin.php` affiche :
- « Vinyles (N) » avec une ligne par vinyle. La ligne d'un vinyle épuisé a la classe `ligne-epuisee` ;
- « Utilisateurs (N) » avec une ligne par utilisateur (badge `badge--bientot` pour un admin, `badge--ok` sinon).

### 10.3 Ajouter un vinyle

Route `POST /admin/ajouter` → `admin_ajouter()`, **protégée de la même façon** :
- si le titre ou l'artiste est vide, ou si le prix n'est pas supérieur à 0 → `/admin?erreur=1` ;
- sinon, ajoute le vinyle avec un nouvel `id` (`count($vinyles) + 1`), enregistre avec `ecrireTable(FICHIER_VINYLES, $vinyles)` et redirige vers `/admin?ajout=1`.

La vue affiche le bon message selon `$_GET['ajout']` ou `$_GET['erreur']` :

| Paramètre | Message |
|---|---|
| `ajout` | `<p class="flash flash--succes">Le vinyle a bien été ajouté au catalogue.</p>` |
| `erreur` | `<p class="flash flash--erreur">Le titre et l'artiste sont obligatoires, et le prix doit être supérieur à 0.</p>` |

**✅ Résultat attendu**

- Non connecté, ou connecté en tant que Léa : `/admin` renvoie vers la connexion.
- En admin : « Vinyles (12) » avec 2 lignes rosées, et « Utilisateurs (2) » (ou plus).
- Ajout de *Blue Train* (John Coltrane, 1957, jazz, 21 €, stock 3) :
  - message vert et « Vinyles (13) » ;
  - `/vinyle/13` affiche *Blue Train* ;
  - `/catalogue?genre=jazz` affiche 3 vinyles.
- Le fichier `data/vinyles.php` contient le nouveau disque.

---

## Bonus

- **Retirer une ligne du panier** : un bouton « Retirer » sur chaque ligne (`POST /panier/retirer/[i:index]`, avec `unset($_SESSION['panier'][$index])`).
- **Pas plus que le stock** : dans `panier_ajouter()`, refuse l'ajout si la quantité demandée dépasse le stock du vinyle.
- **Supprimer un vinyle** depuis l'admin (`POST /admin/supprimer/[i:id]`) : reconstruis le tableau sans ce vinyle, puis `ecrireTable`.

---

## Barème (sur 20)

| Mission | Points |
|---|---|
| 1. Routes et pages | 1,5 |
| 2. Accueil dynamique | 2 |
| 3. Catalogue et filtres GET | 2,5 |
| 4. Page d'un vinyle | 2 |
| 5. JavaScript (tri, compteur, vérification) | 2,5 |
| 6. jQuery et AJAX | 2 |
| 7. Inscription | 2 |
| 8. Connexion, session, déconnexion | 2 |
| 9. Panier | 2 |
| 10. Administration | 1,5 |
| Bonus | jusqu'à +2 |

**Avant de rendre :**
- [ ] aucune erreur PHP (*Warning*, *Fatal error*) sur aucune page ;
- [ ] aucune erreur rouge dans la console du navigateur ;
- [ ] `data/vinyles.php` et `data/utilisateurs.php` sont remis propres (supprime tes comptes et tes vinyles de test).

Bon courage, et bonne écoute 🎧
