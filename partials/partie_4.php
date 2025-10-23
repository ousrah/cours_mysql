<!-- =================================================================== -->
<!-- PARTIE 4 : FIABILITÉ ET ROBUSTESSE : TRANSACTIONS & EXCEPTIONS -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Partie 4 : Fiabilité et Robustesse : Transactions & Exceptions</h2>

<!-- ========== CHAPITRE 10 : GARANTIR LA FIABILITÉ : GESTION DES TRANSACTIONS ========== -->
<section id="transactions" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 10 : Garantir la Fiabilité : Gestion des Transactions</h3>
    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
        Dans de nombreuses applications, une opération métier n'est pas une seule requête SQL, mais un ensemble de plusieurs requêtes qui doivent toutes réussir ou toutes échouer ensemble. Un virement bancaire, par exemple, implique de débiter un compte ET de créditer un autre. Si l'une des deux actions échoue, l'autre doit être annulée. C'est le rôle des **transactions**.
    </p>
    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500 mb-8">
        <h3 class="text-2xl font-bold mb-2">Les Propriétés ACID</h3>
        <p class="text-gray-700">Une transaction garantit les propriétés ACID, qui sont le fondement des bases de données relationnelles fiables :</p>
        <ul class="list-disc ml-6 mt-4 text-gray-700 space-y-2">
            <li><strong>Atomicité :</strong> La transaction est une unité de travail indivisible. Soit toutes les opérations réussissent, soit aucune n'est appliquée.</li>
            <li><strong>Consistance :</strong> La transaction amène la base de données d'un état valide à un autre, en respectant toutes les règles et contraintes.</li>
            <li><strong>Isolation :</strong> Les transactions concurrentes n'interfèrent pas les unes avec les autres. Tout se passe comme si elles étaient exécutées en série.</li>
            <li><strong>Durabilité :</strong> Une fois qu'une transaction est validée (`COMMIT`), ses modifications sont permanentes, même en cas de panne système.</li>
        </ul>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-sm border space-y-6">
        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">10.1. Les Commandes Clés</h4>
            <ul class="list-disc ml-6 text-gray-700 space-y-2">
                <li><code>START TRANSACTION;</code> : Démarre une nouvelle transaction. Toutes les instructions suivantes font partie de ce bloc.</li>
                <li><code>COMMIT;</code> : Valide la transaction. Toutes les modifications effectuées depuis le `START TRANSACTION` deviennent permanentes.</li>
                <li><code>ROLLBACK;</code> : Annule la transaction. Toutes les modifications effectuées sont annulées, et la base de données revient à l'état où elle était au début de la transaction.</li>
            </ul>
        </div>
        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">10.2. Cas Pratique : Le Virement Bancaire</h4>
            <p class="text-gray-700 mb-4">Voici l'exemple parfait d'une procédure qui doit être transactionnelle. Si la mise à jour du deuxième compte échoue (par exemple à cause d'une contrainte), la première mise à jour doit être impérativement annulée.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP DATABASE IF EXISTS</span> <span class="token-variable">bank</span>;
<span class="token-keyword">CREATE DATABASE</span> <span class="token-variable">bank</span> <span class="token-keyword">COLLATE</span> <span class="token-string">utf8mb4_general_ci</span>;
<span class="token-keyword">USE</span> <span class="token-variable">bank</span>;

<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">account</span> (
    <span class="token-variable">account_number</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>) <span class="token-keyword">PRIMARY KEY</span>,
    <span class="token-variable">funds</span> <span class="token-type">DECIMAL</span>(<span class="token-number">8</span>,<span class="token-number">2</span>),
    <span class="token-keyword">CHECK</span> (<span class="token-variable">funds</span> <span class="token-operator">>=</span> <span class="token-number">0</span>)
);
<span class="token-keyword">INSERT INTO</span> <span class="token-variable">account</span> <span class="token-keyword">VALUES</span> (<span class="token-number">1</span>, <span class="token-number">10000</span>), (<span class="token-number">2</span>, <span class="token-number">10000</span>);

