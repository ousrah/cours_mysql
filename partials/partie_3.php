<!-- =================================================================== -->
<!-- PARTIE 3 : L'AUTOMATISATION AVEC LES DÉCLENCHEURS (TRIGGERS) -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Partie 3 : L'Automatisation avec les Déclencheurs (Triggers)</h2>

<!-- ========== CHAPITRE 7 : INTRODUCTION AUX DÉCLENCHEURS ========== -->
<section id="triggers-intro" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 7 : Introduction aux Déclencheurs</h3>
    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
        Imaginez pouvoir exécuter un bloc de code automatiquement chaque fois qu'un événement spécifique se produit sur une table, comme l'ajout, la modification ou la suppression d'une ligne. C'est exactement ce que permettent les **déclencheurs**, ou **triggers**. Ils sont la clé pour garantir l'intégrité des données et automatiser des règles métier complexes directement au niveau de la base de données.
    </p>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
            <h3 class="text-2xl font-bold mb-2">Intégrité des Données</h3>
            <p class="text-gray-700">Les triggers peuvent imposer des règles de validation plus complexes que les contraintes standards, assurant que les données restent toujours cohérentes.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
            <h3 class="text-2xl font-bold mb-2">Automatisation</h3>
            <p class="text-gray-700">Ils permettent d'automatiser des actions en cascade. Par exemple, mettre à jour un stock après une vente, calculer un champ dérivé ou archiver une ligne supprimée.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-purple-500">
            <h3 class="text-2xl font-bold mb-2">Journalisation (Audit)</h3>
            <p class="text-gray-700">Un trigger est l'outil parfait pour tracer les modifications sur des données sensibles en enregistrant qui a modifié quoi et quand dans une table d'audit.</p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-sm border mt-8 space-y-6">
        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">7.1. Syntaxe et Événements</h4>
            <p class="text-gray-700 mb-4">Un trigger est associé à une table et se déclenche à un moment précis pour un événement donné :</p>
            <ul class="list-disc ml-6 text-gray-600 text-sm space-y-1 mb-4">
                <li><strong>Moment du déclenchement :</strong> `BEFORE` (avant l'opération) ou `AFTER` (après l'opération).</li>
                <li><strong>Événement :</strong> `INSERT`, `UPDATE`, ou `DELETE`.</li>
            </ul>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">CREATE TRIGGER</span> <span class="token-variable">nom_du_trigger</span>
    {<span class="token-keyword">BEFORE</span> | <span class="token-keyword">AFTER</span>} {<span class="token-keyword">INSERT</span> | <span class="token-keyword">UPDATE</span> | <span class="token-keyword">DELETE</span>}
    <span class="token-keyword">ON</span> <span class="token-variable">nom_de_la_table</span>
    <span class="token-keyword">FOR EACH ROW</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-comment">-- Instructions à exécuter...</span>
<span class="token-keyword">END</span>;</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">7.2. Les alias `NEW` et `OLD`</h4>
            <p class="text-gray-700 mb-4">À l'intérieur d'un trigger, MySQL nous donne accès à des alias spéciaux pour manipuler les données concernées par l'événement. Leur disponibilité dépend de l'événement :</p>
            <ul class="list-disc ml-6 text-gray-700 space-y-2">
                <li>Pour un `INSERT` : Seul `NEW` est disponible. `NEW.colonne` représente la valeur de la colonne de la nouvelle ligne qui est en train d'être insérée.</li>
                <li>Pour un `DELETE` : Seul `OLD` est disponible. `OLD.colonne` représente la valeur de la colonne de la ligne qui est en train d'être supprimée.</li>
                <li>Pour un `UPDATE` : `OLD` et `NEW` sont tous deux disponibles. `OLD.colonne` est la valeur avant la modification, et `NEW.colonne` est la nouvelle valeur proposée.</li>
            </ul>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>

<!-- ========== CHAPITRE 8 : TRIGGERS EN ACTION : GESTION DE STOCK ========== -->
<section id="triggers-cas-pratique" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 8 : Triggers en Action : Gestion de Stock</h3>
    <p class="text-gray-700 mb-6">Le cas d'usage le plus classique pour les triggers est la gestion d'un stock. Nous allons créer une base de données simple `ventes` et automatiser la mise à jour du stock de nos produits à chaque opération de vente.</p>
    
    <div class="bg-gray-100 p-4 rounded-lg border my-8">
        <h4 class="text-xl font-bold text-gray-800 mb-2">Mise en place de la base `ventes`</h4>
        <div class="code-block-wrapper">
            <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP DATABASE IF EXISTS</span> <span class="token-variable">ventes</span>;
