<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<ul class="nav nav-tabs" id="tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="first-tab" data-toggle="tab"
           href="#first-tab-content" role="tab" aria-controls="first-tab-content"
           aria-selected="true">Мои пациенты <span class="badge btn-success badge-pill"><?=$arResult['AllCountPat']?></span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="first2-tab" data-toggle="tab"
           href="#first2-tab-content" role="tab" aria-controls="first2-tab-content"
           aria-selected="true">Курс лечения / график приёма</a>
    </li>	
    <li class="nav-item">
        <a class="nav-link" id="second-tab" data-toggle="tab"
           href="#second-tab-content" role="tab" aria-controls="second-tab-content"
           aria-selected="false">Мониторинг</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="third-tab" data-toggle="tab"
           href="#third-tab-content" role="tab" aria-controls="third-tab-content"
           aria-selected="false">Мои уведомления <span class="badge badge-pill <?=($arResult['CheckCountsNotif']!=$arResult['AllCountNotif'])?'btn-danger':'btn-success'?>"><?=$arResult['CheckCountsNotif'] ?> / <?=$arResult['AllCountNotif']?></span></a>
    </li>
</ul>

<div class="tab-content" id="tab-content">
    <div class="tab-pane fade show active" id="first-tab-content" role="tabpanel"
         aria-labelledby="first-tab">
		<?
		$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			Array(
				"AREA_FILE_SHOW" => "file", 
				"PATH" => SITE_TEMPLATE_PATH."/include/botInfo.php",
				"EDIT_TEMPLATE" => ""
			)
		);
		?>
		<div class="sticker-right sticker-info" data-sticker="Для удобства используйте поиск и сортировку по столбцам">
					<table id="patients" class="dataTablesEx table table-bordered" style="width:100%">
						<thead>
							<tr>
								<th>Последняя активность</th>
								<th>Пациент</th>
								<?if($arResult['isAdmin']):?>
									<th>Лечащий врач</th>
								<?endif;?>								
								<th>Действие</th>
							</tr>
						</thead>
						<tbody>
						<?foreach($arResult['DataElementsPats'] as $arPats):?>
							<tr>
								<?if($arResult['LastDateNotif'][$arPats['id']] && $arPats['LAST_ACTIVE_DATE']):?>
									<td><?=(strtotime($arResult['LastDateNotif'][$arPats['id']]) >= strtotime($arPats['LAST_ACTIVE_DATE']) ? $arResult['LastDateNotif'][$arPats['id']] : $arPats['LAST_ACTIVE_DATE'])?></td>
								<?else:?>
									<td><?=$arPats['LAST_ACTIVE_DATE'] ?? '-'?></td>
								<?endif;?>
								<td><?=$arPats['name']?></td>
								<?if($arResult['isAdmin']):?>
									<td><?=$arResult['Fio'][$arPats['doctor_id']]?></td>
								<?endif;?>
								<td> 
									<a class="btn btn-info" onclick="goToHeal(<?=$arPats['id']?>)" href="javascript:">Курс лечения</a></br>
									<a class="btn btn-warning mt-1" onclick="goToMonitoring(<?=$arPats['id']?>)" href="javascript:">Мониторинг</a></br>
									<?if($arResult['NoCheckNotif'][$arPats['id']]):?>
										<a class="btn btn-danger mt-1 goToNotif notifBtn-<?=$arResult['NoCheckNotif'][$arPats['id']]?>" onclick="goToNotif(<?=$arResult['NoCheckNotif'][$arPats['id']]?>)" href="javascript:">Уведомление</a>
									<?endif;?>
								</td>
							</tr>
						<?endforeach;?>      
						</tbody>
						<tfoot>
							<tr>
								<th>Последняя активность</th>
								<th>Пациент</th>								
								<?if($arResult['isAdmin']):?>
									<th>Лечащий врач</th>
								<?endif;?>								
								<th>Действие</th>
							</tr>
						</tfoot>
					</table>

		</div>


    </div>
    <div class="tab-pane fade" id="first2-tab-content" role="tabpanel"
         aria-labelledby="first2-tab">
        <div class="sticker-right sticker-info" data-sticker="Для загрузки графика приёма требуется выбрать нужного вам пациента">
			<div class="form-group">
				<label for="FormControlSelect1" data-live-search="true">Выберите нужного пациента</label><br><br>
				<select class="form-control" id="FormControlSelect1">
					<option selected disabled>Для поиска начните вводить часть ФИО</option>
					<?foreach($arResult['DataElementsPats'] as $arPats):?>
						<option value="<?=$arPats['id']?>"><?=$arPats['name']?></option>	
					<?endforeach;?>
				</select>
			</div>	
			<div class="topContentMedic"></div>
		</div>
		<div class="mainContentMedic"></div>
    </div>	
    <div class="tab-pane fade" id="second-tab-content" role="tabpanel"
         aria-labelledby="second-tab">
        <div class="sticker-right sticker-info" data-sticker="Для загрузки мониторинга требуется выбрать нужного вам пациента">
			<div class="form-group">
				<label for="FormControlSelect2" data-live-search="true">Выберите нужного пациента</label><br><br>
				<select class="form-control" id="FormControlSelect2">
					<option selected disabled>Для поиска начните вводить часть ФИО</option>
					<?foreach($arResult['DataElementsPats'] as $arPats):?>
						<option value="<?=$arPats['id']?>"><?=$arPats['name']?></option>	
					<?endforeach;?>
				</select><br><br>
				<label >Выберите период анализа</label><br><br>
				<input name="dates" value="<?=date('01-m-Y');?> - <?=date('d-m-Y');?>" type="text" id="DateRange">
			</div>	
			<div class="topContentMon"></div>
		</div>
		<div class="mainContentMon1 mainContentBlock" id="mainContentMon1"></div>
		<div class="mainContentMon2 mainContentBlock" id="mainContentMon2"></div>
		<div class="mainContentMon3 mainContentBlock" id="mainContentMon3"></div>
		<div class="mainContentMon4 mainContentBlock" id="mainContentMon4"></div>
    </div>
    <div class="tab-pane fade" id="third-tab-content" role="tabpanel"
         aria-labelledby="third-tab">

		<div class="sticker-right sticker-info" data-sticker="На данной странице представлены все уведомления из проложения от ваших пациентов">
			<table id="notif" class="dataTablesEx table table-bordered" style="width:100%">
				<thead>
					<tr>
						<th>Дата поступления</th>
						<?if($arResult['isAdmin']):?>
						<th>Лечащий врач</th>
						<?endif;?>
						<th>Пациент</th>
						<th>Описание</th>
						<th>Действие</th>
					</tr>
				</thead>
				<tbody>
				<?foreach($arResult['HlElementsNotif'] as $arVal):?>			
					<tr class="classId<?=$arVal['ID']?> <?=($arVal['UF_CHECK']?'table-success':'')?>">
						<td><?=$arVal['UF_DATE']?></td>
						<?if($arResult['isAdmin']):?>
							<td><?=$arResult['Fio'][$arVal['UF_DOC']]?></td>
						<?endif;?>
						<td><?=$arResult['DataElementsPats'][$arVal['UF_PATIENT']]['name']??'-'?></td>
						<td><?=$arVal['UF_DESC']?></td>
						<td><?=!$arVal['UF_CHECK']?'<a class="btn btn-success" onclick="readNotif('.$arVal['ID'].', $(this))" href="javascript:">Прочтено</a>':''?></td>
					</tr>
				<?endforeach;?>      
				</tbody>
				<tfoot>
					<tr>
						<th>Дата поступления</th>
						<?if($arResult['isAdmin']):?>
						<th>Лечащий врач</th>
						<?endif;?>
						<th>Пациент</th>
						<th>Описание</th>
						<th>Действие</th>
					</tr>
				</tfoot>
			</table>
		</div>


    </div>
</div>