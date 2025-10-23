<!-- =================================================================== -->
<!-- PARTIE 2 : LA PUISSANCE DU SQL PROCÉDURAL : LES PROCÉDURES STOCKÉES -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Partie 2 : La Puissance du SQL Procédural : Les Procédures Stockées</h2>

<!-- ========== CHAPITRE 4 : INTRODUCTION AUX PROCÉDURES STOCKÉES ========== -->
<section id="procedures-stockees" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 4 : Introduction aux Procédures Stockées</h3>
    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
        Alors que les fonctions sont excellentes pour retourner une valeur unique, les procédures stockées offrent une flexibilité et une puissance bien plus grandes. Une procédure stockée est un ensemble d'instructions SQL précompilées, stockées directement dans la base de données. Elles peuvent exécuter des actions complexes, manipuler des données (`INSERT`, `UPDATE`, `DELETE`), et même retourner plusieurs jeux de résultats.
    </p>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
            <h3 class="text-2xl font-bold mb-2">Performance Accrue</h3>
            <p class="text-gray-700">Le code est analysé et optimisé une seule fois lors de sa création. Les exécutions suivantes sont beaucoup plus rapides, ce qui réduit la charge sur le serveur et le trafic réseau.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
            <h3 class="text-2xl font-bold mb-2">Réutilisabilité et Maintenance</h3>
            <p class="text-gray-700">Une logique métier complexe peut être centralisée dans une procédure. Si les règles changent, il suffit de mettre à jour la procédure, sans avoir à modifier le code de toutes les applications qui l'utilisent.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-purple-500">
            <h3 class="text-2xl font-bold mb-2">Sécurité Renforcée</h3>
            <p class="text-gray-700">On peut accorder aux utilisateurs le droit d'exécuter une procédure stockée sans leur donner un accès direct aux tables sous-jacentes, prévenant ainsi les manipulations de données non désirées.</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border space-y-8 mt-8">
        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">4.1. Syntaxe de base et exécution</h4>
            <p class="text-gray-700 mb-4">La syntaxe est similaire à celle d'une fonction, mais sans la clause `RETURNS`. On utilise le mot-clé `CREATE PROCEDURE`. Pour l'exécuter, on utilise la commande `CALL` suivie du nom de la procédure.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-comment">-- On change le délimiteur</span>
<span class="token-keyword">DELIMITER</span> $$

<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">liste_complete</span>()
<span class="token-keyword">BEGIN</span>
    <span class="token-comment">-- Une procédure peut retourner plusieurs jeux de résultats</span>
	<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">cheval</span>;
    <span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">jockey</span>;
<span class="token-keyword">END</span>$$

<span class="token-comment">-- On restaure le délimiteur</span>
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- On appelle la procédure pour l'exécuter</span>
<span class="token-keyword">CALL</span> <span class="token-function">liste_complete</span>();</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>

<!-- ========== CHAPITRE 5 : PARAMÈTRES DES PROCÉDURES STOCKÉES ========== -->
<section id="parametres" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 5 : Paramètres des Procédures Stockées (`IN`, `OUT`)</h3>
    <p class="text-gray-700 mb-6">La véritable puissance des procédures vient de leur capacité à accepter des paramètres. MySQL définit trois modes de passage de paramètres :</p>
    <ul class="list-disc ml-6 text-gray-700 space-y-2 mb-8">
        <li><strong>`IN`</strong> : Le paramètre est en lecture seule. C'est une valeur que l'on passe à la procédure. C'est le mode par défaut.</li>
        <li><strong>`OUT`</strong> : Le paramètre est en écriture seule. La procédure peut lui assigner une valeur qui sera accessible à l'extérieur après l'appel.</li>
        <li><strong>`INOUT`</strong> : Un mélange des deux. On passe une valeur qui peut être lue et modifiée par la procédure.</li>
    </ul>

    <div class="space-y-8">
        <!-- Paramètres IN -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">5.1. Paramètres d'entrée `IN`</h4>
            <p class="text-gray-700 mb-4">C'est le cas le plus courant. On utilise la valeur passée pour filtrer une requête, effectuer un calcul ou insérer une donnée.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">proprio</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">proprio</span>(<span class="token-keyword">IN</span> <span class="token-variable">id_cheval_recherche</span> <span class="token-type">INT</span>)
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">SELECT</span> <span class="token-variable">NOM_PROPRIETAIRE</span>, <span class="token-variable">PRENOM_PRORIETAIRE</span> 
    <span class="token-keyword">FROM</span> <span class="token-variable">proprietaire</span> <span class="token-variable">p</span>
    <span class="token-keyword">JOIN</span> <span class="token-variable">cheval</span> <span class="token-variable">c</span> <span class="token-keyword">USING</span>(<span class="token-variable">id_proprietaire</span>) 
    <span class="token-keyword">WHERE</span> <span class="token-variable">id_cheval</span> <span class="token-operator">=</span> <span class="token-variable">id_cheval_recherche</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">CALL</span> <span class="token-function">proprio</span>(<span class="token-number">3</span>);</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <!-- Paramètres OUT -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">5.2. Paramètres de sortie `OUT`</h4>
            <p class="text-gray-700 mb-4">Les paramètres `OUT` sont le principal moyen pour une procédure de "retourner" des valeurs scalaires. Lors de l'appel, on doit fournir une variable de session (préfixée par `@`) pour stocker le résultat.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">somme</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">somme</span>(
    <span class="token-keyword">IN</span> <span class="token-variable">a</span> <span class="token-type">INT</span>, 
    <span class="token-keyword">IN</span> <span class="token-variable">b</span> <span class="token-type">INT</span>, 
    <span class="token-keyword">OUT</span> <span class="token-variable">resultat</span> <span class="token-type">INT</span>
)
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">SET</span> <span class="token-variable">resultat</span> <span class="token-operator">=</span> <span class="token-variable">a</span> <span class="token-operator">+</span> <span class="token-variable">b</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- On appelle la procédure en passant une variable @s pour recevoir le résultat</span>
<span class="token-keyword">CALL</span> <span class="token-function">somme</span>(<span class="token-number">3</span>, <span class="token-number">5</span>, @s);

