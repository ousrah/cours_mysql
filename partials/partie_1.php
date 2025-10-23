<!-- =================================================================== -->
<!-- PARTIE 1 : FONDAMENTAUX DU SQL PROCÉDURAL -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Partie 1 : Fondamentaux du SQL Procédural</h2>

<!-- ========== CHAPITRE 1 : ACCUEIL & OBJECTIFS ========== -->
<section id="accueil" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 1 : Accueil & Objectifs Pédagogiques</h3>
    <p class="text-xl text-gray-600 mb-8 leading-relaxed">Bienvenue dans ce cours sur MySQL ! Alors que le SQL standard excelle dans la manipulation de données (lire, insérer, modifier, supprimer), il atteint ses limites lorsqu'il s'agit d'implémenter une logique applicative complexe. Ce cours vous fera passer au niveau supérieur en vous apprenant à programmer directement au sein de votre base de données grâce au SQL procédural.</p>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
            <h3 class="text-2xl font-bold mb-2">Objectif Principal</h3>
            <p class="text-gray-700">Maîtriser les concepts clés du SQL procédural : fonctions, procédures stockées, triggers, et curseurs. L'objectif est de vous rendre capable d'écrire un code serveur robuste, performant et sécurisé, qui garantit l'intégrité de vos données.</p>
        </div>
         <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
            <h3 class="text-2xl font-bold mb-2">Compétences Visées</h3>
            <p class="text-gray-700">À la fin de ce cours, vous saurez automatiser des tâches, gérer des transactions complexes, implémenter des règles métier directement dans la base de données et manipuler les données avec une granularité fine, des compétences essentielles pour tout développeur back-end ou administrateur de bases de données.</p>
        </div>
         <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-purple-500">
            <h3 class="text-2xl font-bold mb-2">Approche Pédagogique</h3>
            <p class="text-gray-700">Ce cours privilégie une approche pratique. Chaque concept théorique sera immédiatement illustré par des exemples de code concrets, des exercices d'application et des scénarios réels pour vous aider à assimiler durablement les connaissances.</p>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>

<!-- ========== CHAPITRE 2 : BLOCS D'INSTRUCTIONS ET FONCTIONS ========== -->
<section id="fonctions" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 2 : Blocs d'Instructions et Fonctions</h3>
    <p class="text-gray-700 mb-4">Le SQL procédural nous permet d'exécuter des blocs de code complexes, bien au-delà des simples requêtes. Un bloc est délimité par les mots-clés `BEGIN` et `END`. Pour organiser ce code de manière réutilisable, nous utilisons principalement des **fonctions**.</p>
    <p class="text-gray-700 mb-4">Une fonction est un sous-programme qui effectue une opération et **retourne toujours une seule valeur**. Elle est idéale pour des calculs, des formatages ou toute logique que l'on souhaite centraliser et réutiliser.</p>
    <p class="text-gray-700 mb-8">Comme le point-virgule (`;`) termine chaque instruction à l'intérieur du bloc, nous devons temporairement changer le délimiteur standard de MySQL avec la commande `DELIMITER $$`. Cela permet au serveur de comprendre où se termine la définition complète de notre fonction, sans s'arrêter au premier point-virgule rencontré.</p>

    <div class="bg-white p-6 rounded-lg shadow-sm border space-y-8">
        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">2.1. Syntaxe de base d'une fonction</h4>
            <p class="text-gray-700 mb-4">Voici la structure d'une fonction simple. Le mot-clé `DETERMINISTIC` est une indication pour MySQL : il signifie que la fonction retournera toujours le même résultat pour les mêmes paramètres d'entrée, ce qui peut aider le moteur à optimiser son exécution.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-comment">-- On change le délimiteur pour pouvoir utiliser ';' à l'intérieur de la fonction</span>
<span class="token-keyword">DELIMITER</span> $$

<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">hello</span>()
    <span class="token-keyword">RETURNS</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>)
    <span class="token-keyword">DETERMINISTIC</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">RETURN</span> <span class="token-string">'hello everybody'</span>;
<span class="token-keyword">END</span>$$

<span class="token-comment">-- On restaure le délimiteur par défaut</span>
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- On appelle la fonction comme n'importe quelle autre fonction native de SQL</span>
<span class="token-keyword">SELECT</span> <span class="token-function">hello</span>();</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            
            <h5 class="font-semibold text-gray-800 mb-2 mt-6">Fonction avec paramètres</h5>
            <p class="text-gray-700 mb-4">Les fonctions deviennent vraiment puissantes lorsqu'on leur passe des paramètres en entrée. On les déclare simplement entre les parenthèses avec leur nom et leur type.</p>
             <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">hello_one</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">hello_one</span>(<span class="token-variable">name</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>))
	<span class="token-keyword">RETURNS</span> <span class="token-type">VARCHAR</span>(<span class="token-number">100</span>)
	<span class="token-keyword">DETERMINISTIC</span>
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">RETURN</span> <span class="token-function">CONCAT</span>(<span class="token-string">'Hello '</span>, <span class="token-variable">name</span>, <span class="token-string">'!'</span>);
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">SELECT</span> <span class="token-function">hello_one</span>(<span class="token-string">'Youssef'</span>) <span class="token-keyword">AS</span> <span class="token-string">'Salutation'</span>;</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">2.2. Déclaration de variables</h4>
            <p class="text-gray-700 mb-4">À l'intérieur d'un bloc `BEGIN...END`, on peut déclarer des variables locales avec `DECLARE`. Il est obligatoire de leur donner un type, et on peut optionnellement leur assigner une valeur par défaut avec `DEFAULT`.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">hello_name_var</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">hello_name_var</span>(<span class="token-variable">name</span> <span class="token-type">VARCHAR</span>(<span class="token-number">20</span>))
    <span class="token-keyword">RETURNS</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>)
    <span class="token-keyword">DETERMINISTIC</span>
