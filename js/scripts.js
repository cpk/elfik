function createClasses(){
	$('tr:odd').addClass('odd');
	$('tr:event').removeClass('odd');
        $('#new-items li:odd').addClass('odd');
} 
function showStatus(data){
	var html = '<p class="'+ (data.err === 0 ? "ok" : "err") +'">'+ data.msg +'</p>',
	o = $("#status");
	o.html(html).center().fadeIn();
	setTimeout(function() {o.fadeOut(100);}, 4000);
}

function validate(f){
	var inputs = f.find('input.required, textarea.required'),
	valid = true,

	vldt = {
		required : function(v,i) {return {r : !!v ,  msg : 'Nie sú výplnené povinné hodnoty'}},
		email	 : function(v,i) {return {r : v.match( /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/ ), msg : 'Neplatná e-mailová adresa'}},
		fiveplus : function(v,i) {return {r : v.length >= 5, msg : 'Hodnota musí mať min. 5 znakov'}},
		numeric  : function(v,i) {return {r : !isNaN(v), msg : 'Hodnota '+v+' nie je číslo.'}},
		mobil	 : function(v,i) {return {r : v.length > 8 && v.length <=10,  msg : 'Hodnota musí mať 9-10 znakov'}}
	};
	inputs.removeClass('formerr');
	inputs.each(function(){
		var input = $(this),
			val = input.val(),
			cls = input.attr("class").split(' ');

		for(i in cls){
			if(vldt.hasOwnProperty(cls[i])){
				var res = vldt[cls[i]](val,input);
				if(!res.r){
					input.addClass('formerr');
					showStatus({err : 1, msg : res.msg});
					valid = false;
				}
			}
		}
	});
	return valid;	
}

function is_numeric(n){
    return !isNaN(parseInt(n)) && isFinite(n);
}

function renameArr(a){
	var d = {};	
	for (i in a) {
		d[a[i].name] = a[i].value;
	}
	return d;
}


function addToBasket(pid){
	$.getJSON("/inc/ajax.cart.php?cb=?",{pid: pid, act : 1}, 
	function(json) { 
		if(json.err === 0){
			$('#price').html(json.price);
		}
		showStatus(json);
				
	}); 
}

function getVid(obj){
	if(obj.length === 0){
		return 0;
	}	
	return obj.attr("id").replace("_input_", "");
}
// function set delivery and payment
jQuery.extend({
	setDP: function(data) {
		var r = false;
		$.ajax({
			url	: "/inc/ajax.cart.php",
			type: 'get',
			data : data,
			dataType : 'json',
			async: false,
			success: function(json) {
				if(json.err === 0) {
					r = true;
				}else{
					showStatus(json);
				}
			}
		});
		return r;
	}
});

