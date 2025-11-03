<!-- =================================================================== -->
<!-- PARTIE 6 : ADMINISTRATION, SÉCURITÉ ET OPTIMISATION -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Partie 6 : Administration, Sécurité et Optimisation</h2>

<!-- ========== CHAPITRE 14 : LA SÉCURITÉ : GESTION DES UTILISATEURS ET DES RÔLES ========== -->
<section id="securite" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 14 : La Sécurité : Gestion des Utilisateurs et des Rôles</h3>
    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
        Une base de données contient souvent des informations sensibles. Assurer sa sécurité est une priorité absolue. Cela passe par une gestion rigoureuse des accès : qui peut se connecter, et une fois connecté, qui a le droit de voir ou de modifier quelles données. Le principe fondamental est celui du **moindre privilège** : un utilisateur ne doit avoir que les permissions strictement nécessaires à l'accomplissement de ses tâches.
    </p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">14.1. Gestion des Utilisateurs</h4>
            <p class="text-gray-700 mb-4">La première étape consiste à créer des comptes utilisateurs distincts. Chaque utilisateur est identifié par un nom et un hôte de connexion (par exemple, `'localhost'` pour les connexions locales, `'%'` pour n'importe quel hôte).</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-comment">-- Supprimer un utilisateur s'il existe déjà pour éviter une erreur</span>
<span class="token-keyword">DROP USER IF EXISTS</span> <span class="token-string">'yousra'</span>@<span class="token-string">'localhost'</span>;

<span class="token-comment">-- Créer un nouvel utilisateur avec son mot de passe</span>
<span class="token-keyword">CREATE USER</span> <span class="token-string">'yousra'</span>@<span class="token-string">'localhost'</span> <span class="token-keyword">IDENTIFIED BY</span> <span class="token-string">'123456'</span>;

<span class="token-comment">-- Modifier le mot de passe d'un utilisateur existant</span>
<span class="token-keyword">SET PASSWORD FOR</span> <span class="token-string">'yousra'</span>@<span class="token-string">'localhost'</span> <span class="token-operator">=</span> <span class="token-string">"abcdefg"</span>;
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">14.2. Attribution des Privilèges (`GRANT`)</h4>
            <p class="text-gray-700 mb-4">Une fois l'utilisateur créé, il n'a aucun droit. La commande `GRANT` permet de lui accorder des permissions spécifiques sur des objets de la base de données.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-comment">-- Attribuer TOUS les droits sur TOUS les objets de la base 'cuisine'</span>
<span class="token-keyword">GRANT ALL PRIVILEGES ON</span> <span class="token-variable">cuisine</span>.<span class="token-operator">*</span> <span class="token-keyword">TO</span> <span class="token-string">'yousra'</span>@<span class="token-string">'localhost'</span>;

<span class="token-comment">-- Attribuer des droits spécifiques (SELECT, INSERT, UPDATE) sur une table précise</span>
<span class="token-keyword">GRANT SELECT</span>, <span class="token-keyword">INSERT</span>, <span class="token-keyword">UPDATE ON</span> <span class="token-variable">vols</span>.<span class="token-variable">avion</span> <span class="token-keyword">TO</span> <span class="token-string">'yousra'</span>@<span class="token-string">'localhost'</span>;

<span class="token-comment">-- Attribuer des droits sur des colonnes spécifiques</span>
<span class="token-comment">-- L'utilisateur ne pourra voir que les villes de départ et d'arrivée</span>
<span class="token-keyword">GRANT SELECT</span>(<span class="token-variable">villed</span>, <span class="token-variable">villea</span>) <span class="token-keyword">ON</span> <span class="token-variable">vols</span>.<span class="token-variable">vol</span> <span class="token-keyword">TO</span> <span class="token-string">'yousra'</span>@<span class="token-string">'localhost'</span>;
<span class="token-comment">-- L'utilisateur ne pourra modifier que ces deux colonnes</span>
<span class="token-keyword">GRANT UPDATE</span>(<span class="token-variable">villed</span>, <span class="token-variable">villea</span>) <span class="token-keyword">ON</span> <span class="token-variable">vols</span>.<span class="token-variable">vol</span> <span class="token-keyword">TO</span> <span class="token-string">'yousra'</span>@<span class="token-string">'localhost'</span>;
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">14.3. Révocation des Privilèges (`REVOKE`)</h4>
            <p class="text-gray-700 mb-4">La commande `REVOKE` est le contraire de `GRANT`. Elle permet de retirer des permissions à un utilisateur.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-comment">-- Pour voir les droits actuels d'un utilisateur</span>
<span class="token-keyword">SHOW GRANTS FOR</span> <span class="token-string">'yousra'</span>@<span class="token-string">'localhost'</span>;

<span class="token-comment">-- Enlever les droits d'insertion et de modification sur la table 'avion'</span>
<span class="token-keyword">REVOKE INSERT</span>, <span class="token-keyword">UPDATE ON</span> <span class="token-variable">vols</span>.<span class="token-variable">avion</span> <span class="token-keyword">FROM</span> <span class="token-string">'yousra'</span>@<span class="token-string">'localhost'</span>;

<span class="token-comment">-- Vérifier que les droits ont bien été retirés</span>
<span class="token-keyword">SHOW GRANTS FOR</span> <span class="token-string">'yousra'</span>@<span class="token-string">'localhost'</span>;
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">14.4. Simplifier la Gestion avec les Rôles</h4>
            <p class="text-gray-700 mb-4">Quand on gère des dizaines d'utilisateurs, attribuer les droits un par un devient fastidieux. Les **rôles** permettent de créer des profils de permissions (ex: 'lecteur', 'editeur', 'administrateur'), d'attribuer les droits à ces rôles, puis d'attribuer simplement les rôles aux utilisateurs. C'est beaucoup plus simple à maintenir.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-comment">-- 1. Créer les rôles</span>
<span class="token-keyword">CREATE ROLE IF NOT EXISTS</span> <span class="token-string">'etudiants'</span>@<span class="token-string">'localhost'</span>, <span class="token-string">'profs'</span>@<span class="token-string">'localhost'</span>;

<span class="token-comment">-- 2. Donner des droits aux rôles</span>
<span class="token-keyword">GRANT SELECT ON</span> <span class="token-variable">vols</span>.<span class="token-variable">vol</span> <span class="token-keyword">TO</span> <span class="token-string">'etudiants'</span>@<span class="token-string">'localhost'</span>;
<span class="token-keyword">GRANT ALL PRIVILEGES ON</span> <span class="token-variable">vols</span>.<span class="token-operator">*</span> <span class="token-keyword">TO</span> <span class="token-string">'profs'</span>@<span class="token-string">'localhost'</span>;

<span class="token-comment">-- 3. Créer des utilisateurs</span>
<span class="token-keyword">CREATE USER IF NOT EXISTS</span> <span class="token-string">'u1'</span>@<span class="token-string">'localhost'</span> <span class="token-keyword">IDENTIFIED BY</span> <span class="token-string">'123'</span>;
<span class="token-keyword">CREATE USER IF NOT EXISTS</span> <span class="token-string">'u4'</span>@<span class="token-string">'localhost'</span> <span class="token-keyword">IDENTIFIED BY</span> <span class="token-string">'123'</span>;

<span class="token-comment">-- 4. Affecter les rôles aux utilisateurs</span>
<span class="token-keyword">GRANT</span> <span class="token-string">'etudiants'</span>@<span class="token-string">'localhost'</span> <span class="token-keyword">TO</span> <span class="token-string">'u1'</span>@<span class="token-string">'localhost'</span>;
<span class="token-keyword">GRANT</span> <span class="token-string">'etudiants'</span>@<span class="token-string">'localhost'</span>, <span class="token-string">'profs'</span>@<span class="token-string">'localhost'</span> <span class="token-keyword">TO</span> <span class="token-string">'u4'</span>@<span class="token-string">'localhost'</span>;

<span class="token-comment">-- 5. Activer les rôles par défaut pour les utilisateurs</span>
<span class="token-keyword">SET DEFAULT ROLE ALL TO</span> <span class="token-string">'u1'</span>@<span class="token-string">'localhost'</span>, <span class="token-string">'u4'</span>@<span class="token-string">'localhost'</span>;

<span class="token-comment">-- Vérifier les droits de u4 (il a ses propres droits + ceux des deux rôles)</span>
<span class="token-keyword">SHOW GRANTS FOR</span> <span class="token-string">'u4'</span>@<span class="token-string">'localhost'</span>;
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>

<!-- ========== CHAPITRE 15 : OBJETS DE BASE DE DONNÉES VIRTUELS ========== -->
<section id="objets-virtuels" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 15 : Objets de Base de Données Virtuels : Vues et Tables Temporaires</h3>
    <p class="text-gray-700 mb-6">En plus des tables physiques, MySQL propose des structures de données "virtuelles" ou éphémères qui sont extrêmement utiles pour simplifier le code et organiser les traitements de données complexes.</p>

    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">15.1. Les Vues (`VIEW`)</h4>
            <p class="text-gray-700 mb-4">Une **vue** est une requête `SELECT` qui est stockée dans la base de données et qui se comporte comme une table virtuelle. C'est un outil puissant pour :</p>
            <ul class="list-disc ml-6 text-gray-600 text-sm space-y-1 mb-4">
                <li><strong>Simplifier la complexité :</strong> Une jointure complexe ou un calcul répétitif peut être masqué derrière une vue simple à interroger.</li>
                <li><strong>Sécuriser les données :</strong> On peut créer une vue qui n'expose que certaines lignes ou colonnes d'une table, et donner aux utilisateurs l'accès à la vue plutôt qu'à la table complète.</li>
            </ul>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">USE</span> <span class="token-variable">vols</span>;

<span class="token-comment">-- On crée une vue qui représente une jointure complexe</span>
<span class="token-keyword">CREATE OR REPLACE VIEW</span> <span class="token-variable">v_details_vols</span> <span class="token-keyword">AS</span>
    <span class="token-keyword">SELECT</span> 
        <span class="token-variable">v</span>.<span class="token-variable">numvol</span>, <span class="token-variable">v</span>.<span class="token-variable">villed</span>, <span class="token-variable">v</span>.<span class="token-variable">villea</span>,
        <span class="token-variable">p</span>.<span class="token-variable">nom</span> <span class="token-keyword">AS</span> <span class="token-string">'nom_pilote'</span>,
        <span class="token-variable">a</span>.<span class="token-variable">typeav</span> <span class="token-keyword">AS</span> <span class="token-string">'type_avion'</span>
    <span class="token-keyword">FROM</span> <span class="token-variable">vol</span> <span class="token-variable">v</span>
    <span class="token-keyword">JOIN</span> <span class="token-variable">pilote</span> <span class="token-variable">p</span> <span class="token-keyword">ON</span> <span class="token-variable">v</span>.<span class="token-variable">numpil</span> <span class="token-operator">=</span> <span class="token-variable">p</span>.<span class="token-variable">numpilote</span>
    <span class="token-keyword">JOIN</span> <span class="token-variable">avion</span> <span class="token-variable">a</span> <span class="token-keyword">ON</span> <span class="token-variable">v</span>.<span class="token-variable">numav</span> <span class="token-operator">=</span> <span class="token-variable">a</span>.<span class="token-variable">numav</span>;

<span class="token-comment">-- Maintenant, on peut interroger la vue comme une simple table</span>
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">v_details_vols</span> <span class="token-keyword">WHERE</span> <span class="token-variable">nom_pilote</span> <span class="token-operator">=</span> <span class="token-string">'hassan'</span>;
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">15.2. Les Tables Temporaires (`TEMPORARY TABLE`)</h4>
            <p class="text-gray-700 mb-4">Une **table temporaire** est une table qui n'existe que pour la durée de la session de connexion en cours. Elle est automatiquement détruite à la déconnexion. Elles sont idéales pour stocker des résultats intermédiaires lors d'une analyse de données en plusieurs étapes.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-comment">-- Créer une table temporaire vide avec une structure définie</span>
<span class="token-keyword">CREATE TEMPORARY TABLE</span> <span class="token-variable">tva</span> (
    <span class="token-variable">id</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>, 
    <span class="token-variable">nom</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>), 
    <span class="token-variable">valeur</span> <span class="token-type">DOUBLE</span>
);
<span class="token-keyword">INSERT INTO</span> <span class="token-variable">tva</span> (<span class="token-variable">nom</span>, <span class="token-variable">valeur</span>) <span class="token-keyword">VALUES</span> (<span class="token-string">'normal'</span>, <span class="token-number">0.20</span>), (<span class="token-string">'reduit'</span>, <span class="token-number">0.07</span>);

