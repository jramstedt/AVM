/*
 * jQuery stuff
 */
$(document).ready(function() {
	$('.box_title').live('click' , function () {
		$(this).next(".box_content").slideToggle("slow");
	});
	
	$(".editNumeric").live('keypress', function(e){
    	if( e.which!=8 && e.which!=0 && (e.which < 48 || e.which > 57) && event.keyCode != 13)
			return false;
	});
	
	$('a[name=watched], a[name=seed]').live('click', function() {
    	var url = $(this).attr('href');
    	$.get(url, parseAjaxResult);
	  	return false;
	});
	
	function parseAjaxResult(data) {
		$(data.documentElement).children().each(function(index) {
  		  if(this.tagName == 'remove') {
  			  var id = $(this).text().replace(":", "\\:");
  			  $('#'+id).remove();
		  } else {
			  var element = document.importNode(this, true);
			  var id = $(element).attr("id");

			  if(id != undefined) {
				  id = id.replace(":", "\\:");
				  $('#'+id).replaceWith(element);
	  		  } else if($(element).hasClass('ok') || $(element).hasClass('error') || $(element).hasClass('info')) {
	  			  $('#infobar').append(element);
	  			  $(element).effect("shake", { times:3 }, 300).delay(5000).slideToggle("slow", function() {$(this).remove(); });
	  		  }
		  }
  	  });	
	}
	
	function post(form) {
		var url = form.attr('action');
    	$.post(url, form.serialize(), parseAjaxResult);
	}
	
	$('.datepicker').datepicker({firstDay: 1,
		 showAnim: 'fadeIn',
		 onSelect: function(year, month, inst) {
		 	post($(this).closest('form'));
		 }
	}).click(function () { return false; });
	
	$('input[name=filter]').live('click', function () { return false; });
	$('input[name=filter]').live('keypress', function(e) {
		if(e.keyCode == 13 || e.which == 13) {
			e.preventDefault();
			post($(this).closest('form'));
			return false;
		}
	});
	
	$('form').live('submit', function() {
    	post($(this));
	  	return false;
	});
});

/*
 * Generates url from form
 */
generateUrl = function (formId) {
	var form = document.getElementById(formId);
	var url = form.action;
	
	for(var i = 0; form.elements.length > i; i++)
	{	
		if(form.elements[i].nodeName == "INPUT") {
			if(form.elements[i].type == "checkbox") {
				if(form.elements[i].checked == true)
					url += "/"+encodeURI(form.elements[i].name)+"/"+encodeURI(form.elements[i].value);
			} else if(form.elements[i].type == "text" || form.elements[i].type == "password" || form.elements[i].type == "hidden") {
				url += "/"+encodeURI(form.elements[i].name)+"/"+encodeURI(form.elements[i].value);
			} else if(form.elements[i].type == "radio") {
				if(form.elements[i].checked == true)
					url += "/"+encodeURI(form.elements[i].name)+"/"+encodeURI(form.elements[i].value);
			}
		} else if(form.elements[i].nodeName == "SELECT") {
			var aParams = new Array();

			for(var a = 0; form.elements[i].length > a; a++) {
				if(form.elements[i].options[a].selected == true) {
					aParams.push(form.elements[i].options[a].value);
				}
			}
			
			url += "/"+encodeURI(form.elements[i].name)+"/"+encodeURI(aParams.join());
		} else if(form.elements[i].nodeName == "TEXTAREA") {
			url += "/"+encodeURI(form.elements[i].name)+"/"+encodeURI(form.elements[i].value);
		}
	}
	
	return url;
}

/*
 * Generates post string to url from form
 * Deprecated! use jQuery serialize()
 */
generatePostString = function (formId) {
	var form = document.getElementById(formId);
	var url = "";
	
	for(var i = 0; form.elements.length > i; i++)
	{	
		if(form.elements[i].nodeName == "INPUT") {
			if(form.elements[i].type == "checkbox") {
				if(form.elements[i].checked == true)
					url += "&"+encodeURI(form.elements[i].name)+"="+encodeURI(form.elements[i].value);
			} else if(form.elements[i].type == "text" || form.elements[i].type == "password" || form.elements[i].type == "hidden") {
				url += "&"+encodeURI(form.elements[i].name)+"="+encodeURI(form.elements[i].value);
			} else if(form.elements[i].type == "radio") {
				if(form.elements[i].checked == true)
					url += "&"+encodeURI(form.elements[i].name)+"="+encodeURI(form.elements[i].value);
			}
		} else if(form.elements[i].nodeName == "SELECT") {
			var aParams = new Array();

			for(var a = 0; form.elements[i].length > a; a++) {
				if(form.elements[i].options[a].selected == true) {
					aParams.push(form.elements[i].options[a].value);
				}
			}
			
			url += "&"+encodeURI(form.elements[i].name)+"="+encodeURI(aParams.join());
		} else if(form.elements[i].nodeName == "TEXTAREA") {
			url += "&"+encodeURI(form.elements[i].name)+"="+encodeURI(form.elements[i].value);
		}
	}
	
	return url.substring(1); // remove first &
}