<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">transfert</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">transfert</span>(<span class="token-variable">acc1</span> <span class="token-type">INT</span>, <span class="token-variable">acc2</span> <span class="token-type">INT</span>, <span class="token-variable">amount</span> <span class="token-type">DOUBLE</span>)
<span class="token-keyword">BEGIN</span>
    <span class="token-comment">-- Si une erreur SQL se produit, le bloc BEGIN...END du handler est exécuté</span>
	<span class="token-keyword">DECLARE EXIT HANDLER FOR SQLEXCEPTION</span>
    <span class="token-keyword">BEGIN</span>
       <span class="token-keyword">ROLLBACK</span>; <span class="token-comment">-- On annule toutes les modifications de la transaction</span>
       <span class="token-keyword">SELECT</span> ("Opération annulée en raison d'une erreur.") <span class="token-keyword">AS</span> <span class="token-string">'Statut'</span>;
    <span class="token-keyword">END</span>;

	<span class="token-keyword">START TRANSACTION</span>;
		<span class="token-keyword">UPDATE</span> <span class="token-variable">account</span> <span class="token-keyword">SET</span> <span class="token-variable">funds</span> <span class="token-operator">=</span> <span class="token-variable">funds</span> <span class="token-operator">-</span> <span class="token-variable">amount</span> <span class="token-keyword">WHERE</span> <span class="token-variable">account_number</span> <span class="token-operator">=</span> <span class="token-variable">acc1</span>;
		<span class="token-keyword">UPDATE</span> <span class="token-variable">account</span> <span class="token-keyword">SET</span> <span class="token-variable">funds</span> <span class="token-operator">=</span> <span class="token-variable">funds</span> <span class="token-operator">+</span> <span class="token-variable">amount</span> <span class="token-keyword">WHERE</span> <span class="token-variable">account_number</span> <span class="token-operator">=</span> <span class="token-variable">acc2</span>;
	<span class="token-keyword">COMMIT</span>; <span class="token-comment">-- Si tout s'est bien passé, on valide.</span>
    <span class="token-keyword">SELECT</span> ("Transfert réussi.") <span class="token-keyword">AS</span> <span class="token-string">'Statut'</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Test 1: Transfert valide</span>
<span class="token-keyword">CALL</span> <span class="token-function">transfert</span>(<span class="token-number">1</span>, <span class="token-number">2</span>, <span class="token-number">1000</span>); <span class="token-comment">-- Succès</span>
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">account</span>; <span class="token-comment">-- Compte 1: 9000, Compte 2: 11000</span>

<span class="token-comment">-- Test 2: Transfert invalide (viole la contrainte CHECK funds >= 0)</span>
<span class="token-keyword">CALL</span> <span class="token-function">transfert</span>(<span class="token-number">1</span>, <span class="token-number">2</span>, <span class="token-number">20000</span>); <span class="token-comment">-- Échec</span>
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">account</span>; <span class="token-comment">-- Les fonds n'ont pas bougé. La transaction a été annulée.</span>
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>

