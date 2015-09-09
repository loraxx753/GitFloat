jQuery.extend({
    widget : function(id, values, callback) {
        $("#"+id+" button[type=submit]").on("click", function(e) {
            $this = $(this);
            e.preventDefault();
            var postData = {};
            postData.author = $(this).closest(".widget").data('author');
            $(".loading").show();
            postData.request = id;
            if(values) {
                for (i in values) {
                    postData[values[i]] = $("#"+values[i]).val();
                }
            }
            $.post("./", postData, function(data) {
                var panelName = $this.closest('.panel-info').children('.panel-heading').children('.panel-title').html();
                $("#results-header h4").html(panelName);
                $(".loading").hide();
                $("#results").html(data);
                if(callback) {
                    callback();
                }
                $('#myModal').modal();
            });
        });
    },
    getInfo : function(request, values, callback) {
        var postData = {};
        postData.request = request;

        if(typeof values == 'object') {
            for (i in values) {
                postData[i] = values[i];
            }
        }
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