$(function() {
    
    createClasses();
    $('a[rel=lightbox]').lightBox();          
		
		
    $("#k img").hover(function (e) { 
            var a =  $(this).attr('alt');
            if(a.length < 5) return;
            $('#imgbox').load('/inc/img.php?a=' + a,  function() {
                    $('#iabs').css({'top': e.pageY - 150, 'left' : e.pageX - 262});
            });
    },  function () { 
            $('#iabs').remove();
    });	

    $("#k img").mousemove(function (e){
            $('#iabs').css({'top': e.pageY - 150, 'left' : e.pageX - 262});
    })
               
    // slider
    $("#slider").easySlider({auto: true, continuous: true });
    
    
    
    $("ul.toplevel li").hover(function(){
        var o = $(this);
        o.addClass("hover");
        $('ul:first',o).show(200);
    }, function(){
        var o = $(this);
        o.removeClass("hover");
        $('ul:first',o).hide();
    
    });
    
    $("ul.toplevel li ul li:has(ul)").find("a:first").addClass("multi");

                
	//  SUBMIT ORDER  ---------------------
	
	$("form[name=order]").submit(function(){
		var form = $(this),
		data = renameArr( form.serializeArray() );
		
		if( ! validate( form) ){
			return false;
		}
		$("#loader").show(100);
		$.getJSON("/inc/ajax.cart.php?cb=?",data, function(json) { 
			if(json.err === 0){
				$("#bucketBox").html(json.html);
			}
                        showStatus(json);
			$("#loader").hide(100);
		}); 
		return false;
	});
	
		
	//  kosik3 FO or PO box show ---------------------
	
	$(".input-nav input[name=is_fo]").change(function(){
		var o = $("#hi");
		if($(this).val() === "0"){
			o.hide(500).find('.company').removeClass("required");
		}else{
			o.show(500).find('.company').addClass("required");
		}
	});
	
	$(".input-nav input[name=diff_addr]").change(function(){
		if($(this).val() === "0"){
			$("#dv").hide(300).find('.del').removeClass("required");
		}else{
			$("#dv").show(300).find('.del').addClass("required");
		}
	});
	
	// editing  backet ----------------------------------------
	$('#k .edit').click(function(){
		var o = $(this), 
		id = o.attr("href").replace("#",""), 
		q = o.prev().val();
		if(!is_numeric(q) || q <= 0 || q > 9999){
			showStatus({msg : 'Hodnota musí byť číslo (1-9999).', err : 1});
			return false;
		}
		$.getJSON("/inc/ajax.cart.php?cb=?",{id: id, q : q, act : 2}, function(json) { 
		if(json.err === 0){
			$('#sum').html(json.html);
			$('#price').html(json.price);
			$('#count').text(json.qunatity);
		}else{
			showStatus(json);
		}
	}); 
		return false;
	});
	
	// deleting  backet ----------------------------------------
	$('#k .del').click(function(){
		var o = $(this), 
		id = o.attr("href").replace("#","");
		
		if(!confirm("Skutočne chcete odstrániť položku z košíka?")){
			return false;
		}
		
		$.getJSON("/inc/ajax.cart.php?cb=?",{id: id, act : 3}, function(json) { 
			if(json.err === 0){
				$('#sum').html(json.html);
				$('#price').html(json.price);
				$('#count').text(json.qunatity);
				o.parent().parent().fadeOut(500);
				createClasses();
			}else{
				showStatus(json);
			}
		}); 
		return false;
	});
			
	// adding to backet ----------------------------------------	
	$('.addToBasket').click(function(){
		var o = $(this), 
		pid = o.attr("href").replace("#","");
		addToBasket(pid);
		return false;
	});
	
	
	// payment delivery ----------------------------------------
        function setOpacity(){
            var i = $('.bx input');
            i.filter('input:disabled').css("opacity","0.5");
            i.not('input:disabled').css("opacity","1.0");
        }	
        setOpacity();
	// payment delivery ----------------------------------------	
	$('.left input').change(function(){
		var l = $('.left input'),
                    r = $('.right input'),
                    idLeft = $(this).attr("name"),
                    all = $('.bx input'),
                    idRight = r.filter(":checked").attr("name");
  
                if(l.filter(":checked").length === 0){
                    
                    if(r.filter(":checked").length === 0){
                        all.prop("disabled", false);
                    }else{
                        l.prop("disabled", false);
                        if(idRight === "p1"){
                            $('input[name=d3]').prop("disabled", true);
                        }
                        if(idRight === "p2"){
                            $('input[name=d1], input[name=d2]').prop("disabled", true);
                        }
                    } 
               }else{
                   l.not($(this)).prop("disabled", true);
                   if(r.filter(":checked").length === 0){
                       if(idLeft === "d1" || idLeft === "d2"){
                           $('input[name=p1], input[name=p3]').prop("disabled", false);
                           $('input[name=p2]').prop("disabled", true);
                       }
                       if(idLeft === "d3"){
                           $('input[name=p2], input[name=p3]').prop("disabled", false);
                           $('input[name=p1]').prop("disabled", true);
                       }
                   }
               }
                setOpacity();
		return false;
	});
        
	
	$('.right input').change(function(){
		var l = $('.left input'),
                    r = $('.right input'),
                    idRight = $(this).attr("name"),
                    all = $('.bx input'),
                    idLeft = l.filter(":checked").attr("name");

                if(r.filter(":checked").length === 0){
                    
                    // ak platba neni zaskrtnuta
                    if(l.filter(":checked").length === 0){
                        all.prop("disabled", false);
                    }else{
                        r.prop("disabled", false);
                        if(idLeft === "d1" || idLeft === "d2"){
                            $('input[name=p2]').prop("disabled", true);
                        }
                         if(idLeft === "d3"){
                            $('input[name=p1]').prop("disabled", true);
                        }
                       
                    } 
               }else{
                   // ak platba je zaskrtnuta
                   r.not($(this)).prop("disabled", true);
                   if(l.filter(":checked").length === 0){
                       if(idRight === "p1"){
                           $('input[name=d1], input[name=d2]').prop("disabled", false);
                           $('input[name=d3]').prop("disabled", true);
                       }
                       if(idRight === "p2"){
                           $('input[name=d1], input[name=d2]').prop("disabled", true);
                           $('input[name=d3]').prop("disabled", false);
                       }
                       if(idRight === "p3"){
                           all.prop("disabled", false);
                       }
                  }
               }
                setOpacity();
		return false;
	});
	
	$('.dp').click(function(){
		var p = $('.right input:checked'),
			d = $('.left input:checked');
			
		if(p.length != 1 || d.length != 1){
			showStatus({err : 1, msg : 'Vyberte spôsob dopravy a platby.'});
			return false;	  
		}
                $("#loader").show(100);
		var data = {
			act : 4,
			did : d.attr('name').replace("d",""),
			pid : p.attr('name').replace("p","")
		}
		$.ajax({
			url	: "/inc/ajax.cart.php",
			type: 'get',
			data : data,
			dataType : 'json',
			success: function(json) {
				if(json.err === 0) {
					location.href =  "/shop/kosik3";
				}else{
					showStatus(json);
				}
			}
		});
                $("#loader").hide(100);
		return false;
	});
	
	$('.buy').click(function(){ 
		$('body').data('id', $(this).attr("href").replace('#','')); 
			$( "#dialog-form" ).dialog( "open" );
	});
	
	
	
	$( "#dialog-form" ).dialog({
			height: 245,
			width: 596,
			autoOpen: false,
			dialogClass: 'dform', 
			buttons: {
				"zavrieť": function() {
					$( this ).dialog( "close" );
				},
				"Vložiť do košíka": function() {
					var o = $( this ),
					pid = $("input[name=pid]").val(),
					vid = getVid(o.find('.selected').eq(0));
					addToBasket(pid, vid);
					o.dialog( "close" );
				}
		},
			open: function(){
				$.getJSON("/inc/form.php?cb=?",{id: $('body').data('id')}, function(json) { 
					if(json.err === 0){
						$("#modal").html(json.html);
						$('#dialog-form .thumb').lightBox();
						$('#dialog-form select').selectbox();
					}else{
						showStatus(json);
					}
				}); 
			}
		});
		
		$('.map').click(function(){ 
			$('body').data('id', $(this).attr("href").replace('#map','')); 
				$( "#map-dialog" ).dialog( "open", "dialogClass", 'alert' );
			});
	
		$( "#map-dialog" ).dialog({
			height: 500,
			width: 600,
			dialogClass: 'dmap', 
			autoOpen: false,
			buttons: {
				"zavrieť": function() {
					$( this ).dialog( "close" );
				}	
		},
			open: function(){
				$.getJSON("/inc/map.php?cb=?",{id: $('body').data('id')}, function(json) { 
					if(json.err === 0){
						$("#map-dialog").html(json.html);
					}else{
						showStatus(json);
					}
				
				});
				
			}
		});
});


jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
    this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    return this;
}
