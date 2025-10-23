<!-- =================================================================== -->
<!-- PARTIE 5 : ITÉRATION SUR LES DONNÉES : LES CURSEURS -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Partie 5 : Itération sur les Données : Les Curseurs</h2>

<!-- ========== CHAPITRE 12 : INTRODUCTION AUX CURSEURS ========== -->
<section id="curseurs-intro" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 12 : Le Traitement Ligne par Ligne : Les Curseurs</h3>
    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
        Jusqu'à présent, toutes nos opérations SQL fonctionnaient sur des **ensembles** de données. Un `UPDATE` modifie toutes les lignes qui correspondent à un critère, un `SELECT` retourne un jeu complet de résultats. Mais que faire si nous avons besoin de parcourir ce jeu de résultats, une ligne à la fois, pour effectuer un traitement complexe sur chacune d'entre elles ? C'est là qu'interviennent les **curseurs**.
    </p>
    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500 mb-8">
        <h3 class="text-2xl font-bold mb-2">Qu'est-ce qu'un curseur ?</h3>
        <p class="text-gray-700">Un curseur est un pointeur qui nous permet de "marcher" à travers le jeu de résultats d'une requête `SELECT`, ligne par ligne. Il nous donne la possibilité d'exécuter une logique procédurale (conditions, appels à d'autres procédures, etc.) pour chaque enregistrement individuel.</p>
        <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800">
            <strong>Attention à la performance :</strong> Les curseurs sont puissants mais doivent être utilisés avec parcimonie. Le traitement ligne par ligne est intrinsèquement plus lent qu'une opération SQL basée sur un ensemble. Privilégiez toujours une solution SQL pure si elle existe.
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-sm border space-y-6">
        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">12.1. Le Cycle de Vie d'un Curseur</h4>
            <p class="text-gray-700 mb-4">L'utilisation d'un curseur suit toujours quatre étapes précises :</p>
            <ol class="list-decimal ml-6 text-gray-700 space-y-2">
                <li><strong>`DECLARE` :</strong> On déclare le curseur et on l'associe à une requête `SELECT`. À ce stade, la requête n'est pas encore exécutée.</li>
                <li><strong>`OPEN` :</strong> On ouvre le curseur. La requête `SELECT` est exécutée, et les lignes du résultat sont stockées en mémoire, prêtes à être parcourues.</li>
                <li><strong>`FETCH` :</strong> On récupère la ligne suivante pointée par le curseur et on stocke ses valeurs dans des variables locales. C'est cette étape que l'on place à l'intérieur d'une boucle.</li>
                <li><strong>`CLOSE` :</strong> Une fois le traitement terminé, on ferme le curseur pour libérer les ressources allouées par la base de données.</li>
            </ol>
        </div>
        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">12.2. Gérer la Fin de la Boucle</h4>
            <p class="text-gray-700 mb-4">Comment savoir quand on a atteint la dernière ligne ? Le `FETCH` lève une condition `NOT FOUND` lorsqu'il n'y a plus de lignes à lire. Nous devons l'intercepter avec un `HANDLER` pour sortir proprement de notre boucle.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-sql"><span class="token-keyword">-- Déclaration d'une variable 'drapeau' pour contrôler la boucle</span>
<span class="token-keyword">DECLARE</span> <span class="token-variable">flag</span> <span class="token-type">BOOLEAN</span> <span class="token-keyword">DEFAULT</span> <span class="token-keyword">FALSE</span>;
<span class="token-comment">-- ... déclaration du curseur ...</span>

<span class="token-comment">-- Le handler qui se déclenchera quand FETCH ne trouvera plus de ligne</span>
<span class="token-keyword">DECLARE CONTINUE HANDLER FOR NOT FOUND SET</span> <span class="token-variable">flag</span> <span class="token-operator">=</span> <span class="token-keyword">TRUE</span>;

<span class="token-keyword">OPEN</span> <span class="token-variable">mon_curseur</span>;
    <span class="token-variable">ma_boucle</span>: <span class="token-keyword">LOOP</span>
        <span class="token-keyword">FETCH</span> <span class="token-variable">mon_curseur</span> <span class="token-keyword">INTO</span> <span class="token-variable">var1</span>, <span class="token-variable">var2</span>;
        <span class="token-keyword">IF</span> <span class="token-variable">flag</span> <span class="token-keyword">THEN</span>
            <span class="token-keyword">LEAVE</span> <span class="token-variable">ma_boucle</span>;
        <span class="token-keyword">END IF</span>;
        <span class="token-comment">-- ... Traitement de la ligne en cours ...</span>
    <span class="token-keyword">END LOOP</span> <span class="token-variable">ma_boucle</span>;
<span class="token-keyword">CLOSE</span> <span class="token-variable">mon_curseur</span>;
</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>

<!-- ========== CHAPITRE 13 : CURSEURS EN PRATIQUE ========== -->
<section id="curseurs-pratique" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 13 : Curseurs en Pratique</h3>
    <p class="text-gray-700 mb-6">Voyons un exemple concret. Nous souhaitons calculer le nombre total d'heures de vol pour chaque pilote et mettre à jour un champ `nbhv` dans la table `Pilote`. Sans curseur, il faudrait faire une requête par pilote. Avec un curseur, une seule procédure peut tout gérer.</p>
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <h4 class="text-xl font-bold text-gray-800 mb-2">Exemple : Calcul des heures de vol</h4>
        <div class="code-block-wrapper">
            <pre class="code-block"><code class="language-sql"><span class="token-keyword">USE</span> <span class="token-variable">vols</span>;
<span class="token-keyword">ALTER TABLE</span> <span class="token-variable">pilote</span> <span class="token-keyword">ADD</span> <span class="token-variable">nbhv</span> <span class="token-type">TIME</span> <span class="token-keyword">DEFAULT</span> <span class="token-string">"00:00:00"</span>;
<span class="token-keyword">ALTER TABLE</span> <span class="token-variable">vol</span> <span class="token-keyword">MODIFY</span> <span class="token-variable">datea</span> <span class="token-type">DATETIME</span>;
<span class="token-keyword">ALTER TABLE</span> <span class="token-variable">vol</span> <span class="token-keyword">MODIFY</span> <span class="token-variable">dated</span> <span class="token-type">DATETIME</span>;

<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">set_nbhv</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">set_nbhv</span>()
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">flag</span> <span class="token-type">BOOLEAN</span> <span class="token-keyword">DEFAULT</span> <span class="token-keyword">FALSE</span>;
	<span class="token-keyword">DECLARE</span> <span class="token-variable">id_pilote_en_cours</span> <span class="token-type">INT</span>;
    <span class="token-keyword">DECLARE</span> <span class="token-variable">temps_total</span> <span class="token-type">TIME</span>;
    
    <span class="token-comment">-- 1. DECLARE CURSOR</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">c1</span> <span class="token-keyword">CURSOR FOR SELECT</span> <span class="token-variable">numpilote</span> <span class="token-keyword">FROM</span> <span class="token-variable">pilote</span>;
    
    <span class="token-comment">-- Déclaration du handler pour la fin du curseur</span>
    <span class="token-keyword">DECLARE CONTINUE HANDLER FOR NOT FOUND SET</span> <span class="token-variable">flag</span> <span class="token-operator">=</span> <span class="token-keyword">TRUE</span>;
    
    <span class="token-comment">-- 2. OPEN CURSOR</span>
    <span class="token-keyword">OPEN</span> <span class="token-variable">c1</span>;
    
    <span class="token-variable">boucle_pilotes</span>: <span class="token-keyword">LOOP</span>
		<span class="token-comment">-- 3. FETCH CURSOR</span>
		<span class="token-keyword">FETCH</span> <span class="token-variable">c1</span> <span class="token-keyword">INTO</span> <span class="token-variable">id_pilote_en_cours</span>;
        
        <span class="token-keyword">IF</span> <span class="token-variable">flag</span> <span class="token-keyword">THEN</span>
			<span class="token-keyword">LEAVE</span> <span class="token-variable">boucle_pilotes</span>;
        <span class="token-keyword">END IF</span>;
        
        <span class="token-comment">-- Traitement pour le pilote en cours</span>
        <span class="token-keyword">SELECT</span> <span class="token-function">SEC_TO_TIME</span>(<span class="token-function">SUM</span>(<span class="token-function">TIME_TO_SEC</span>(<span class="token-function">TIMEDIFF</span>(<span class="token-variable">datea</span>, <span class="token-variable">dated</span>)))) <span class="token-keyword">INTO</span> <span class="token-variable">temps_total</span> 
		<span class="token-keyword">FROM</span> <span class="token-variable">vol</span> 
        <span class="token-keyword">WHERE</span> <span class="token-variable">numpil</span> <span class="token-operator">=</span> <span class="token-variable">id_pilote_en_cours</span>;
        
        <span class="token-keyword">UPDATE</span> <span class="token-variable">pilote</span> <span class="token-keyword">SET</span> <span class="token-variable">nbhv</span> <span class="token-operator">=</span> <span class="token-variable">temps_total</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numpilote</span> <span class="token-operator">=</span> <span class="token-variable">id_pilote_en_cours</span>;
    <span class="token-keyword">END LOOP</span> <span class="token-variable">boucle_pilotes</span>;
    
    <span class="token-comment">-- 4. CLOSE CURSOR</span>
    <span class="token-keyword">CLOSE</span> <span class="token-variable">c1</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;

<span class="token-keyword">CALL</span> <span class="token-function">set_nbhv</span>();
</code></pre>
            <button class="copy-btn">Copier</button>
        </div>
    </div>
    <div class="text-right mt-8"> <a href="#page-top" class="text-sm font-semibold text-blue-600 hover:underline">↑ Retour en haut</a> </div>
</section>

<!-- ========== ATELIERS PRATIQUES DE LA PARTIE 5 ========== -->
<section id="exercices-partie5" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Ateliers Pratiques : Curseurs</h3>
    <p class="text-gray-700 mb-8">Les exercices suivants vous mettront au défi de manipuler des curseurs simples et imbriqués pour résoudre des problèmes qui seraient complexes à gérer avec du SQL standard.</p>
    
    <div class="space-y-10">
        <!-- Exercice 1 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 1 : Rapport complet des recettes</h4>
            <p class="text-gray-700 mb-4">Reprendre la PS9 de la série sur les Procédures Stockées. Le but est de créer une procédure qui affiche une fiche détaillée pour **chaque** recette de la base `cuisine`, en utilisant un curseur pour itérer sur les recettes.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">USE</span> <span class="token-variable">cuisine</span>;
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">Ps9_rapport_complet</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">Ps9_rapport_complet</span>()
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">flag</span> <span class="token-type">BOOLEAN</span> <span class="token-keyword">DEFAULT</span> <span class="token-keyword">FALSE</span>;
	<span class="token-keyword">DECLARE</span> <span class="token-variable">idRec</span> <span class="token-type">INT</span>;
    <span class="token-keyword">DECLARE</span> <span class="token-variable">nameRec</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>);
    <span class="token-keyword">DECLARE</span> <span class="token-variable">methode</span> <span class="token-type">VARCHAR</span>(<span class="token-number">250</span>);
    <span class="token-keyword">DECLARE</span> <span class="token-variable">tpRec</span> <span class="token-type">VARCHAR</span>(<span class="token-number">20</span>);
    <span class="token-keyword">DECLARE</span> <span class="token-variable">prix</span> <span class="token-type">DOUBLE</span>;
    
    <span class="token-keyword">DECLARE</span> <span class="token-variable">c</span> <span class="token-keyword">CURSOR FOR SELECT</span> <span class="token-variable">numrec</span>, <span class="token-variable">nomrec</span>, <span class="token-variable">tempsPreparation</span>, <span class="token-variable">methodePreparation</span> <span class="token-keyword">FROM</span> <span class="token-variable">recettes</span>;
    <span class="token-keyword">DECLARE CONTINUE HANDLER FOR NOT FOUND SET</span> <span class="token-variable">flag</span><span class="token-operator">=</span><span class="token-keyword">TRUE</span>;
    
    <span class="token-keyword">OPEN</span> <span class="token-variable">c</span>;
    <span class="token-variable">l</span>:<span class="token-keyword">LOOP</span>
		<span class="token-keyword">FETCH</span> <span class="token-variable">c</span> <span class="token-keyword">INTO</span> <span class="token-variable">idRec</span>, <span class="token-variable">nameRec</span>, <span class="token-variable">tpRec</span>, <span class="token-variable">methode</span>;
		<span class="token-keyword">IF</span> <span class="token-variable">flag</span> <span class="token-keyword">THEN</span> 
			<span class="token-keyword">LEAVE</span> <span class="token-variable">l</span>;
		<span class="token-keyword">END IF</span>;
        
		<span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>("Recette : ", <span class="token-variable">nameRec</span>, ", temps de préparation : ", <span class="token-variable">tpRec</span>, " min") <span class="token-keyword">AS</span> "--- Message ---";
        
        <span class="token-keyword">SELECT</span> <span class="token-variable">NomIng</span>, <span class="token-variable">QteUtilisee</span> <span class="token-keyword">FROM</span> <span class="token-variable">composition_recette</span> <span class="token-keyword">JOIN</span> <span class="token-variable">ingredients</span> <span class="token-keyword">USING</span> (<span class="token-variable">NumIng</span>) <span class="token-keyword">WHERE</span> <span class="token-variable">NumRec</span><span class="token-operator">=</span><span class="token-variable">idRec</span>;
		
        <span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>("Sa méthode de préparation est : ", <span class="token-variable">methode</span>) <span class="token-keyword">AS</span> "--- Méthode ---";
    
	    <span class="token-keyword">SELECT</span> <span class="token-function">SUM</span>(<span class="token-variable">puIng</span> <span class="token-operator">*</span> <span class="token-variable">qteUtilisee</span>) <span class="token-keyword">INTO</span> <span class="token-variable">prix</span> <span class="token-keyword">FROM</span> <span class="token-variable">ingredients</span> <span class="token-keyword">JOIN</span> <span class="token-variable">composition_recette</span> <span class="token-keyword">USING</span>(<span class="token-variable">numIng</span>) <span class="token-keyword">WHERE</span> <span class="token-variable">numRec</span><span class="token-operator">=</span><span class="token-variable">idRec</span>;
	    <span class="token-keyword">IF</span> <span class="token-variable">prix</span> <span class="token-operator">&lt;</span> <span class="token-number">50</span> <span class="token-keyword">THEN</span>
		   <span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>("Prix intéressant : ", <span class="token-function">FORMAT</span>(<span class="token-variable">prix</span>, <span class="token-number">2</span>), " DH") <span class="token-keyword">AS</span> "--- Avis Prix ---" ;
	    <span class="token-keyword">END IF</span>;
    
    <span class="token-keyword">END LOOP</span> <span class="token-variable">l</span>;
    <span class="token-keyword">CLOSE</span> <span class="token-variable">c</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">Ps9_rapport_complet</span>();
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>
        
        <!-- Exercice 2 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 2 : Trigger de mise à jour des prix avec curseur</h4>
            <p class="text-gray-700 mb-4">C'est le moment de résoudre le problème laissé en suspens dans la partie sur les Triggers. Créer un trigger `AFTER UPDATE` sur la table `Ingredients` qui, lorsqu'un prix unitaire change, recalcule le prix de **toutes** les recettes qui utilisent cet ingrédient.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mt-6">
                    <h5 class="text-lg font-bold text-blue-800 mb-2">Logique</h5>
                    <p class="text-gray-700">Quand le prix de l'ingrédient `N` change, le trigger doit : <br>1. Trouver toutes les recettes qui contiennent l'ingrédient `N`. <br>2. Utiliser un curseur pour parcourir cette liste de recettes. <br>3. Pour chaque recette, appeler la fonction `get_recette_price()` (créée précédemment) et mettre à jour son prix.</p>
                </div>
                <div class="code-block-wrapper mt-4">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP TRIGGER IF EXISTS</span> <span class="token-function">trg_after_update_ingredient_price</span>;