<span class="token-comment">-- Créer une table temporaire à partir du résultat d'un SELECT</span>
<span class="token-keyword">CREATE TEMPORARY TABLE</span> <span class="token-variable">volsOfCasa</span> <span class="token-keyword">AS</span> 
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">vol</span> <span class="token-keyword">WHERE</span> <span class="token-variable">villed</span> <span class="token-operator">=</span> <span class="token-string">'casablanca'</span>;

<span class="token-keyword">SELECT</span> <span class="token-function">COUNT</span>(<span class="token-operator">*</span>) <span class="token-keyword">FROM</span> <span class="token-variable">volsOfCasa</span>;

<span class="token-comment">-- Si vous fermez votre connexion à MySQL et la rouvrez, la table 'volsOfCasa' n'existera plus.</span>
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm border">
<h4 class="text-xl font-bold text-gray-800 mb-2">15.3. Les Tables en Mémoire (`ENGINE=MEMORY`)</h4>
<p class="text-gray-700 mb-4">
Une autre forme de table non persistante est la table en mémoire, créée avec le moteur de stockage <strong>MEMORY</strong>. Contrairement à une table temporaire qui est liée à une session, une table en mémoire est visible par toutes les sessions connectées au serveur MySQL, mais son contenu est entièrement stocké dans la RAM.
</p>
<p class="text-gray-700 mb-4">
Cela les rend extrêmement rapides, idéales pour des données de référence ou des caches qui doivent être accessibles rapidement et par plusieurs utilisateurs. Cependant, leur contenu est <strong>volatile</strong> : si le serveur MySQL redémarre ou s'arrête, les données de ces tables sont perdues.
</p>
<p class="text-gray-700 mb-4">Les principales utilisations sont :</p>
<ul class="list-disc ml-6 text-gray-600 text-sm space-y-1 mb-4">
<li><strong>Mise en cache rapide :</strong> Pour stocker des résultats de requêtes complexes ou des données rarement modifiées afin d'accélérer les lectures.</li>
</ul>
<div class="code-block-wrapper">
<pre class="code-block"><code class="language-sql"><span class="token-comment">-- Créer une table de cache en mémoire pour des paramètres d'application</span>
<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">app_cache</span> (
<span class="token-variable">param_name</span> <span class="token-type">VARCHAR</span>(<span class="token-number">100</span>) <span class="token-keyword">PRIMARY KEY</span>,
<span class="token-variable">param_value</span> <span class="token-type">VARCHAR</span>(<span class="token-number">255</span>)
) <span class="token-keyword">ENGINE</span><span class="token-operator">=</span><span class="token-variable">MEMORY</span>;
<span class="token-comment">-- On peut l'alimenter comme n'importe quelle autre table</span>
<span class="token-keyword">INSERT INTO</span> <span class="token-variable">app_cache</span> (<span class="token-variable">param_name</span>, <span class="token-variable">param_value</span>) <span class="token-keyword">VALUES</span>
(<span class="token-string">'site_name'</span>, <span class="token-string">'Mon Super Site'</span>),
(<span class="token-string">'max_connections'</span>, <span class="token-string">'100'</span>);
<span class="token-comment">-- La lecture des données est très rapide</span>
<span class="token-keyword">SELECT</span> <span class="token-variable">param_value</span> <span class="token-keyword">FROM</span> <span class="token-variable">app_cache</span> <span class="token-keyword">WHERE</span> <span class="token-variable">param_name</span> <span class="token-operator">=</span> <span class="token-string">'site_name'</span>;
<span class="token-comment">-- Si le serveur MySQL redémarre, la table 'app_cache' sera vide.</span>
<span class="token-comment">-- Pour la supprimer manuellement, on utilise DROP TABLE.</span>
<span class="token-keyword">DROP TABLE</span> <span class="token-variable">app_cache</span>;
</code></pre>
<button class="copy-btn">Copier</button>
</div>
</div>

    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>

