<!DOCTYPE html>
<html lang="<?= $this->getLanguageLabel() ?>">
<head>
	<title su:var="pageTitle"></title>

	<!-- Main Meta Tags -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="charset" content="<?= $this->getLanguageCharset() ?>">

	<!-- SEO Meta Tags -->
	<meta name="description" content="<su: var='metaDescription'/>" />
	<meta name="keywords" content="<su: var='metaKeywords'/>" />
	<meta name="title" content="<su: var='metaTitle'/>" />

	<!-- Meta OG Tags for Social Media -->
	<meta property="og:type" content="website">
	<meta property="og:image" content="<su: var='ogImage'/>">
	<meta property="og:image:width" content="<su: var='ogImageWidth'/>">
	<meta property="og:image:height" content="<su: var='ogImageHeight'/>">
	<meta property="og:title" content="<su: var='ogTitle'/>">
	<meta property="og:description" content="<su: var='ogDescription'/>">
	<meta property="og:locale" content="<?= $this->getLanguageLabel() ?>">

	<!-- Icons -->
	<link rel="apple-touch-icon" sizes="180x180" href="~public/images/favicons/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="32x32" href="~public/images/favicons/favicon-32x32.png">

    <!-- Dynamic Header Tags -->
	<su: tags="Header"/>
</head>
<body>