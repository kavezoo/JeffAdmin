<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="generator" content="CoolAdmin 3.4.0"/>
    <meta name="description" content="Responsive data tables with horizontal scroll affordances and striped/hover variants."/>
    <title>Data tables | CoolAdmin Bootstrap 5 Admin Dashboard</title>
<?php /*
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="Data tables | CoolAdmin Bootstrap 5 Admin Dashboard"/>
    <meta property="og:description" content="Responsive data tables with horizontal scroll affordances and striped/hover variants."/>
    <meta property="og:image" content="screenshots/cooladmin-bootstrap-dashboard-2.png"/>
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:title" content="Data tables | CoolAdmin Bootstrap 5 Admin Dashboard"/>
    <meta name="twitter:description" content="Responsive data tables with horizontal scroll affordances and striped/hover variants."/>
    <meta name="theme-color" content="#4272d7"/>
    <link href="css/font-face.css" rel="stylesheet" media="all"/>
    <link rel="preconnect" href="https://rsms.me/"/>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css"/>
*/ ?>
    <!-- JeffAdmin CSS --><?= $this->Html->css([
		'JeffAdmin./vendor/fontawesome-7.3.1/css/all.min',
		'JeffAdmin./vendor/bootstrap-5.3.8.min',
		'JeffAdmin./vendor/tom-select/css/tom-select.bootstrap5.min',
		'JeffAdmin./vendor/flatpickr/dist/flatpickr.min',
		'JeffAdmin./vendor/css-hamburgers/hamburgers.min',
		'JeffAdmin./vendor/sweetalert2/sweetalert2.min',
		'JeffAdmin./css/theme',
		'JeffAdmin./css/app',
		'JeffAdmin./css/main',
	]) ?>

	<!-- Vendor CSS --><?= $this->fetch('css') ?>
	
  </head>
  <body class="app"><a class="visually-hidden-focusable skip-link" href="#main-content">Skip to main content</a>
    <div class="page-wrapper">
      
	  <?= $this->element('JeffAdmin.header_top') ?>
	  
      <aside class="menu-sidebar" id="main-sidebar">
        <div class="logo"><a class="logo-link" href="index.html" aria-label="CoolAdmin home"><span class="logo-mark" aria-hidden="true">J</span><span class="logo-text">NEW JeffAdmin</span></a>
          <button class="sidebar-close js-sidebar-toggle" type="button" aria-label="Close navigation"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        </div>
        <div class="menu-sidebar__content js-scrollbar1">
		  <?= $this->element('JeffAdmin.nav') ?>
        </div>
      </aside>
      <div class="page-container">
	  
        <?= $this->element('JeffAdmin.header') ?>
		
        <main class="main-content" id="main-content">
          <div class="section__content section__content--p30">
            <div class="container-fluid">

<?php /*
						<div class="alert alert-primary alert-dismissible fade show shadow" role="alert">
							<i class="fa-solid fa-circle-info" style="margin-right:8px;"></i>
							<strong>Heads up</strong> — this is an informational alert with default Bootstrap styling.
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>


            <div class="row">
              <div class="col-md-4 col-sm-12 mb-3">
                  <section class="m-card notice-card notice-card--warning">
                      <span class="notice-card__icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                      <div class="notice-card__body">
                          <h3 class="notice-card__title">Storage almost full</h3>
                          <p class="notice-card__text">You’re using 82% of your 100 GB plan. Consider upgrading or pruning old projects to avoid hitting the cap.</p>
                          <div class="notice-card__actions">
                              <button type="button" class="m-btn m-btn--primary" style="height: 28px; padding: 0 10px; font-size: 12px;">Upgrade plan</button>
                          </div>
                      </div>
                  </section>
              </div>

              <div class="col-md-4 col-sm-12 mb-3">
                  <section class="m-card notice-card notice-card--warning">
                      <span class="notice-card__icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                      <div class="notice-card__body">
                          <h3 class="notice-card__title">Storage almost full</h3>
                          <p class="notice-card__text">You’re using 82% of your 100 GB plan. Consider upgrading or pruning old projects to avoid hitting the cap.</p>
                          <div class="notice-card__actions">
                              <button type="button" class="m-btn m-btn--primary" style="height: 28px; padding: 0 10px; font-size: 12px;">Upgrade plan</button>
                          </div>
                      </div>
                  </section>
              </div>
            </div>
*/ ?>

<?= $this->fetch('content') ?>

              <?= $this->element('JeffAdmin.footer') ?>

            </div>
          </div>
        </main>
      </div>
    </div>

    <!-- JeffAdmin JS --><?= $this->Html->script([
		'JeffAdmin./js/vanilla-utils',
		'JeffAdmin./vendor/bootstrap-5.3.8.bundle.min',
		'JeffAdmin./vendor/tom-select/js/tom-select.complete.min',
		'JeffAdmin./vendor/flatpickr/dist/flatpickr.min',
		'JeffAdmin./vendor/flatpickr/dist/l10n/hu',
		'JeffAdmin./vendor/sweetalert2/sweetalert2.min',
		'JeffAdmin./js/form-datetime-config',	// xx
		'JeffAdmin./js/form-datetime',			// xx
		'JeffAdmin./js/form-number-config',		// xx
		'JeffAdmin./js/number-spinner',
		'JeffAdmin./js/table-row-select',
		'JeffAdmin./js/table-row-dblclick',
		'JeffAdmin./js/confirm-delete',
		'JeffAdmin./vendor/hugerte/hugerte.min',
		'JeffAdmin./js/bootstrap5-init',
		'JeffAdmin./js/main-vanilla',
		'JeffAdmin./js/modern-plugins',
	]) ?>

	<!-- Vendor JS --><?= $this->fetch('script') ?>
	
  </body>
</html>