<!-- ========== CHAPITRE 16 : SAUVEGARDE ET RESTAURATION ========== -->
<section id="backup-restore" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 16 : Sauvegarde et Restauration (`mysqldump`)</h3>
    <p class="text-gray-700 mb-6">La sauvegarde régulière des données est la tâche la plus critique de l'administration de bases de données. Elle protège contre la perte de données due à une défaillance matérielle, une erreur humaine, ou une attaque malveillante. L'outil standard pour effectuer des sauvegardes logiques (qui génèrent un fichier `.sql`) est `mysqldump`.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">16.1. Sauvegarder une base de données (`mysqldump`)</h4>
            <p class="text-gray-700 mb-4">La commande `mysqldump` s'exécute depuis la ligne de commande (Terminal, PowerShell, CMD), et non depuis le client MySQL. Vous devez vous placer dans le répertoire `bin` de votre installation MySQL ou l'ajouter à votre `PATH` système.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash"><span class="token-comment"># Syntaxe de base : sauvegarder la base 'cuisine' dans un fichier .sql</span>
<span class="token-comment"># L'option -p sans mot de passe vous demandera le mot de passe de manière sécurisée.</span>
mysqldump -u root -p cuisine > sauvegarde_cuisine.sql

<span class="token-comment"># Spécifier l'hôte (-h) et le port (-P, majuscule) si ce ne sont pas les valeurs par défaut</span>
mysqldump -h 127.0.0.1 -P 3306 -u root -p cuisine > sauvegarde_cuisine_host.sql