<span class="token-comment">-- On affiche le contenu de la variable de session</span>
<span class="token-keyword">SELECT</span> @s <span class="token-keyword">AS</span> <span class="token-string">'Résultat de la somme'</span>;</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            
            <h5 class="font-semibold text-gray-800 mb-2 mt-6">Exemple avec plusieurs paramètres `OUT`</h5>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">calcules</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">calcules</span>(
    <span class="token-keyword">IN</span> <span class="token-variable">x</span> <span class="token-type">INT</span>, <span class="token-keyword">IN</span> <span class="token-variable">y</span> <span class="token-type">INT</span>, 
    <span class="token-keyword">OUT</span> <span class="token-variable">s</span> <span class="token-type">INT</span>, <span class="token-keyword">OUT</span> <span class="token-variable">m</span> <span class="token-type">INT</span>, <span class="token-keyword">OUT</span> <span class="token-variable">a</span> <span class="token-type">INT</span>, <span class="token-keyword">OUT</span> <span class="token-variable">d</span> <span class="token-type">FLOAT</span>
)
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">SET</span> <span class="token-variable">s</span> <span class="token-operator">=</span> <span class="token-variable">x</span> <span class="token-operator">-</span> <span class="token-variable">y</span>;
    <span class="token-keyword">SET</span> <span class="token-variable">m</span> <span class="token-operator">=</span> <span class="token-variable">x</span> <span class="token-operator">*</span> <span class="token-variable">y</span>;
    <span class="token-keyword">SET</span> <span class="token-variable">a</span> <span class="token-operator">=</span> <span class="token-variable">x</span> <span class="token-operator">+</span> <span class="token-variable">y</span>;
    <span class="token-keyword">SET</span> <span class="token-variable">d</span> <span class="token-operator">=</span> <span class="token-variable">x</span> <span class="token-operator">/</span> <span class="token-variable">y</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">CALL</span> <span class="token-function">calcules</span>(<span class="token-number">10</span>, <span class="token-number">5</span>, @soustraction, @multiplication, @addition, @division);
<span class="token-keyword">SELECT</span> @soustraction, @multiplication, @addition, @division;</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>

<!-- ========== CHAPITRE 6 : LOGIQUE APPLICATIVE ET MANIPULATION DE DONNÉES ========== -->
<section id="logique-procedures" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 6 : Logique Applicative et Manipulation de Données</h3>
    <p class="text-gray-700 mb-6">C'est ici que les procédures stockées révèlent tout leur potentiel. Elles permettent d'encapsuler une logique métier complexe, incluant des conditions, des boucles, et des opérations de modification de données, tout en garantissant l'atomicité et l'intégrité des opérations.</p>

    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">6.1. `SELECT ... INTO` pour récupérer une valeur</h4>
            <p class="text-gray-700 mb-4">Une des constructions les plus utiles dans une procédure est `SELECT ... INTO`. Elle permet d'exécuter une requête qui retourne une seule ligne et d'affecter les valeurs de ses colonnes à des variables locales. C'est le moyen le plus direct de récupérer une donnée de la base pour l'utiliser dans la logique de la procédure.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-comment">-- Cette procédure récupère le stock actuel dans une variable AVANT de le modifier</span>
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ex2_vente</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ex2_vente</span>(<span class="token-keyword">IN</span> <span class="token-variable">num_produit</span> <span class="token-type">INT</span>, <span class="token-keyword">IN</span> <span class="token-variable">qte_vendue</span> <span class="token-type">INT</span>)
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">stock_actuel</span> <span class="token-type">INT</span>;
    
    <span class="token-comment">-- On récupère la valeur du stock de la table et on la met dans notre variable</span>
	<span class="token-keyword">SELECT</span> <span class="token-variable">stock</span> <span class="token-keyword">INTO</span> <span class="token-variable">stock_actuel</span> <span class="token-keyword">FROM</span> <span class="token-variable">produit</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numproduit</span> <span class="token-operator">=</span> <span class="token-variable">num_produit</span>;
    
	<span class="token-keyword">IF</span> <span class="token-variable">stock_actuel</span> <span class="token-operator">&lt;</span> <span class="token-variable">qte_vendue</span> <span class="token-keyword">THEN</span> 
		<span class="token-keyword">SELECT</span> <span class="token-string">'Opération impossible : stock insuffisant'</span> <span class="token-keyword">AS</span> <span class="token-string">'message'</span>;
	<span class="token-keyword">ELSE</span>
		<span class="token-keyword">UPDATE</span> <span class="token-variable">produit</span> <span class="token-keyword">SET</span> <span class="token-variable">stock</span> <span class="token-operator">=</span> <span class="token-variable">stock_actuel</span> <span class="token-operator">-</span> <span class="token-variable">qte_vendue</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numproduit</span> <span class="token-operator">=</span> <span class="token-variable">num_produit</span>;
		<span class="token-keyword">IF</span> <span class="token-variable">stock_actuel</span> <span class="token-operator">-</span> <span class="token-variable">qte_vendue</span> <span class="token-operator">&lt;</span> <span class="token-number">10</span> <span class="token-keyword">THEN</span> 
			<span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>(<span class="token-string">'Besoin de réapprovisionnement, stock restant : '</span>, <span class="token-variable">stock_actuel</span> <span class="token-operator">-</span> <span class="token-variable">qte_vendue</span>) <span class="token-keyword">AS</span> <span class="token-string">'message'</span>;
		<span class="token-keyword">ELSE</span>
			<span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>(<span class="token-string">'Opération effectuée avec succès, stock restant : '</span>, <span class="token-variable">stock_actuel</span> <span class="token-operator">-</span> <span class="token-variable">qte_vendue</span>) <span class="token-keyword">AS</span> <span class="token-string">'message'</span>;
		<span class="token-keyword">END IF</span>;
	<span class="token-keyword">END IF</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">CALL</span> <span class="token-function">ex2_vente</span>(<span class="token-number">4</span>, <span class="token-number">15</span>); <span class="token-comment">-- Vente de 15 PC (stock 20) -> succès, stock restant 5 -> réapprovisionnement</span>