<span class="token-keyword">DELIMITER</span> //
<span class="token-keyword">CREATE TRIGGER</span> <span class="token-function">trg_after_update_ingredient_price</span> <span class="token-keyword">AFTER UPDATE ON</span> <span class="token-variable">Ingredients</span> <span class="token-keyword">FOR EACH ROW</span>
<span class="token-keyword">BEGIN</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">id_recette_a_maj</span> <span class="token-type">INT</span>;
    <span class="token-keyword">DECLARE</span> <span class="token-variable">flag</span> <span class="token-type">BOOLEAN</span> <span class="token-keyword">DEFAULT</span> <span class="token-keyword">FALSE</span>;
    
    <span class="token-comment">-- Le curseur sélectionne toutes les recettes affectées par le changement de prix</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">c1</span> <span class="token-keyword">CURSOR FOR SELECT</span> <span class="token-variable">NumRec</span> <span class="token-keyword">FROM</span> <span class="token-variable">composition_recette</span> <span class="token-keyword">WHERE</span> <span class="token-variable">NumIng</span> <span class="token-operator">=</span> <span class="token-variable">NEW</span>.<span class="token-variable">NumIng</span>;
    <span class="token-keyword">DECLARE CONTINUE HANDLER FOR NOT FOUND SET</span> <span class="token-variable">flag</span> <span class="token-operator">=</span> <span class="token-keyword">TRUE</span>;
    
    <span class="token-comment">-- On exécute ce code seulement si le prix a réellement changé</span>
    <span class="token-keyword">IF</span> <span class="token-variable">OLD</span>.<span class="token-variable">PUIng</span> <span class="token-operator">!=</span> <span class="token-variable">NEW</span>.<span class="token-variable">PUIng</span> <span class="token-keyword">THEN</span>
        <span class="token-keyword">OPEN</span> <span class="token-variable">c1</span>;
            <span class="token-variable">boucle_recettes</span>: <span class="token-keyword">LOOP</span>
                <span class="token-keyword">FETCH</span> <span class="token-variable">c1</span> <span class="token-keyword">INTO</span> <span class="token-variable">id_recette_a_maj</span>;
                <span class="token-keyword">IF</span> <span class="token-variable">flag</span> <span class="token-keyword">THEN</span>
                    <span class="token-keyword">LEAVE</span> <span class="token-variable">boucle_recettes</span>;
                <span class="token-keyword">END IF</span>;
                <span class="token-keyword">UPDATE</span> <span class="token-variable">recettes</span> <span class="token-keyword">SET</span> <span class="token-variable">prix</span> <span class="token-operator">=</span> <span class="token-function">get_recette_price</span>(<span class="token-variable">id_recette_a_maj</span>) <span class="token-keyword">WHERE</span> <span class="token-variable">numrec</span> <span class="token-operator">=</span> <span class="token-variable">id_recette_a_maj</span>;
            <span class="token-keyword">END LOOP</span> <span class="token-variable">boucle_recettes</span>;
        <span class="token-keyword">CLOSE</span> <span class="token-variable">c1</span>;
    <span class="token-keyword">END IF</span>;