<span class="token-comment"># Sauvegarder plusieurs bases de données en même temps</span>
mysqldump -u root -p --databases vols cuisine > sauvegarde_multiples.sql

<span class="token-comment"># Sauvegarder TOUTES les bases de données du serveur</span>
mysqldump -u root -p --all-databases > sauvegarde_totale_serveur.sql
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">16.2. Restaurer une base de données</h4>
            <p class="text-gray-700 mb-4">Pour restaurer une sauvegarde, il faut d'abord créer une base de données vide pour accueillir les données, puis importer le fichier `.sql`.</p>
            
            <h5 class="font-semibold text-gray-800 mb-2 mt-4">Méthode 1 : Ligne de commande (recommandée pour les gros fichiers)</h5>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash"><span class="token-comment"># 1. Se connecter à MySQL et créer la base de données de destination</span>
mysql -u root -p
<span class="token-keyword">CREATE DATABASE</span> restauration_test;
<span class="token-keyword">EXIT</span>;

<span class="token-comment"># 2. Importer le fichier .sql dans la nouvelle base de données</span>
mysql -u root -p restauration_test < sauvegarde_cuisine.sql
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>

            <h5 class="font-semibold text-gray-800 mb-2 mt-6">Méthode 2 : Commande `SOURCE` dans le client MySQL</h5>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-comment">-- 1. Se connecter à MySQL</span>