<!-- ========== CHAPITRE 11 : MAÎTRISER LES ERREURS : GESTION DES EXCEPTIONS ========== -->
<section id="exceptions" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 11 : Maîtriser les Erreurs : Gestion des Exceptions</h3>
    <p class="text-gray-700 mb-6">Par défaut, lorsqu'une erreur SQL se produit dans une procédure, son exécution s'arrête brutalement. La gestion des exceptions nous permet d'intercepter ces erreurs pour exécuter un code alternatif, comme annuler une transaction, enregistrer un log, ou renvoyer un message personnalisé, rendant nos procédures beaucoup plus robustes.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">11.1. Le `HANDLER` : Intercepter les Erreurs</h4>
            <p class="text-gray-700 mb-4">La déclaration `DECLARE HANDLER` permet de définir un "gestionnaire" pour un type d'erreur spécifique. On peut intercepter des erreurs très générales ou très spécifiques.</p>
            
            <h5 class="font-semibold text-gray-800 mb-2 mt-6">A. Gestion Générique (`SQLEXCEPTION`)</h5>
            <p class="text-gray-700 mb-4">`SQLEXCEPTION` est un raccourci qui intercepte n'importe quelle erreur SQL.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">USE</span> <span class="token-variable">commerce</span>; <span class="token-comment">-- Assurez-vous d'avoir créé cette base</span>
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">add_product_generic_handler</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">add_product_generic_handler</span>(<span class="token-variable">name</span> <span class="token-type">VARCHAR</span>(<span class="token-number">100</span>), <span class="token-variable">price</span> <span class="token-type">DOUBLE</span>)
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE EXIT HANDLER FOR SQLEXCEPTION</span>
    <span class="token-keyword">BEGIN</span>
		<span class="token-keyword">SELECT</span> "Une erreur d'insertion s'est produite." <span class="token-keyword">AS</span> <span class="token-string">'erreur'</span>;
    <span class="token-keyword">END</span>;
	<span class="token-keyword">INSERT INTO</span> <span class="token-variable">produit</span> (<span class="token-variable">nom</span>, <span class="token-variable">prix</span>) <span class="token-keyword">VALUES</span> (<span class="token-variable">name</span>, <span class="token-variable">price</span>);
    <span class="token-keyword">SELECT</span> "Insertion effectuée avec succès." <span class="token-keyword">AS</span> <span class="token-string">'succes'</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">add_product_generic_handler</span>(<span class="token-string">'imprimante'</span>, <span class="operator">-</span><span class="token-number">100</span>);
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            
            <h5 class="font-semibold text-gray-800 mb-2 mt-6">B. Gestion par Code d'Erreur ou `SQLSTATE`</h5>
            <p class="text-gray-700 mb-4">Pour une gestion plus fine, on peut cibler un code d'erreur MySQL (ex: `1062` pour `Duplicate entry`) ou un `SQLSTATE` (ex: `'23000'` pour une violation de contrainte d'unicité). L'utilisation de `SQLSTATE` est souvent préférée car elle est standardisée.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">add_product_specific_handler</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">add_product_specific_handler</span>(<span class="token-variable">name</span> <span class="token-type">VARCHAR</span>(<span class="token-number">100</span>), <span class="token-variable">price</span> <span class="token-type">DOUBLE</span>)
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">msg</span> <span class="token-type">VARCHAR</span>(<span class="token-number">100</span>) <span class="token-keyword">DEFAULT</span> <span class="token-string">''</span>;
    <span class="token-keyword">BEGIN</span>
        <span class="token-comment">-- Handler pour la violation de la clé unique (code 1062)</span>
		<span class="token-keyword">DECLARE EXIT HANDLER FOR</span> <span class="token-number">1062</span> <span class="token-keyword">SET</span> <span class="token-variable">msg</span> <span class="token-operator">=</span> "Ce produit existe déjà.";
        <span class="token-comment">-- Handler pour une colonne NOT NULL (code 1048)</span>
		<span class="token-keyword">DECLARE EXIT HANDLER FOR</span> <span class="token-number">1048</span> <span class="token-keyword">SET</span> <span class="token-variable">msg</span> <span class="token-operator">=</span> "Le nom du produit ne peut pas être null.";
        <span class="token-comment">-- Handler pour une violation de contrainte CHECK (code 3819)</span>
		<span class="token-keyword">DECLARE EXIT HANDLER FOR</span> <span class="token-number">3819</span> <span class="token-keyword">SET</span> <span class="token-variable">msg</span> <span class="token-operator">=</span> "Le prix du produit ne peut pas être négatif.";
		<span class="token-keyword">INSERT INTO</span> <span class="token-variable">produit</span> (<span class="token-variable">nom</span>, <span class="token-variable">prix</span>) <span class="token-keyword">VALUES</span> (<span class="token-variable">name</span>, <span class="token-variable">price</span>);
	<span class="token-keyword">END</span>;
    <span class="token-keyword">IF</span> <span class="token-variable">msg</span> <span class="token-operator">!=</span> <span class="token-string">''</span> <span class="token-keyword">THEN</span>
		<span class="token-keyword">SELECT</span> <span class="token-variable">msg</span> <span class="token-keyword">AS</span> <span class="token-string">'erreur'</span>;
	<span class="token-keyword">ELSE</span>
        <span class="token-keyword">SELECT</span> "Insertion effectuée avec succès." <span class="token-keyword">AS</span> <span class="token-string">'succes'</span>;
	<span class="token-keyword">END IF</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">add_product_specific_handler</span>(<span class="token-string">'souris'</span>, <span class="token-number">150</span>); <span class="token-comment">-- En supposant que 'souris' existe déjà</span>
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            
            <h5 class="font-semibold text-gray-800 mb-2 mt-6">C. `GET DIAGNOSTICS` : Obtenir les détails de l'erreur</h5>
            <p class="text-gray-700 mb-4">Pour créer des logs ou des messages d'erreur vraiment précis, `GET DIAGNOSTICS` permet de récupérer des informations sur l'erreur qui vient de se produire.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">add_product_with_diagnostics</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">add_product_with_diagnostics</span>(<span class="token-variable">name</span> <span class="token-type">VARCHAR</span>(<span class="token-number">100</span>), <span class="token-variable">price</span> <span class="token-type">DOUBLE</span>)
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">numero_erreur</span> <span class="token-type">INT</span>;
    <span class="token-keyword">DECLARE</span> <span class="token-variable">message_original</span> <span class="token-type">VARCHAR</span>(<span class="token-number">255</span>);
    
    <span class="token-keyword">DECLARE EXIT HANDLER FOR SQLEXCEPTION</span>
    <span class="token-keyword">BEGIN</span>
        <span class="token-keyword">GET DIAGNOSTICS CONDITION</span> <span class="token-number">1</span>
            <span class="token-variable">numero_erreur</span> <span class="token-operator">=</span> MYSQL_ERRNO,
            <span class="token-variable">message_original</span> <span class="token-operator">=</span> MESSAGE_TEXT;
        <span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>("Erreur N°", <span class="token-variable">numero_erreur</span>, " : ", <span class="token-variable">message_original</span>) <span class="token-keyword">AS</span> "Erreur détaillée";
    <span class="token-keyword">END</span>;
    
    <span class="token-keyword">INSERT INTO</span> <span class="token-variable">produit</span> (<span class="token-variable">nom</span>, <span class="token-variable">prix</span>) <span class="token-keyword">VALUES</span> (<span class="token-variable">name</span>, <span class="token-variable">price</span>);
    <span class="token-keyword">SELECT</span> "Insertion réussie." <span class="token-keyword">AS</span> "Succès";
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">add_product_with_diagnostics</span>(<span class="keyword">NULL</span>, <span class="token-number">150</span>);
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">11.2. `SIGNAL` : Déclencher ses Propres Erreurs</h4>
            <p class="text-gray-700 mb-4">Parfois, une erreur n'est pas une erreur SQL, mais une violation d'une règle métier (ex: "un client VIP ne peut pas avoir un solde négatif"). `SIGNAL` nous permet de déclencher manuellement une erreur SQL, qui peut ensuite être interceptée par un `HANDLER`.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">diviser</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">diviser</span>(<span class="token-variable">a</span> <span class="token-type">INT</span>, <span class="token-variable">b</span> <span class="token-type">INT</span>, <span class="token-keyword">OUT</span> <span class="token-variable">r</span> <span class="token-type">DOUBLE</span>)
