<?
include ($_SERVER["DOCUMENT_ROOT"]. "/bitrix/modules/main/include/prolog_before.php");
if(!$USER->IsAuthorized()) exit;
$arMessages=array(
	"method_error"=>"Метод не указан",
);

$arResult["result"] = $arError = [];
if(!$_REQUEST["method"]) $arError[] = $arMessages["method_error"];

if(!$arError)
{
	switch($_REQUEST["method"]){
		case "readNotif":
			if((int)$_REQUEST['ID']>0){
				HLH::getInstance()->updateElement(HLB_NOTIF, (int)$_REQUEST['ID'], ['UF_CHECK' => HLB_NOTIF_CHECK]);	
			}
		break;		
		case "getStat":
			if((int)$_REQUEST['idPat']>0){
				$arTitle = [
					"Temp" => "Температура",
					"PressureSystol" => "Систолическое кровяное давление",
					"PressureDiastol" => "Диастолическое кровяное давление",
					"text" => "Самочувствие",
				
				];
				$arFilter = $USER->IsAdmin() != true ? ["doctor_id" => $USER->GetID()] : [];
				$arFilter['id'] = $_REQUEST['idPat'];
				$arFilter['date'] = $_REQUEST['date'];
				$arResultStat = getInfo($arFilter,1);	
				$arResult["result"] = $arALL = [];
				$prop = 1;
				foreach($arResultStat as $keyType => $arType){
					foreach($arType as $key => $arData){
						$arALL[$keyType][] = "['".$arData['date'].($arData['dayPart']=='m'?"У":"В")."',".$arData['val']."]";
							
					}						
					$arResult["result"][$keyType] .= "
				
							<script>
								google.charts.load('current',{packages:['corechart']});
								google.charts.setOnLoadCallback(drawChart".$prop.");

								function drawChart".$prop."() {
								// Set Data
								const data = google.visualization.arrayToDataTable([
								  ['Дата', '".$arTitle[$keyType]."'],
								  ".implode(",",$arALL[$keyType])."
								]);
								// Set Options
								const options = {
								  title: '".$arTitle[$keyType]."',
					
								  // curveType: 'function',
								  legend: { position: 'bottom' }

								};
								// Draw
								const chart = new google.visualization.LineChart(document.getElementById('mainContentMon".$prop."'));
								chart.draw(data, options);
								}	

							</script>

						";									
					$prop++;
				}
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