<span class="token-keyword">CALL</span> <span class="token-function">ex2_vente</span>(<span class="token-number">4</span>, <span class="token-number">10</span>); <span class="token-comment">-- Vente de 10 PC (stock 5) -> impossible</span>
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">6.2. Suppression de données</h4>
            <p class="text-gray-700 mb-4">Les procédures sont idéales pour sécuriser les opérations de suppression. On peut y ajouter des vérifications, de la journalisation, ou des suppressions en cascade complexes.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">q4_ps_supprimer_produit</span>;
<span class="token-keyword">DELIMITER</span> &&
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">q4_ps_supprimer_produit</span>(<span class="token-variable">num_prod</span> <span class="token-type">INT</span>)
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DELETE</span> <span class="token-keyword">FROM</span> <span class="token-variable">produit</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numProduit</span> <span class="token-operator">=</span> <span class="token-variable">num_prod</span>;
<span class="token-keyword">END</span>&&
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">CALL</span> <span class="token-function">q4_ps_supprimer_produit</span>(<span class="token-number">2</span>); <span class="token-comment">-- Supprime le produit 'chaise'</span>
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">produit</span>;
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>


<!-- ========== ATELIERS PRATIQUES DE LA PARTIE 2 ========== -->
<section id="exercices-partie2" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Ateliers Pratiques : Procédures Stockées</h3>
    <p class="text-gray-700 mb-8">Passons à la pratique. Les exercices suivants vous permettront de créer des procédures stockées pour des cas d'usage variés : affichage, modification de données, calculs et logique métier.</p>

    <!-- Base de Données "Produits" -->
    <div class="bg-gray-100 p-4 rounded-lg border my-8">
        <h4 class="text-xl font-bold text-gray-800 mb-2">Exercices 1 à 5 : Base de Données `produits`</h4>
        <p class="text-gray-700 mb-4">Les exercices suivants utilisent la base de données `produits`. Exécutez le script ci-dessous pour la mettre en place.</p>
        <div class="code-block-wrapper">
            <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP DATABASE IF EXISTS</span> <span class="token-variable">produits</span>;
<span class="token-keyword">CREATE DATABASE</span> <span class="token-variable">produits</span> <span class="token-keyword">COLLATE</span> <span class="token-string">utf8mb4_general_ci</span>;
<span class="token-keyword">USE</span> <span class="token-variable">produits</span>;

<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">Produit</span>(
    <span class="token-variable">numProduit</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>,
    <span class="token-variable">libelle</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>),
    <span class="token-variable">PU</span> <span class="token-type">FLOAT</span>,
    <span class="token-variable">stock</span> <span class="token-type">INT</span>
);

<span class="token-keyword">INSERT INTO</span> <span class="token-variable">produit</span> <span class="token-keyword">VALUES</span> 
(<span class="token-number">1</span>,<span class="token-string">'table'</span>,<span class="token-number">350</span>,<span class="token-number">100</span>), (<span class="token-number">2</span>,<span class="token-string">'chaise'</span>,<span class="token-number">100</span>,<span class="token-number">10</span>), (<span class="token-number">3</span>,<span class="token-string">'armoire'</span>,<span class="token-number">2350</span>,<span class="token-number">10</span>),
(<span class="token-number">4</span>,<span class="token-string">'pc'</span>,<span class="token-number">3500</span>,<span class="token-number">20</span>), (<span class="token-number">5</span>,<span class="token-string">'clavier'</span>,<span class="token-number">150</span>,<span class="token-number">200</span>), (<span class="token-number">6</span>,<span class="token-string">'souris'</span>,<span class="token-number">50</span>,<span class="token-number">200</span>),
(<span class="token-number">7</span>,<span class="token-string">'ecran'</span>,<span class="token-number">2350</span>,<span class="token-number">70</span>), (<span class="token-number">8</span>,<span class="token-string">'scanner'</span>,<span class="token-number">1350</span>,<span class="token-number">5</span>), (<span class="token-number">9</span>,<span class="token-string">'imprimante'</span>,<span class="token-number">950</span>,<span class="token-number">5</span>);
</code></pre>
            <button class="copy-btn">Copier</button>
        </div>
    </div>

    <div class="space-y-10">
        <!-- Exercice 1 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 1 : Procédures d'affichage simple</h4>
            <p class="text-gray-700 mb-4">
                1. Écrire une PS qui affiche tous les produits.<br>
                2. Écrire une PS qui affiche les libellés des produits dont le stock est inférieur à 10.<br>
                3. Écrire une PS qui admet en paramètre un numéro de produit et affiche un message contenant le libellé, le prix et le stock.<br>
                4. Écrire une PS qui permet de supprimer un produit en passant son numéro en paramètre.
            </p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-comment">-- 1. Afficher tous les produits</span>
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">q1_prdt</span>;
<span class="token-keyword">DELIMITER</span> &&
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">q1_prdt</span>()
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">produit</span>;
<span class="token-keyword">END</span>&&
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">q1_prdt</span>();

<span class="token-comment">-- 2. Afficher les produits avec stock faible</span>
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">q2_stockfaible</span>;
<span class="token-keyword">DELIMITER</span> &&
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">q2_stockfaible</span>()
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">SELECT</span> <span class="token-variable">libelle</span> <span class="token-keyword">FROM</span> <span class="token-variable">produit</span> <span class="token-keyword">WHERE</span> <span class="token-variable">stock</span> <span class="token-operator">&lt;</span> <span class="token-number">10</span>;
<span class="token-keyword">END</span>&&
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">q2_stockfaible</span>();

