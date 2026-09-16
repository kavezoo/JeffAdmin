<?php
/**
 * Sidebar shell (logo + nav).
 *
 * Override in the host (prefix-first), e.g.:
 * - templates/Admin/element/aside.php
 * - templates/Admin/element/plugin/JeffAdmin/aside.php
 * - templates/plugin/JeffAdmin/Admin/element/aside.php
 *
 * @var \Cake\View\View $this
 */
?>
      <aside class="menu-sidebar" id="main-sidebar">
        <div class="logo"><a class="logo-link" href="<?= $this->Url->build('/') ?>" aria-label="JeffAdmin home"><span class="logo-mark" aria-hidden="true">J</span><span class="logo-text">JeffAdmin</span></a>
          <button class="sidebar-close js-sidebar-toggle" type="button" aria-label="Close navigation"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        </div>
        <div class="menu-sidebar__content js-scrollbar1">
		  <?= $this->Layout->element('nav') ?>
        </div>
      </aside>
