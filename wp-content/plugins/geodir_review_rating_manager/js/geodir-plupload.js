jQuery.fn.exists = function() {
	return jQuery(this).length > 0;
}

jQuery(document).ready(function($) {
    geodir_review_upload_init();
});

function geodir_review_upload_init(){

    if (jQuery(".gd-plupload-upload-uic").exists()) {
        var pconfig = false;
        jQuery(".gd-plupload-upload-uic").each(function() {
            var $this = jQuery(this);
            var id1 = $this.attr("id");
            var imgId = id1.replace("plupload-upload-ui", "");
            gd_plu_show_thumbs(imgId);
            pconfig = JSON.parse(geodir_reviewrating_plupload_localize.geodir_reviewrating_plupload_config);
            pconfig["browse_button"] = imgId + pconfig["browse_button"];
            pconfig["container"] = imgId + pconfig["container"];
            pconfig["drop_element"] = imgId + pconfig["drop_element"];
            pconfig["file_data_name"] = imgId + pconfig["file_data_name"];
            pconfig["multipart_params"]["imgid"] = imgId;
            pconfig["multipart_params"]["_ajax_nonce"] = $this.find(".ajaxnonceplu").attr("id").replace("ajaxnonceplu", "");
            if ($this.hasClass("gd-plupload-upload-uic-multiple")) {
                pconfig["multi_selection"] = true;
            }
            if ($this.find(".plupload-resize").exists()) {
                var w = parseInt($this.find(".plupload-width").attr("id").replace("plupload-width", ""));
                var h = parseInt($this.find(".plupload-height").attr("id").replace("plupload-height", ""));
                pconfig["resize"] = {
                    width: w,
                    height: h,
                    quality: 90
                };
            }
            var uploader = new plupload.Uploader(pconfig);
            uploader.bind('Init', function(up) {
                //alert(1);
            });
            uploader.init();
            uploader.bind('Error', function(up, files) {
                if (files.code == -600) {
                    jQuery('#upload-error').addClass('upload-error');
                    jQuery('#upload-error').html(files.message + ' : You tried to upload a image over ' + geodir_reviewrating_plupload_localize.geodir_upload_img_size);
                } else {
                    jQuery('#upload-error').addClass('upload-error');
                    jQuery('#upload-error').html(files.message);
                }
            });
            // a file was added in the queue
            geodir_totalImg = geodir_reviewrating_plupload_localize.geodir_totalImg;
            geodir_limitImg = geodir_reviewrating_plupload_localize.geodir_image_limit;
            uploader.bind('FilesAdded', function(up, files) {
                jQuery('#upload-error').html('');
                jQuery('#upload-error').removeClass('upload-error');
                //geodir_totalImg = geodir_totalImg + up.files.length;
                if (geodir_limitImg) {
                    if (geodir_totalImg == geodir_limitImg) {
                        while (up.files.length > 0) {
                            up.removeFile(up.files[0]);
                        } // remove images
                        jQuery('#upload-error').addClass('upload-error');
                        jQuery('#upload-error').html('You have reached your upload limit of ' + geodir_limitImg);
                        return false;
                    }
                    if (up.files.length > geodir_limitImg) {
                        while (up.files.length > 0) {
                            up.removeFile(up.files[0]);
                        } // remove images
                        jQuery('#upload-error').addClass('upload-error');
                        jQuery('#upload-error').html('You may only upload ' + geodir_limitImg + ' with this package, please try again.');
                        return false;
                    }
                    if (parseInt(up.files.length) + parseInt(geodir_totalImg) > parseInt(geodir_limitImg)) {
                        while (up.files.length > 0) {
                            up.removeFile(up.files[0]);
                        } // remove images
                        jQuery('#upload-error').addClass('upload-error');
                        jQuery('#upload-error').html('You may only upload another ' + (parseInt(geodir_limitImg) - parseInt(geodir_totalImg)) + ' with this package, please try again.');
                        return false;
                    }
                }
                jQuery.each(files, function(i, file) {
                    $this.find('.filelist').append(
                        '<div class="file" id="' + file.id + '"><b>' +
                        file.name + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') ' +
                        '<div class="fileprogress"></div></div>');
                });
                up.refresh();
                up.start();
            });
            uploader.bind('UploadProgress', function(up, file) {
                jQuery('#' + file.id + " .fileprogress").width(file.percent + "%");
                jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
            });
            // a file was uploaded
            var timer;
            var i = 0;
            var indexes = new Array();
            uploader.bind('FileUploaded', function(up, file, response) {
                //geodir_totalImg++;
                //up.removeFile(up.files[0]); // remove images
                indexes[i] = up;
                clearInterval(timer);
                timer = setTimeout(function() {
                    //geodir_review_remove_file_index(indexes);
                }, 1000);
                i++;
                jQuery('#' + file.id).fadeOut();
                response = response["response"]
                if (response != null && response != 'null' && response != '' ) {
                    geodir_totalImg++;
                    // add url to the hidden field
                    if ($this.hasClass("gd-plupload-upload-uic-multiple")) {
                        // multiple
                        var v1 = jQuery.trim(jQuery("#" + imgId).val());
                        if (v1) {
                            v1 = v1 + "," + response;
                        } else {
                            v1 = response;
                        }
                        jQuery("#" + imgId).val(v1);
                    } else {
                        // single
                        jQuery("#" + imgId).val(response + "");
                    }
                }
                // show thumbs
                gd_plu_show_thumbs(imgId);
            });
        });
    }
}

function geodir_review_remove_file_index(indexes) {
	for (var i = 0; i < indexes.length; i++) {
		if (indexes[i].files.length > 0) {
			indexes[i].removeFile(indexes[i].files[0]);
		}
	}
}

function gd_plu_show_thumbs(imgId) {
	var $ = jQuery;
	var thumbsC = $("#" + imgId + "plupload-thumbs");
	thumbsC.html("");
	// get urls
	var imagesS = $("#" + imgId).val();
	var images = imagesS.split(",");
	for (var i = 0; i < images.length; i++) {
		if (images[i] && images[i] != null && images[i] != 'null') {
			var thumb = $('<div class="thumb" id="thumb' + imgId + i + '"><div class="thumbi"><a id="thumbremovelink' + imgId + i + '" href="#">Remove</a></div><img src="' + images[i] + '" alt=""  /></div>');
			thumbsC.append(thumb);
			thumb.find("a").click(function() {
				geodir_totalImg--; // remove image from total
				jQuery('#upload-error').html('');
				jQuery('#upload-error').removeClass('upload-error');
				var ki = $(this).attr("id").replace("thumbremovelink" + imgId, "");
				ki = parseInt(ki);
				var kimages = [];
				imagesS = $("#" + imgId).val();
				images = imagesS.split(",");
				for (var j = 0; j < images.length; j++) {
					if (j != ki) {
						kimages[kimages.length] = images[j];
					}
				}
				$("#" + imgId).val(kimages.join());
				gd_plu_show_thumbs(imgId);
				return false;
			});
		}
	}
	if (images.length > 1) {
		thumbsC.sortable({
			update: function(event, ui) {
				var kimages = [];
				thumbsC.find("img").each(function() {
					kimages[kimages.length] = $(this).attr("src");
					$("#" + imgId).val(kimages.join());
					gd_plu_show_thumbs(imgId);
				});
			}
		});
		thumbsC.disableSelection();
	}
}