<?
global $USER;

define('GROUP_DOCS', 6);  	//Группа юзеров 6 (врачи) 
define('IB_MEDIC', 5); 	//График приема лек. средств
define('HLB_NOTIF', 1); 	//ХЛБ уведомления
define('HLB_NOTIF_CHECK', 2); 	//ХЛБ уведомления UF_CHECK = Y
define('URL_TELEGRAM_BOT', "https://t.me/MedHelper71_Bot?start="); 	//Ссылка на приглашение в TLG


use \Bitrix\Highloadblock as HL,
    \Bitrix\Main\Entity,
    \Bitrix\Main\Loader;
use Bitrix\Main\Application;

function inviteTLG(int $id){
	return URL_TELEGRAM_BOT.(base64_encode($id.":".rand(100,1000)));
}


class HLH
{
    private static $instance;
    public static $LAST_ERROR;

    public static function getInstance()
    {
        if (!self::$instance) {
            Loader::includeModule('highloadblock');
            self::$instance = new HLH();
        }
        return self::$instance;
    }

    public function getList($arOrder = [], $arFilter = [], $arMoreParams = [])
    {
        $arParams = [];
        if ($arOrder) $arParams['order'] = $arOrder;
        if ($arFilter) $arParams['filter'] = $arFilter;
        if ($arMoreParams) {
            foreach ($arMoreParams as $k => $arMoreParam) {
                $key = \mb_strtolower($k);
                $arParams[$key] = $arMoreParam;
            }
        }
        $rHlblock = HL\HighloadBlockTable::getList($arParams);
        return $rHlblock->fetchAll();
    }

    public function getOne($arOrder=[],$arFilter=[],$arMoreParams=[])
    {
        $arParams = [];
        if($arOrder) $arParams['order'] = $arOrder;
        if($arFilter) $arParams['filter'] = $arFilter;
        if($arMoreParams) {
            foreach ($arMoreParams as $k=>$arMoreParam) {
                $key = \mb_strtolower($k);
                $arParams[$key] = $arMoreParam;
            }
        }
        return HL\HighloadBlockTable::getList($arParams)->fetch();
    }

    public function getEntityTable($hlblockID)
    {
        if (!$hlblockID) return false;
        $hlblock = HL\HighloadBlockTable::getById($hlblockID)->fetch();
        if (!$hlblock) return false;
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }

    public function getElementsResource($hlblockID, $arFilter = [], $arOrder = ["ID" => "ASC"], $arSelect = ['*'], $arMoreParams = [])
    {
        $entity = $this->getEntityTable($hlblockID);
        $arParams = [];
        if ($arFilter) $arParams['filter'] = $arFilter;
        if ($arOrder) $arParams['order'] = $arOrder;
        if ($arSelect) $arParams['select'] = $arSelect;
        if ($arMoreParams) {
            foreach ($arMoreParams as $k => $arMoreParam) {
                if (!$arMoreParam) continue;
                $key = \mb_strtolower($k);
                $arParams[$key] = $arMoreParam;
            }
        }
        return $entity::getList($arParams);
    }

    public function getElementList($hlblockID, $arFilter = [], $arOrder = ["ID" => "ASC"], $arSelect = ['*'], $arMoreParams = [])
    {
        if (!$hlblockID) return false;
        $rsData = $this->getElementsResource($hlblockID, $arFilter, $arOrder, $arSelect, $arMoreParams);
        $arResult = [];
        while ($arData = $rsData->Fetch()) {
            $arResult[] = $arData;
        }
        return $arResult;
    }

    public function getTotalCount($hlblockID, $arFilter=[], $cache=[])
    {
        $entity = $this->getEntityTable($hlblockID);
        return (int) $entity::getCount($arFilter, $cache);
    }

    public function getElement($hlblockID, $arFilter = [], $arSelect = ['*'], $arMoreParams = [])
    {
        if (!$hlblockID) return false;
        return $this->getElementsResource($hlblockID, $arFilter, [], $arSelect, $arMoreParams)->Fetch();
    }

    public function getElementById($hlblockID, $id, $arMoreParams = [])
    {
        if (!$hlblockID) return false;
        return $this->getElement($hlblockID, ['ID' => $id], [], $arMoreParams);
    }

    public function addElement($hlblockID, $arFields = [])
    {
        if (!$hlblockID || !$arFields) return false;
        $entity = $this->getEntityTable($hlblockID);
        $result = $entity::add($arFields);
        if ($result->isSuccess()) {
            return $result->getId();
        } else {
            self::$LAST_ERROR = $result->getErrors();
        }
        return false;
    }

    public function deleteElement($hlblockID, $ID = null)
    {
        if (!$hlblockID || !$ID) return false;
        $entity = $this->getEntityTable($hlblockID);
        $result = $entity::delete($ID);
        if ($result->isSuccess()) {
            return true;
        } else {
            self::$LAST_ERROR = $result->getErrors();
        }
        return false;
    }

    public function updateElement($hlblockID, $ID = null, $arFields = [])
    {
        if (!$hlblockID || !$ID || !$arFields) return false;
        $entity = $this->getEntityTable($hlblockID);
        $result = $entity::update($ID, $arFields);
        if ($result->isSuccess()) {
            return true;
        } else {
            self::$LAST_ERROR = $result->getErrors();
        }
        return false;
    }

    public function getFieldValue($fieldName = '', $fieldID = null)
    {
        $arResult = $this->getFieldValuesList([], [
            'USER_FIELD_NAME' => $fieldName,
            'ID' => $fieldID,
        ]);
        if ($arResult[0]) {
            return $arResult[0];
        }
        return false;
    }

    public function getFieldValues($fieldName = '', $arSort = ['SORT' => 'ASC'])
    {
        return $this->getFieldValuesList($arSort, ['USER_FIELD_NAME' => $fieldName]);
    }

    public function getFieldValuesList($arSort = ['SORT' => 'ASC'], $arFilter = [])
    {
        $oFieldEnum = new \CUserFieldEnum;
        $rsValues = $oFieldEnum->GetList($arSort, $arFilter);
        $arResult = [];
        while ($value = $rsValues->Fetch()) {
            $arResult[] = $value;
        }
        return $arResult;
    }

    public function getFields($hlblockID)
    {
        if (!$hlblockID) return false;
        $hlblock = HL\HighloadBlockTable::getById($hlblockID)->fetch();
        if (!$hlblock) return false;
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        return $entity->getFields();
    }

}

function getInfo($arFilter = [], $iType = 0){
	$data = array(
		'method'  => 'getInfo',
		'ApiKey' => '***********',
		'filter' => $arFilter,
		'type' => $iType
	);	
	
	$ch = curl_init('http://*********');
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE)); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$res = curl_exec($ch);
	curl_close($ch);	 

	$arResult = json_decode($res, JSON_UNESCAPED_UNICODE);

	return $arResult;
}