<span class="token-comment">-- 3. Afficher les détails d'un produit spécifique</span>
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">q3_aff_prod</span>;
<span class="token-keyword">DELIMITER</span> &&
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">q3_aff_prod</span>(<span class="token-variable">num_prod</span> <span class="token-type">INT</span>)
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>(<span class="token-string">'Libellé: '</span>, <span class="token-variable">libelle</span>, <span class="token-string">", PU: "</span>, <span class="token-variable">pu</span>, <span class="token-string">", Stock: "</span>, <span class="token-variable">stock</span>) <span class="token-keyword">AS</span> <span class="token-string">'message'</span>
	<span class="token-keyword">FROM</span> <span class="token-variable">produit</span>
	<span class="token-keyword">WHERE</span> <span class="token-variable">numProduit</span> <span class="token-operator">=</span> <span class="token-variable">num_prod</span>;
<span class="token-keyword">END</span>&&
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">q3_aff_prod</span>(<span class="token-number">3</span>);

<span class="token-comment">-- 4. Supprimer un produit (déjà vu au chapitre 6)</span>
<span class="token-comment">-- CALL q4_ps_supprimer_produit(9);</span>
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>
        
        <!-- Exercice 2 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 2 : Mettre à jour le stock après une vente</h4>
            <p class="text-gray-700 mb-4">Écrire une PS qui gère une vente. Elle reçoit en paramètre le numéro du produit et la quantité à vendre, puis retourne un message de statut : 'Opération impossible', 'Besoin de réapprovisionnement', ou 'Opération effectuée avec succès'.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-comment">-- La solution est la procédure ex2_vente détaillée au chapitre 6.</span>
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ex2_vente</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ex2_vente</span>(<span class="token-keyword">IN</span> <span class="token-variable">num</span> <span class="token-type">INT</span>, <span class="token-keyword">IN</span> <span class="token-variable">qte</span> <span class="token-type">INT</span>)
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">stock_reste</span> <span class="token-type">INT</span>;
	<span class="token-keyword">SELECT</span> <span class="token-variable">stock</span> <span class="token-keyword">INTO</span> <span class="token-variable">stock_reste</span> <span class="token-keyword">FROM</span> <span class="token-variable">produit</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numproduit</span><span class="token-operator">=</span><span class="token-variable">num</span>;
	<span class="token-keyword">IF</span> <span class="token-variable">stock_reste</span> <span class="token-operator">&lt;</span> <span class="token-variable">qte</span> <span class="token-keyword">THEN</span> 
		<span class="token-keyword">SELECT</span> <span class="token-string">'Opération impossible'</span> <span class="token-keyword">AS</span> <span class="token-string">'message'</span>;
	<span class="token-keyword">ELSE</span>
		<span class="token-keyword">UPDATE</span> <span class="token-variable">produit</span> <span class="token-keyword">SET</span> <span class="token-variable">stock</span><span class="token-operator">=</span> <span class="token-variable">stock_reste</span> <span class="token-operator">-</span> <span class="token-variable">qte</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numproduit</span><span class="token-operator">=</span><span class="token-variable">num</span>;
		<span class="token-keyword">IF</span> <span class="token-variable">stock_reste</span><span class="token-operator">-</span><span class="token-variable">qte</span> <span class="token-operator">&lt;</span><span class="token-number">10</span> <span class="token-keyword">THEN</span> 
			<span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>(<span class="token-string">'Besoin de réapprovisionnement, reste : '</span>, <span class="token-variable">stock_reste</span><span class="token-operator">-</span><span class="token-variable">qte</span>) <span class="token-keyword">AS</span> <span class="token-string">'message'</span>;
		<span class="token-keyword">ELSE</span>
			<span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>(<span class="token-string">'Opération effectuée avec succès, reste : '</span>, <span class="token-variable">stock_reste</span><span class="token-operator">-</span><span class="token-variable">qte</span>) <span class="token-keyword">AS</span> <span class="token-string">'message'</span>;
		<span class="token-keyword">END IF</span>;
	<span class="token-keyword">END IF</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">CALL</span> <span class="token-function">ex2_vente</span>(<span class="token-number">4</span>, <span class="token-number">11</span>);
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>
        
        <!-- Exercice 3 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 3 : Calculer le prix moyen</h4>
            <p class="text-gray-700 mb-4">Écrire une PS qui retourne le prix moyen des produits en utilisant un paramètre `OUT`.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ex3_moyenne</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ex3_moyenne</span>(<span class="token-keyword">OUT</span> <span class="token-variable">moyenne</span> <span class="token-type">FLOAT</span>)
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">SELECT</span> <span class="token-function">AVG</span>(<span class="token-variable">pu</span>) <span class="token-keyword">INTO</span> <span class="token-variable">moyenne</span> <span class="token-keyword">FROM</span> <span class="token-variable">produit</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">CALL</span> <span class="token-function">ex3_moyenne</span>(@m);
<span class="token-keyword">SELECT</span> @m <span class="token-keyword">AS</span> <span class="token-string">'Prix Unitaire Moyen'</span>;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>
        
        <!-- Exercice 4 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 4 : Calculer le factoriel d'un nombre</h4>
            <p class="text-gray-700 mb-4">Créer une procédure stockée qui accepte un entier et retourne son factoriel. Gérer le cas où le nombre est négatif.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ex4_factoriel</span>;