<span class="token-keyword">BEGIN</span>
	<span class="token-comment">-- 1. On déclare une condition nommée pour notre erreur métier</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">erreur_division</span> <span class="token-keyword">CONDITION FOR</span> SQLSTATE <span class="token-string">'45000'</span>;
    
    <span class="token-comment">-- 3. Le handler intercepte notre erreur personnalisée</span>
    <span class="token-keyword">DECLARE EXIT HANDLER FOR</span> <span class="token-variable">erreur_division</span> 
    <span class="token-keyword">BEGIN</span>
        <span class="token-keyword">SELECT</span> "Problème de division par zéro détecté." <span class="token-keyword">AS</span> <span class="token-string">'Erreur'</span>;
    <span class="token-keyword">END</span>;

	<span class="token-keyword">IF</span> <span class="token-variable">b</span> <span class="token-operator">=</span> <span class="token-number">0</span> <span class="token-keyword">THEN</span>
		<span class="token-comment">-- 2. On déclenche l'erreur si notre condition métier est violée</span>
		<span class="token-keyword">SIGNAL</span> <span class="token-variable">erreur_division</span> <span class="token-keyword">SET</span> MESSAGE_TEXT <span class="token-operator">=</span> 'Division par zéro interdite';
	<span class="token-keyword">ELSE</span>
		<span class="token-keyword">SET</span> <span class="token-variable">r</span> <span class="token-operator">=</span> <span class="token-variable">a</span> <span class="token-operator">/</span> <span class="token-variable">b</span>;
	<span class="token-keyword">END IF</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">CALL</span> <span class="token-function">diviser</span>(<span class="token-number">10</span>, <span class="token-number">0</span>, @r);
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>


