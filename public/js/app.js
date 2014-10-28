jQuery(document).ready(function ($) {
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
            });
        })
    }, 5000);

    // Handle receiver requests

    $("body").on('click', 'button.sender', function () {
        var button = this;
        var items = $.getJSON(
            "/api/receivers/" + $(this).data('plugintype') + "/" + $(this).data('receivertype'),
            function (data) {
                if (data.plugins == null) {
                    $(button).siblings(".dropdown-menu").html(
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
                        $(button).siblings(".dropdown-menu").html(
                            $("<li />")
                                .attr('role', 'presentation')
                                .html(
                                $("<a />")
                                    .addClass("dropdown-item data-send")
                                    .attr("role", "menuitem")
                                    .attr("tabindex", "-1")
                                    .data('plugintype', $(button).data('plugintype'))
                                    .data("receivertype", $(button).data('receivertype'))
                                    .data("pluginid", pluginData.pluginId)
                                    .data("content", $(button).data('content'))
                                    .data("test", "test")
                                    .attr("href", "#")
                                    .html(pluginData.plugin)
                            )
                        );
                    })
                }
            }
        );
    });

    $("body").on('click', '.data-send', function () {
        $.post(
            "/api/send/" + $(this).data('plugintype') + "/" + $(this).data('pluginid'),
            {
                'content': $(this).data('content'),
                'receivertype': $(this).data('receivertype')
            },
            function (data) {
                console.log(data);
            }
        );
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