<span class="token-keyword">DELIMITER</span> //
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ex4_factoriel</span>(<span class="token-keyword">IN</span> <span class="token-variable">nb</span> <span class="token-type">INT</span>, <span class="token-keyword">OUT</span> <span class="token-variable">factoriel</span> <span class="token-type">BIGINT</span>)
<span class="token-keyword">BEGIN</span> 
    <span class="token-keyword">DECLARE</span> <span class="token-variable">i</span> <span class="token-type">INT</span> <span class="token-keyword">DEFAULT</span> <span class="token-number">1</span>;

    <span class="token-keyword">IF</span> <span class="token-variable">nb</span> <span class="token-operator">&lt;</span> <span class="token-number">0</span> <span class="token-keyword">THEN</span> 
		<span class="token-keyword">SET</span> <span class="token-variable">factoriel</span> <span class="token-operator">=</span> <span class="token-keyword">NULL</span>; <span class="token-comment">-- Factoriel n'est pas défini pour les négatifs</span>
	<span class="token-keyword">ELSE</span>
        <span class="token-keyword">SET</span> <span class="token-variable">factoriel</span> <span class="token-operator">=</span> <span class="token-number">1</span>;
		<span class="token-keyword">REPEAT</span> 
			<span class="token-keyword">SET</span> <span class="token-variable">factoriel</span> <span class="token-operator">=</span> <span class="token-variable">i</span> <span class="token-operator">*</span> <span class="token-variable">factoriel</span>;
            <span class="token-keyword">SET</span> <span class="token-variable">i</span> <span class="token-operator">=</span> <span class="token-variable">i</span> <span class="token-operator">+</span> <span class="token-number">1</span>;
        <span class="token-keyword">UNTIL</span> <span class="token-variable">i</span> <span class="token-operator">></span> <span class="token-variable">nb</span> <span class="token-keyword">END REPEAT</span>;
    <span class="token-keyword">END IF</span>;
<span class="token-keyword">END</span>//
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">CALL</span> <span class="token-function">ex4_factoriel</span>(<span class="token-number">5</span>, @f);
<span class="token-keyword">SELECT</span> @f; <span class="token-comment">-- Résultat: 120</span>
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>
        
        <!-- Exercice 5 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 5 : Une calculatrice universelle</h4>
            <p class="text-gray-700 mb-4">Créer une procédure qui reçoit 2 entiers, un opérateur sous forme de chaîne ('+', '-', '*', '/', '%') et retourne le résultat du calcul dans un paramètre `OUT`. Gérer les divisions par zéro.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">calcule</span>;
<span class="token-keyword">DELIMITER</span> $$ 
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">calcule</span>(<span class="token-keyword">IN</span> <span class="token-variable">a</span> <span class="token-type">INT</span>, <span class="token-keyword">IN</span> <span class="token-variable">b</span> <span class="token-type">INT</span>, <span class="token-keyword">IN</span> <span class="token-variable">op</span> <span class="token-type">CHAR</span>(<span class="token-number">1</span>), <span class="token-keyword">OUT</span> <span class="token-variable">resultat</span> <span class="token-type">FLOAT</span>)
<span class="token-keyword">BEGIN</span> 
	<span class="token-keyword">IF</span> <span class="token-variable">op</span> <span class="token-keyword">IN</span> (<span class="token-string">'/'</span>, <span class="token-string">'%'</span>) <span class="token-keyword">AND</span> <span class="token-variable">b</span><span class="token-operator">=</span><span class="token-number">0</span> <span class="token-keyword">THEN</span>
		<span class="token-keyword">SET</span> <span class="token-variable">resultat</span> <span class="token-operator">=</span> <span class="token-keyword">NULL</span>;
	<span class="token-keyword">ELSE</span>
		<span class="token-keyword">SET</span> <span class="token-variable">resultat</span> <span class="token-operator">=</span> <span class="token-keyword">CASE</span> <span class="token-variable">op</span> 
			<span class="token-keyword">WHEN</span> <span class="token-string">'+'</span> <span class="token-keyword">THEN</span>  <span class="token-variable">a</span><span class="token-operator">+</span><span class="token-variable">b</span> 
			<span class="token-keyword">WHEN</span> <span class="token-string">'/'</span> <span class="token-keyword">THEN</span>  <span class="token-variable">a</span><span class="token-operator">/</span><span class="token-variable">b</span> 
			<span class="token-keyword">WHEN</span> <span class="token-string">'*'</span> <span class="token-keyword">THEN</span>  <span class="token-variable">a</span><span class="token-operator">*</span><span class="token-variable">b</span> 
			<span class="token-keyword">WHEN</span> <span class="token-string">'-'</span> <span class="token-keyword">THEN</span>  <span class="token-variable">a</span><span class="token-operator">-</span><span class="token-variable">b</span> 
			<span class="token-keyword">WHEN</span> <span class="token-string">'%'</span> <span class="token-keyword">THEN</span>  <span class="token-variable">a</span><span class="token-operator">%</span><span class="token-variable">b</span> 
            <span class="token-keyword">ELSE</span> <span class="token-keyword">NULL</span>
		<span class="token-keyword">END</span>;
	<span class="token-keyword">END IF</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">CALL</span> <span class="token-function">calcule</span>(<span class="token-number">5</span>, <span class="token-number">0</span>, <span class="token-string">'/'</span>, @res);
<span class="token-keyword">SELECT</span> @res; <span class="token-comment">-- Résultat: NULL</span>
<span class="token-keyword">CALL</span> <span class="token-function">calcule</span>(<span class="token-number">10</span>, <span class="token-number">3</span>, <span class="token-string">'%'</span>, @res);
<span class="token-keyword">SELECT</span> @res; <span class="token-comment">-- Résultat: 1</span>
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

    </div>
    
    <!-- Base de Données "Cuisine" -->
    <div class="bg-gray-100 p-4 rounded-lg border my-8">
        <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 6 : Base de Données `cuisine`</h4>
        <p class="text-gray-700 mb-4">L'exercice suivant est un cas d'étude complet sur la gestion de recettes de cuisine. Exécutez le script ci-dessous pour mettre en place la base de données `cuisine`.</p>
        <div class="code-block-wrapper">
            <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP DATABASE IF EXISTS</span> <span class="token-variable">cuisine</span>;