<span class="token-keyword">END</span>//
<span class="token-keyword">DELIMITER</span> ;
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

        <!-- Exercice 3 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 3 : Rapport sur les pilotes et mise à jour de salaire</h4>
            <p class="text-gray-700 mb-4">Cet exercice en plusieurs étapes utilise des curseurs imbriqués pour d'abord afficher un rapport, puis pour mettre à jour des données basées sur ce rapport.</p>
            
            <h5 class="font-semibold text-gray-800 mb-2 mt-4">Question 1 & 2 : Rapport détaillé des vols par pilote (curseurs imbriqués)</h5>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">USE</span> <span class="token-variable">vols</span>;
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">rapport_vols_pilotes</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">rapport_vols_pilotes</span>()
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">flag_p</span> <span class="token-type">BOOLEAN</span> <span class="token-keyword">DEFAULT</span> <span class="token-keyword">FALSE</span>;
	<span class="token-keyword">DECLARE</span> <span class="token-variable">idpilote</span> <span class="token-type">INT</span>;
    <span class="token-keyword">DECLARE</span> <span class="token-variable">nomP</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>);
    
    <span class="token-comment">-- Curseur externe pour les pilotes</span>
    <span class="token-keyword">DECLARE</span> <span class="token-variable">c_pilotes</span> <span class="token-keyword">CURSOR FOR SELECT</span> <span class="token-variable">numpilote</span>, <span class="token-variable">nom</span> <span class="token-keyword">FROM</span> <span class="token-variable">pilote</span>;
    <span class="token-keyword">DECLARE CONTINUE HANDLER FOR NOT FOUND SET</span> <span class="token-variable">flag_p</span> <span class="token-operator">=</span> <span class="token-keyword">TRUE</span>;
    
    <span class="token-keyword">OPEN</span> <span class="token-variable">c_pilotes</span>;
		<span class="token-variable">boucle_pilotes</span>: <span class="token-keyword">LOOP</span>
			<span class="token-keyword">FETCH</span> <span class="token-variable">c_pilotes</span> <span class="token-keyword">INTO</span> <span class="token-variable">idpilote</span>, <span class="token-variable">nomP</span>;
			<span class="token-keyword">IF</span> <span class="token-variable">flag_p</span> <span class="token-keyword">THEN</span> <span class="token-keyword">LEAVE</span> <span class="token-variable">boucle_pilotes</span>; <span class="token-keyword">END IF</span>;
			
            <span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>("--- Le pilote '", <span class="token-variable">nomP</span>, "' est affecté aux vols : ---") <span class="token-keyword">AS</span> "Pilote";
            
            <span class="token-comment">-- Bloc interne pour le curseur des vols</span>
            <span class="token-keyword">BEGIN</span>
				<span class="token-keyword">DECLARE</span> <span class="token-variable">flag_v</span> <span class="token-type">BOOLEAN</span> <span class="token-keyword">DEFAULT</span> <span class="token-keyword">FALSE</span>;
                <span class="token-keyword">DECLARE</span> <span class="token-variable">vd</span>, <span class="token-variable">va</span> <span class="token-type">VARCHAR</span>(<span class="token-number">50</span>);
                
                <span class="token-comment">-- Curseur interne pour les vols du pilote en cours</span>
                <span class="token-keyword">DECLARE</span> <span class="token-variable">c_vols</span> <span class="token-keyword">CURSOR FOR SELECT</span> <span class="token-variable">villed</span>, <span class="token-variable">villea</span> <span class="token-keyword">FROM</span> <span class="token-variable">vol</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numpil</span> <span class="token-operator">=</span> <span class="token-variable">idpilote</span>;
                <span class="token-keyword">DECLARE CONTINUE HANDLER FOR NOT FOUND SET</span> <span class="token-variable">flag_v</span> <span class="token-operator">=</span> <span class="token-keyword">TRUE</span>;
                
                <span class="token-keyword">OPEN</span> <span class="token-variable">c_vols</span>;
					<span class="token-variable">boucle_vols</span>: <span class="token-keyword">LOOP</span>
						<span class="token-keyword">FETCH</span> <span class="token-variable">c_vols</span> <span class="token-keyword">INTO</span> <span class="token-variable">vd</span>, <span class="token-variable">va</span>;
						<span class="token-keyword">IF</span> <span class="token-variable">flag_v</span> <span class="token-keyword">THEN</span> <span class="token-keyword">LEAVE</span> <span class="token-variable">boucle_vols</span>; <span class="token-keyword">END IF</span>;
                        <span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>("     Départ : ", <span class="token-variable">vd</span>, " | Arrivée : ", <span class="token-variable">va</span>) <span class="token-keyword">AS</span> "Vol";
                    <span class="token-keyword">END LOOP</span> <span class="token-variable">boucle_vols</span>;
                <span class="token-keyword">CLOSE</span> <span class="token-variable">c_vols</span>;
            <span class="token-keyword">END</span>;
		<span class="token-keyword">END LOOP</span> <span class="token-variable">boucle_pilotes</span>;
    <span class="token-keyword">CLOSE</span> <span class="token-variable">c_pilotes</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">rapport_vols_pilotes</span>();
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>

            <h5 class="font-semibold text-gray-800 mb-2 mt-4">Question 3 : Mise à jour du salaire en fonction du nombre de vols</h5>
            <p class="text-gray-700 mb-4">Modifier la procédure précédente pour mettre à jour le salaire du pilote selon les règles suivantes : 0 vol = 5000; 1-3 vols = 7000; >3 vols = 8000.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">ALTER TABLE</span> <span class="token-variable">pilote</span> <span class="token-keyword">ADD</span> <span class="token-variable">salaire</span> <span class="token-type">DOUBLE</span>;