<span class="token-keyword">BEGIN</span>
	<span class="token-comment">-- Déclaration d'une variable 'salutation' avec une valeur par défaut</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">salutation</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>) <span class="token-keyword">DEFAULT</span> <span class="token-string">'Hello '</span>;
	<span class="token-keyword">RETURN</span> <span class="token-function">CONCAT</span>(<span class="token-variable">salutation</span>, <span class="token-variable">name</span>, <span class="token-string">'!'</span>);
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">SELECT</span> <span class="token-function">hello_name_var</span>(<span class="token-string">'Hassan'</span>);</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">2.3. Affectation de valeurs</h4>
            <p class="text-gray-700 mb-4">Après avoir déclaré une variable, on peut lui affecter une valeur de deux manières principales : avec `SET` (la plus commune) ou avec `SELECT ... INTO`.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">division</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">division</span>(<span class="token-variable">a</span> <span class="token-type">INT</span>, <span class="token-variable">b</span> <span class="token-type">INT</span>)
	<span class="token-keyword">RETURNS</span> <span class="token-type">FLOAT</span>
	<span class="token-keyword">DETERMINISTIC</span>
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">resultat</span> <span class="token-type">FLOAT</span>;
    
    <span class="token-comment">-- Affectation avec SET</span>
    <span class="token-comment">-- SET resultat = a / b;</span>
    
    <span class="token-comment">-- Affectation alternative avec SELECT ... INTO</span>
    <span class="token-keyword">SELECT</span> <span class="token-variable">a</span> <span class="token-operator">/</span> <span class="token-variable">b</span> <span class="token-keyword">INTO</span> <span class="token-variable">resultat</span>;
    
	<span class="token-keyword">RETURN</span> <span class="token-variable">resultat</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">SELECT</span> <span class="token-function">division</span>(<span class="token-number">3</span>, <span class="token-number">2</span>);</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>

<!-- ========== CHAPITRE 3 : STRUCTURES DE CONTRÔLE ========== -->
<section id="controle" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 3 : Structures de Contrôle (Conditions & Boucles)</h3>
    <p class="text-gray-700 mb-6">Pour implémenter une logique applicative, il est indispensable de pouvoir contrôler le flux d'exécution du code. MySQL nous offre pour cela des structures conditionnelles et des boucles, similaires à celles que l'on trouve dans les langages de programmation traditionnels.</p>
    
    <div class="space-y-8">
        <!-- Conditions IF/ELSEIF/ELSE -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">3.1. Les Conditions : `IF`, `ELSEIF`, `ELSE`</h4>
            <p class="text-gray-700 mb-4">La structure `IF` est la plus fondamentale. Elle permet d'exécuter un bloc de code uniquement si une condition est vraie. On peut l'étendre avec `ELSEIF` pour tester des conditions multiples et `ELSE` pour définir une action par défaut. La structure doit toujours se terminer par `END IF;`.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">division_securisee</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">division_securisee</span>(<span class="token-variable">a</span> <span class="token-type">INT</span>, <span class="token-variable">b</span> <span class="token-type">INT</span>)
    <span class="token-keyword">RETURNS</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>)
    <span class="token-keyword">DETERMINISTIC</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-comment">-- On vérifie si le diviseur est nul avant de faire le calcul</span>
    <span class="token-keyword">IF</span> <span class="token-variable">b</span> <span class="token-operator">=</span> <span class="token-number">0</span> <span class="token-keyword">THEN</span>
		<span class="token-keyword">RETURN</span> <span class="token-string">'Impossible de diviser par zéro'</span>;
	<span class="token-keyword">ELSE</span>
		<span class="token-keyword">RETURN</span> <span class="token-variable">a</span> <span class="token-operator">/</span> <span class="token-variable">b</span>;
	<span class="token-keyword">END IF</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">SELECT</span> <span class="token-function">division_securisee</span>(<span class="token-number">3</span>, <span class="token-number">0</span>);</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <!-- Structure CASE -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">3.2. L'alternative `CASE`</h4>
            <p class="text-gray-700 mb-4">Quand on doit vérifier une même variable contre plusieurs valeurs possibles, la structure `CASE` est souvent plus lisible et plus élégante. Elle existe sous deux formes principales :</p>
            <ul class="list-disc ml-6 text-gray-600 text-sm space-y-1 mb-4">
                <li><strong>`CASE variable WHEN valeur THEN ...`</strong> : Idéale pour tester l'égalité avec une série de valeurs.</li>
                <li><strong>`CASE WHEN condition THEN ...`</strong> : Plus flexible, permet de tester des conditions complexes (ex: `note > 10 AND note < 12`).</li>
            </ul>
            <h5 class="font-semibold text-gray-800 mb-2 mt-6">Exemple avec `CASE variable ...`</h5>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">getNomJour</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">getNomJour</span>(<span class="token-variable">j</span> <span class="token-type">INT</span>)
    <span class="token-keyword">RETURNS</span> <span class="token-type">VARCHAR</span>(<span class="token-number">100</span>)
    <span class="token-keyword">DETERMINISTIC</span>
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">resultat</span> <span class="token-type">VARCHAR</span>(<span class="token-number">100</span>);
	<span class="token-keyword">SET</span> <span class="token-variable">resultat</span> <span class="token-operator">=</span> <span class="token-keyword">CASE</span> <span class="token-variable">j</span>
		<span class="token-keyword">WHEN</span> <span class="token-number">1</span> <span class="token-keyword">THEN</span> <span class="token-string">'الأحد'</span>
		<span class="token-keyword">WHEN</span> <span class="token-number">2</span> <span class="token-keyword">THEN</span> <span class="token-string">'الإثنين'</span>
		<span class="token-keyword">WHEN</span> <span class="token-number">3</span> <span class="token-keyword">THEN</span> <span class="token-string">'الثلاثاء'</span>
		<span class="token-keyword">WHEN</span> <span class="token-number">4</span> <span class="token-keyword">THEN</span> <span class="token-string">'الأربعاء'</span>
		<span class="token-keyword">WHEN</span> <span class="token-number">5</span> <span class="token-keyword">THEN</span> <span class="token-string">'الخميس'</span>
		<span class="token-keyword">WHEN</span> <span class="token-number">6</span> <span class="token-keyword">THEN</span> <span class="token-string">'الجمعة'</span>
		<span class="token-keyword">WHEN</span> <span class="token-number">7</span> <span class="token-keyword">THEN</span> <span class="token-string">'السبت'</span>
		<span class="token-keyword">ELSE</span> <span class="token-string">'Numéro de jour invalide'</span>
    <span class="token-keyword">END</span>;
    <span class="token-keyword">RETURN</span> <span class="token-variable">resultat</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            <h5 class="font-semibold text-gray-800 mb-2 mt-6">Exemple avec `CASE WHEN condition ...`</h5>
             <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">getMention</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">getMention</span>(<span class="token-variable">note</span> <span class="token-type">FLOAT</span>)
    <span class="token-keyword">RETURNS</span> <span class="token-type">VARCHAR</span>(<span class="token-number">100</span>)
    <span class="token-keyword">DETERMINISTIC</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">RETURN</span> <span class="token-keyword">CASE</span> 
        <span class="token-keyword">WHEN</span> <span class="token-variable">note</span> <span class="token-operator">&lt;</span> <span class="token-number">0</span> <span class="token-keyword">OR</span> <span class="token-variable">note</span> <span class="token-operator">></span> <span class="token-number">20</span> <span class="token-keyword">THEN</span> <span class="token-string">'Erreur: note invalide'</span>
        <span class="token-keyword">WHEN</span> <span class="token-variable">note</span> <span class="token-operator">&lt;</span> <span class="token-number">5</span> <span class="token-keyword">THEN</span> <span class="token-string">'Très faible'</span>
        <span class="token-keyword">WHEN</span> <span class="token-variable">note</span> <span class="token-operator">&lt;</span> <span class="token-number">9</span> <span class="token-keyword">THEN</span> <span class="token-string">'Faible'</span>
        <span class="token-keyword">WHEN</span> <span class="token-variable">note</span> <span class="token-operator">&lt;</span> <span class="token-number">10</span> <span class="token-keyword">THEN</span> <span class="token-string">'Insuffisant'</span>
        <span class="token-keyword">WHEN</span> <span class="token-variable">note</span> <span class="token-operator">&lt;</span> <span class="token-number">12</span> <span class="token-keyword">THEN</span> <span class="token-string">'Passable'</span>
        <span class="token-keyword">WHEN</span> <span class="token-variable">note</span> <span class="token-operator">&lt;</span> <span class="token-number">14</span> <span class="token-keyword">THEN</span> <span class="token-string">'Assez bien'</span>
        <span class="token-keyword">WHEN</span> <span class="token-variable">note</span> <span class="token-operator">&lt;</span> <span class="token-number">16</span> <span class="token-keyword">THEN</span> <span class="token-string">'Bien'</span>
        <span class="token-keyword">WHEN</span> <span class="token-variable">note</span> <span class="token-operator">&lt;</span> <span class="token-number">18</span> <span class="token-keyword">THEN</span> <span class="token-string">'Très bien'</span>
        <span class="token-keyword">ELSE</span> <span class="token-string">'Excellent'</span>
    <span class="token-keyword">END</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">SELECT</span> <span class="token-function">getMention</span>(<span class="token-number">15.5</span>);</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <!-- Boucles -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">3.3. Les Boucles : `WHILE`, `REPEAT`, et `LOOP`</h4>
            <p class="text-gray-700 mb-4">Les boucles permettent d'exécuter un bloc de code plusieurs fois. MySQL en propose trois types, avec de légères différences :</p>
            <ul class="list-disc ml-6 text-gray-700 space-y-2 mb-4">
                <li><strong>WHILE ... END WHILE</strong> : La condition est testée <strong>avant</strong> chaque itération. Si la condition est fausse au départ, la boucle ne s'exécute jamais.</li>
                <li><strong>REPEAT ... UNTIL ... END REPEAT</strong> : La boucle s'exécute <strong>au moins une fois</strong>, car la condition est testée à la fin. La boucle continue *tant que* la condition de fin est fausse.</li>
                <li><strong>LOOP ... END LOOP</strong> : Crée une boucle infinie qui doit être quittée manuellement avec `LEAVE nom_de_la_boucle`. C'est utile pour des logiques de sortie plus complexes.</li>
            </ul>

            <h5 class="font-semibold text-gray-800 mb-2">Exemple : Somme des N premiers entiers avec les 3 boucles</h5>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-comment">-- Avec la boucle WHILE</span>