<!-- ========== ATELIERS PRATIQUES DE LA PARTIE 4 ========== -->
<section id="exercices-partie4" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Ateliers Pratiques : Transactions et Exceptions</h3>
    <p class="text-gray-700 mb-8">Il est temps de combiner ces deux concepts pour construire des procédures stockées véritablement robustes et sécurisées.</p>
    
    <div class="space-y-10">
        <!-- Exercice 1 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 1 : Transfert de chaises sécurisé</h4>
            <p class="text-gray-700 mb-4">En utilisant la base de données `salles`, créer une procédure stockée qui gère le transfert de chaises d'une salle à une autre. L'opération doit être transactionnelle et échouer proprement si une des contraintes (nombre de chaises entre 20 et 30) est violée.</p>
            <div class="bg-gray-100 p-4 rounded-lg border my-4">
                <p class="text-gray-700 mb-4">Script de mise en place de la base `salles` :</p>
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP DATABASE IF EXISTS</span> <span class="token-variable">salles</span>;
<span class="token-keyword">CREATE DATABASE</span> <span class="token-variable">salles</span> <span class="token-keyword">COLLATE</span> <span class="token-string">utf8mb4_general_ci</span>;
<span class="token-keyword">USE</span> <span class="token-variable">salles</span>;

<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">Salle</span> (<span class="token-variable">NumSalle</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>, <span class="token-variable">Etage</span> <span class="token-type">INT</span>, <span class="token-variable">NombreChaises</span> <span class="token-type">INT</span>, 
<span class="token-keyword">CONSTRAINT</span> <span class="token-variable">chk_NombreChaises</span> <span class="token-keyword">CHECK</span> (<span class="token-variable">NombreChaises</span> <span class="token-keyword">BETWEEN</span> <span class="token-number">20</span> <span class="token-keyword">AND</span> <span class="token-number">30</span>));

<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">Transfert</span> (<span class="token-variable">NumSalleOrigine</span> <span class="token-type">INT</span>, <span class="token-variable">NumSalleDestination</span> <span class="token-type">INT</span>, <span class="token-variable">DateTransfert</span> <span class="token-type">DATE</span>, <span class="token-variable">NbChaisesTransferees</span> <span class="token-type">INT</span>,
<span class="token-keyword">CONSTRAINT</span> <span class="token-variable">fk_NumSalleOrigine</span> <span class="token-keyword">FOREIGN KEY</span> (<span class="token-variable">NumSalleOrigine</span>) <span class="token-keyword">REFERENCES</span> <span class="token-variable">salle</span>(<span class="token-variable">numsalle</span>),
<span class="token-keyword">CONSTRAINT</span> <span class="token-variable">fk_NumSalleDestination</span> <span class="token-keyword">FOREIGN KEY</span> (<span class="token-variable">NumSalleDestination</span>) <span class="token-keyword">REFERENCES</span> <span class="token-variable">salle</span>(<span class="token-variable">numsalle</span>));

