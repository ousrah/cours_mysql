<?php
// Configuration générale du cours
define('COURSE_TITLE', 'Maîtrise du SQL Procédural avec MySQL');
define('COURSE_AUTHOR', 'P. Rahmouni Oussama');
define('COURSE_LAST_UPDATE', 'Octobre 2025');

// Structure du cours pour générer le sommaire dynamiquement
// Les 'id' correspondent aux ID des balises <section> dans le HTML.
$course_parts = [
    "Partie 1 : Fondamentaux du SQL Procédural" => [
        ['id' => 'accueil', 'title' => "Chapitre 1 : Accueil & Objectifs"],
        ['id' => 'fonctions', 'title' => "Chapitre 2 : Blocs d'Instructions et Fonctions"],
        ['id' => 'controle', 'title' => "Chapitre 3 : Structures de Contrôle"],
        ['id' => 'exercices-partie1', 'title' => "Ateliers Pratiques (Partie 1)"]
    ],
    "Partie 2 : La Puissance des Procédures Stockées" => [
        ['id' => 'procedures-stockees', 'title' => "Chapitre 4 : Introduction aux Procédures Stockées"],
        ['id' => 'parametres', 'title' => "Chapitre 5 : Paramètres (IN, OUT)"],
        ['id' => 'logique-procedures', 'title' => "Chapitre 6 : Logique Applicative et Manipulation"],
        ['id' => 'exercices-partie2', 'title' => "Ateliers Pratiques (Partie 2)"]
    ],
    "Partie 3 : L'Automatisation avec les Déclencheurs" => [
        ['id' => 'triggers-intro', 'title' => "Chapitre 7 : Introduction aux Déclencheurs (Triggers)"],
        ['id' => 'triggers-cas-pratique', 'title' => "Chapitre 8 : Triggers en Action (AFTER)"],
        ['id' => 'triggers-before', 'title' => "Chapitre 9 : Validation avec les Triggers (BEFORE)"],
        ['id' => 'exercices-partie3', 'title' => "Ateliers Pratiques (Partie 3)"]
    ],
    "Partie 4 : Fiabilité et Robustesse" => [
        ['id' => 'transactions', 'title' => "Chapitre 10 : Gestion des Transactions"],
        ['id' => 'exceptions', 'title' => "Chapitre 11 : Gestion des Exceptions"],
        ['id' => 'exercices-partie4', 'title' => "Ateliers Pratiques (Partie 4)"]
    ],
    "Partie 5 : Itération sur les Données" => [
        ['id' => 'curseurs-intro', 'title' => "Chapitre 12 : Traitement Ligne par Ligne (Curseurs)"],
        ['id' => 'curseurs-pratique', 'title' => "Chapitre 13 : Curseurs en Pratique"],
        ['id' => 'exercices-partie5', 'title' => "Ateliers Pratiques (Partie 5)"]
    ],
    "Partie 6 : Administration et Sécurité" => [
        ['id' => 'securite', 'title' => "Chapitre 14 : Gestion des Utilisateurs et Rôles"],
        ['id' => 'objets-virtuels', 'title' => "Chapitre 15 : Vues et Tables Temporaires"],
        ['id' => 'backup-restore', 'title' => "Chapitre 16 : Sauvegarde et Restauration"],
        ['id' => 'exercices-partie6', 'title' => "Ateliers Pratiques (Partie 6)"]
    ]
];
?>