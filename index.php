<?php
// Ci dessous, je déclare la session ouverte
session_start();

// Les chemins vers mes "tables"
const FICHIER_VINYLES = __DIR__ . "/data/vinyles.php";
const FICHIER_UTILISATEURS = __DIR__ . "/data/utilisateurs.php";

// J'importe le fichier AltoRouter.php
require __DIR__ . "/AltoRouter.php";

$router = new AltoRouter();

$router->map('GET', '/', 'accueil');

// TODO : toutes les autres routes (voir l'énoncé)

$match = $router->match();
if ($match !== false && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    http_response_code(404);
    erreur();
}

// =====================================================================
//  PAGES
// =====================================================================

function accueil() {
    require_once(__DIR__ . "/views/accueil.php");
}

function erreur() {
    require_once(__DIR__ . "/views/404.php");
}

// TODO : les autres fonctions (une par route)

// =====================================================================
//  OUTILS (fourni)
// =====================================================================

// Réécrit entièrement un fichier de data/ avec le tableau $lignes
// Exemple : ecrireTable(FICHIER_UTILISATEURS, $users);
function ecrireTable(string $fichier, array $lignes): void
{
    $contenu = "<?php\n\nreturn " . var_export(array_values($lignes), true) . ";\n";
    file_put_contents($fichier, $contenu, LOCK_EX);

    // PHP garde les fichiers en cache (OPcache) : on lui dit que celui-ci a changé,
    // sinon le prochain require_once pourrait relire l'ANCIENNE version pendant 2 secondes.
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($fichier, true);
    }
}