<span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">maj_salaires_pilotes</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">maj_salaires_pilotes</span>()
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">flag</span> <span class="token-type">BOOLEAN</span> <span class="token-keyword">DEFAULT</span> <span class="token-keyword">FALSE</span>;
	<span class="token-keyword">DECLARE</span> <span class="token-variable">idpilote</span> <span class="token-type">INT</span>;
    <span class="token-keyword">DECLARE</span> <span class="token-variable">salaire_actuel</span>, <span class="token-variable">salaire_nv</span> <span class="token-type">DOUBLE</span>;
    <span class="token-keyword">DECLARE</span> <span class="token-variable">nb_vols</span> <span class="token-type">INT</span>;
    
    <span class="token-keyword">DECLARE</span> <span class="token-variable">c</span> <span class="token-keyword">CURSOR FOR SELECT</span>  <span class="token-variable">numpilote</span>, <span class="token-variable">salaire</span> <span class="token-keyword">FROM</span> <span class="token-variable">pilote</span>;
    <span class="token-keyword">DECLARE CONTINUE HANDLER FOR NOT FOUND SET</span> <span class="token-variable">flag</span> <span class="token-operator">=</span> <span class="token-keyword">TRUE</span>;
    
    <span class="token-keyword">OPEN</span> <span class="token-variable">c</span>;
		<span class="token-variable">b1</span>: <span class="token-keyword">LOOP</span>
			<span class="token-keyword">FETCH</span> <span class="token-variable">c</span> <span class="token-keyword">INTO</span> <span class="token-variable">idpilote</span>, <span class="token-variable">salaire_actuel</span>;
			<span class="token-keyword">IF</span> <span class="token-variable">flag</span> <span class="token-keyword">THEN</span> <span class="token-keyword">LEAVE</span> <span class="token-variable">b1</span>; <span class="token-keyword">END IF</span>;
			
            <span class="token-keyword">SELECT</span> <span class="token-function">COUNT</span>(<span class="token-operator">*</span>) <span class="token-keyword">INTO</span> <span class="token-variable">nb_vols</span> <span class="token-keyword">FROM</span> <span class="token-variable">vol</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numPil</span> <span class="token-operator">=</span> <span class="token-variable">idpilote</span>;
            
            <span class="token-keyword">CASE</span>
                <span class="token-keyword">WHEN</span> <span class="token-variable">nb_vols</span> <span class="token-operator">=</span> <span class="token-number">0</span> <span class="token-keyword">THEN</span> <span class="token-keyword">SET</span> <span class="token-variable">salaire_nv</span> <span class="token-operator">=</span> <span class="token-number">5000</span>;
                <span class="token-keyword">WHEN</span> <span class="token-variable">nb_vols</span> <span class="token-keyword">BETWEEN</span> <span class="token-number">1</span> <span class="token-keyword">AND</span> <span class="token-number">3</span> <span class="token-keyword">THEN</span> <span class="token-keyword">SET</span> <span class="token-variable">salaire_nv</span> <span class="token-operator">=</span> <span class="token-number">7000</span>;
                <span class="token-keyword">ELSE SET</span> <span class="token-variable">salaire_nv</span> <span class="token-operator">=</span> <span class="token-number">8000</span>;
            <span class="token-keyword">END CASE</span>;

            <span class="token-keyword">UPDATE</span> <span class="token-variable">pilote</span> <span class="token-keyword">SET</span> <span class="token-variable">salaire</span> <span class="token-operator">=</span> <span class="token-variable">salaire_nv</span> <span class="token-keyword">WHERE</span> <span class="token-variable">numpilote</span> <span class="token-operator">=</span> <span class="token-variable">idpilote</span>;
            <span class="token-keyword">SELECT</span> <span class="token-function">CONCAT</span>("Pilote ID ", <span class="token-variable">idpilote</span>, ": Ancien salaire=", <span class="token-function">IFNULL</span>(<span class="token-variable">salaire_actuel</span>, <span class="token-number">0</span>), ", Nouveau salaire=", <span class="token-variable">salaire_nv</span>) <span class="token-keyword">AS</span> "Mise à jour";
		<span class="token-keyword">END LOOP</span> <span class="token-variable">b1</span>;
    <span class="token-keyword">CLOSE</span> <span class="token-variable">c</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">maj_salaires_pilotes</span>();
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
            </div>
        </div>

        <!-- Exercice 4 -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">Exercice 4 : Affecter les employés fatigués au groupe 'besoin vacances'</h4>
            <p class="text-gray-700 mb-4">Pour la base `vacances`, créer une procédure avec un curseur qui parcourt la table `employe` et insère dans la table `groupe` tous les employés dont l'état est 'fatigué'.</p>
            <button class="solution-toggle">Voir la solution</button>
            <div class="solution-content">
                <div class="code-block-wrapper">
                    <pre class="code-block"><code class="language-sql"><span class="token-keyword">DROP PROCEDURE IF EXISTS</span> <span class="token-function">affecter_vacances</span>;
