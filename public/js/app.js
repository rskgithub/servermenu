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
	var url = "/ajax/"+$(this).data('loadtype')+"/"+$(this).data('loadid');
	$(this).load(url, function(e,s,j){
	    if (s == 'error') {
		// Don't keep reloading items that don't work.
		$(this).removeClass('data-reload');
		$(this).append('[Error loading '+$(this).data('loadtype')+'/'+$(this).data('loadid')+']');
	    } else {
		$(this).finished();
		$(".ttip").tooltip();
	    }
	});
    });

    setInterval(function() {
	$(".data-reload").each(function() {
	    $(this).load("/ajax/"+$(this).data('loadtype')+"/"+$(this).data('loadid'), function(e,s,j){
		if (s != 'error') {
		    $(".ttip").tooltip();
		}
	    });
	})
    }, 5000);

    // Miscellaneous UI stuff

    $("a[data-id=feed-0]").tab('show'); // Load first feed tab

});