<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult['isAdmin'] = $USER->IsAdmin();
$arFilter = $arResult['isAdmin'] != true ? ["UF_DOC" => $USER->GetID()] : [];

$arHlElementsNotif = HLH::getInstance()->getElementList(
	HLB_NOTIF,
	($arFilter)
); 

$arFilter = $arResult['isAdmin'] != true ? ["doctor_id" => $USER->GetID()] : [];
$arDataElements = getInfo($arFilter);
$arResult['DataElementsPats'] = $arDataElements['USERS'];
$arResult['Fio'] = $arResult['NoCheckNotif'] = $arResult['LastDateNotif'] = []; 

foreach($arHlElementsNotif as $key => $arVal){
	if(!$arResult['Fio'][$arVal['UF_DOC']]){
		$rsUser = CUser::GetByID($arVal['UF_DOC']);
		$arUser = $rsUser->Fetch();
		$arResult['Fio'][$arVal['UF_DOC']] = $arUser['LAST_NAME'].' '.$arUser['NAME'].' '.$arUser['SECOND_NAME'];
	}
	$arResult['LastDateNotif'][$arVal['UF_PATIENT']] = $arVal['UF_DATE'];
	if(!$arVal['UF_CHECK']){
		$arResult['NoCheckNotif'][$arVal['UF_PATIENT']] = $arVal['ID'];
	}
}
$arResult['CheckCountsNotif'] = array_count_values(array_column($arHlElementsNotif, 'UF_CHECK'))[HLB_NOTIF_CHECK];
$arResult['AllCountNotif'] = is_array($arHlElementsNotif) ? count($arHlElementsNotif) : 0;
$arResult['AllCountPat'] = is_array($arResult['DataElementsPats']) ? count($arResult['DataElementsPats']) : 0;
$arResult['HlElementsNotif'] = $arHlElementsNotif;

$this->__component->arResult = $arResult; 
?>