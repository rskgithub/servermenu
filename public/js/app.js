jQuery(document).ready(function ($) {
	var stripHtmlRegex = /(<([^>]+)>)/ig;
	
    jQuery.fn.extend({
        loading: function () {
            return this.each(function () {
                $(this).addClass('data-loading');
            });
        },
        finished: function () {
            return this.each(function () {
                $(this).removeClass('data-loading');
            })
        }
    });

    // Handle AJAX functionality

    $(".data-load").each(function () {
        $(this).loading();
        var url = "/ajax/" + $(this).data('loadtype') + "/" + $(this).data('loadid');
        $(this).load(url, function (e, s, j) {
            if (s == 'error') {
                // Don't keep reloading items that don't work.
                $(this).removeClass('data-reload');
                $(this).append('[Error loading ' + $(this).data('loadtype') + '/' + $(this).data('loadid') + ']');
            } else {
                $(this).finished();
                $(".ttip").tooltip();
            }
        });
    });

    setInterval(function () {
        $(".data-reload").each(function () {
            $(this).load("/ajax/" + $(this).data('loadtype') + "/" + $(this).data('loadid'), function (e, s, j) {
                if (s != 'error') {
                    $(".ttip").tooltip();
                }
                $(this).finished();
            });
        })
    }, 5000);

    // Handle receiver requests

    $("body").on('click', 'button.sender', function () {
        var button = this;

        $(this).siblings('ul.dropdown-menu').children('.sendertype').each(function(){
            var current = this;

            $.getJSON(
                "/api/receivers/" + $(this).data('plugintype') + "/" + $(this).data('receivertype'),
                function (data) {
                    $(current).siblings(".dropdown-loading").remove();
                    $(button).removeClass('sender');
                    if (data.plugins == null) {
                        $(current).after(
                            $("<li />")
                                .attr('role', 'presentation')
                                .addClass('disabled')
                                .html(
                                $("<a />")
                                    .addClass("dropdown-item")
                                    .attr("role", "menuitem")
                                    .html("No receivers available")
                            )
                        );
                    } else {
                        $(data.plugins).each(function (index, pluginData) {
                            $(current).after(
                                $("<li />")
                                    .attr('role', 'presentation')
                                    .html(
                                    $("<a />")
                                        .addClass("dropdown-item data-send")
                                        .attr("role", "menuitem")
                                        .attr("tabindex", "-1")
                                        .data('plugintype', $(current).data('plugintype'))
                                        .data("receivertype", $(current).data('receivertype'))
                                        .data("pluginid", pluginData.pluginId)
                                        .data("content", $(current).data('content'))
                                        .attr("href", "#")
                                        .html(pluginData.plugin)
                                )
                            );
                        })
                    }
                }
            );
        });
    });

    $("body").on('click', '.data-send', function () {
        if ($(this).data('plugintype') == 'SearchEngines') {
            $("#searchQuery").val($(this).data('content').replace(stripHtmlRegex, ''));
            $("#searchEngine").val($(this).data('pluginid'));
            $("#search").click();
        } else {
            // Instantly show service as loading
            if ($(this).data('plugintype') == 'Services') {
                $("#service-"+$(this).data('pluginid')).loading();
            }
            $.post("/api/send/" + $(this).data('plugintype') + "/" + $(this).data('pluginid'), {
                'content': $(this).data('content'),
                'receivertype': $(this).data('receivertype')
            });
        }
        $(this).closest('div.dropdown').removeClass('open');
        return false;
    });

    $("body").on('click', '.openModal', function(e) {
        if(! /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            e.preventDefault();
            $("#mdlTitle").html($(this).data('title'));
            $("#modal iframe").attr('src', $(this).attr('href'));
            $("#modal").modal('show');
        }
    });

    $("#search").click(function(){
        var url = "/ajax/search/" + $("#searchEngine").val() + "/" + encodeURIComponent($("#searchQuery").val());
        $("#searchResults").show().loading().load(url, function(){
            $(this).finished();
        });
    });

    // Miscellaneous UI stuff

    $("a[data-id=feed-0]").tab('show'); // Load first feed tab

});