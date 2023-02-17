<!DOCTYPE html>
<html lang="<?= $this->getLanguageLabel() ?>">

	<!--begin::Head-->
	<head>
		<meta charset="utf-8" />
		<title su:var="pageTitle"></title>
		
		<!-- Main Meta Tags -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="charset" content="<?= $this->getLanguageCharset() ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

		<!--end::Fonts-->

		<!--begin::Page Vendors Styles(used by this page)-->
		<!-- <link href="~public/cms/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css?v=7.0.4" rel="stylesheet" type="text/css" /> -->
		<script src="~public/cms/assets/plugins/global/plugins.bundle.min.js?v=7.0.4"></script>

		<!--end::Page Vendors Styles-->

		<!--begin::Global Theme Styles(used by all pages)-->
		<link href="~public/cms/assets/plugins/global/plugins.bundle.min.css?v=7.0.4" rel="stylesheet" type="text/css" />
		<link href="~public/cms/assets/plugins/custom/prismjs/prismjs.bundle.css?v=7.0.4" rel="stylesheet" type="text/css" />
		<link href="~public/cms/assets/css/style.bundle.min.css?v=7.0.4" rel="stylesheet" type="text/css" />
		<!--end::Global Theme Styles-->

		<!--begin::Layout Themes(used by all pages)-->
		<link href="~public/cms/assets/css/themes/layout/header/base/light.css?v=7.0.4" rel="stylesheet" type="text/css" />
		<link href="~public/cms/assets/css/themes/layout/header/menu/light.css?v=7.0.4" rel="stylesheet" type="text/css" />
		<link href="~public/cms/assets/css/themes/layout/brand/light.css?v=7.0.4" rel="stylesheet" type="text/css" />
		<link href="~public/cms/assets/css/themes/layout/aside/light.css?v=7.0.4" rel="stylesheet" type="text/css" />

		<link href="<?= $this->getRouteURL('cms-style') ?>" rel="stylesheet" type="text/css" />

		<!--begin::Global Theme Bundle(used by all pages)-->
		<script src="~public/cms/assets/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.4"></script>
		<script src="~public/cms/assets/js/scripts.bundle.min.js?v=7.0.4"></script>

		<!--end::Layout Themes-->
		<!-- Icons -->
		<link rel="apple-touch-icon" sizes="180x180" href="~public/images/favicons/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="32x32" href="~public/images/favicons/favicon-32x32.png">

		<!-- Dynamic Header Tags -->
	<su: tags="Header"/>

	
	<style>
		.hasError{
				border-color: red;
			}
		.kt-aside-menu .kt-menu__nav > .kt-menu__item.kt-menu__item--active > .kt-menu__heading, .kt-aside-menu .kt-menu__nav > .kt-menu__item.kt-menu__item--active > .kt-menu__link{
			background-color: #666482;
		}
		.growlUI h1{
			font-size: 16px;
		}
		.growlUI h2{
			font-size: 14px;
		}
	</style>

	</head>
	<!--end::Head-->