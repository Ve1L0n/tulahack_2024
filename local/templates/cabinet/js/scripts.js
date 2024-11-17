 $(document).ready(function() {
	 
	 function copyToClipboard(text) {
	  if (!navigator.clipboard) {

		const $copyEl = $('<input style="position:fixed;top:0;left:0;" type="text" />');
		$copyEl.val(text).appendTo('body')
		  .trigger('focus').trigger('select');
		try {
		  document.execCommand('copy');
		} catch (err) {
		  console.log('Unable to copy', err);
		}
		$copyEl.remove();
		return;
	  } 
	  navigator.clipboard.writeText(text).then(function() {
		console.log(`Copied: ${text}`);
	  }, function(err) {
		console.log('Unable to copy', err);
	  });
	}

	$('#copy-button').on('click', function(e) {
	  e.preventDefault();
	  const $prevEl = $(this).prev();
	  let text = $prevEl.val();
	  if (!text) {
		text = $prevEl.text();
	  }
	  copyToClipboard(text);
	  $(this).text("Скопировано!").addClass("btn-success").removeClass("btn-info");
	})

	
    // var table = $('#notif').DataTable( {
        // lengthChange: false,
        // buttons: [ 'copy', 'excel', 'pdf', 'colvis' ]
    // } );
 
	

	$('.dataTablesEx').dataTable({
		"language": {
			"url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Russian.json"
		},
		"order": [[0, "desc" ]]
	});	

	 $('#FormControlSelect1, #FormControlSelect2').select2({ 
		width: '100%',
		"language": {
		   "noResults": function(){
			   return "Нет результатов";
		   }
		}
	 });
	$('#FormControlSelect1').on("select2:select", function(e) {
		var ID = this.value;
		$.ajax({
		  method: "POST",
		  dataType: 'json',
		  url: "/api/index.php",
		  data: { method: "get_recepts", idPat: ID },
			success: function(data){
				var btn = "<br><br><a href='javascript:' class='btn btn-info addMedicatments' onclick='addMedicatments("+this.value+")'>Добавить новый</a>";
				
				if(data.status == 'ok' && data.message.ALL){
					var lastID; 
					var mess = "";
					
					$.each( data.message.ALL, function( key, value ) {
						var cont = "";
					  cont += "<p>Дата создания: <b>" + value.DATE_CREATE + "</b></p><hr>";
					  $.each( value.PROP.MEDICAMENTS.VALUE, function( key1, value1 ) {
						  cont += "<p>"+value1+" - <b>" + value.PROP.MEDICAMENTS.DESCRIPTION[key1] + "</b></p>";
					  })
					  if(value.DETAIL_TEXT.length>0){
						cont += "<hr><p>Рекомендации по заболеванию: <b>" + value.DETAIL_TEXT + "</b></p>";
					  }
					  mess += "<div class='sticker-left sticker-" + (key > 0 ? 'secondary' : 'success')+ "' data-sticker='" + value.NAME + " от " + value.DATE_CREATE + "'>" + cont + "</div>";
					  lastID = value.ID;
					});
					var btn2 = "<br><br><a href='javascript:' class='btn btn-success addMedicatments' onclick='addMedicatments(" + ID + "," + lastID + ")'>Скорректировать последний</a>";
					
					$(".topContentMedic").html(btn+btn2);
					$(".mainContentMedic").html(mess);
				} else {
					$(".topContentMedic").html("<br>Ничего не найдено!"+btn);
					$(".mainContentMedic").html('');
				}
			}	  
		});
	});

	$('#FormControlSelect2').on("select2:select", function(e) {
		var ID = this.value;
		var DATE = $("#DateRange").val();
		$.ajax({
		  method: "POST",
		  dataType: 'json',
		  url: "/local/templates/cabinet/ajax/ajax.php",
		  data: { method: "getStat", idPat: ID, date: DATE },
			success: function(data){
				if(data.status == 'ok' && data.message.Temp){	
					$(".topContentMon").html('');
					$(".mainContentMon1").html(data.message.Temp);
					$(".mainContentMon2").html(data.message.PressureSystol);
					$(".mainContentMon3").html(data.message.PressureDiastol);
				} else {
					$(".topContentMon").html("<br>Ничего не найдено!");
					$(".mainContentMon1,.mainContentMon2,.mainContentMon3").html('');
				}
			}
		});
	})
	
	$('input[name="dates"]').daterangepicker({ "locale": {
        "format": "DD.MM.YYYY",
        "separator": " - ",
        "applyLabel": "Сохранить",
        "cancelLabel": "Назад",
        "daysOfWeek": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        "monthNames": [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        "firstDay": 1
    }, timePicker: true, timePickerIncrement: 30, format: 'DD.MM.YYYY' });
} );

 
 function readNotif(id, _this){
	$.ajax({
	  method: "POST",
	  dataType: 'json',
	  url: "/local/templates/cabinet/ajax/ajax.php",
	  data: { method: "readNotif", ID: id },
		success: function(data){
			if(data.status == 'ok'){
				$(_this).parents("tr").addClass("table-success").removeClass("selected");
				$(_this).remove();
				$(".notifBtn-"+id).remove();
			}
				
		}	  
	});
	  
 }
 function showTab(id){
	$('.nav-tabs a, .tab-content div').removeClass("active show");
	$('.nav-tabs a[href="#'+id+'"], #'+id).addClass("active show");	 
 }
 function goToHeal(id){
	  showTab('first2-tab-content');
	  $("#FormControlSelect1").val(id).trigger("change").trigger('select2:select');
  }  
 function goToMonitoring(id){
	  showTab('second-tab-content');
	  $("#FormControlSelect2").val(id).trigger("change").trigger('select2:select');
  }
 function goToNotif(id){
	 showTab('third-tab-content');
	var selection = $( "#notif .classId"+id );
    $(".dataTables_scrollBody").scrollTo(selection);
    $("tr[role='row']").removeClass("selected");
    selection.addClass("selected");
 }