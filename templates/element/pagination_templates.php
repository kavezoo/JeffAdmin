<?php
	$this->Paginator->setTemplates([
		'sort' => '<a href="{{url}}">{{text}}</a>',
		'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <span class="sort-arrows">' . $this->Icon->filled('triangle', 'sort-arrows__icon') . '</span></a>',
		'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <span class="sort-arrows">' . $this->Icon->filled('triangle-inverted', 'sort-arrows__icon') . '</span></a>',
	]);
?>