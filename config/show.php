<?php
/**
 * JeffAdmin — alapértelmezett megjelenítési kapcsolók.
 *
 * A plugin bootstrap betölti: Configure::load('JeffAdmin.show').
 * A sablonok Configure::read('JeffAdmin')-nel olvassák, majd a saját
 * $showLocal tömbjükkel felülírhatják (csak a felülírandó kulcsokat
 * kell megadni; a többi az itt lévő alapértelmezés marad).
 *
 * Értékek:
 *   true / false — megjelenik / elrejtve
 *   rowDblClick  — 'edit' | 'view' | 'none' (más érték = none)
 *
 * Szerkezet: JeffAdmin.<action>.<kapcsoló>
 *   index  — listanézet (táblázat)
 *   add    — új rekord űrlap
 *   edit   — szerkesztő űrlap
 *   view   — részletes nézet
 */
return [
	'JeffAdmin' => [

		// --- index: listanézet oszlopai és műveleti gombjai ---
		'index' => [
			// Sorok kijelölése: checkbox oszlop + „összes kijelölése” a fejlécben.
			'rowCheckbox' 	=> true,
			// Rekord azonosító (id) oszlop.
			'rowId' 		=> true,
			// Láthatóság (visible) oszlop: szem / áthúzott szem ikon.
			'visible' 		=> true,
			// Sorrend / pozíció (pos) oszlop.
			'pos' 			=> true,
			// Létrehozás dátuma (created); a modified-dal közös datetime oszlopban.
			'created' 		=> true,
			// Utolsó módosítás dátuma (modified); a created-del közös datetime oszlopban.
			'modified' 		=> true,
			// Kapcsolódó rekordok darabszáma: a *_count mezők külön oszlopban.
			'counts' 		=> true,
			// Műveletek: megtekintés (view) gomb.
			'viewButton' 	=> true,
			// Műveletek: szerkesztés (edit) gomb.
			'editButton' 	=> true,
			// Műveletek: törlés (delete) gomb, megerősítéssel.
			'deleteButton' 	=> true,
			// Sor dupla kattintás: 'edit' | 'view' | 'none' (alap: edit).
			'rowDblClick' 	=> 'edit',
		],

		// --- edit: szerkesztő űrlap lábléc gombjai ---
		'edit' => [
			// Mentés (submit) gomb.
			'saveButton' 	=> true,
			// Mégse: visszalépés a listára, mentés nélkül.
			'cancelButton' 	=> true,
		],

		// --- add: új rekord űrlap lábléc gombjai ---
		'add' => [
			// Mentés (submit) gomb.
			'saveButton' 	=> true,
			// Mégse: visszalépés a listára, mentés nélkül.
			'cancelButton' 	=> true,
		],

		// --- view: részletes nézet gombjai és kapcsolódó táblák ---
		'view' => [
			// Szerkesztés gomb a részletes nézeten.
			'editButton' 	=> true,
			// Vissza / mégsem: visszalépés a listára.
			'cancelButton' 	=> true,
			// Kapcsolódó táblák (hasMany / belongsToMany) listája a rekord alatt.
			'relatedTables'	=> true,
		],
	],
];