<span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">somme_while</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">somme_while</span>(<span class="token-variable">n</span> <span class="token-type">INT</span>) <span class="token-keyword">RETURNS BIGINT DETERMINISTIC</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">s</span> <span class="token-type">BIGINT</span> <span class="token-keyword">DEFAULT</span> <span class="token-number">0</span>; <span class="token-keyword">DECLARE</span> <span class="token-variable">i</span> <span class="token-type">INT</span> <span class="token-keyword">DEFAULT</span> <span class="token-number">1</span>;
    <span class="token-keyword">WHILE</span> <span class="token-variable">i</span> <span class="token-operator">&lt;=</span> <span class="token-variable">n</span> <span class="token-keyword">DO</span> <span class="token-keyword">SET</span> <span class="token-variable">s</span> <span class="token-operator">=</span> <span class="token-variable">s</span> <span class="token-operator">+</span> <span class="token-variable">i</span>; <span class="token-keyword">SET</span> <span class="token-variable">i</span> <span class="token-operator">=</span> <span class="token-variable">i</span> <span class="token-operator">+</span> <span class="token-number">1</span>; <span class="token-keyword">END WHILE</span>;
    <span class="token-keyword">RETURN</span> <span class="token-variable">s</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Avec la boucle REPEAT</span>
<span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">somme_repeat</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">somme_repeat</span>(<span class="token-variable">n</span> <span class="token-type">INT</span>) <span class="token-keyword">RETURNS BIGINT DETERMINISTIC</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">s</span> <span class="token-type">BIGINT</span> <span class="token-keyword">DEFAULT</span> <span class="token-number">0</span>; <span class="token-keyword">DECLARE</span> <span class="token-variable">i</span> <span class="token-type">INT</span> <span class="token-keyword">DEFAULT</span> <span class="token-number">1</span>;
    <span class="token-keyword">REPEAT</span> <span class="token-keyword">SET</span> <span class="token-variable">s</span> <span class="token-operator">=</span> <span class="token-variable">s</span> <span class="token-operator">+</span> <span class="token-variable">i</span>; <span class="token-keyword">SET</span> <span class="token-variable">i</span> <span class="token-operator">=</span> <span class="token-variable">i</span> <span class="token-operator">+</span> <span class="token-number">1</span>; <span class="token-keyword">UNTIL</span> <span class="token-variable">i</span> <span class="token-operator">></span> <span class="token-variable">n</span> <span class="token-keyword">END REPEAT</span>;
    <span class="token-keyword">RETURN</span> <span class="token-variable">s</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Avec la boucle LOOP</span>
<span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">somme_loop</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">somme_loop</span>(<span class="token-variable">n</span> <span class="token-type">INT</span>) <span class="token-keyword">RETURNS BIGINT DETERMINISTIC</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">s</span> <span class="token-type">BIGINT</span> <span class="token-keyword">DEFAULT</span> <span class="token-number">0</span>; <span class="token-keyword">DECLARE</span> <span class="token-variable">i</span> <span class="token-type">INT</span> <span class="token-keyword">DEFAULT</span> <span class="token-number">1</span>;
    <span class="token-variable">boucle1</span>: <span class="token-keyword">LOOP</span>
        <span class="token-keyword">IF</span> (<span class="token-variable">i</span> <span class="token-operator">></span> <span class="token-variable">n</span>) <span class="token-keyword">THEN</span> <span class="token-keyword">LEAVE</span> <span class="token-variable">boucle1</span>; <span class="token-keyword">END IF</span>;
        <span class="token-keyword">SET</span> <span class="token-variable">s</span> <span class="token-operator">=</span> <span class="token-variable">s</span> <span class="token-operator">+</span> <span class="token-variable">i</span>; <span class="token-keyword">SET</span> <span class="token-variable">i</span> <span class="token-operator">=</span> <span class="token-variable">i</span> <span class="token-operator">+</span> <span class="token-number">1</span>;
    <span class="token-keyword">END LOOP</span> <span class="token-variable">boucle1</span>;
    <span class="token-keyword">RETURN</span> <span class="token-variable">s</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>