mysql -u root -p

<span class="token-comment">-- 2. Créer et utiliser la base de données</span>
<span class="token-keyword">CREATE DATABASE</span> restauration_test2;
<span class="token-keyword">USE</span> restauration_test2;

<span class="token-comment">-- 3. Exécuter la commande SOURCE (préciser le chemin complet si nécessaire)</span>
<span class="token-keyword">SOURCE</span> C:/chemin/vers/sauvegarde_cuisine.sql;
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>


<!-- ========== ATELIERS PRATIQUES DE LA PARTIE 6 ========== -->
<section id="exercices-partie6" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Ateliers Pratiques : Administration et Sécurité</h3>
    <p class="text-gray-700 mb-8">Appliquons ces concepts d'administration pour gérer les accès et optimiser les requêtes sur nos bases de données.</p>
    
    <div class="space-y-10">
        <!-- Exercice 1 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 1 : Gestion des Rôles pour un Restaurant</h4>
            <p class="text-gray-700 mb-4">Pour la base `cuisine`, vous devez mettre en place une politique de sécurité basée sur les rôles pour trois types d'employés : 'Gestionnaire', 'Cuisinier' et 'Stagiaire'.</p>
            <ul class="list-disc ml-6 text-gray-600 text-sm space-y-1 mb-4">
                <li><strong>Gestionnaire :</strong> Doit avoir tous les droits sur toute la base de données.</li>
                <li><strong>Cuisinier :</strong> Doit pouvoir tout lire (`SELECT`) sur toutes les tables, mais ne peut modifier (`INSERT`, `UPDATE`) que les tables `Recettes` et `Composition_Recette`.</li>
                <li><strong>Stagiaire :</strong> Ne peut que lire (`SELECT`) la table `Ingredients` et la table `Recettes`.</li>
            </ul>
            <p class="text-gray-700 mb-4">Créez les rôles, assignez-leur les bons privilèges, puis créez un utilisateur pour chaque profil et assignez-leur le rôle approprié.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-comment">-- 1. Création des rôles</span>
