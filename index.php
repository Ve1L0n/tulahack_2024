<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет лечащего врача");

$APPLICATION->IncludeComponent(
    "medical:component",
    "",
    Array(
		"IBLOCK_NB_ID" => "103",
        "HLB_NOTIF" => HLB_NOTIF,
        "IBLOCK_ISSUES_ID" => "97",
        "IBLOCK_MOBILEIDS_ID" => "179",
    )
);