<!-- ========== ATELIERS PRATIQUES DE LA PARTIE 1 ========== -->
<section id="exercices-partie1" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Ateliers Pratiques : Fonctions, Conditions et Boucles</h3>
    <p class="text-gray-700 mb-8">Il est temps de mettre en pratique tout ce que vous avez appris. Les exercices suivants, tirés de cas concrets, couvrent plusieurs concepts clés et vous aideront à consolider vos compétences de base en SQL procédural.</p>

    <div class="space-y-10">

        <!-- Exercice 1 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 1 : Formatter une date en français</h4>
            <p class="text-gray-700 mb-4">Écrire une fonction qui reçoit une date et la retourne sous la forme "Jour NomDuMois Année" (ex: "12 septembre 2024"). Comparez une approche manuelle avec `CASE` et une approche utilisant les fonctions natives de MySQL.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <h5 class="font-semibold text-gray-800 mb-2 mt-4">Approche 1 : Manuelle avec `CASE`</h5>
                 <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">format_date_manuel</span>;
<span class="token-keyword">DELIMITER</span> $$ 
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">format_date_manuel</span>(<span class="token-variable">d</span> <span class="token-type">DATE</span>)
    <span class="token-keyword">RETURNS</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>)
    <span class="token-keyword">DETERMINISTIC</span>
<span class="token-keyword">BEGIN</span>  
    <span class="token-keyword">DECLARE</span> <span class="token-variable">nom_mois</span> <span class="token-type">VARCHAR</span>(<span class="token-number">20</span>);
    <span class="token-keyword">SET</span> <span class="token-variable">nom_mois</span> <span class="token-operator">=</span> <span class="token-keyword">CASE</span> <span class="token-function">MONTH</span>(<span class="token-variable">d</span>) 
        <span class="token-keyword">WHEN</span> <span class="token-number">1</span> <span class="token-keyword">THEN</span> <span class="token-string">'janvier'</span>   <span class="token-keyword">WHEN</span> <span class="token-number">7</span> <span class="token-keyword">THEN</span> <span class="token-string">'juillet'</span>
        <span class="token-keyword">WHEN</span> <span class="token-number">2</span> <span class="token-keyword">THEN</span> <span class="token-string">'février'</span>   <span class="token-keyword">WHEN</span> <span class="token-number">8</span> <span class="token-keyword">THEN</span> <span class="token-string">'août'</span>
        <span class="token-keyword">WHEN</span> <span class="token-number">3</span> <span class="token-keyword">THEN</span> <span class="token-string">'mars'</span>      <span class="token-keyword">WHEN</span> <span class="token-number">9</span> <span class="token-keyword">THEN</span> <span class="token-string">'septembre'</span>
        <span class="token-keyword">WHEN</span> <span class="token-number">4</span> <span class="token-keyword">THEN</span> <span class="token-string">'avril'</span>     <span class="token-keyword">WHEN</span> <span class="token-number">10</span> <span class="token-keyword">THEN</span> <span class="token-string">'octobre'</span>
        <span class="token-keyword">WHEN</span> <span class="token-number">5</span> <span class="token-keyword">THEN</span> <span class="token-string">'mai'</span>       <span class="token-keyword">WHEN</span> <span class="token-number">11</span> <span class="token-keyword">THEN</span> <span class="token-string">'novembre'</span>
        <span class="token-keyword">WHEN</span> <span class="token-number">6</span> <span class="token-keyword">THEN</span> <span class="token-string">'juin'</span>      <span class="token-keyword">WHEN</span> <span class="token-number">12</span> <span class="token-keyword">THEN</span> <span class="token-string">'décembre'</span>
    <span class="token-keyword">END</span>;
    <span class="token-keyword">RETURN</span> <span class="token-function">CONCAT</span>(<span class="token-function">DAY</span>(<span class="token-variable">d</span>), <span class="token-string">' '</span>, <span class="token-variable">nom_mois</span>, <span class="token-string">' '</span>, <span class="token-function">YEAR</span>(<span class="token-variable">d</span>));
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
                <h5 class="font-semibold text-gray-800 mb-2 mt-6">Approche 2 : Avec `DATE_FORMAT` et `lc_time_names` (recommandée)</h5>
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">date_format_fr</span>;
<span class="token-keyword">DELIMITER</span> $$ 
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">date_format_fr</span> (<span class="token-variable">d</span> <span class="token-type">DATE</span>) 
    <span class="token-keyword">RETURNS</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>) 
    <span class="token-keyword">DETERMINISTIC</span> 
<span class="token-keyword">BEGIN</span> 
	<span class="token-keyword">DECLARE</span> <span class="token-variable">resultat</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>);
	<span class="token-keyword">DECLARE</span> <span class="token-variable">ancienne_locale</span> <span class="token-type">VARCHAR</span>(<span class="token-number">10</span>);
	<span class="token-keyword">SET</span> <span class="token-variable">ancienne_locale</span> <span class="token-operator">=</span> @@lc_time_names;
	<span class="token-keyword">SET</span> lc_time_names <span class="token-operator">=</span> <span class="token-string">'fr_FR'</span>;
	<span class="token-keyword">SET</span> <span class="token-variable">resultat</span> <span class="token-operator">=</span> <span class="token-function">DATE_FORMAT</span>(<span class="token-variable">d</span>, <span class="token-string">'%e %M %Y'</span>); 
	<span class="token-keyword">SET</span> lc_time_names <span class="token-operator">=</span> <span class="token-variable">ancienne_locale</span>;
	<span class="token-keyword">RETURN</span> <span class="token-variable">resultat</span>;
<span class="token-keyword">END</span>$$ 
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">SELECT</span> <span class="token-function">date_format_fr</span>(<span class="token-string">'2024-02-01'</span>);</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

        <!-- Exercice 2 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 2 : Calculer un écart entre deux dates</h4>
            <p class="text-gray-700 mb-4">Écrire une fonction qui reçoit deux dates (`DATETIME`) et une unité de calcul ('jour', 'mois', 'année', 'heure', etc.) et qui retourne l'écart entre les deux dates selon l'unité demandée.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">calcul_ecart_date</span>;