<span class="token-keyword">CREATE DATABASE</span> <span class="token-variable">ventes</span> <span class="token-keyword">COLLATE</span> <span class="token-string">utf8mb4_general_ci</span>;
<span class="token-keyword">USE</span> <span class="token-variable">ventes</span>;

<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">produit</span>(
    <span class="token-variable">id_produit</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>,
    <span class="token-variable">nom</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>),
    <span class="token-variable">prix</span> <span class="token-type">FLOAT</span>,
    <span class="token-variable">stock</span> <span class="token-type">INT</span>,
    <span class="token-keyword">CHECK</span> (<span class="token-variable">stock</span> <span class="token-operator">>=</span> <span class="token-number">0</span>) <span class="token-comment">-- Contrainte pour ne jamais avoir de stock négatif</span>
);

<span class="token-keyword">CREATE TABLE</span> <span class="token-variable">vente</span>(
    <span class="token-variable">id_vente</span> <span class="token-type">INT AUTO_INCREMENT PRIMARY KEY</span>,
    <span class="token-variable">date_vente</span> <span class="token-type">DATETIME</span> <span class="token-keyword">DEFAULT</span> <span class="token-function">CURRENT_TIMESTAMP</span>,
    <span class="token-variable">qte</span> <span class="token-type">INT</span>,
    <span class="token-variable">id_produit</span> <span class="token-type">INT</span>,
    <span class="token-keyword">CONSTRAINT</span> <span class="token-variable">fk_vente_produit</span> <span class="token-keyword">FOREIGN KEY</span> (<span class="token-variable">id_produit</span>) <span class="token-keyword">REFERENCES</span> <span class="token-variable">produit</span>(<span class="token-variable">id_produit</span>)
);

<span class="token-keyword">INSERT INTO</span> <span class="token-variable">produit</span> <span class="token-keyword">VALUES</span> (<span class="token-number">1</span>,<span class="token-string">'chaise'</span>,<span class="token-number">200</span>,<span class="token-number">20</span>), (<span class="token-number">2</span>,<span class="token-string">'table'</span>,<span class="token-number">1500</span>,<span class="token-number">10</span>), (<span class="token-number">3</span>,<span class="token-string">'armoire'</span>,<span class="token-number">5000</span>,<span class="token-number">5</span>);
</code></pre>
            <button class="copy-btn">Copier</button>
        </div>
    </div>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">8.1. Le Déclencheur `AFTER INSERT`</h4>
            <p class="text-gray-700 mb-4">Chaque fois qu'une nouvelle vente est insérée dans la table `vente`, nous devons décrémenter le stock du produit correspondant dans la table `produit`.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">after_insert_vente</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">after_insert_vente</span> 
<span class="token-keyword">AFTER INSERT ON</span> <span class="token-variable">vente</span> 
<span class="token-keyword">FOR EACH ROW</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">UPDATE</span> <span class="token-variable">produit</span> <span class="token-keyword">SET</span> <span class="token-variable">stock</span> <span class="token-operator">=</span> <span class="token-variable">stock</span> <span class="token-operator">-</span> <span class="token-variable">NEW</span>.<span class="token-variable">qte</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_produit</span> <span class="token-operator">=</span> <span class="token-variable">NEW</span>.<span class="token-variable">id_produit</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Test : vendons 2 tables (stock initial : 10)</span>
<span class="token-keyword">INSERT INTO</span> <span class="token-variable">vente</span> (<span class="token-variable">qte</span>, <span class="token-variable">id_produit</span>) <span class="token-keyword">VALUES</span> (<span class="token-number">2</span>, <span class="token-number">2</span>);
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">produit</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_produit</span> <span class="token-operator">=</span> <span class="token-number">2</span>; <span class="token-comment">-- Le stock est maintenant à 8</span>
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">8.2. Le Déclencheur `AFTER DELETE`</h4>
            <p class="text-gray-700 mb-4">Si une vente est annulée (supprimée de la table `vente`), il faut logiquement remettre les produits dans le stock.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">after_delete_vente</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">after_delete_vente</span> 
