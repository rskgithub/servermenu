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

    // Handle receiver requests

    $("#feeds").on('click', 'button', function() {
	var button = this;
	var items = $.getJSON(
	    "/api/receivers/"+$(this).data('plugintype')+"/"+$(this).data('receivertype'),
		function(data) {
		    $(data.plugins).each(function() {
			    $(button).siblings(".dropdown-menu").html(
				"<li role='presentation'>" +
				"<a class='dropdown-item data-send' role='menuitem' tabindex='-1' data-pluginType='"+ $(button).data('plugintype') +"' " +
				"data-receivertype='"+ $(button).data('receivertype') +"' " +
				"data-pluginid='"+ this.pluginId +"' data-content='"+ $(button).data('content') +"' href='#'>"+this.plugin+"</a>" +
				"</li>"
			    );
			}
		    );

		}
	);
    });

    $("body").on('click', '.data-send', function() {
	console.log($(this).data());
    });

    // Miscellaneous UI stuff

    $("a[data-id=feed-0]").tab('show'); // Load first feed tab

});