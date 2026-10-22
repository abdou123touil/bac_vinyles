<?php

// =====================================================================
//  TABLE « utilisateurs »
//  Comptes de départ :
//    - admin@bacavinyles.fr / Vinyle2026  (role admin)
//    - lea@exemple.fr       / Client2026  (role user)
// =====================================================================

return [
    [
        'id' => 1,
        'nom' => 'Admin Bac à Vinyles',
        'email' => 'admin@bacavinyles.fr',
        'mot_de_passe' => '$2y$10$sRp8Wjhe7UmCaXER90Jq9O6FdBWM4.wkeJqUP330ikvQmoUxIpRMa',
        'role' => 'admin',
        'date_inscription' => '2026-09-01 09:00:00',
    ],
    [
        'id' => 2,
        'nom' => 'Léa Martin',
        'email' => 'lea@exemple.fr',
        'mot_de_passe' => '$2y$10$OplQR0LAbfr2FdLvfg.yvu8XRszeT0MGIhT3apW966zI4W5MOl7cO',
        'role' => 'user',
        'date_inscription' => '2026-09-15 18:30:00',
    ],
];