<span class="token-keyword">CREATE ROLE IF NOT EXISTS</span> 
    <span class="token-string">'gestionnaire_cuisine'</span>@<span class="token-string">'localhost'</span>, 
    <span class="token-string">'cuisinier'</span>@<span class="token-string">'localhost'</span>, 
    <span class="token-string">'stagiaire_cuisine'</span>@<span class="token-string">'localhost'</span>;

<span class="token-comment">-- 2. Attribution des privilèges aux rôles</span>
<span class="token-keyword">GRANT ALL PRIVILEGES ON</span> <span class="token-variable">cuisine</span>.<span class="token-operator">*</span> <span class="token-keyword">TO</span> <span class="token-string">'gestionnaire_cuisine'</span>@<span class="token-string">'localhost'</span>;

<span class="token-keyword">GRANT SELECT ON</span> <span class="token-variable">cuisine</span>.<span class="token-operator">*</span> <span class="token-keyword">TO</span> <span class="token-string">'cuisinier'</span>@<span class="token-string">'localhost'</span>;
<span class="token-keyword">GRANT INSERT</span>, <span class="token-keyword">UPDATE ON</span> <span class="token-variable">cuisine</span>.<span class="token-variable">Recettes</span> <span class="token-keyword">TO</span> <span class="token-string">'cuisinier'</span>@<span class="token-string">'localhost'</span>;
<span class="token-keyword">GRANT INSERT</span>, <span class="token-keyword">UPDATE ON</span> <span class="token-variable">cuisine</span>.<span class="token-variable">Composition_Recette</span> <span class="token-keyword">TO</span> <span class="token-string">'cuisinier'</span>@<span class="token-string">'localhost'</span>;

<span class="token-keyword">GRANT SELECT ON</span> <span class="token-variable">cuisine</span>.<span class="token-variable">Ingredients</span> <span class="token-keyword">TO</span> <span class="token-string">'stagiaire_cuisine'</span>@<span class="token-string">'localhost'</span>;
<span class="token-keyword">GRANT SELECT ON</span> <span class="token-variable">cuisine</span>.<span class="token-variable">Recettes</span> <span class="token-keyword">TO</span> <span class="token-string">'stagiaire_cuisine'</span>@<span class="token-string">'localhost'</span>;

<span class="token-comment">-- 3. Création des utilisateurs</span>
<span class="token-keyword">CREATE USER IF NOT EXISTS</span> <span class="token-string">'ali_gestionnaire'</span>@<span class="token-string">'localhost'</span> <span class="token-keyword">IDENTIFIED BY</span> <span class="token-string">'pass1'</span>;
<span class="token-keyword">CREATE USER IF NOT EXISTS</span> <span class="token-string">'fatima_cuisiniere'</span>@<span class="token-string">'localhost'</span> <span class="token-keyword">IDENTIFIED BY</span> <span class="token-string">'pass2'</span>;
<span class="token-keyword">CREATE USER IF NOT EXISTS</span> <span class="token-string">'karim_stagiaire'</span>@<span class="token-string">'localhost'</span> <span class="token-keyword">IDENTIFIED BY</span> <span class="token-string">'pass3'</span>;

