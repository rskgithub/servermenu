jQuery( document ).ready(function( $ ) {
    jQuery.fn.extend({
	loading: function() {
	    return this.each(function() {
		$(this).addClass('data-loading');
	    });
	},
	finished: function() {
	    return this.each(function() {
		$(this).removeClass('data-loading');
	    })
	}
    });

    // Handle AJAX functionality

    $(".data-load").each(function(){
	$(this).loading();
	$(this).load("/ajax/"+$(this).data('loadtype')+"/"+$(this).data('id'), function(e,s,j){
	    if (s == 'error')
	    	$(this).removeClass('data-reload');
	});
	$(this).finished();
    });

    setInterval(function() {
	$(".data-reload").each(function() {
	    $(this).loading();
	    $(this).load("/ajax/"+$(this).data('loadtype')+"/"+$(this).data('id'));
	    $(this).finished();
	})
    }, 5000);

});