<span class="token-keyword">CREATE DATABASE</span> <span class="token-variable">cuisine</span>;
<span class="token-keyword">USE</span> <span class="token-variable">cuisine</span>;

<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">Fournisseur</span> (<span class="token-variable">NumFou</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>, <span class="token-variable">RSFou</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>), <span class="token-variable">AdrFou</span> <span class="token-type">VARCHAR</span>(<span class="token-number">100</span>));
<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">Recettes</span> (<span class="token-variable">NumRec</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>, <span class="token-variable">NomRec</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>), <span class="token-variable">MethodePreparation</span> <span class="token-type">VARCHAR</span>(<span class="token-number">60</span>), <span class="token-variable">TempsPreparation</span> <span class="token-type">INT</span>);
<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">Ingredients</span> (<span class="token-variable">NumIng</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>, <span class="token-variable">NomIng</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>), <span class="token-variable">PUIng</span> <span class="token-type">FLOAT</span>, <span class="token-variable">UniteMesureIng</span> <span class="token-type">VARCHAR</span>(<span class="token-number">20</span>), <span class="token-variable">NumFou</span> <span class="token-type">INT</span>, <span class="token-keyword">CONSTRAINT</span> <span class="token-variable">fk_ing_fou</span> <span class="token-keyword">FOREIGN KEY</span> (<span class="token-variable">NumFou</span>) <span class="token-keyword">REFERENCES</span> <span class="token-variable">Fournisseur</span>(<span class="token-variable">NumFou</span>));
<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">Composition_Recette</span> (<span class="token-variable">NumRec</span> <span class="token-type">INT NOT NULL</span>, <span class="token-variable">NumIng</span> <span class="token-type">INT NOT NULL</span>, <span class="token-variable">QteUtilisee</span> <span class="token-type">FLOAT</span>, <span class="token-keyword">CONSTRAINT</span> <span class="token-variable">fk_cr_rec</span> <span class="token-keyword">FOREIGN KEY</span> (<span class="token-variable">NumRec</span>) <span class="token-keyword">REFERENCES</span> <span class="token-variable">Recettes</span>(<span class="token-variable">NumRec</span>), <span class="token-keyword">CONSTRAINT</span> <span class="token-variable">fk_cr_ing</span> <span class="token-keyword">FOREIGN KEY</span> (<span class="token-variable">NumIng</span>) <span class="token-keyword">REFERENCES</span> <span class="token-variable">Ingredients</span>(<span class="token-variable">NumIng</span>), <span class="token-keyword">CONSTRAINT</span> <span class="token-variable">pk_cr</span> <span class="token-keyword">PRIMARY KEY</span> (<span class="token-variable">NumIng</span>,<span class="token-variable">NumRec</span>));

<span class="token-comment">-- Les instructions INSERT sont dans le fichier original</span>
</code></pre>
            <button class="copy-btn">Copier</button>
        </div>
    </div>
    
    <div class="space-y-10">
        <!-- Exercice 6 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 6 : Cas d'étude - Gestion de recettes</h4>
            <p class="text-gray-700 mb-4">Créer une série de procédures stockées pour gérer et analyser les recettes de cuisine.</p>
                      <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS1 :</strong> Affiche la liste des ingrédients avec le nom de leur fournisseur.</p>
                   <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS2 :</strong> Affiche pour chaque recette son nom, le nombre d'ingrédients et son coût total.</p>
                <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS4 :</strong> Reçoit un numéro de recette et retourne son nom.</p>
                <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS6 :</strong> Reçoit un numéro de recette et affiche la liste de ses ingrédients (nom, quantité, montant).</p>
                <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS7 :</strong> Reçoit un numéro de recette et affiche une fiche complète en appelant les autres procédures.</p>
                <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS8 :</strong> Gère les fournisseurs : vérifie son existence et, s'il n'a pas d'ingrédients, le supprime.</p>
                <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS9 :</strong> Affiche une fiche de synthèse pour une recette donnée.</p>

            <button class="solution-toggle">Voir toutes les solutions</button>
            <div class="solution-content space-y-4">
                <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS1 :</strong> Affiche la liste des ingrédients avec le nom de leur fournisseur.</p>
    
                <div class="code-block-wrapper"><pre class="code-block"><code class="language-sql">
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ps1</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ps1</span>()
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">SELECT</span> <span class="token-variable">i</span>.<span class="token-variable">NumIng</span>, <span class="token-variable">i</span>.<span class="token-variable">NomIng</span>, <span class="token-variable">f</span>.<span class="token-variable">RSFou</span> <span class="token-keyword">FROM</span> <span class="token-variable">ingredients</span> <span class="token-variable">i</span> <span class="token-keyword">JOIN</span> <span class="token-variable">fournisseur</span> <span class="token-variable">f</span> <span class="token-keyword">USING</span> (<span class="token-variable">numfou</span>);
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">ps1</span>();
</code></pre><button class="copy-btn">Copier</button></div>
                
                <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS2 :</strong> Affiche pour chaque recette son nom, le nombre d'ingrédients et son coût total.</p>
 
                <div class="code-block-wrapper"><pre class="code-block"><code class="language-sql">
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ps2</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ps2</span>()
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">SELECT</span> <span class="token-variable">numrec</span>, <span class="token-variable">nomrec</span>, <span class="token-function">COUNT</span>(<span class="token-variable">cr</span>.<span class="token-variable">numing</span>) <span class="token-keyword">AS</span> <span class="token-string">'Nombre Ingredients'</span>, <span class="token-function">SUM</span>(<span class="token-variable">qteUtilisee</span> <span class="token-operator">*</span> <span class="token-variable">PUIng</span>) <span class="token-keyword">AS</span> <span class="token-string">'Coût Total'</span> 
	<span class="token-keyword">FROM</span> <span class="token-variable">recettes</span> <span class="token-variable">r</span> 
	<span class="token-keyword">LEFT JOIN</span> <span class="token-variable">composition_recette</span> <span class="token-variable">cr</span> <span class="token-keyword">USING</span> (<span class="token-variable">numrec</span>)
	<span class="token-keyword">LEFT JOIN</span> <span class="token-variable">ingredients</span> <span class="token-variable">i</span> <span class="token-keyword">USING</span> (<span class="token-variable">numing</span>)
	<span class="token-keyword">GROUP BY</span> <span class="token-variable">numrec</span>, <span class="token-variable">nomrec</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">ps2</span>();