<span class="token-keyword">DELIMITER</span> $$ 
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">calcul_ecart_date</span>(
    <span class="token-variable">date_debut</span> <span class="token-type">DATETIME</span>,
    <span class="token-variable">date_fin</span> <span class="token-type">DATETIME</span>,
    <span class="token-variable">unite</span> <span class="token-type">VARCHAR</span>(<span class="token-number">20</span>)
)
	<span class="token-keyword">RETURNS</span> <span class="token-type">BIGINT</span> 
    <span class="token-keyword">DETERMINISTIC</span>
<span class="token-keyword">BEGIN</span> 
	<span class="token-keyword">DECLARE</span> <span class="token-variable">resultat</span> <span class="token-type">BIGINT</span>;
    <span class="token-keyword">SET</span> <span class="token-variable">unite</span> <span class="token-operator">=</span> <span class="token-function">LOWER</span>(<span class="token-variable">unite</span>); <span class="token-comment">-- Rendre la comparaison insensible à la casse</span>
    
    <span class="token-keyword">SET</span> <span class="token-variable">resultat</span> <span class="token-operator">=</span> <span class="token-keyword">CASE</span> <span class="token-variable">unite</span>
		<span class="token-keyword">WHEN</span> <span class="token-string">'annee'</span> <span class="token-keyword">THEN</span> <span class="token-function">TIMESTAMPDIFF</span>(YEAR, <span class="token-variable">date_debut</span>, <span class="token-variable">date_fin</span>)
		<span class="token-keyword">WHEN</span> <span class="token-string">'mois'</span> <span class="token-keyword">THEN</span> <span class="token-function">TIMESTAMPDIFF</span>(MONTH, <span class="token-variable">date_debut</span>, <span class="token-variable">date_fin</span>)
		<span class="token-keyword">WHEN</span> <span class="token-string">'jour'</span> <span class="token-keyword">THEN</span> <span class="token-function">TIMESTAMPDIFF</span>(DAY, <span class="token-variable">date_debut</span>, <span class="token-variable">date_fin</span>)
        <span class="token-keyword">WHEN</span> <span class="token-string">'heure'</span> <span class="token-keyword">THEN</span> <span class="token-function">TIMESTAMPDIFF</span>(HOUR, <span class="token-variable">date_debut</span>, <span class="token-variable">date_fin</span>)
        <span class="token-keyword">WHEN</span> <span class="token-string">'minute'</span> <span class="token-keyword">THEN</span> <span class="token-function">TIMESTAMPDIFF</span>(MINUTE, <span class="token-variable">date_debut</span>, <span class="token-variable">date_fin</span>)
        <span class="token-keyword">WHEN</span> <span class="token-string">'seconde'</span> <span class="token-keyword">THEN</span> <span class="token-function">TIMESTAMPDIFF</span>(SECOND, <span class="token-variable">date_debut</span>, <span class="token-variable">date_fin</span>)
		<span class="token-keyword">ELSE</span> <span class="token-number">NULL</span> <span class="token-comment">-- Retourne NULL si l'unité est invalide</span>
	<span class="token-keyword">END</span>;
	<span class="token-keyword">RETURN</span> <span class="token-variable">resultat</span>;
<span class="token-keyword">END</span>$$    
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">SELECT</span> <span class="token-function">calcul_ecart_date</span>(<span class="token-string">'2025-10-10 12:15:00'</span>, <span class="token-string">'2025-10-11 14:18:03'</span>, <span class="token-string">'heure'</span>) <span class="token-keyword">AS</span> <span class="token-string">'Ecart en Heures'</span>;
<span class="token-keyword">SELECT</span> <span class="token-function">calcul_ecart_date</span>(<span class="token-string">'2025-10-10 12:15:00'</span>, <span class="token-string">'2026-11-15 12:15:00'</span>, <span class="token-string">'Mois'</span>) <span class="token-keyword">AS</span> <span class="token-string">'Ecart en Mois'</span>;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>
        
        <!-- Tous les autres exercices que vous avez listés... -->

        <!-- Section Base de Données "Vols" -->
        <div class="bg-gray-100 p-4 rounded-lg border my-8">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercices sur la Base de Données `vols`</h4>
            <p class="text-gray-700 mb-4">Les exercices suivants nécessitent la base de données `vols`. Exécutez le script ci-dessous pour la mettre en place.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP DATABASE IF EXISTS</span> <span class="token-variable">vols</span>;
<span class="token-keyword">CREATE DATABASE</span> <span class="token-variable">vols</span> <span class="token-keyword">COLLATE</span> <span class="token-string">utf8mb4_general_ci</span>;
<span class="token-keyword">USE</span> <span class="token-variable">vols</span>;

<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">Pilote</span>(<span class="token-variable">numpilote</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>, <span class="token-variable">nom</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>), <span class="token-variable">villepilote</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>), <span class="token-variable">datedebut</span> <span class="token-type">DATE</span>);
<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">Avion</span>(<span class="token-variable">numav</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>, <span class="token-variable">typeav</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>), <span class="token-variable">capav</span> <span class="token-type">INT</span>);
<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">Vol</span>(<span class="token-variable">numvol</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>, <span class="token-variable">numpil</span> <span class="token-type">INT NOT NULL</span>, <span class="token-variable">numav</span> <span class="token-type">INT NOT NULL</span>);

<span class="token-keyword">ALTER TABLE</span> <span class="token-variable">Vol</span> <span class="token-keyword">ADD CONSTRAINT</span> <span class="token-variable">fk_vol_pilote</span> <span class="token-keyword">FOREIGN KEY</span>(<span class="token-variable">numpil</span>) <span class="token-keyword">REFERENCES</span> <span class="token-variable">Pilote</span>(<span class="token-variable">numpilote</span>);
<span class="token-keyword">ALTER TABLE</span> <span class="token-variable">Vol</span> <span class="token-keyword">ADD CONSTRAINT</span> <span class="token-variable">fk_vol_avion</span> <span class="token-keyword">FOREIGN KEY</span>(<span class="token-variable">numav</span>) <span class="token-keyword">REFERENCES</span> <span class="token-variable">Avion</span>(<span class="token-variable">numav</span>);