<span class="token-keyword">INSERT INTO</span> <span class="token-variable">salle</span> <span class="token-keyword">VALUES</span> (<span class="token-number">1</span>,<span class="token-number">1</span>,<span class="token-number">24</span>), (<span class="token-number">2</span>,<span class="token-number">1</span>,<span class="token-number">26</span>), (<span class="token-number">3</span>,<span class="token-number">1</span>,<span class="token-number">26</span>), (<span class="token-number">4</span>,<span class="token-number">2</span>,<span class="token-number">28</span>);
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ps_transfert_chaises</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ps_transfert_chaises</span>(<span class="token-variable">salle_origine</span> <span class="token-type">INT</span>, <span class="token-variable">salle_dest</span> <span class="token-type">INT</span>, <span class="token-variable">nb_chaises</span> <span class="token-type">INT</span>)
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">DECLARE EXIT HANDLER FOR SQLEXCEPTION</span>
		<span class="token-keyword">BEGIN</span>
			<span class="token-keyword">ROLLBACK</span>;
            <span class="token-keyword">SELECT</span> "Impossible d’effectuer le transfert : une contrainte a été violée." <span class="token-keyword">AS</span> <span class="token-string">'message'</span>;
		<span class="token-keyword">END</span>;

	<span class="token-keyword">START TRANSACTION</span>;
        <span class="token-keyword">UPDATE</span> <span class="token-variable">salle</span> <span class="token-keyword">SET</span> <span class="token-variable">nombreChaises</span> <span class="token-operator">=</span> <span class="token-variable">nombreChaises</span> <span class="token-operator">-</span> <span class="token-variable">nb_chaises</span> <span class="token-keyword">WHERE</span> <span class="token-variable">NumSalle</span> <span class="token-operator">=</span> <span class="token-variable">salle_origine</span>;
        <span class="token-keyword">UPDATE</span> <span class="token-variable">salle</span> <span class="token-keyword">SET</span> <span class="token-variable">nombreChaises</span> <span class="token-operator">=</span> <span class="token-variable">nombreChaises</span> <span class="token-operator">+</span> <span class="token-variable">nb_chaises</span> <span class="token-keyword">WHERE</span> <span class="token-variable">NumSalle</span> <span class="token-operator">=</span> <span class="token-variable">salle_dest</span>;
        <span class="token-keyword">INSERT INTO</span> <span class="token-variable">transfert</span> <span class="token-keyword">VALUES</span> (<span class="token-variable">salle_origine</span>, <span class="token-variable">salle_dest</span>, <span class="token-function">CURDATE</span>(), <span class="token-variable">nb_chaises</span>);
	<span class="token-keyword">COMMIT</span>;
    <span class="token-keyword">SELECT</span> "Transfert effectué avec succès." <span class="token-keyword">AS</span> <span class="token-string">'message'</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Test 1 : Transfert valide (26-4=22 ; 26+4=30. OK)</span>
<span class="token-keyword">CALL</span> <span class="token-function">ps_transfert_chaises</span>(<span class="token-number">2</span>, <span class="token-number">3</span>, <span class="token-number">4</span>);
<span class="token-comment">-- Test 2 : Transfert invalide (22-4=18. NON OK)</span>
<span class="token-keyword">CALL</span> <span class="token-function">ps_transfert_chaises</span>(<span class="token-number">2</span>, <span class="token-number">3</span>, <span class="token-number">4</span>);
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

        <!-- Exercice 2 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 2 : Procédure d'insertion de produit sécurisée</h4>
            <p class="text-gray-700 mb-4">Créer une procédure `ps_safe_add_product` pour la base `commerce`. Cette procédure doit accepter un nom et un prix, et retourner un message clair au format JSON indiquant le succès ou l'échec de l'opération, en gérant spécifiquement les erreurs de nom dupliqué, de nom `NULL` et de prix négatif.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">USE</span> <span class="token-variable">commerce</span>;
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">ps_safe_add_product</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">ps_safe_add_product</span>(<span class="token-variable">p_name</span> <span class="token-type">VARCHAR</span>(<span class="token-number">100</span>), <span class="token-variable">p_price</span> <span class="token-type">DOUBLE</span>)
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">err_msg</span> <span class="token-type">TEXT</span>;
    <span class="token-keyword">DECLARE</span> <span class="token-variable">err_code</span> <span class="token-type">INT</span>;
    
    <span class="token-keyword">DECLARE EXIT HANDLER FOR SQLEXCEPTION</span>
    <span class="token-keyword">BEGIN</span>
        <span class="token-keyword">GET DIAGNOSTICS CONDITION</span> <span class="token-number">1</span> <span class="token-variable">err_code</span> <span class="token-operator">=</span> MYSQL_ERRNO, <span class="token-variable">err_msg</span> <span class="token-operator">=</span> MESSAGE_TEXT;
        <span class="token-keyword">SELECT</span> <span class="token-function">JSON_OBJECT</span>(
            'statut', 'echec',
            'erreur_code', <span class="token-variable">err_code</span>,
            'message', <span class="token-variable">err_msg</span>
        ) <span class="token-keyword">AS</span> <span class="token-string">'resultat'</span>;
    <span class="token-keyword">END</span>;
    
    <span class="token-keyword">INSERT INTO</span> <span class="token-variable">produit</span>(<span class="token-variable">nom</span>, <span class="token-variable">prix</span>) <span class="token-keyword">VALUES</span> (<span class="token-variable">p_name</span>, <span class="token-variable">p_price</span>);
    
    <span class="token-keyword">SELECT</span> <span class="token-function">JSON_OBJECT</span>(
        'statut', 'succes',
        'id_produit_insere', <span class="token-function">LAST_INSERT_ID</span>()
    ) <span class="token-keyword">AS</span> <span class="token-string">'resultat'</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Test 1: Succès</span>