<span class="token-keyword">AFTER DELETE ON</span> <span class="token-variable">vente</span> 
<span class="token-keyword">FOR EACH ROW</span>
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">UPDATE</span> <span class="token-variable">produit</span> <span class="token-keyword">SET</span> <span class="token-variable">stock</span> <span class="token-operator">=</span> <span class="token-variable">stock</span> <span class="token-operator">+</span> <span class="token-variable">OLD</span>.<span class="token-variable">qte</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_produit</span> <span class="token-operator">=</span> <span class="token-variable">OLD</span>.<span class="token-variable">id_produit</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Test : annulons la vente précédente (id_vente = 1, qte = 2)</span>
<span class="token-keyword">DELETE FROM</span> <span class="token-variable">vente</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_vente</span> <span class="token-operator">=</span> <span class="token-number">1</span>;
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">produit</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_produit</span> <span class="token-operator">=</span> <span class="token-number">2</span>; <span class="token-comment">-- Le stock est de retour à 10</span>
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">8.3. Le Déclencheur `AFTER UPDATE`</h4>
            <p class="text-gray-700 mb-4">Si la quantité d'une vente est modifiée, le stock doit être ajusté en conséquence. Nous devons soustraire la nouvelle quantité et ajouter l'ancienne.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">after_update_vente</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">after_update_vente</span> 
<span class="token-keyword">AFTER UPDATE ON</span> <span class="token-variable">vente</span> 
<span class="token-keyword">FOR EACH ROW</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-comment">-- Formule : stock_final = stock_actuel - (nouvelle_qte - ancienne_qte)</span>
	<span class="token-keyword">UPDATE</span> <span class="token-variable">produit</span> <span class="token-keyword">SET</span> <span class="token-variable">stock</span> <span class="token-operator">=</span> <span class="token-variable">stock</span> <span class="token-operator">-</span> (<span class="token-variable">NEW</span>.<span class="token-variable">qte</span> <span class="token-operator">-</span> <span class="token-variable">OLD</span>.<span class="token-variable">qte</span>) 
    <span class="token-keyword">WHERE</span> <span class="token-variable">id_produit</span> <span class="token-operator">=</span> <span class="token-variable">NEW</span>.<span class="token-variable">id_produit</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Test : vendons 5 chaises (stock: 20 -> 15)</span>