<span class="token-keyword">INSERT INTO</span> <span class="token-variable">Avion</span>(<span class="token-variable">numav</span>, <span class="token-variable">typeav</span>, <span class="token-variable">capav</span>) <span class="token-keyword">VALUES</span> (<span class="token-number">1</span>,<span class="token-string">'Boeing'</span>,<span class="token-number">350</span>), (<span class="token-number">2</span>,<span class="token-string">'Caravelle'</span>,<span class="token-number">250</span>), (<span class="token-number">3</span>,<span class="token-string">'Airbus'</span>,<span class="token-number">500</span>), (<span class="token-number">4</span>,<span class="token-string">'Concorde'</span>,<span class="token-number">150</span>);
<span class="token-keyword">INSERT INTO</span> <span class="token-variable">Pilote</span>(<span class="token-variable">numpilote</span>, <span class="token-variable">nom</span>, <span class="token-variable">villepilote</span>, <span class="token-variable">datedebut</span>) <span class="token-keyword">VALUES</span> (<span class="token-number">1</span>,<span class="token-string">'Hassan'</span>,<span class="token-string">'Tétouan'</span>,<span class="token-string">'2022-01-01'</span>), (<span class="token-number">2</span>,<span class="token-string">'Saida'</span>,<span class="token-string">'Casablanca'</span>,<span class="token-string">'2005-01-01'</span>), (<span class="token-number">3</span>,<span class="token-string">'Youssef'</span>,<span class="token-string">'Tanger'</span>,<span class="token-string">'2002-01-01'</span>);
<span class="token-keyword">INSERT INTO</span> <span class="token-variable">Vol</span>(<span class="token-variable">numvol</span>, <span class="token-variable">numpil</span>, <span class="token-variable">numav</span>) <span class="token-keyword">VALUES</span> (<span class="token-number">1</span>,<span class="token-number">1</span>,<span class="token-number">1</span>), (<span class="token-number">2</span>,<span class="token-number">1</span>,<span class="token-number">1</span>), (<span class="token-number">3</span>,<span class="token-number">2</span>,<span class="token-number">2</span>), (<span class="token-number">4</span>,<span class="token-number">2</span>,<span class="token-number">2</span>), (<span class="token-number">5</span>,<span class="token-number">3</span>,<span class="token-number">3</span>), (<span class="token-number">6</span>,<span class="token-number">3</span>,<span class="token-number">3</span>), (<span class="token-number">7</span>,<span class="token-number">1</span>,<span class="token-number">1</span>), (<span class="token-number">8</span>,<span class="token-number">1</span>,<span class="token-number">1</span>), (<span class="token-number">9</span>,<span class="token-number">1</span>,<span class="token-number">2</span>), (<span class="token-number">10</span>,<span class="token-number">1</span>,<span class="token-number">2</span>), (<span class="token-number">11</span>,<span class="token-number">3</span>,<span class="token-number">3</span>), (<span class="token-number">12</span>,<span class="token-number">3</span>,<span class="token-number">3</span>), (<span class="token-number">13</span>,<span class="token-number">2</span>,<span class="token-number">1</span>), (<span class="token-number">14</span>,<span class="token-number">3</span>,<span class="token-number">1</span>);
</code></pre>
                 <button class="copy-btn">Copier</button>
            </div>
        </div>

        <!-- Exercice 3 - vols -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 3 : Nombre de pilotes par volume de vols</h4>
            <p class="text-gray-700 mb-4">Écrire une fonction qui retourne le nombre de pilotes ayant effectué un nombre de vols strictement supérieur à un seuil donné en paramètre.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">q1_nbpilotes</span>; 
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">q1_nbpilotes</span> (<span class="token-variable">nb</span> <span class="token-type">INT</span>)
    <span class="token-keyword">RETURNS</span> <span class="token-type">INT</span>
    <span class="token-keyword">READS SQL DATA</span>
<span class="token-keyword">BEGIN</span> 
	<span class="token-keyword">DECLARE</span> <span class="token-variable">total</span> <span class="token-type">INT</span>;
		<span class="token-comment">-- On utilise une sous-requête (ou une CTE) pour d'abord compter les vols par pilote</span>
		<span class="token-keyword">SELECT</span> <span class="token-function">COUNT</span>(<span class="token-operator">*</span>) <span class="token-keyword">INTO</span> <span class="token-variable">total</span> <span class="token-keyword">FROM</span> (
            <span class="token-keyword">SELECT</span> <span class="token-variable">numpil</span>
            <span class="token-keyword">FROM</span> <span class="token-variable">Vol</span> 
            <span class="token-keyword">GROUP BY</span> <span class="token-variable">numpil</span>
            <span class="token-keyword">HAVING</span> <span class="token-function">COUNT</span>(<span class="token-operator">*</span>) <span class="token-operator">></span> <span class="token-variable">nb</span>
        ) <span class="token-keyword">AS</span> <span class="token-variable">PilotesFiltres</span>;
    <span class="token-keyword">RETURN</span> <span class="token-variable">total</span>;
<span class="token-keyword">END</span>$$ 
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">SELECT</span> <span class="token-function">q1_nbpilotes</span>(<span class="token-number">5</span>); <span class="token-comment">-- Pilote 1 a 6 vols > 5. Résultat: 1</span>
<span class="token-keyword">SELECT</span> <span class="token-function">q1_nbpilotes</span>(<span class="token-number">3</span>); <span class="token-comment">-- Pilote 1 (6), Pilote 3 (5). Résultat: 2</span>
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

        <!-- Exercice 4 - vols -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 4 : Ancienneté d'un pilote</h4>
            <p class="text-gray-700 mb-4">Écrire une fonction qui retourne l'ancienneté (en années complètes) d'un pilote dont l'ID est passé en paramètre.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">q2_duree_travail_annees</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">q2_duree_travail_annees</span>(<span class="token-variable">id_pilote</span> <span class="token-type">INT</span>)
<span class="token-keyword">RETURNS</span> <span class="token-type">INT</span>
<span class="token-keyword">READS SQL DATA</span>
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">duree</span> <span class="token-type">INT</span>;
	<span class="token-keyword">SELECT</span> <span class="token-function">TIMESTAMPDIFF</span>(YEAR, <span class="token-variable">datedebut</span>, <span class="token-function">CURDATE</span>()) <span class="token-keyword">INTO</span> <span class="token-variable">duree</span> 
    <span class="token-keyword">FROM</span> <span class="token-variable">Pilote</span>
    <span class="token-keyword">WHERE</span> <span class="token-variable">numpilote</span> <span class="token-operator">=</span> <span class="token-variable">id_pilote</span>;
	<span class="token-keyword">RETURN</span> <span class="token-variable">duree</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">SELECT</span> <span class="token-function">q2_duree_travail_annees</span>(<span class="token-number">3</span>); <span class="token-comment">-- Pilote Youssef, a commencé en 2002</span>
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

        <!-- Exercice 5 - vols -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 5 : Avions non affectés</h4>
            <p class="text-gray-700 mb-4">Écrire une fonction qui renvoie le nombre des avions qui ne sont pas du tout affectés à des vols.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">q3_avion_non_affectes</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">q3_avion_non_affectes</span>()
