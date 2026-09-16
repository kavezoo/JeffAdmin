<?php
/**
 * JeffAdmin — oldalsáv menü (alapértelmezés: üres).
 *
 * A plugin bootstrap betölti: Configure::load('JeffAdmin.nav').
 * A host alkalmazás Configure::write('JeffAdmin.nav', …) vagy
 * templates/plugin/JeffAdmin/element/nav.php felülírással adja meg a tételeket.
 *
 * Elem típusok:
 *   group — lenyíló csoport (label, icon?, items[])
 *   link  — egy link (controller, label, icon?, action? = index, prefix? = kérés prefix)
 */
return [
	'JeffAdmin' => [
		'nav' => [
			// Host / más projekt tölti fel. Példa:
			// [
			// 	'type' => 'group',
			// 	'label' => 'Tables',
			// 	'icon' => 'fa-solid fa-table',
			// 	'items' => [
			// 		['controller' => 'Articles', 'label' => 'Articles'],
			// 	],
			// ],
			// [
			// 	'type' => 'link',
			// 	'controller' => 'Dashboard',
			// 	'label' => 'Dashboard',
			// 	'icon' => 'fa-solid fa-gauge',
			// ],
		],
	],
];
