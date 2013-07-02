var getUrl = './modules/shop/inc/ajax.get.php?cb=?';

function request(form){
	var data = renameArr(form.serializeArray());
		if(!validate( form )){
			return false;
		}
		$.getJSON(getUrl, data, function(json) {  
		 	if(json.err === 0){
				if(json.html !== undefined){
					$(json.selector).html(json.html);
				}
				if(json.append !== undefined){
					$("table tr").removeClass("mark");
					$(json.selector).append(json.append);
				}
				if(json.pagi !== undefined){
						$('#pagi').html(json.pagi);
					}
				createClasses();
				if(json.update === undefined || json.update !== 1){
					$(form).find('input[type=text], textarea').val('');
				}
			}
			if(json.msg.length > 0)	showStatus(json); 
		}); 
		return false;
}

function roundVal(n) {
	var nn = new Number(n+'').toFixed(parseInt(2));
	return parseFloat(nn); 
}

function compute(o){
   
};

function priceWithDPH(){
        console.log('priceWithDPH');
	var  o = $("#psave input[name=price]");
	o.parent().find("input[name=skip]").val( roundVal(o.val() * 1.2) );
        o = $("#psave input[name=price_sale]");
	o.parent().find("input[name=skip]").val( roundVal(o.val() * 1.2) );
	return false;
}

$(function() {

	priceWithDPH();
	$('input[name=skip]').keyup(function(){
            $(this).parent().find(".pricePom").val( roundVal( $(this).val() / 120 * 100 ) );
	});
        
        $('.pricePom').keyup(function(){
            $(this).parent().find("input[name=skip]").val( roundVal( $(this).val() / 100 * 120 ) );
        });
        
	// show new page form
	$('.newPage').click(function(){$(this).hide(200).parent().find('.box').show();return false;}); 	
	
	// NEW PRODUCT -----------------------------
	$("#pnew").submit(function (){
		return validate($(this));
	});
	
	
	// SEARCH -----------------------------------------------------------
	$('.shopSearch').submit(function (){
		var data = {
			q : $('input[name=q]').val(),
			url : $('.shopSearch input[name=url]').val(),
			table : $('.shopSearch input[name=table]').val(),
			act : 1
		}
		if(data.q.length  === 0){
			return false;
		}
		$.getJSON(getUrl, data, function(json) {  
					if(json.err === 1){
						return false;
					}
					$('table .'+ data.table ).html(json.html);
					$(".navigator").first().remove();
					$('.right .breadcrumb').html('<strong>Výsledky vyhľadávania: '+ data.q +'</strong>');
					$('#pagi').html(json.pagi);
					createClasses();
			}); 
		
		return false;
	})
	
	
	// SAVE Product -----------------------------------------------------------
	$('#psave').submit(function (){
		var o = $(this),  
		arr = o.serializeArray(),
		data = { act : 2};
		for (i in arr) {
			data[arr[i].name] = (arr[i].name == "content_sk" ? CKEDITOR.instances.editor1.getData() : arr[i].value);
		}
		if(!validate( o )){
			return false;
		}
		 $.post( './modules/shop/inc/ajax.post.php', data, function(json) {  
				showStatus(json);
		 }, "json");
		
		return false;
	})	
	
	
	// INLINE EDITING -----------------------------------------------------------
	$(".inline").delegate(".inline .edit", 'click', function () {
			var o = $(this),
			id = o.attr("href").replace("#id",""),
			tr = o.parent().parent().addClass("editing").find('.il'), 
			names = o.parents('.inline').find('th.il');
			$('body').data("id", id);
			names.each(function(i){
				var cls = $(this).attr("class").split(" "),
					input = cls[1].split("-");
					obj = $(this); // current thead th item
				if(input.length === 2 && input[0] === "text"){
					tr.eq(i).html('<input style="width:'+ (obj.width() - 20) +'px" type="text" name="' + 
					input[1] + '" value="'+ tr.eq(i).text() +'" class="ii '+(obj.hasClass("required") ? 'required' : '')+ '" />');
				}else{
					tr.eq(i).html('<textarea style="width:'+ (obj.width() - 20) +'px;height:70px;" name="' +input[1] + '" class="ii '+
					(obj.hasClass("required") ? 'required' : '')+ '" >'+ tr.eq(i).text() +'</textarea>');
				}
			});
			o.parent().append('<input type="submit" id="#iibtn" value="Uložiť" class="ibtn" />');
			$('.inline .edit').hide();
		return false;
	})
	
	$(".inlineEditing").submit( function () {
			var o = $(this),
			tr = $("tr.editing").eq(0);
			if(!validate( o )){
				return false;
			}
			var data = renameArr(o.serializeArray());
			data.id = $('body').data('id');			
			$.getJSON(getUrl, data, function(json) {  
		 		if(json.err === 1){ 
					showStatus(json);
					return false;
				}else{
				tr.find('.ii').each(function(){
				var input = $(this),
					val = input.val();
					input.parent('td').text(val);
					input.remove();				
				});
				tr.removeClass("editing");
				$(".inline .ibtn").remove();
				$('.inline .edit').show();
				}
			});	
		return false;
	})
	
	// AJAXOVE odosielanie formulárov -----------------------------------------
	$('.ajaxSubmit').submit(function (){request($(this));return false;})	
	

	$(".tt").hover(
		  function () {
			var o = $(this),
			t = o.attr("title");
			if(t.length > 0){
				o.load('./tooltip/' + t, function() { o.attr("title", "");
				o.find('.tooltip').show(); 
				});
			}
			o.find('.tooltip').show();
		  }, 
		  function () {
			$(this).find('.tooltip').hide();
		  }
		);


	
	
});