<span class="token-comment">-- 4. Attribution des rôles aux utilisateurs</span>
<span class="token-keyword">GRANT</span> <span class="token-string">'gestionnaire_cuisine'</span>@<span class="token-string">'localhost'</span> <span class="token-keyword">TO</span> <span class="token-string">'ali_gestionnaire'</span>@<span class="token-string">'localhost'</span>;
<span class="token-keyword">GRANT</span> <span class="token-string">'cuisinier'</span>@<span class="token-string">'localhost'</span> <span class="token-keyword">TO</span> <span class="token-string">'fatima_cuisiniere'</span>@<span class="token-string">'localhost'</span>;
<span class="token-keyword">GRANT</span> <span class="token-string">'stagiaire_cuisine'</span>@<span class="token-string">'localhost'</span> <span class="token-keyword">TO</span> <span class="token-string">'karim_stagiaire'</span>@<span class="token-string">'localhost'</span>;

<span class="token-comment">-- 5. Activation des rôles</span>
<span class="token-keyword">SET DEFAULT ROLE ALL TO</span> 
    <span class="token-string">'ali_gestionnaire'</span>@<span class="token-string">'localhost'</span>,
    <span class="token-string">'fatima_cuisiniere'</span>@<span class="token-string">'localhost'</span>,
    <span class="token-string">'karim_stagiaire'</span>@<span class="token-string">'localhost'</span>;

<span class="token-keyword">FLUSH PRIVILEGES</span>;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

        <!-- Exercice 2 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 2 : Vues de Synthèse pour l'Aéroport</h4>
            <p class="text-gray-700 mb-4">Pour la base `vols`, créez deux vues pour simplifier le reporting :</p>
            <ol class="list-decimal ml-6 text-gray-600 text-sm space-y-1 mb-4">
                <li><strong>`v_vols_boeing` :</strong> Une vue qui n'affiche que les informations des vols (numéro de vol, villes départ/arrivée, nom du pilote) effectués avec un avion de type 'boeing'.</li>
                <li><strong>`v_pilotes_experimentes` :</strong> Une vue qui liste les pilotes (nom, ville, date de début) ayant commencé à travailler avant le 1er janvier 2010.</li>
            </ol>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">USE</span> <span class="token-variable">vols</span>;

<span class="token-comment">-- 1. Vue pour les vols en Boeing</span>
<span class="token-keyword">CREATE OR REPLACE VIEW</span> <span class="token-variable">v_vols_boeing</span> <span class="token-keyword">AS</span>
    <span class="token-keyword">SELECT</span> v.<span class="token-variable">numvol</span>, v.<span class="token-variable">villed</span>, v.<span class="token-variable">villea</span>, p.<span class="token-variable">nom</span> <span class="token-keyword">AS</span> <span class="token-string">'nom_pilote'</span>
    <span class="token-keyword">FROM</span> <span class="token-variable">vol</span> v
    <span class="token-keyword">JOIN</span> <span class="token-variable">avion</span> a <span class="token-keyword">ON</span> v.<span class="token-variable">numav</span> <span class="token-operator">=</span> a.<span class="token-variable">numav</span>
    <span class="token-keyword">JOIN</span> <span class="token-variable">pilote</span> p <span class="token-keyword">ON</span> v.<span class="token-variable">numpil</span> <span class="token-operator">=</span> p.<span class="token-variable">numpilote</span>
    <span class="token-keyword">WHERE</span> a.<span class="token-variable">typeav</span> <span class="token-operator">=</span> <span class="token-string">'boeing'</span>;

<span class="token-comment">-- Utilisation de la vue</span>
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">v_vols_boeing</span>;


<span class="token-comment">-- 2. Vue pour les pilotes expérimentés</span>
<span class="token-keyword">CREATE OR REPLACE VIEW</span> <span class="token-variable">v_pilotes_experimentes</span> <span class="token-keyword">AS</span>
    <span class="token-keyword">SELECT</span> <span class="token-variable">nom</span>, <span class="token-variable">villepilote</span>, <span class="token-variable">datedebut</span>
    <span class="token-keyword">FROM</span> <span class="token-variable">pilote</span>
    <span class="token-keyword">WHERE</span> <span class="token-variable">datedebut</span> <span class="token-operator">&lt;</span> <span class="token-string">'2010-01-01'</span>;

<span class="token-comment">-- Utilisation de la vue</span>
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">v_pilotes_experimentes</span>;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>
        
    </div>
</section>