</code></pre><button class="copy-btn">Copier</button></div>

                <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS4 :</strong> Reçoit un numéro de recette et retourne son nom.</p>

                <div class="code-block-wrapper"><pre class="code-block"><code class="language-sql">
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ps4</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ps4</span>(<span class="token-keyword">IN</span> <span class="token-variable">n</span> <span class="token-type">INT</span>, <span class="token-keyword">OUT</span> <span class="token-variable">nom</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>))
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">SELECT</span> <span class="token-variable">NomRec</span> <span class="token-keyword">INTO</span> <span class="token-variable">nom</span> <span class="token-keyword">FROM</span> <span class="token-variable">recettes</span> <span class="token-keyword">WHERE</span> <span class="token-variable">NumRec</span><span class="token-operator">=</span><span class="token-variable">n</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">ps4</span>(<span class="token-number">2</span>, @r);
<span class="token-keyword">SELECT</span> @r;
</code></pre><button class="copy-btn">Copier</button></div>
                
                <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS6 :</strong> Reçoit un numéro de recette et affiche la liste de ses ingrédients (nom, quantité, montant).</p>
 
                <div class="code-block-wrapper"><pre class="code-block"><code class="language-sql">
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ps6</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ps6</span>(<span class="token-keyword">IN</span> <span class="token-variable">num_recette</span> <span class="token-type">INT</span>)
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">SELECT</span> 
        <span class="token-variable">i</span>.<span class="token-variable">NomIng</span>,
        <span class="token-variable">cr</span>.<span class="token-variable">QteUtilisee</span>, 
        (<span class="token-variable">i</span>.<span class="token-variable">PUIng</span> <span class="token-operator">*</span> <span class="token-variable">cr</span>.<span class="token-variable">QteUtilisee</span>) <span class="token-keyword">AS</span> <span class="token-string">'montant'</span>
	<span class="token-keyword">FROM</span> <span class="token-variable">composition_recette</span> <span class="token-variable">cr</span>
	<span class="token-keyword">JOIN</span> <span class="token-variable">ingredients</span> <span class="token-variable">i</span> <span class="token-keyword">USING</span> (<span class="token-variable">numing</span>)
	<span class="token-keyword">WHERE</span> <span class="token-variable">cr</span>.<span class="token-variable">numrec</span> <span class="token-operator">=</span> <span class="token-variable">num_recette</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">ps6</span>(<span class="token-number">1</span>);