<span class="token-keyword">RETURNS</span> <span class="token-type">INT</span>
<span class="token-keyword">READS SQL DATA</span> 
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">total</span> <span class="token-type">INT</span>;
	<span class="token-keyword">SELECT</span> <span class="token-function">COUNT</span>(<span class="token-operator">*</span>) <span class="token-keyword">INTO</span> <span class="token-variable">total</span>
	<span class="token-keyword">FROM</span> <span class="token-variable">Avion</span> 
	<span class="token-keyword">WHERE</span> <span class="token-variable">numav</span> <span class="token-keyword">NOT IN</span> (<span class="token-keyword">SELECT DISTINCT</span> <span class="token-variable">numav</span> <span class="token-keyword">FROM</span> <span class="token-variable">Vol</span>);
    <span class="token-keyword">RETURN</span> <span class="token-variable">total</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">SELECT</span> <span class="token-function">q3_avion_non_affectes</span>(); <span class="token-comment">-- Avion 4 n'a pas de vol. Résultat: 1</span>
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

        <!-- Exercice 6 - vols -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 6 : Pilote le plus ancien sur un avion</h4>
            <p class="text-gray-700 mb-4">Écrire une fonction qui retourne le numéro du pilote le plus ancien (basé sur `datedebut`) ayant piloté un avion dont le numéro est passé en paramètre.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">q4_plus_ancien_pilote_sur_avion</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">q4_plus_ancien_pilote_sur_avion</span>(<span class="token-variable">id_avion</span> <span class="token-type">INT</span>)
<span class="token-keyword">RETURNS</span> <span class="token-type">INT</span>
<span class="token-keyword">READS SQL DATA</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">id_pilote</span> <span class="token-type">INT</span>;
	<span class="token-keyword">SELECT</span> <span class="token-variable">p</span>.<span class="token-variable">numpilote</span> <span class="token-keyword">INTO</span> <span class="token-variable">id_pilote</span>
	<span class="token-keyword">FROM</span> <span class="token-variable">Pilote</span> <span class="token-variable">p</span>
	<span class="token-keyword">JOIN</span> <span class="token-variable">Vol</span> <span class="token-variable">v</span> <span class="token-keyword">ON</span> <span class="token-variable">p</span>.<span class="token-variable">numpilote</span> <span class="token-operator">=</span> <span class="token-variable">v</span>.<span class="token-variable">numpil</span>
	<span class="token-keyword">WHERE</span> <span class="token-variable">v</span>.<span class="token-variable">numav</span> <span class="token-operator">=</span> <span class="token-variable">id_avion</span>
	<span class="token-keyword">ORDER BY</span> <span class="token-variable">p</span>.<span class="token-variable">datedebut</span> <span class="token-keyword">ASC</span> 
	<span class="token-keyword">LIMIT</span> <span class="token-number">1</span>;
    <span class="token-keyword">RETURN</span> <span class="token-variable">id_pilote</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Sur l'avion 1, pilotes 1, 2, 3 ont volé. Le plus ancien est Youssef (3) qui a commencé en 2002.</span>
<span class="token-keyword">SELECT</span> <span class="token-function">q4_plus_ancien_pilote_sur_avion</span>(<span class="token-number">1</span>); 
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

        <!-- Section Base de Données "Employés" -->
        <div class="bg-gray-100 p-4 rounded-lg border my-8">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercices sur la Base de Données `employes`</h4>
            <p class="text-gray-700 mb-4">Les exercices suivants nécessitent la base de données `employes`. Exécutez le script ci-dessous pour la mettre en place.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP DATABASE IF EXISTS</span> <span class="token-variable">employes</span>;
<span class="token-keyword">CREATE DATABASE</span> <span class="token-variable">employes</span> <span class="token-keyword">COLLATE</span> <span class="token-string">'utf8mb4_general_ci'</span>;
<span class="token-keyword">USE</span> <span class="token-variable">employes</span>;

<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">DEPARTEMENT</span> (<span class="token-variable">ID_DEP</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>, <span class="token-variable">NOM_DEP</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>), <span class="token-variable">Ville</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>));
<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">EMPLOYE</span> (<span class="token-variable">ID_EMP</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>, <span class="token-variable">NOM_EMP</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>), <span class="token-variable">PRENOM_EMP</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>), <span class="token-variable">SALAIRE</span> <span class="token-type">FLOAT</span>, <span class="token-variable">ID_DEP</span> <span class="token-type">INT</span>, <span class="token-keyword">CONSTRAINT</span> <span class="token-variable">fk_emp_dep</span> <span class="token-keyword">FOREIGN KEY</span> (<span class="token-variable">ID_DEP</span>) <span class="token-keyword">REFERENCES</span> <span class="token-variable">DEPARTEMENT</span>(<span class="token-variable">ID_DEP</span>));

