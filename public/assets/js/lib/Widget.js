// Extend that jquery
jQuery.extend({
    /**
     * Sets up the widget
     * @param  {String}   id       Widget form id
     * @param  {Array}    values   The id's of the values to get
     * @param  {Function} callback Callback function after ajax
     * @return {Null}            
     */
    widget : function(id, values, callback) {
        // Set up the submit button to fire stuff
        $("#"+id+" button[type=submit]").on("click", function(e) {
            $this = $(this);
            e.preventDefault();
            var postData = {};
            // Get the author from data-author of the widget
            postData.author = $(this).closest(".widget").data('author');
            postData.auth = $(this).closest(".widget").data('auth');
            $(".loading").show();
            postData.request = id;
            // Add the valuse to the postData
            if(values) {
                for (i in values) {
                    postData[values[i]] = $("#"+values[i]).val();
                }
            }
            // Post to itself
            $.post("./", postData, function(data) {
                // Get the panel name of the widget
                var panelName = $this.closest('.panel-info').children('.panel-heading').children('.panel-title').html();
                // Update the modal name accordingly
                $("#results-header h4").html(panelName);
                $(".loading").hide();
                $("#results").html(data);
                // Run the callback to clean up info
                if(callback) {
                    callback();
                }
                // Execute modal
                $('#myModal').modal();
            });
        });
    },
    /**
     * Runs an ajax call on the fly without a widget
     * @param  {String}   request  Request name (run_*) name of the Processor method
     * @param  {Array}    values   What values to send to the method
     * @param  {Function} callback Pose callback
     * @return {Null}            
     */
    getInfo : function(request, values, callback) {
        var postData = {};
        var splitted = request.split("\.");
        if(splitted.length > 1) {
            postData.author = splitted[0];
            postData.auth = splitted[1];
            postData.request = splitted[2];
            if(splitted[3]) {
                postData.call = splitted[3];
            }
        }
        else {
            postData.request = request;
        }

        console.log(postData);

        if(typeof values == 'object') {
            for (i in values) {
                postData[i] = values[i];
            }
        }
        $.post("./", postData, function(data) {
            if(callback) {
                callback(data);
            }
            // They don't have to add values and can just call function
            else if(typeof values == 'function') {
                values(data);
            }
        })
    }
});