<span class="token-keyword">DELIMITER</span> $$
<span class="token-keyword">CREATE PROCEDURE</span> <span class="token-function">affecter_vacances</span>()
<span class="token-keyword">BEGIN</span>
	<span class="token-keyword">DECLARE</span> <span class="token-variable">flag</span> <span class="token-type">BOOLEAN</span> <span class="token-keyword">DEFAULT</span> <span class="token-keyword">FALSE</span>;
	<span class="token-keyword">DECLARE</span> <span class="token-variable">mat</span> <span class="token-type">INT</span>;
    <span class="token-keyword">DECLARE</span> <span class="token-variable">c</span> <span class="token-keyword">CURSOR FOR SELECT</span> <span class="token-variable">matricule</span> <span class="token-keyword">FROM</span> <span class="token-variable">employe</span> <span class="token-keyword">WHERE</span> <span class="token-variable">etat</span> <span class="token-operator">=</span> <span class="token-string">'fatigué'</span>;
    <span class="token-keyword">DECLARE CONTINUE HANDLER FOR NOT FOUND SET</span> <span class="token-variable">flag</span> <span class="token-operator">=</span> <span class="token-keyword">TRUE</span>;
    
    <span class="token-keyword">OPEN</span> <span class="token-variable">c</span>;
		<span class="token-variable">b1</span>: <span class="token-keyword">LOOP</span>
			<span class="token-keyword">FETCH</span> <span class="token-variable">c</span> <span class="token-keyword">INTO</span> <span class="token-variable">mat</span>;
			<span class="token-keyword">IF</span> <span class="token-variable">flag</span> <span class="token-keyword">THEN</span> <span class="token-keyword">LEAVE</span> <span class="token-variable">b1</span>; <span class="token-keyword">END IF</span>;
			<span class="token-keyword">INSERT INTO</span> <span class="token-variable">groupe</span> <span class="token-keyword">VALUES</span> (<span class="token-variable">mat</span>, 'besoin vacances');
		<span class="token-keyword">END LOOP</span> <span class="token-variable">b1</span>;
    <span class="token-keyword">CLOSE</span> <span class="token-variable">c</span>;
