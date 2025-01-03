<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (isset($arParams["COMPONENT_ENABLE"]) && $arParams["COMPONENT_ENABLE"] === false)
	return;


$bDesignMode = $GLOBALS["APPLICATION"]->GetShowIncludeAreas() && is_object($GLOBALS["USER"]) && $GLOBALS["USER"]->IsAdmin();

if (!$bDesignMode && $arParams["IS_RSS"] == "Y")
{
	$APPLICATION->RestartBuffer();
	header("Content-Type: text/xml; charset=".LANG_CHARSET);
	header("Pragma: no-cache");
}

$arNavParams = CDBResult::GetNavParams();

$ADDITIONAL_CACHE_ID[] = $arNavParams["PAGEN"];
$ADDITIONAL_CACHE_ID[] = $arNavParams["SIZEN"];

$CACHE_PATH = "/".SITE_ID."/".LANGUAGE_ID.$this->__relativePath;
$arParams["CACHE_TIME"] = $arParams["CACHE_TIME"]>0?$arParams["CACHE_TIME"]:0;
if($this->StartResultCache($arParams["CACHE_TIME"], $ADDITIONAL_CACHE_ID, $CACHE_PATH))
{
	if($arParams["IS_RSS"] == "Y" && $bDesignMode)
	{
		ob_start();
		$this->IncludeComponentTemplate();
		$contents = ob_get_contents();
		ob_end_clean();
		echo "<pre>",htmlspecialchars($contents),"</pre>";
	}
	else
		$this->IncludeComponentTemplate();
}

if (!$bDesignMode && $arParams["IS_RSS"] == "Y")
{
	$r = $APPLICATION->EndBufferContentMan();
	echo $r;
	if(defined("HTML_PAGES_FILE") && !defined("ERROR_404")) CHTMLPagesCache::writeFile(HTML_PAGES_FILE, $r);
	die();
}

$modifier_path = $_SERVER["DOCUMENT_ROOT"].$arResult["__TEMPLATE_FOLDER"]."/result_modifier_nc.php";
$modifier_short_path = $_SERVER["DOCUMENT_ROOT"].$arResult["__TEMPLATE_FOLDER"]."/nc.php";

if (file_exists($modifier_short_path))
{
	require_once($modifier_short_path);
	$mod_name = "nc.php";
}
elseif (file_exists($modifier_path))
{
	require_once($modifier_path);
	$mod_name = "result_modifier_nc.php";
}

$nocahe_template_path = $_SERVER["DOCUMENT_ROOT"].$arResult["__TEMPLATE_FOLDER"]."/template_nc.php";
if (file_exists($nocahe_template_path))
{
	require_once($nocahe_template_path);
}


if($GLOBALS["APPLICATION"]->GetShowIncludeAreas() && $USER->isAdmin())
{

	$filename = ".parameters.php";
	$result_modifier_edit = "jsPopup.ShowDialog('/bitrix/admin/public_file_edit_src.php?site=".SITE_ID."&path=".urlencode($arResult["__TEMPLATE_FOLDER"])."%2F".$filename."', {'width':'770', 'height':'570', 'resize':true })";

	$this->AddIncludeAreaIcon(
	array(
		'URL'   => "javascript:".$result_modifier_edit.";",
		'SRC'   => $this->GetPath().'/images/edit.gif',
		'TITLE' => "Редактировать файл .parameters.php"
	));

	$filename = "result_modifier.php";
	$result_modifier_edit = "jsPopup.ShowDialog('/bitrix/admin/public_file_edit_src.php?site=".SITE_ID."&path=".urlencode($arResult["__TEMPLATE_FOLDER"])."%2F".$filename."', {'width':'770', 'height':'570', 'resize':true })";

	$this->AddIncludeAreaIcon(
	array(
		'URL'   => "javascript:".$result_modifier_edit.";",
		'SRC'   => $this->GetPath().'/images/edit.gif',
		'TITLE' => "Редактировать файл result_modifier.php"
	));

	$filename = $mod_name;
	$result_modifier_edit = "jsPopup.ShowDialog('/bitrix/admin/public_file_edit_src.php?site=".SITE_ID."&path=".urlencode($arResult["__TEMPLATE_FOLDER"])."%2F".$filename."', {'width':'770', 'height':'570', 'resize':true })";

	$this->AddIncludeAreaIcon(
	array(
		'URL'   => "javascript:".$result_modifier_edit.";",
		'SRC'   => $this->GetPath().'/images/edit.gif',
		'TITLE' => "Редактировать файл result_modifier_nc.php"
	));

	$filename = "template_nc.php";
	$template_nc_edit = "jsPopup.ShowDialog('/bitrix/admin/public_file_edit_src.php?site=".SITE_ID."&path=".urlencode($arResult["__TEMPLATE_FOLDER"])."%2F".$filename."', {'width':'770', 'height':'570', 'resize':true })";

	$this->AddIncludeAreaIcon(
	array(
		'URL'   => "javascript:".$template_nc_edit.";",
		'SRC'   => $this->GetPath().'/images/edit.gif',
		'TITLE' => "Редактировать файл template_nc.php"
	));

}

if (!empty($arResult["__RETURN_VALUE"]))
	return $arResult["__RETURN_VALUE"];
?>
