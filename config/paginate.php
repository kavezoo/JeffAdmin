<?php
/**
 * JeffAdmin — controller-szintű lista lapozás.
 *
 * A plugin bootstrap betölti: Configure::load('JeffAdmin.paginate').
 * A JeffAdmin\Controller\AppController::initialize() írja a
 * $this->paginate['limit'] / ['maxLimit'] értékeket.
 *
 * Template-láthatóság (oszlopok, gombok): config/show.php — ez a fájl nem ahhoz tartozik.
 *
 * Host felülírás: Configure::write('JeffAdmin.paginate.limit', 25);
 * Controllerenként: $this->paginate['limit'] = 25; az index()-ben.
 */
return [
	'JeffAdmin' => [
		'paginate' => [
			// Oldalankénti sorok ($this->paginate['limit']).
			'limit' => 10,
			// Felső korlát / query param max ($this->paginate['maxLimit']).
			'maxLimit' => 100,
		],
	],
];