<span class="token-keyword">END</span>$$
<span class="token-keyword">DELIMITER</span> ;
<span class="token-keyword">CALL</span> <span class="token-function">affecter_vacances</span>();
</code></pre>
                    <button class="copy-btn">Copier</button>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200 mt-6">
                    <h5 class="text-lg font-bold text-green-800 mb-2">Alternative sans curseur</h5>
                    <p class="text-gray-700">Dans ce cas précis, un simple `INSERT ... SELECT` est beaucoup plus performant et lisible. C'est un excellent exemple de situation où il faut éviter d'utiliser un curseur.</p>
                     <div class="code-block-wrapper mt-2">
                        <pre class="code-block"><code class="language-sql"><span class="token-keyword">INSERT INTO</span> <span class="token-variable">groupe</span> (<span class="token-variable">matricule</span>, <span class="token-variable">groupe</span>) 
<span class="token-keyword">SELECT</span> <span class="token-variable">matricule</span>, 'besoin vacances' 
<span class="token-keyword">FROM</span> <span class="token-variable">employe</span> 
<span class="token-keyword">WHERE</span> <span class="token-variable">etat</span> <span class="token-operator">=</span> <span class="token-string">'fatigué'</span>;
</code></pre>
                        <button class="copy-btn">Copier</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>