<span class="token-keyword">INSERT INTO</span> <span class="token-variable">vente</span> (<span class="token-variable">qte</span>, <span class="token-variable">id_produit</span>) <span class="token-keyword">VALUES</span> (<span class="token-number">5</span>, <span class="token-number">1</span>);
<span class="token-comment">-- Modifions la vente : le client en veut 8 au lieu de 5 (+3)</span>
<span class="token-keyword">UPDATE</span> <span class="token-variable">vente</span> <span class="token-keyword">SET</span> <span class="token-variable">qte</span> <span class="token-operator">=</span> <span class="token-number">8</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_vente</span> <span class="token-operator">=</span> <span class="token-number">2</span>;
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">produit</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_produit</span> <span class="token-operator">=</span> <span class="token-number">1</span>; <span class="token-comment">-- Le stock est maintenant à 12 (15 - (8-5))</span>
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-yellow-50 p-6 rounded-lg shadow-sm border border-yellow-300">
            <h4 class="text-xl font-bold text-yellow-800 mb-2">8.4. Triggers et Transactions : une alliance puissante</h4>
            <p class="text-gray-700 mb-4">Un point crucial : **l'instruction qui déclenche le trigger et le trigger lui-même s'exécutent au sein d'une seule et même transaction.** Si une erreur survient dans le trigger (par exemple, la mise à jour du stock viole la contrainte `CHECK (stock >= 0)`), l'opération initiale (l'`INSERT` de la vente) est entièrement annulée. C'est une garantie d'intégrité fondamentale.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-comment">-- Tentons de vendre 10 armoires alors qu'il n'y en a que 5 en stock.</span>
<span class="token-keyword">INSERT INTO</span> <span class="token-variable">vente</span> (<span class="token-variable">qte</span>, <span class="token-variable">id_produit</span>) <span class="token-keyword">VALUES</span> (<span class="token-number">10</span>, <span class="token-number">3</span>);
<span class="token-comment">-- ==> ERREUR: CHECK constraint 'produit_chk_1' is violated.</span>

<span class="token-comment">-- Vérifions les tables :</span>
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">produit</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_produit</span> <span class="token-operator">=</span> <span class="token-number">3</span>; <span class="token-comment">-- Le stock est toujours à 5.</span>
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">vente</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_produit</span> <span class="token-operator">=</span> <span class="token-number">3</span>;  <span class="token-comment">-- La vente n'a pas été insérée.</span>
<span class="token-comment">-- La transaction a été annulée (ROLLBACK). L'état de la base est cohérent.</span>
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>

<!-- ========== CHAPITRE 9 : ALLER PLUS LOIN : DÉCLENCHEURS `BEFORE` ========== -->
<section id="triggers-before" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 9 : Aller plus loin : Déclencheurs `BEFORE`</h3>
    <p class="text-gray-700 mb-6">Jusqu'à présent, nous avons utilisé des triggers `AFTER`, qui s'exécutent après la modification des données. Mais il existe aussi les triggers `BEFORE`, qui s'exécutent **avant** que les données ne soient écrites dans la table. Ils sont parfaits pour deux usages principaux :</p>
    <ul class="list-disc ml-6 text-gray-700 space-y-2 mb-8">
        <li><strong>Valider les données :</strong> On peut vérifier des conditions complexes et, si elles ne sont pas remplies, générer une erreur pour empêcher l'opération.</li>
        <li><strong>Modifier les données à la volée :</strong> On peut nettoyer, formater ou calculer des valeurs avant qu'elles ne soient insérées. Par exemple, mettre un nom en majuscules, calculer un hash de mot de passe, etc.</li>
    </ul>

    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <h4 class="text-xl font-bold text-gray-800 mb-2">Exemple : Formater un nom et valider un stock</h4>
        <p class="text-gray-700 mb-4">Créons un trigger `BEFORE INSERT` sur notre table `produit` qui mettra automatiquement le nom du produit en majuscules et vérifiera que le stock initial n'est pas négatif (même si nous avons déjà un `CHECK`, cela montre le principe de validation).</p>
        <div class="code-block-wrapper">
            <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">before_insert_produit</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">before_insert_produit</span>
<span class="token-keyword">BEFORE INSERT ON</span> <span class="token-variable">produit</span>
<span class="token-keyword">FOR EACH ROW</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-comment">-- 1. Modification des données : Mettre le nom en majuscules</span>
    <span class="token-keyword">SET</span> <span class="token-variable">NEW</span>.<span class="token-variable">nom</span> <span class="token-operator">=</span> <span class="token-function">UPPER</span>(<span class="token-variable">NEW</span>.<span class="token-variable">nom</span>);

    <span class="token-comment">-- 2. Validation des données : Empêcher un stock initial négatif</span>
    <span class="token-keyword">IF</span> <span class="token-variable">NEW</span>.<span class="token-variable">stock</span> <span class="token-operator">&lt;</span> <span class="token-number">0</span> <span class="token-keyword">THEN</span>
        <span class="token-comment">-- SIGNAL est la manière propre de générer une erreur personnalisée</span>
        <span class="token-keyword">SIGNAL</span> SQLSTATE <span class="token-string">'45000'</span> <span class="token-keyword">SET</span> MESSAGE_TEXT <span class="token-operator">=</span> <span class="token-string">'Le stock initial ne peut pas être négatif.'</span>;
    <span class="token-keyword">END IF</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Test 1: Insertion normale</span>
<span class="token-keyword">INSERT INTO</span> <span class="token-variable">produit</span>(<span class="token-variable">nom</span>, <span class="token-variable">prix</span>, <span class="token-variable">stock</span>) <span class="token-keyword">VALUES</span> (<span class="token-string">'bureau'</span>, <span class="token-number">800</span>, <span class="token-number">15</span>);
<span class="token-keyword">SELECT</span> <span class="token-operator">*</span> <span class="token-keyword">FROM</span> <span class="token-variable">produit</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_produit</span> <span class="token-operator">=</span> <span class="token-number">4</span>; <span class="token-comment">-- Le nom est 'BUREAU'</span>

<span class="token-comment">-- Test 2: Insertion invalide</span>
<span class="token-keyword">INSERT INTO</span> <span class="token-variable">produit</span>(<span class="token-variable">nom</span>, <span class="token-variable">prix</span>, <span class="token-variable">stock</span>) <span class="token-keyword">VALUES</span> (<span class="token-string">'lampe'</span>, <span class="token-number">120</span>, <span class="token-operator">-</span><span class="token-number">5</span>);
<span class="token-comment">-- ==> ERREUR 1644 (45000): Le stock initial ne peut pas être négatif.</span>
</code></pre>
            <button class="copy-btn">Copier</button>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>


<!-- ========== ATELIERS PRATIQUES DE LA PARTIE 3 ========== -->
<section id="exercices-partie3" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Ateliers Pratiques : Déclencheurs</h3>
    <p class="text-gray-700 mb-8">Mettons en application ces concepts avec des cas d'usage réels pour automatiser la maintenance de nos bases de données.</p>
    
    <div class="space-y-10">
        <!-- Exercice 1 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 1 : Calcul automatique des heures de vol</h4>
            <p class="text-gray-700 mb-4">Nous allons utiliser la base `vols`. Le but est de maintenir un compteur d'heures de vol pour chaque pilote, mis à jour automatiquement à chaque ajout, modification ou suppression de vol.</p>
            <div class="bg-gray-100 p-4 rounded-lg border my-4">
                 <p class="text-gray-700 mb-4">Exécutez ce code pour préparer la table `Pilote`.</p>
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">USE</span> <span class="token-variable">vols</span>;
<span class="token-comment">-- Ajout du champ pour stocker le nombre d'heures de vol</span>
<span class="token-keyword">ALTER TABLE</span> <span class="token-variable">pilote</span> <span class="token-keyword">ADD COLUMN</span> <span class="token-variable">NBHV</span> <span class="token-type">TIME</span> <span class="token-keyword">DEFAULT</span> <span class="token-string">'00:00:00'</span>;
<span class="token-comment">-- Modification des colonnes de date pour permettre des calculs précis</span>
<span class="token-keyword">ALTER TABLE</span> <span class="token-variable">vol</span> <span class="token-keyword">MODIFY</span> <span class="token-variable">dated</span> <span class="token-type">DATETIME</span>;
<span class="token-keyword">ALTER TABLE</span> <span class="token-variable">vol</span> <span class="token-keyword">MODIFY</span> <span class="token-variable">datea</span> <span class="token-type">DATETIME</span>;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>

            <h5 class="font-semibold text-gray-800 mb-2 mt-4">Question 1 : Trigger après l'ajout d'un vol</h5>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">after_insert_vol</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">after_insert_vol</span> 
<span class="token-keyword">AFTER INSERT ON</span> <span class="token-variable">vol</span> 
<span class="token-keyword">FOR EACH ROW</span>
<span class="token-keyword">BEGIN</span> 
    <span class="token-keyword">UPDATE</span> <span class="token-variable">pilote</span> <span class="token-keyword">SET</span> <span class="token-variable">NBHV</span> <span class="token-operator">=</span> <span class="token-function">ADDTIME</span>(<span class="token-variable">NBHV</span>, <span class="token-function">TIMEDIFF</span>(<span class="token-variable">NEW</span>.<span class="token-variable">datea</span>, <span class="token-variable">NEW</span>.<span class="token-variable">dated</span>))
    <span class="token-keyword">WHERE</span> <span class="token-variable">numpilote</span> <span class="token-operator">=</span> <span class="token-variable">NEW</span>.<span class="token-variable">numpil</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
            
            <h5 class="font-semibold text-gray-800 mb-2 mt-4">Question 2 : Trigger après la suppression d'un vol</h5>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">after_delete_vol</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">after_delete_vol</span> 
<span class="token-keyword">AFTER DELETE ON</span> <span class="token-variable">vol</span> 
<span class="token-keyword">FOR EACH ROW</span>
<span class="token-keyword">BEGIN</span> 
	<span class="token-keyword">UPDATE</span> <span class="token-variable">pilote</span> <span class="token-keyword">SET</span> <span class="token-variable">NBHV</span> <span class="token-operator">=</span> <span class="token-function">SUBTIME</span>(<span class="token-variable">NBHV</span>, <span class="token-function">TIMEDIFF</span>(<span class="token-variable">OLD</span>.<span class="token-variable">datea</span>, <span class="token-variable">OLD</span>.<span class="token-variable">dated</span>))
    <span class="token-keyword">WHERE</span> <span class="token-variable">numpilote</span> <span class="token-operator">=</span> <span class="token-variable">OLD</span>.<span class="token-variable">numpil</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>

            <h5 class="font-semibold text-gray-800 mb-2 mt-4">Question 3 : Trigger après la modification d'un vol</h5>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">after_update_vol</span>;
<span class="token-keyword">DELIMITER</span> &&
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">after_update_vol</span>
<span class="token-keyword">AFTER UPDATE ON</span> <span class="token-variable">vol</span>
<span class="token-keyword">FOR EACH ROW</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">UPDATE</span> <span class="token-variable">pilote</span>
    <span class="token-keyword">SET</span> <span class="token-variable">NBHV</span> <span class="token-operator">=</span> <span class="token-function">ADDTIME</span>(
                        <span class="token-function">SUBTIME</span>(<span class="token-variable">NBHV</span>, <span class="token-function">TIMEDIFF</span>(<span class="token-variable">OLD</span>.<span class="token-variable">datea</span>, <span class="token-variable">OLD</span>.<span class="token-variable">dated</span>)),
                        <span class="token-function">TIMEDIFF</span>(<span class="token-variable">NEW</span>.<span class="token-variable">datea</span>, <span class="token-variable">NEW</span>.<span class="token-variable">dated</span>)
					)
    <span class="token-keyword">WHERE</span> <span class="token-variable">numpilote</span> <span class="token-operator">=</span> <span class="token-variable">NEW</span>.<span class="token-variable">numpil</span>;
<span class="token-keyword">END</span>&&
<span class="token-keyword">DELIMITER</span> ;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>
        
        <!-- Exercice 2 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 2 : Calcul automatique du salaire moyen par département</h4>
            <p class="text-gray-700 mb-4">Dans la base `employes`, nous voulons que le salaire moyen de chaque département soit toujours à jour. Pour cela, nous allons ajouter une colonne `salaire_moyen` à la table `DEPARTEMENT` et créer les triggers nécessaires sur la table `EMPLOYE`.</p>
            <div class="bg-gray-100 p-4 rounded-lg border my-4">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">USE</span> <span class="token-variable">employes</span>;
<span class="token-keyword">ALTER TABLE</span> <span class="token-variable">departement</span> <span class="token-keyword">ADD</span> <span class="token-variable">salaire_moyen</span> <span class="token-type">DECIMAL</span>(<span class="token-number">8</span>,<span class="token-number">2</span>);
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
            
            <h5 class="font-semibold text-gray-800 mb-2 mt-4">Question : Créer les triggers sur la table `EMPLOYE`</h5>
            <p class="text-gray-600 mb-4 text-sm">Note : MySQL requiert un trigger distinct pour chaque événement (`INSERT`, `UPDATE`, `DELETE`).</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-comment">-- Trigger pour INSERT</span>
<span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">after_insert_employe</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">after_insert_employe</span> <span class="token-keyword">AFTER INSERT ON</span> <span class="token-variable">employe</span> <span class="token-keyword">FOR EACH ROW</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">UPDATE</span> <span class="token-variable">departement</span> <span class="token-keyword">SET</span> <span class="token-variable">salaire_moyen</span> <span class="token-operator">=</span> (<span class="token-keyword">SELECT</span> <span class="token-function">AVG</span>(<span class="token-variable">salaire</span>) <span class="token-keyword">FROM</span> <span class="token-variable">employe</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_dep</span> <span class="token-operator">=</span> <span class="token-variable">NEW</span>.<span class="token-variable">id_dep</span>) 
    <span class="token-keyword">WHERE</span> <span class="token-variable">id_dep</span> <span class="token-operator">=</span> <span class="token-variable">NEW</span>.<span class="token-variable">id_dep</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Trigger pour UPDATE</span>
<span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">after_update_employe</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">after_update_employe</span> <span class="token-keyword">AFTER UPDATE ON</span> <span class="token-variable">employe</span> <span class="token-keyword">FOR EACH ROW</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-comment">-- Mise à jour de l'ancien département si l'employé a changé de département</span>
    <span class="token-keyword">IF</span> <span class="token-variable">OLD</span>.<span class="token-variable">id_dep</span> <span class="token-operator">!=</span> <span class="token-variable">NEW</span>.<span class="token-variable">id_dep</span> <span class="token-keyword">THEN</span>
        <span class="token-keyword">UPDATE</span> <span class="token-variable">departement</span> <span class="token-keyword">SET</span> <span class="token-variable">salaire_moyen</span> <span class="token-operator">=</span> (<span class="token-keyword">SELECT</span> <span class="token-function">AVG</span>(<span class="token-variable">salaire</span>) <span class="token-keyword">FROM</span> <span class="token-variable">employe</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_dep</span> <span class="token-operator">=</span> <span class="token-variable">OLD</span>.<span class="token-variable">id_dep</span>) 
        <span class="token-keyword">WHERE</span> <span class="token-variable">id_dep</span> <span class="token-operator">=</span> <span class="token-variable">OLD</span>.<span class="token-variable">id_dep</span>;
    <span class="token-keyword">END IF</span>;
    <span class="token-comment">-- Mise à jour du nouveau département</span>
    <span class="token-keyword">UPDATE</span> <span class="token-variable">departement</span> <span class="token-keyword">SET</span> <span class="token-variable">salaire_moyen</span> <span class="token-operator">=</span> (<span class="token-keyword">SELECT</span> <span class="token-function">AVG</span>(<span class="token-variable">salaire</span>) <span class="token-keyword">FROM</span> <span class="token-variable">employe</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_dep</span> <span class="token-operator">=</span> <span class="token-variable">NEW</span>.<span class="token-variable">id_dep</span>) 
    <span class="token-keyword">WHERE</span> <span class="token-variable">id_dep</span> <span class="token-operator">=</span> <span class="token-variable">NEW</span>.<span class="token-variable">id_dep</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Trigger pour DELETE</span>
<span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">after_delete_employe</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">after_delete_employe</span> <span class="token-keyword">AFTER DELETE ON</span> <span class="token-variable">employe</span> <span class="token-keyword">FOR EACH ROW</span>
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">UPDATE</span> <span class="token-variable">departement</span> <span class="token-keyword">SET</span> <span class="token-variable">salaire_moyen</span> <span class="token-operator">=</span> (<span class="token-keyword">SELECT</span> <span class="token-function">AVG</span>(<span class="token-variable">salaire</span>) <span class="token-keyword">FROM</span> <span class="token-variable">employe</span> <span class="token-keyword">WHERE</span> <span class="token-variable">id_dep</span> <span class="token-operator">=</span> <span class="token-variable">OLD</span>.<span class="token-variable">id_dep</span>) 
    <span class="token-keyword">WHERE</span> <span class="token-variable">id_dep</span> <span class="token-operator">=</span> <span class="token-variable">OLD</span>.<span class="token-variable">id_dep</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

        <!-- Exercice 3 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 3 : Calcul automatique du prix d'une recette</h4>
            <p class="text-gray-700 mb-4">Dans la base `cuisine`, nous allons ajouter une colonne `prix` à la table `Recettes` et la maintenir à jour automatiquement lorsque la composition d'une recette change.</p>
            <h5 class="font-semibold text-gray-800 mb-2 mt-4">Bonne pratique : Utiliser une fonction d'aide</h5>
            <p class="text-gray-700 mb-4">Le calcul du prix total d'une recette sera nécessaire dans plusieurs triggers. Pour éviter de dupliquer le code, nous allons d'abord créer une fonction qui s'en charge. C'est une pratique vivement recommandée.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">USE</span> <span class="token-variable">cuisine</span>;
<span class="token-keyword">ALTER TABLE</span> <span class="token-variable">recettes</span> <span class="token-keyword">ADD COLUMN</span> <span class="token-variable">prix</span> <span class="token-type">FLOAT</span> <span class="token-keyword">DEFAULT</span> <span class="token-number">0</span>;

<span class="token-keyword">DROP FUNCTION IF EXISTS</span> <span class="token-function">get_recette_price</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE FUNCTION</span> <span class="token-function">get_recette_price</span>(<span class="token-variable">r_id</span> <span class="token-type">INT</span>)
<span class="token-keyword">RETURNS</span> <span class="token-type">FLOAT</span>
<span class="token-keyword">READS SQL DATA</span>
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">cout</span> <span class="token-type">FLOAT</span>;
	<span class="token-keyword">SELECT</span> <span class="token-function">SUM</span>(<span class="token-variable">i</span>.<span class="token-variable">PUIng</span> <span class="token-operator">*</span> <span class="token-variable">cr</span>.<span class="token-variable">QteUtilisee</span>) <span class="token-keyword">INTO</span> <span class="token-variable">cout</span> 
    <span class="token-keyword">FROM</span> <span class="token-variable">ingredients</span> <span class="token-variable">i</span> 
    <span class="token-keyword">JOIN</span> <span class="token-variable">composition_recette</span> <span class="token-variable">cr</span> <span class="token-keyword">ON</span> <span class="token-variable">i</span>.<span class="token-variable">NumIng</span> <span class="token-operator">=</span> <span class="token-variable">cr</span>.<span class="token-variable">NumIng</span>
    <span class="token-keyword">WHERE</span> <span class="token-variable">cr</span>.<span class="token-variable">numrec</span> <span class="token-operator">=</span> <span class="token-variable">r_id</span>;
	<span class="token-keyword">RETURN</span> <span class="token-function">IFNULL</span>(<span class="token-variable">cout</span>, <span class="token-number">0</span>);
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            
            <h5 class="font-semibold text-gray-800 mb-2 mt-4">Question : Créer les triggers sur la table `Composition_Recette`</h5>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-comment">-- Trigger pour INSERT</span>
<span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">after_insert_composition</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">after_insert_composition</span> <span class="token-keyword">AFTER INSERT ON</span> <span class="token-variable">composition_recette</span> <span class="token-keyword">FOR EACH ROW</span> 
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">UPDATE</span> <span class="token-variable">recettes</span> <span class="token-keyword">SET</span> <span class="token-variable">prix</span> <span class="token-operator">=</span> <span class="token-function">get_recette_price</span>(<span class="token-variable">NEW</span>.<span class="token-variable">numrec</span>) <span class="token-keyword">WHERE</span> <span class="token-variable">numrec</span> <span class="token-operator">=</span> <span class="token-variable">NEW</span>.<span class="token-variable">numrec</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Trigger pour DELETE</span>
<span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">after_delete_composition</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">after_delete_composition</span> <span class="token-keyword">AFTER DELETE ON</span> <span class="token-variable">composition_recette</span> <span class="token-keyword">FOR EACH ROW</span> 
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">UPDATE</span> <span class="token-variable">recettes</span> <span class="token-keyword">SET</span> <span class="token-variable">prix</span> <span class="token-operator">=</span> <span class="token-function">get_recette_price</span>(<span class="token-variable">OLD</span>.<span class="token-variable">numrec</span>) <span class="token-keyword">WHERE</span> <span class="token-variable">numrec</span> <span class="token-operator">=</span> <span class="token-variable">OLD</span>.<span class="token-variable">numrec</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-comment">-- Trigger pour UPDATE</span>
<span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">after_update_composition</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">after_update_composition</span> <span class="token-keyword">AFTER UPDATE ON</span> <span class="token-variable">composition_recette</span> <span class="token-keyword">FOR EACH ROW</span> 
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">UPDATE</span> <span class="token-variable">recettes</span> <span class="token-keyword">SET</span> <span class="token-variable">prix</span> <span class="token-operator">=</span> <span class="token-function">get_recette_price</span>(<span class="token-variable">NEW</span>.<span class="token-variable">numrec</span>) <span class="token-keyword">WHERE</span> <span class="token-variable">numrec</span> <span class="token-operator">=</span> <span class="token-variable">NEW</span>.<span class="token-variable">numrec</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mt-6">
                <h5 class="text-lg font-bold text-blue-800 mb-2">Pour aller plus loin : que se passe-t-il si le prix d'un ingrédient change ?</h5>
                <p class="text-gray-700">Vous avez raison de noter qu'un `UPDATE` sur la table `Ingredients` devrait aussi mettre à jour le prix des recettes. Créer un trigger `AFTER UPDATE ON Ingredients` est complexe car il faudrait trouver **toutes** les recettes qui utilisent cet ingrédient et les mettre à jour une par une. C'est un scénario où l'on doit parcourir un ensemble de résultats (la liste des recettes concernées) à l'intérieur d'un trigger, ce qui nous amène à des concepts plus avancés comme les **curseurs**, que nous aborderons dans une prochaine partie.</p>
            </div>
        </div>
    </div>
</section>