</code></pre><button class="copy-btn">Copier</button></div>
                
                <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS7 :</strong> Reçoit un numéro de recette et affiche une fiche complète en appelant les autres procédures.</p>
 
                <div class="code-block-wrapper"><pre class="code-block"><code class="language-sql">
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ps7</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ps7</span>(<span class="token-keyword">IN</span> <span class="token-variable">recette</span> <span class="token-type">INT</span>)
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">CALL</span> <span class="token-function">ps4</span>(<span class="token-variable">recette</span>, @nom);
	<span class="token-keyword">SELECT</span> @nom <span class="token-keyword">AS</span> <span class="token-string">'Nom de la Recette'</span>;
	<span class="token-keyword">CALL</span> <span class="token-function">ps6</span>(<span class="token-variable">recette</span>);
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">ps7</span>(<span class="token-number">3</span>);
</code></pre><button class="copy-btn">Copier</button></div>

                <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS8 :</strong> Gère les fournisseurs : vérifie son existence et, s'il n'a pas d'ingrédients, le supprime.</p>
 
                <div class="code-block-wrapper"><pre class="code-block"><code class="language-sql">
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ps8</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ps8</span>(<span class="token-keyword">IN</span> <span class="token-variable">f</span> <span class="token-type">INT</span>)
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">IF</span> <span class="token-keyword">NOT EXISTS</span> (<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">fournisseur</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numfou</span> <span class="token-operator">=</span> <span class="token-variable">f</span>) <span class="token-keyword">THEN</span>
		<span class="token-keyword">SELECT</span> <span class="token-string">'Aucun fournisseur ne porte ce numéro'</span> <span class="token-keyword">AS</span> <span class="token-string">'message'</span>;
	<span class="token-keyword">ELSE</span>
		<span class="token-keyword">IF</span> <span class="token-keyword">NOT EXISTS</span> (<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">ingredients</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numfou</span> <span class="token-operator">=</span> <span class="token-variable">f</span>) <span class="token-keyword">THEN</span>
			<span class="token-keyword">SELECT</span> <span class="token-string">'Ce fournisseur n\'a aucun ingrédient. Il sera supprimé'</span> <span class="token-keyword">AS</span> <span class="token-string">'message'</span>;
			<span class="token-keyword">DELETE</span> <span class="token-keyword">FROM</span> <span class="token-variable">fournisseur</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numfou</span> <span class="token-operator">=</span> <span class="token-variable">f</span>;
		<span class="token-keyword">ELSE</span>
			<span class="token-keyword">SELECT</span> <span class="token-variable">NumIng</span>, <span class="token-variable">NomIng</span> <span class="token-keyword">FROM</span> <span class="token-variable">ingredients</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numfou</span> <span class="token-operator">=</span> <span class="token-variable">f</span>;
		<span class="token-keyword">END IF</span>;
	<span class="token-keyword">END IF</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">INSERT INTO</span> <span class="token-variable">fournisseur</span> <span class="token-keyword">VALUES</span> (<span class="token-number">4</span>, <span class="token-string">'Testeur'</span>, <span class="token-string">'test'</span>);
<span class="token-keyword">CALL</span> <span class="token-function">ps8</span>(<span class="token-number">4</span>); <span class="token-comment">-- Va supprimer le fournisseur 4</span>
<span class="token-keyword">CALL</span> <span class="token-function">ps8</span>(<span class="token-number">1</span>); <span class="token-comment">-- Va lister les ingrédients du fournisseur 1</span>
</code></pre><button class="copy-btn">Copier</button></div>

                <p class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg"><strong>PS9 :</strong> Affiche une fiche de synthèse pour une recette donnée.</p>
                <div class="code-block-wrapper"><pre class="code-block"><code class="language-sql">
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ps9</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ps9</span>(<span class="token-keyword">IN</span> <span class="token-variable">num_recette</span> <span class="token-type">INT</span>)
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">v_nom_rec</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>);
    <span class="token-keyword">DECLARE</span> <span class="token-variable">v_temps</span> <span class="token-type">INT</span>;
    <span class="token-keyword">DECLARE</span> <span class="token-variable">v_methode</span> <span class="token-type">VARCHAR</span>(<span class="token-number">60</span>);
    <span class="token-keyword">DECLARE</span> <span class="token-variable">v_cout_total</span> <span class="token-type">FLOAT</span>;

    <span class="token-comment">-- Récupérer les informations de la recette</span>
    <span class="token-keyword">SELECT</span> <span class="token-variable">NomRec</span>, <span class="token-variable">TempsPreparation</span>, <span class="token-variable">MethodePreparation</span> 
    <span class="token-keyword">INTO</span> <span class="token-variable">v_nom_rec</span>, <span class="token-variable">v_temps</span>, <span class="token-variable">v_methode</span>
    <span class="token-keyword">FROM</span> <span class="token-variable">Recettes</span> <span class="token-keyword">WHERE</span> <span class="token-variable">NumRec</span> <span class="token-operator">=</span> <span class="token-variable">num_recette</span>;
    
    <span class="token-comment">-- Calculer le coût total de la recette</span>
    <span class="token-keyword">SELECT</span> <span class="token-function">SUM</span>(<span class="token-variable">cr</span>.<span class="token-variable">QteUtilisee</span> <span class="token-operator">*</span> <span class="token-variable">i</span>.<span class="token-variable">PUIng</span>) 
    <span class="token-keyword">INTO</span> <span class="token-variable">v_cout_total</span>
    <span class="token-keyword">FROM</span> <span class="token-variable">Composition_Recette</span> <span class="token-variable">cr</span>
    <span class="token-keyword">JOIN</span> <span class="token-variable">Ingredients</span> <span class="token-variable">i</span> <span class="token-keyword">ON</span> <span class="token-variable">cr</span>.<span class="token-variable">NumIng</span> <span class="token-operator">=</span> <span class="token-variable">i</span>.<span class="token-variable">NumIng</span>
    <span class="token-keyword">WHERE</span> <span class="token-variable">cr</span>.<span class="token-variable">NumRec</span> <span class="token-operator">=</span> <span class="token-variable">num_recette</span>;

    <span class="token-comment">-- Afficher les messages et les résultats</span>
    <span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>(<span class="token-string">'Recette : '</span>, <span class="token-variable">v_nom_rec</span>, <span class="token-string">', temps de préparation : '</span>, <span class="token-variable">v_temps</span>, <span class="token-string">' minutes'</span>) <span class="token-keyword">AS</span> <span class="token-string">'Information'</span>;
    
    <span class="token-comment">-- Afficher la liste des ingrédients</span>
    <span class="token-keyword">SELECT</span> <span class="token-variable">i</span>.<span class="token-variable">NomIng</span>, <span class="token-variable">cr</span>.<span class="token-variable">QteUtilisee</span>
    <span class="token-keyword">FROM</span> <span class="token-variable">Composition_Recette</span> <span class="token-variable">cr</span>
    <span class="token-keyword">JOIN</span> <span class="token-variable">Ingredients</span> <span class="token-variable">i</span> <span class="token-keyword">ON</span> <span class="token-variable">cr</span>.<span class="token-variable">NumIng</span> <span class="token-operator">=</span> <span class="token-variable">i</span>.<span class="token-variable">NumIng</span>
    <span class="token-keyword">WHERE</span> <span class="token-variable">cr</span>.<span class="token-variable">NumRec</span> <span class="token-operator">=</span> <span class="token-variable">num_recette</span>;

    <span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>(<span class="token-string">'Sa méthode de préparation est : '</span>, <span class="token-variable">v_methode</span>) <span class="token-keyword">AS</span> <span class="token-string">'Préparation'</span>;

    <span class="token-keyword">IF</span> <span class="token-variable">v_cout_total</span> <span class="token-operator">&lt;</span> <span class="token-number">50</span> <span class="token-keyword">THEN</span>
        <span class="token-keyword">SELECT</span> <span class="token-string">'Prix intéressant'</span> <span class="token-keyword">AS</span> <span class="token-string">'Avis sur le coût'</span>;
    <span class="token-keyword">ELSE</span>
        <span class="token-keyword">SELECT</span> <span class="token-string">'Prix élevé'</span> <span class="token-keyword">AS</span> <span class="token-string">'Avis sur le coût'</span>;
    <span class="token-keyword">END IF</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">CALL</span> <span class="token-function">ps9</span>(<span class="token-number">1</span>);
</code></pre><button class="copy-btn">Copier</button></div>

            </div>
        </div>
    </div>
</section>