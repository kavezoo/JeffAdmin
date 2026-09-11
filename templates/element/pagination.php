<?php
/**
 * Lista-lapozó. A card-footerbe kell tenni:
 *
 * <div class="card-footer border-top d-flex align-items-center justify-content-between">
 *     <?= $this->element('JeffAdmin.pagination') ?>
 * </div>
 *
 * @var \App\View\AppView $this
 */

$paging = $this->Paginator->params();
if (!isset($paging['pageCount']) || (!isset($paging['currentPage']) && !isset($paging['page']))) {
    foreach ($paging as $pagingData) {
        if (is_array($pagingData) && isset($pagingData['pageCount'])) {
            $paging = $pagingData;
            break;
        }
    }
}

$pageCount = (int)($paging['pageCount'] ?? 1);
$recordCurrent = (int)($paging['count'] ?? 0);
$recordTotal = (int)($paging['totalCount'] ?? $paging['count'] ?? 0);
$requestedPage = (int)$this->getRequest()->getQuery('page', 0);
$currentPage = $requestedPage > 0
    ? $requestedPage
    : (int)($paging['currentPage'] ?? $paging['page'] ?? 1);
$currentPage = $pageCount > 0 ? max(1, min($currentPage, $pageCount)) : max(1, $currentPage);

$request = $this->getRequest();
$pageUrl = function (int $page) use ($request): string {
    $params = $this->Paginator->generateUrlParams(['page' => $page]);
    $query = $params['?'] ?? [];
    $query['page'] = $page;

    return $request->getPath() . '?' . http_build_query($query);
};

$link = function (string $url, string $text, array $options = []): string {
    $disabled = !empty($options['disabled']);
    $active = !empty($options['active']);
    $label = (string)($options['label'] ?? '');

    $li = 'page-item';
    if ($active) {
        $li .= ' active';
    }
    if ($disabled) {
        $li .= ' disabled';
    }

    $aria = '';
    if ($label !== '') {
        $aria .= ' aria-label="' . h($label) . '"';
    }

    if ($active) {
        return '<li class="' . $li . '"><span class="page-link" aria-current="page">' . $text . '</span></li>';
    }
    if ($disabled) {
        return '<li class="' . $li . '"><span class="page-link"' . $aria . ' aria-disabled="true">' . $text . '</span></li>';
    }

    return '<li class="' . $li . '"><a class="page-link" href="' . h($url) . '"' . $aria . '>' . $text . '</a></li>';
};

$iconFirst = $this->Icon->outline('chevrons-left', 'ja-page-icon');
$iconPrev = $this->Icon->outline('chevron-left', 'ja-page-icon');
$iconNext = $this->Icon->outline('chevron-right', 'ja-page-icon');
$iconLast = $this->Icon->outline('chevrons-right', 'ja-page-icon');
$ellipsis = '<li class="page-item ja-page-ellipsis disabled" aria-hidden="true"><span class="page-link">&hellip;</span></li>';

$window = 7;
$midStart = max(1, $currentPage - (int)floor(($window - 1) / 2));
$midEnd = min($pageCount, $midStart + $window - 1);
$midStart = max(1, $midEnd - $window + 1);
?>
<span class="small text-muted ja-page-info"><strong><?= (int)$recordCurrent ?></strong>/<?= (int)$recordTotal ?> rekord, <strong><?= (int)$currentPage ?></strong>/<?= (int)$pageCount ?> oldal</span>
<?php if ($pageCount > 1) : ?>
<nav aria-label="<?= h(__('Lapozás')) ?>">
	<ul class="pagination mb-0">
		<?= $link($pageUrl(1), $iconFirst, ['disabled' => $currentPage <= 1, 'label' => __('Első oldal')]) ?>
		<?= $link($pageUrl($currentPage - 1), $iconPrev, ['disabled' => $currentPage <= 1, 'label' => __('Előző oldal')]) ?>
<?php if ($midStart > 1) : ?>
		<?= $ellipsis ?>
<?php endif; ?>
<?php
		for ($p = $midStart; $p <= $midEnd; $p++) :
			echo $link($pageUrl($p), (string)$p, ['active' => $p === $currentPage]);
		endfor;
?>
<?php if ($midEnd < $pageCount) : ?>
		<?= $ellipsis ?>
<?php endif; ?>
		<?= $link($pageUrl($currentPage + 1), $iconNext, ['disabled' => $currentPage >= $pageCount, 'label' => __('Következő oldal')]) ?>
		<?= $link($pageUrl($pageCount), $iconLast, ['disabled' => $currentPage >= $pageCount, 'label' => __('Utolsó oldal')]) ?>
	</ul>
</nav>
<?php endif; ?>