<span class="token-keyword">INSERT INTO</span> <span class="token-variable">DEPARTEMENT</span>(<span class="token-variable">NOM_DEP</span>, <span class="token-variable">Ville</span>) <span class="token-keyword">VALUES</span> (<span class="token-string">'FINANCIER'</span>,<span class="token-string">'Tanger'</span>), (<span class="token-string">'Informatique'</span>,<span class="token-string">'Tétouan'</span>), (<span class="token-string">'Marketing'</span>,<span class="token-string">'Martil'</span>), (<span class="token-string">'GRH'</span>,<span class="token-string">'Mdiq'</span>);
<span class="token-keyword">INSERT INTO</span> <span class="token-variable">EMPLOYE</span>(<span class="token-variable">NOM_EMP</span>, <span class="token-variable">PRENOM_EMP</span>, <span class="token-variable">SALAIRE</span>, <span class="token-variable">ID_DEP</span>) <span class="token-keyword">VALUES</span> 
(<span class="token-string">'said'</span>,<span class="token-string">'said'</span>,<span class="token-number">8000</span>,<span class="token-number">1</span>), (<span class="token-string">'hassan'</span>,<span class="token-string">'hassan'</span>,<span class="token-number">8500</span>,<span class="token-number">1</span>), (<span class="token-string">'khalid'</span>,<span class="token-string">'khalid'</span>,<span class="token-number">7000</span>,<span class="token-number">2</span>),
(<span class="token-string">'souad'</span>,<span class="token-string">'souad'</span>,<span class="token-number">6500</span>,<span class="token-number">2</span>), (<span class="token-string">'Farida'</span>,<span class="token-string">'Farida'</span>,<span class="token-number">5000</span>,<span class="token-number">3</span>), (<span class="token-string">'Amal'</span>,<span class="token-string">'Amal'</span>,<span class="token-number">6000</span>,<span class="token-number">4</span>), (<span class="token-string">'Mohamed'</span>,<span class="token-string">'Mohamed'</span>,<span class="token-number">7000</span>,<span class="token-number">4</span>);
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
        
        <!-- Exercice 7 - employés -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 7 : Création de fonctions statistiques globales</h4>
            <p class="text-gray-700 mb-4">Créez quatre fonctions distinctes pour l'ensemble des employés : `nombre_total_employes()`, `somme_salaires()`, `salaire_minimum()` et `salaire_maximum()`. Puis, utilisez-les dans une seule requête pour afficher un tableau de bord global.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <h5 class="font-semibold text-gray-800 mb-2 mt-4">Les 4 fonctions</h5>
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-comment">-- 1. Nombre total d'employés</span>
<span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">nombre_total_employes</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">nombre_total_employes</span>() <span class="token-keyword">RETURNS INT READS SQL DATA</span>
<span class="token-keyword">BEGIN</span> <span class="token-keyword">RETURN</span> (<span class="token-keyword">SELECT</span> <span class="token-function">COUNT</span>(<span class="token-operator">*</span>) <span class="token-keyword">FROM</span> <span class="token-variable">EMPLOYE</span>); <span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- 2. Somme totale des salaires</span>
<span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">somme_salaires</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">somme_salaires</span>() <span class="token-keyword">RETURNS FLOAT READS SQL DATA</span>
<span class="token-keyword">BEGIN</span> <span class="token-keyword">RETURN</span> (<span class="token-keyword">SELECT</span> <span class="token-function">SUM</span>(<span class="token-variable">SALAIRE</span>) <span class="token-keyword">FROM</span> <span class="token-variable">EMPLOYE</span>); <span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- 3. Salaire minimum</span>
<span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">salaire_minimum</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">salaire_minimum</span>() <span class="token-keyword">RETURNS FLOAT READS SQL DATA</span>
<span class="token-keyword">BEGIN</span> <span class="token-keyword">RETURN</span> (<span class="token-keyword">SELECT</span> <span class="token-function">MIN</span>(<span class="token-variable">SALAIRE</span>) <span class="token-keyword">FROM</span> <span class="token-variable">EMPLOYE</span>); <span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- 4. Salaire maximum</span>
<span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">salaire_maximum</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">salaire_maximum</span>() <span class="token-keyword">RETURNS FLOAT READS SQL DATA</span>
<span class="token-keyword">BEGIN</span> <span class="token-keyword">RETURN</span> (<span class="token-keyword">SELECT</span> <span class="token-function">MAX</span>(<span class="token-variable">SALAIRE</span>) <span class="token-keyword">FROM</span> <span class="token-variable">EMPLOYE</span>); <span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
                <h5 class="font-semibold text-gray-800 mb-2 mt-6">Requête d'utilisation</h5>
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">SELECT</span> 
    <span class="token-function">nombre_total_employes</span>() <span class="token-keyword">AS</span> <span class="token-string">'Nb Employés'</span>,
    <span class="token-function">somme_salaires</span>() <span class="token-keyword">AS</span> <span class="token-string">'Masse Salariale'</span>,
    <span class="token-function">salaire_minimum</span>() <span class="token-keyword">AS</span> <span class="token-string">'Salaire Min'</span>,
    <span class="token-function">salaire_maximum</span>() <span class="token-keyword">AS</span> <span class="token-string">'Salaire Max'</span>;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

        <!-- Exercice 8 - employés -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 8 : Création de fonctions statistiques par département</h4>
            <p class="text-gray-700 mb-4">De la même manière, créez quatre fonctions paramétrées par le nom du département (`NOM_DEP`) pour obtenir les mêmes statistiques, mais cette fois-ci, spécifiques à un département. Puis, utilisez-les pour générer un rapport complet par département.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <h5 class="font-semibold text-gray-800 mb-2 mt-4">Les 4 fonctions paramétrées</h5>
                 <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-comment">-- 1. Nombre d'employés par département</span>
<span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">q6_nb_s</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">q6_nb_s</span>(<span class="token-variable">nom_d</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>)) <span class="token-keyword">RETURNS INT READS SQL DATA</span>
<span class="token-keyword">BEGIN</span> <span class="token-keyword">RETURN</span> (<span class="token-keyword">SELECT</span> <span class="token-function">COUNT</span>(<span class="token-operator">*</span>) <span class="token-keyword">FROM</span> <span class="token-variable">EMPLOYE</span> <span class="token-keyword">JOIN</span> <span class="token-variable">DEPARTEMENT</span> <span class="token-keyword">USING</span>(<span class="token-variable">ID_DEP</span>) <span class="token-keyword">WHERE</span> <span class="token-variable">NOM_DEP</span> <span class="token-operator">=</span> <span class="token-variable">nom_d</span>); <span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- 2. Somme des salaires par département</span>
<span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">q7_sum_s</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">q7_sum_s</span>(<span class="token-variable">nom_d</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>)) <span class="token-keyword">RETURNS FLOAT READS SQL DATA</span>
<span class="token-keyword">BEGIN</span> <span class="token-keyword">RETURN</span> (<span class="token-keyword">SELECT</span> <span class="token-function">SUM</span>(<span class="token-variable">SALAIRE</span>) <span class="token-keyword">FROM</span> <span class="token-variable">EMPLOYE</span> <span class="token-keyword">JOIN</span> <span class="token-variable">DEPARTEMENT</span> <span class="token-keyword">USING</span>(<span class="token-variable">ID_DEP</span>) <span class="token-keyword">WHERE</span> <span class="token-variable">NOM_DEP</span> <span class="token-operator">=</span> <span class="token-variable">nom_d</span>); <span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Et ainsi de suite pour MIN et MAX...</span>
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
                <h5 class="font-semibold text-gray-800 mb-2 mt-6">Rapport final par département</h5>
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-comment">-- Note: Les fonctions pour MIN(q8_min_s) et MAX(q9_max_s) doivent aussi être créées sur le même modèle.</span>
<span class="token-keyword">SELECT</span> 
    <span class="token-function">UPPER</span>(<span class="token-variable">NOM_DEP</span>) <span class="token-keyword">AS</span> <span class="token-string">'Département'</span>, 
    <span class="token-function">q6_nb_s</span>(<span class="token-variable">NOM_DEP</span>) <span class="token-keyword">AS</span> <span class="token-string">'Nb Employés'</span>, 
    <span class="token-function">q7_sum_s</span>(<span class="token-variable">NOM_DEP</span>) <span class="token-keyword">AS</span> <span class="token-string">'Masse Salariale'</span>, 
    <span class="token-function">q8_min_s</span>(<span class="token-variable">NOM_DEP</span>) <span class="token-keyword">AS</span> <span class="token-string">'Salaire Min'</span>, 
    <span class="token-function">q9_max_s</span>(<span class="token-variable">NOM_DEP</span>) <span class="token-keyword">AS</span> <span class="token-string">'Salaire Max'</span> 
<span class="token-keyword">FROM</span> 
    <span class="token-variable">DEPARTEMENT</span>;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

    </div>
</section>