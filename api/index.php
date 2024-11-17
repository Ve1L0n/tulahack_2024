<?
include ($_SERVER["DOCUMENT_ROOT"]. "/bitrix/modules/main/include/prolog_before.php");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Connection:keep-alive");
header("Content-Type: application/json; charset=utf-8");
if (!CModule::IncludeModule("iblock"))
	return false;

$arMessages=array(
	"method_error"=>"Метод не указан",
	"doc_error"=>"Врач не найден",
	"pat_error"=>"Пациент не указан",
);

$arResult["result"] = $arError = [];
if(!$_REQUEST["method"]) $arError[] = $arMessages["method_error"];

if(!$arError)
{
	switch($_REQUEST["method"]){
		// /api/?method=get_list_docs
		case "get_list_docs": 
		
		$rsResult = \Bitrix\Main\UserGroupTable::getList(array(

			'order' => array('USER.LAST_LOGIN'=>'DESC'),

			'filter' => array(
				'USER.ACTIVE'    => 'Y',
				'GROUP_ID' => GROUP_DOCS,
			),

			'select' => array(
				'ID'=>'USER.ID', 
				'NAME'=>'USER.NAME', 
				'LAST_NAME'=>'USER.LAST_NAME', 
				'SECOND_NAME'=>'USER.SECOND_NAME', 
				'PHONE'=>'USER.PERSONAL_PHONE', 
			),

		));

		while ($arUser = $rsResult->fetch()) 

		{
			$arResult["result"][] = $arUser;
		}

		break;
		// /api/?method=add_notif&idDoc=2&idPat=321
		case "add_notif":
			if(!$_REQUEST["idDoc"]) $arError[] = $arMessages["doc_error"];
			if(!$_REQUEST["idPat"]) $arError[] = $arMessages["pat_error"];
			if(!$arError)
			{
				switch((int)$_REQUEST["type"]){
					default:
						$sDesc = 'Уведомление о необходимости встречи';
					break;
					
				}
				$rsUser = CUser::GetByID((int)$_REQUEST["idDoc"]);
				$arUser = $rsUser->Fetch();
				if(!$arUser){ 
					$arError[] = $arMessages["doc_error"];
				} else {
					$arFields = [
						'UF_DOC' => (int)$_REQUEST["idDoc"],
						'UF_PATIENT' => htmlspecialchars($_REQUEST["idPat"]),
						'UF_DESC' => $sDesc
					];
				
					$id = HLH::getInstance()->addElement(HLB_NOTIF, $arFields);		
				}
			}
		break;
		// /api/?method=get_recept&idPat=321		
		case "get_recepts":
			if(!$_REQUEST["idPat"]) $arError[] = $arMessages["pat_error"];
			if(!$arError)
			{	
				$arFilter = [
					"IBLOCK_ID" => IB_MEDIC,
					"PROPERTY_ID_PAT" => $_REQUEST["idPat"],
				];
				$rsElement = CIBlockElement::GetList(['ID'=>'DESC'], $arFilter, false, false, ['ID','IBLOCK_ID','NAME','DATE_CREATE','DETAIL_TEXT']);
				while($obElement = $rsElement->GetNextElement()){
						$arItem = $obElement->GetFields();
						$arItem['PROP'] = $obElement->GetProperties();
						$arResult['ALL'][] = $arItem;		
				}			
				
				$arResult["result"] = $arResult ?? false;
			}
		break;		
	

	}

}

if(count($arError)==0){
	echo json_encode(array("status"=>"ok","message"=>$arResult["result"]));
}
else{
	echo json_encode(array("status"=>"error","message"=>implode("<br>",$arError)));
}
?>