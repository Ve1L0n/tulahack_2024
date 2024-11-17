<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;
Extension::load('ui.bootstrap4');
?>

<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
    <?
    $APPLICATION->ShowHead();
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/scripts.js');

    ?>
	<?CJSCore::Init(array('jquery2'));?>

<link href="//cdn.datatables.net/2.1.8/css/dataTables.bootstrap4.css" type="text/css"  rel="stylesheet" />
<script src="//cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="//cdn.datatables.net/2.1.8/js/dataTables.bootstrap4.js"></script>
<script src="//cdn.jsdelivr.net/jquery.scrollto/2.1.2/jquery.scrollTo.min.js"></script>
<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="//www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <title><?= $APPLICATION->ShowTitle(); ?></title>
</head>

<body>
<? $APPLICATION->ShowPanel(); ?>
    <section class="<?=$USER->IsAuthorized()?"main":"auth"?>">
        <div class="container">
		<?if($USER->IsAuthorized()):?>
			<h1><?$APPLICATION->ShowTitle(false)?></h1>
			<div class="user">
				<span><?=$USER->getFormattedName()?></span>
				<a class="btn btn-info" href="/?logout=yes&<?=bitrix_sessid_get()?>">Выйти</a>
			</div>
      		
		<?endif;?>