<span class="token-keyword">CALL</span> <span class="token-function">ps_safe_add_product</span>(<span class="token-string">'Ecran 4K'</span>, <span class="token-number">4500</span>);
<span class="token-comment">-- Test 2: Nom dupliqué</span>
<span class="token-keyword">CALL</span> <span class="token-function">ps_safe_add_product</span>(<span class="token-string">'Ecran 4K'</span>, <span class="token-number">4600</span>);
<span class="token-comment">-- Test 3: Prix négatif</span>
<span class="token-keyword">CALL</span> <span class="token-function">ps_safe_add_product</span>(<span class="token-string">'Webcam'</span>, <span class="operator">-</span><span class="token-number">50</span>);
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

        <!-- Exercice 3 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 3 : Virement bancaire ultra-sécurisé</h4>
            <p class="text-gray-700 mb-4">Améliorer la procédure de virement bancaire pour la base `bank`. La procédure doit non seulement être transactionnelle, mais aussi vérifier en amont si les comptes existent et si les fonds sont suffisants. Elle doit retourner des messages d'erreur métier clairs et spécifiques pour chaque cas d'échec.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">USE</span> <span class="token-variable">bank</span>;
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">secure_transfert</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">secure_transfert</span>(<span class="token-variable">acc_from</span> <span class="token-type">INT</span>, <span class="token-variable">acc_to</span> <span class="token-type">INT</span>, <span class="token-variable">amount</span> <span class="token-type">DECIMAL</span>(<span class="token-number">8</span>,<span class="token-number">2</span>))
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">funds_from</span> <span class="token-type">DECIMAL</span>(<span class="token-number">8</span>,<span class="token-number">2</span>);
    <span class="token-keyword">DECLARE</span> <span class="token-variable">acc_from_exists</span>, <span class="token-variable">acc_to_exists</span> <span class="token-type">INT</span>;
    
    <span class="token-comment">-- Déclarer des conditions pour nos erreurs métier</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">err_no_funds</span> <span class="token-keyword">CONDITION FOR</span> SQLSTATE <span class="token-string">'45000'</span>;
    <span class="token-keyword">DECLARE</span> <span class="token-variable">err_acc_not_found</span> <span class="token-keyword">CONDITION FOR</span> SQLSTATE <span class="token-string">'45001'</span>;

    <span class="token-keyword">DECLARE EXIT HANDLER FOR SQLEXCEPTION</span> <span class="token-keyword">BEGIN</span> <span class="token-keyword">ROLLBACK</span>; <span class="token-keyword">SELECT</span> 'Erreur SQL inattendue. Transaction annulée.' <span class="token-keyword">AS</span> <span class="token-string">'statut'</span>; <span class="token-keyword">END</span>;

    <span class="token-keyword">START TRANSACTION</span>;
    
    <span class="token-comment">-- Vérifier l'existence des comptes</span>
    <span class="token-keyword">SELECT</span> <span class="token-function">COUNT</span>(<span class="token-operator">*</span>) <span class="token-keyword">INTO</span> <span class="token-variable">acc_from_exists</span> <span class="token-keyword">FROM</span> <span class="token-variable">account</span> <span class="token-keyword">WHERE</span> <span class="token-variable">account_number</span> <span class="token-operator">=</span> <span class="token-variable">acc_from</span>;
    <span class="token-keyword">SELECT</span> <span class="token-function">COUNT</span>(<span class="token-operator">*</span>) <span class="token-keyword">INTO</span> <span class="token-variable">acc_to_exists</span> <span class="token-keyword">FROM</span> <span class="token-variable">account</span> <span class="token-keyword">WHERE</span> <span class="token-variable">account_number</span> <span class="token-operator">=</span> <span class="token-variable">acc_to</span>;

    <span class="token-keyword">IF</span> <span class="token-variable">acc_from_exists</span> <span class="token-operator">=</span> <span class="token-number">0</span> <span class="token-keyword">OR</span> <span class="token-variable">acc_to_exists</span> <span class="token-operator">=</span> <span class="token-number">0</span> <span class="token-keyword">THEN</span>
        <span class="token-keyword">SIGNAL</span> <span class="token-variable">err_acc_not_found</span> <span class="token-keyword">SET</span> MESSAGE_TEXT <span class="token-operator">=</span> 'Compte source ou destination introuvable.';
    <span class="token-keyword">END IF</span>;
    
    <span class="token-comment">-- Vérifier les fonds (en bloquant la ligne pour éviter les lectures concurrentes)</span>
    <span class="token-keyword">SELECT</span> <span class="token-variable">funds</span> <span class="token-keyword">INTO</span> <span class="token-variable">funds_from</span> <span class="token-keyword">FROM</span> <span class="token-variable">account</span> <span class="token-keyword">WHERE</span> <span class="token-variable">account_number</span> <span class="token-operator">=</span> <span class="token-variable">acc_from</span> <span class="token-keyword">FOR UPDATE</span>;
    <span class="token-keyword">IF</span> <span class="token-variable">funds_from</span> <span class="token-operator">&lt;</span> <span class="token-variable">amount</span> <span class="token-keyword">THEN</span>
        <span class="token-keyword">SIGNAL</span> <span class="token-variable">err_no_funds</span> <span class="token-keyword">SET</span> MESSAGE_TEXT <span class="token-operator">=</span> 'Fonds insuffisants sur le compte source.';
    <span class="token-keyword">END IF</span>;
    
    <span class="token-comment">-- Si tout est OK, on procède</span>
    <span class="token-keyword">UPDATE</span> <span class="token-variable">account</span> <span class="token-keyword">SET</span> <span class="token-variable">funds</span> <span class="token-operator">=</span> <span class="token-variable">funds</span> <span class="token-operator">-</span> <span class="token-variable">amount</span> <span class="token-keyword">WHERE</span> <span class="token-variable">account_number</span> <span class="token-operator">=</span> <span class="token-variable">acc_from</span>;
    <span class="token-keyword">UPDATE</span> <span class="token-variable">account</span> <span class="token-keyword">SET</span> <span class="token-variable">funds</span> <span class="token-operator">=</span> <span class="token-variable">funds</span> <span class="token-operator">+</span> <span class="token-variable">amount</span> <span class="token-keyword">WHERE</span> <span class="token-variable">account_number</span> <span class="token-operator">=</span> <span class="token-variable">acc_to</span>;
    
    <span class="token-keyword">COMMIT</span>;
    <span class="token-keyword">SELECT</span> 'Transfert réussi.' <span class="token-keyword">AS</span> <span class="token-string">'statut'</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Test 1: Compte inexistant</span>
<span class="token-keyword">CALL</span> <span class="token-function">secure_transfert</span>(<span class="token-number">1</span>, <span class="token-number">99</span>, <span class="token-number">100</span>);
<span class="token-comment">-- Test 2: Fonds insuffisants</span>
<span class="token-keyword">CALL</span> <span class="token-function">secure_transfert</span>(<span class="token-number">1</span>, <span class="token-number">2</span>, <span class="token-number">50000</span>);
<span class="token-comment">-- Test 3: Succès</span>
<span class="token-keyword">CALL</span> <span class="token-function">secure_transfert</span>(<span class="token-number">2</span>, <span class="token-number">1</span>, <span class="token-number">500</span>);
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>
    </div>
</section>