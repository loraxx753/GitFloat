jQuery.extend({
    widget : function(id, values, callback) {
        $("#"+id+" button").on("click", function(e) {
            e.preventDefault();
            var postData = {};
            postData.author = $(this).closest(".widget").data('author');
            $("#results").slideUp(function() {
                $(".loading").show();
                postData.request = id;
                if(values) {
                    for (i in values) {
                        postData[values[i]] = $("#"+values[i]).val();
                    }
                }
                $.post("./", postData, function(data) {
                    $(".loading").hide();
                    $("#results").html(data);
                    $("#results").slideDown();
                    if(callback) {
                        callback();
                    }
                });
            });
        });
    },
    getInfo : function(request, values, callback) {
        var postData = {};
        postData.request = request;

        if(typeof values == 'object') {
            for (i in values) {
                console.log(i);
                postData[i] = values[i];
            }
        }
        console.log(postData);
        $.post("./", postData, function(data) {
            if(callback) {
                callback(data);
            }
            else if(typeof values == 'function') {
                values(data);
            }
        })
    }
});