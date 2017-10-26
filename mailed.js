jQuery(document).ready(function($) {

	console.log(mailed);

  $('form.mailed-form').submit(function(e){

    e.preventDefault();

		var data = {};

		$.each($(this).serializeArray(), function(_, kv){
			data[kv.name] = kv.value;
		});

		data.action = 'mailed_register_form';

    $('.mailed-show-onsubmit').show();
    $('.mailed-hide-onsubmit').hide();

		$.ajax({
			method: 'POST'
			, url: mailed.ajaxurl
			, dataType: 'json'
			, data: data
      , success: function(response){
        
        if(response.status === 200){
        	$('.mailed-show-onsuccess').show();
        	$('.mailed-hide-onsuccess').hide();
        }else{
          $('.mailed-show-onerror').show();
          $('.mailed-hide-onerror').hide();
        }

        $('.mailed-show-oncomplete').show();
        $('.mailed-hide-oncomplete').hide();

      }
		});

    return false;

	});

});