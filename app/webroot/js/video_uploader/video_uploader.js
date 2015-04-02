
/// jQuery plugin to add support for SwfUpload
/// (c) 2008 Steven Sanderson
/// (c) 2013 Hypernet Inc

(function($) {
    $.fn.videoUploader = function(options) {
        return this.each(function() {
            // Put in place a new container with a unique ID
            var id = $(this).attr("id");
            var val = $(this).attr("value");
            var defaults = {
            	name: 'data[Video][video]',
            	target: id,
            	default_url: '/js/video_uploader/anonymous.jpg',
            	value: val,
            	url_pattern: '/videos/video?id={0}&width={1}&height={2}',
            	editable: true,
            	cancel_caption: 'Cancel',
            	upload_caption : 'Click here to Upload',
            	uploading_caption: 'Uploading ..' ,
            	width: 200,
            	buttonWidth: 200,
            	height: 100,
            	buttonHeight: 41,
            	action: '/videos/uploadVideo',
            	flash_url: '/js/video_uploader/swfupload.swf',
            	error_msg: "Sorry, your file wasn't uploaded" ,
            	button_style: "position: absolute; margin-top: -{0}px; width: {1}px; z-index: -1; cursor: pointer;font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #ffffff; padding: 10px 20px; background: -moz-linear-gradient( top, #cecece 0%, #424242); background: -webkit-gradient( linear, left top, left bottom, from(#cecece), to(#424242)); -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px; border: 3px solid #ffffff; -moz-box-shadow: 0px 3px 11px rgba(000,000,000,0.5), inset 0px 0px 1px rgba(254,254,254,1); -webkit-box-shadow: 0px 3px 11px rgba(000,000,000,0.5), inset 0px 0px 1px rgba(254,254,254,1); box-shadow: 0px 3px 11px rgba(000,000,000,0.5), inset 0px 0px 1px rgba(254,254,254,1); text-shadow: 0px -1px 0px rgba(000,000,000,0.2), 0px 1px 0px rgba(255,255,255,0.3);" 
            };
            
            var settings = $.extend(defaults, options);
            if(!settings.default_url)
            	settings.default_url = defaults.default_url;
            if(!settings.buttonWidth)
            	settings.buttonWidth = settings.width;
            settings.button_style = settings.button_style
            						.replace("{0}", settings.buttonHeight + 8)
            						.replace("{1}", settings.buttonWidth);
            var cancel_caption = settings.cancel_caption;
            var upload_caption = settings.upload_caption;
            var uploading_caption = settings.uploading_caption;
            
            var container = $("<div class='video-uploader'/>");
            container.append($("<div id='video-uploader-progress-bar'> <div>&nbsp;</div> </div>"));
            container.append($("<div id='video-uploader-message'/>"));
            container.append($("<div  id='video-uploader-cancel'>"  + uploading_caption +
            						 "<input type='button' value='" + cancel_caption +"'/></div>"));
            var img_src = settings.default_url;
            
            if(settings.value)
            	img_src = settings.url_pattern
                    			.replace("{0}", settings.value)
                    			.replace("{1}", settings.width)
                    			.replace("{2}", settings.height);
            container.append($("<div id='video-uploader-preview'><img src='"+ img_src +"' width='"+ settings.width +"' height='"+ settings.height +"'></div>"));
         
            if(settings.editable){
            	container.append($("<div id='video-uploader-swf'/>"));
            	container.append($("<div id='video-uploader-button'><button style='" + settings.button_style + "' >" + upload_caption +"</button></div>"));
            }
           
            $(this).before(container);
            $("#video-uploader-progress-bar", container).hide();
            $("div[id=video-uploader-cancel]", container).hide();
             if(!settings.editable)
            	return;

            // Instantiate the uploader SWF
            var swfu;
            var swfu_defaults = {
                flash_url: settings.flash_url,
                upload_url:  settings.action,
                file_post_name: settings.name,
                file_size_limit: "3 MB",
                file_types: "*.mp4",
                file_types_description: "All Video Files",
                file_upload_limit : 10,
		file_queue_limit : 1,
                debug: false,
                button_width: settings.width,
                button_height: settings.buttonHeight ,
                button_placeholder_id: 'video-uploader-swf',
                button_text: null,
                button_text_style: null,
               /*button_text: "<span class='button'><font face='Arial' size='13pt'>" + settings.upload_caption +"</font></span>",
                button_text_style: ".button { font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #ffffff; padding: 10px 20px; background: -moz-linear-gradient( top, #cecece 0%, #424242); background: -webkit-gradient( linear, left top, left bottom, from(#cecece), to(#424242)); -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px; border: 3px solid #ffffff; -moz-box-shadow: 0px 3px 11px rgba(000,000,000,0.5), inset 0px 0px 1px rgba(254,254,254,1); -webkit-box-shadow: 0px 3px 11px rgba(000,000,000,0.5), inset 0px 0px 1px rgba(254,254,254,1); box-shadow: 0px 3px 11px rgba(000,000,000,0.5), inset 0px 0px 1px rgba(254,254,254,1); text-shadow: 0px -1px 0px rgba(000,000,000,0.2), 0px 1px 0px rgba(255,255,255,0.3); }",*/
                button_action : SWFUpload.BUTTON_ACTION.SELECT_FILE,
		button_disabled : false,
		button_cursor : SWFUpload.CURSOR.HAND,
		button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,

                // Called when the user chooses a new file from the file browser prompt (begins the upload)
                file_queued_handler: function(file) { swfu.startUpload(); },

                // Called when a file doesn't even begin to upload, because of some error
                file_queue_error_handler: function(file, code, msg) { alert(settings.error_msg + msg); },

                // Called when an error occurs during upload
                upload_error_handler: function(file, code, msg) { alert(settings.error_msg + msg); },

                // Called when upload is beginning (switches controls to uploading state)
                upload_start_handler: function() {
                    swfu.setButtonDimensions(0, 18);
                    $("#video-uploader-progress-bar  div", container).css("width", "0px");
                    $("#video-uploader-progress-bar", container).show();
                    $("div[id=video-uploader-cancel]", container).show();
                    $("div[id=video-uploader-cancel]", container).html("").hide();
                    $('#video-uploader-message', container).html("").hide();
                    $('div[id=video-uploader-button] button', container).hide();
                },
                //Called when the file dialog is about to show up
                file_dialog_start_handler : function(){
                },

                // Called when upload completed successfully (puts success details into hidden fields)
                upload_success_handler: function(file, data, response) {
                    var json = "";
                    if(data){
			//data doest not work with $.parseJSON or eval, so
			//use an inline js function.
			json = (new Function("return " + data))();
		    }
		    var video_id = 0;
		    var message = '';
		    if(json && json.video_id){
		    	video_id = json.video_id;
		    	if(json.message)
		    		message = json.message;
		    }
                    //$("div[id=video-uploader-message]", container).html(message);
                    if(video_id){
                    	var video_url = settings.url_pattern
                    			.replace("{0}", video_id)
                    			.replace("{1}", settings.width)
                    			.replace("{2}", settings.height);
                    	$("div[id=video-uploader-preview] img", container).attr('src', video_url);
                    	
                    	$("#" + settings.target).val(video_id);
                    }
                },

                // Called when upload is finished (either success or failure - reverts controls to non-uploading state)
                upload_complete_handler: function() {
                    var clearup = function() {
                        $("div[id=video-uploader-progress-bar]", container).hide();
                        $("div[id=video-uploader-message]", container).show();
                        $("div[id=video-uploader-cancel]", container).hide();
                        swfu.setButtonDimensions(settings.width, settings.buttonHeight );
                        $('div[id=video-uploader-button] button', container).show();
                    };
                    clearup();
                },

                // Called periodically during upload (moves the progess bar along)
                upload_progress_handler: function(file, bytes, total) {
                    var percent = 100 * bytes / total;
                    $("div[id=video-uploader-progress-bar] div", container).animate({ width: percent + "%" }, { duration: 500, queue: false });
                }
            };
            swfu = new SWFUpload($.extend(swfu_defaults, settings || {}));
	    $button = $('div[id=video-uploader-button] button', container);
	    swfu.button = $button;
	    
            // Called when user clicks "cancel" (forces the upload to end, and eliminates progress bar immediately)
            $("div[id=video-uploader-cancel] input[type='button']", container).click(function() {
                swfu.cancelUpload(null, false);
            });
        });
    }
})(jQuery);