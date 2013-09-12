jQuery(document).ready(function ($) {
	
	jQuery('#templates-container').isotope();
	// jQuery("#content_ifr").contents().find("img").width(); // image width

	var test = jQuery("#wp_cta_metabox_select_template");
	
	jQuery("#postdivrich").before(test);  
	
	// filter items when filter link is clicked
	jQuery('#template-filter a').click(function(){      
		var selector = jQuery(this).attr('data-filter');
		//alert(selector);
		jQuery('#templates-container').isotope({ filter: selector });
		return false;
	});
	
    $(".inbound-multi-select").select2({
                                placeholder: "Select one or more calls to action to rotate through",
                                allowClear: true,
     });
	/* Ajax loading tabs
		jQuery.koolSwap({
			swapBox : '#poststuff',
			outDuration : 550,
				inDuration : 600,
		});
    */
    /* Tour Start JS
    var tourbutton = '<a class="" id="wp-cta-tour" style="font-size:13px;">Need help? Take the tour</a>';
    jQuery(tourbutton).appendTo("h2:eq(0)");
    jQuery("body").on('click', '#wp-cta-tour', function () {
        var tour = jQuery("#wp-cta-tour-style").length;
         if ( tour === 0 ) {
            jQuery('head').append("<link rel='stylesheet' id='wp-cta-tour-style' href='/wp-content/plugins/wp-call-to-actions/css/admin-tour.css' type='text/css' /><script type='text/javascript' src='/wp-content/plugins/wp-call-to-actions/js/admin/tour/tour.post-edit.js'></script><script type='text/javascript' src='/wp-content/plugins/wp-call-to-actions/js/admin/intro.js'></script>");
          }
        setTimeout(function() {
                introJs().start(); // start tour
        }, 300);

    });
    */
    var current_a_tab = jQuery("#tabs-0").hasClass('nav-tab-special-active');
    if (current_a_tab === true){
        var url_norm = jQuery("#view-post-btn a").attr('href');
        var new_url = url_norm + "?wp-cta-variation-id=0";
        jQuery("#view-post-btn a").attr('href', new_url);
    }
    
    // Fix inactivate theme display
    jQuery("#template-box a").live('click', function () {
		setTimeout(function() {
			jQuery('#TB_window iframe').contents().find("#customize-controls").hide();
			jQuery('#TB_window iframe').contents().find(".wp-full-overlay.expanded").css("margin-left", "0px");
		}, 600);
    });
	
    var calc = jQuery(".calc.button-secondary");

    jQuery("input.cta-width").after(calc);
    jQuery(".calc.button-secondary").css('display', 'inline-block');
	
    // Fix Split testing iframe size
    jQuery("#wp-cta-metabox-splittesting a.thickbox, #leads-table-container-inside .column-details a").live('click', function () {
        jQuery('#TB_iframeContent, #TB_window').hide();
        setTimeout(function() {

         jQuery('#TB_iframeContent, #TB_window').width( 640 ).height( 800 ).css("margin-left", "0px").css("left", "35%");
         jQuery('#TB_iframeContent, #TB_window').show();
        }, 600);     
    });
    
    // Load meta box in correct position on page load
    var current_template = jQuery("input#wp_cta_select_template ").val();
    var current_template_meta = "#wp_cta_" + current_template + "_custom_meta_box";
    jQuery(current_template_meta).removeClass("postbox").appendTo("#template-display-options").addClass("Old-Template");
    var current_template_h3 = "#wp_cta_" + current_template + "_custom_meta_box h3";
    jQuery(current_template_h3).css("background","#f8f8f8");
    jQuery(current_template_meta +' .handlediv').hide();
    jQuery(current_template_meta +' .hndle').css('cursor','default');
        
  
    // Fix Thickbox width/hieght
    jQuery(function($) {
        tb_position = function() {
            var tbWindow = $('#TB_window');
            var width = $(window).width();
            var H = $(window).height();
            var W = ( 1720 < width ) ? 1720 : width;

            if ( tbWindow.size() ) {
                tbWindow.width( W - 50 ).height( H - 45 );
                $('#TB_iframeContent').width( W - 50 ).height( H - 75 );
                tbWindow.css({'margin-left': '-' + parseInt((( W - 50 ) / 2),10) + 'px'});
                if ( typeof document.body.style.maxWidth != 'undefined' )
                    tbWindow.css({'top':'40px','margin-top':'0'});
                //$('#TB_title').css({'background-color':'#fff','color':'#cfcfcf'});
            };

            return $('a.thickbox').each( function() {
                var href = $(this).attr('href');
                if ( ! href ) return;
                href = href.replace(/&width=[0-9]+/g, '');
                href = href.replace(/&height=[0-9]+/g, '');
                $(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 ) );
            });

        };

        jQuery('a.thickbox').click(function(){
            if ( typeof tinyMCE != 'undefined' &&  tinyMCE.activeEditor ) {
                tinyMCE.get('content').focus();
                tinyMCE.activeEditor.windowManager.bookmark = tinyMCE.activeEditor.selection.getBookmark('simple');
            }
           
        });

        $(window).resize( function() { tb_position() } );
    });
    
    // Isotope Styling
    jQuery('#template-filter a').first().addClass('button-primary');
    jQuery('#template-filter a').click(function(){
        jQuery("#template-filter a.button-primary").removeClass("button-primary");
        jQuery(this).addClass('button-primary');
    });
    
    jQuery('.wp_cta_select_template').click(function(){		
		
        var template = jQuery(this).attr('id');
        var label = jQuery(this).attr('label');
        var selected_template_id = "#" + template;
        var currentlabel = jQuery(".currently_selected").show();
		var current_template = jQuery("input#wp_cta_select_template ").val();
        var current_template_meta = "#wp_cta_" + current_template + "_custom_meta_box";
        var current_template_h3 = "#wp_cta_" + current_template + "_custom_meta_box h3";
        var current_template_div = "#wp_cta_" + current_template + "_custom_meta_box .handlediv";
		var open_variation = jQuery("#open_variation").val();

		if (open_variation>0)
		{
			var variation_tag = "-"+open_variation;
		}
		else
		{
			var variation_tag = "";
		}
		jQuery("#template-box.default_template_highlight").removeClass("default_template_highlight");
        
        jQuery(selected_template_id).parent().addClass("default_template_highlight").prepend(currentlabel);
        jQuery(".wp-cta-template-selector-container").fadeOut(500,function(){			
			
			jQuery('#template-display-options').fadeOut(500, function(){
			});
				
			var ajax_data = {
				action: 'wp_cta_get_template_meta',
				selected_template: template,
				post_id: wp_cta_post_edit_ui.post_id,
			};	
	
			jQuery.ajax({
					type: "POST",
					url: wp_cta_post_edit_ui.ajaxurl,
					data: ajax_data,
					dataType: 'html',
					timeout: 7000,
					success: function (response) {	
										
					jQuery('#wp_cta_metabox_select_template .input').remove();
					jQuery('#wp_cta_metabox_select_template .form-table').remove();
						jQuery('#template-display-options').fadeIn(500);
						//alert(response);
						var html = '<input id="wp_cta_select_template" type="hidden" value="'+template+'" name="wp-cta-selected-template'+variation_tag+'">'
								+ '<input type="hidden" value="'+wp_cta_post_edit_ui.wp_call_to_action_template_nonce+'" name="wp_cta_wp-cta_custom_fields_nonce">'
								 + '<h3 class="hndle" style="background: none repeat scroll 0% 0% rgb(248, 248, 248); cursor: default;">'
								 + '<span>'
								 + '<small>'+ template +' Options:</small>'
								 +	'</span>'
								 +	'</h3>'
								 + response;									
						
						jQuery('#wp_cta_metabox_select_template #template-display-options').html(html);
						
					},
					error: function(request, status, err) {					
						alert(status);
					}
				});				
				jQuery(".wrap").fadeIn(500, function(){
            });
        });
		
        jQuery(current_template_meta).appendTo("#template-display-options");
        jQuery('#wp_cta_metabox_select_template h3').first().html('Current Active Template: '+label);
        jQuery('#wp_cta_select_template').val(template);
        jQuery(".Old-Template").hide();
       
        jQuery(current_template_div).css("display","none");
        jQuery(current_template_h3).css("background","#f8f8f8");
        jQuery(current_template_meta).show().appendTo("#template-display-options").removeClass("postbox").addClass("Old-Template");
        //alert(template);
        //alert(label);
    });

	
    jQuery('#wp-cta-cancel-selection').click(function(){
        jQuery(".wp-cta-template-selector-container").fadeOut(500,function(){
            jQuery(".wrap").fadeIn(500, function(){
            });
        });
    
    });
	
    $("#blag").select2();
	
    jQuery("body").on('click', '.calc', function () {
        image_exists = jQuery("#content_ifr").contents().find('img').length;
        if (image_exists > 0){
        width = jQuery("#content_ifr").contents().find('img').width() + 15;
        height = jQuery("#content_ifr").contents().find('img').height() + 15;
        round_height = Math.ceil(height);  
        round_width = Math.ceil(width);
        jQuery(".cta-width").val(round_width);
        jQuery(".cta-height").val(round_height);
        } else {
            alert('No image found. For more complex templates you need to enter height and width manually. You can use free browser plugins like "measureit" to measure screen pixels');
        }
    });
    
    // the_content default overwrite
    jQuery('#overwrite-content').click(function(){
        if (confirm('Are you sure you want to overwrite what is currently in the main edit box above?')) {
            var default_content = jQuery(".default-content").text();
           jQuery("#content_ifr").contents().find("body").html(default_content);
       } else {
    // Do nothing!
    }  
    });
    
    // Colorpicker fix
    jQuery('.jpicker').one('mouseenter', function () {
        jQuery(this).jPicker({
            window: // used to define the position of the popup window only useful in binded mode
            {
                title: null, // any title for the jPicker window itself - displays "Drag Markers To Pick A Color" if left null
                position: {
                    x: 'screenCenter', // acceptable values "left", "center", "right", "screenCenter", or relative px value
                    y: 'center', // acceptable values "top", "bottom", "center", or relative px value
                },
                expandable: false, // default to large static picker - set to true to make an expandable picker (small icon with popup) - set
                // automatically when binded to input element
                liveUpdate: true, // set false if you want the user to click "OK" before the binded input box updates values (always "true"
                // for expandable picker)
                alphaSupport: false, // set to true to enable alpha picking
                alphaPrecision: 0, // set decimal precision for alpha percentage display - hex codes do not map directly to percentage
                // integers - range 0-2
                updateInputColor: true // set to false to prevent binded input colors from changing
            }
        },
        function(color, context)
        {
          var all = color.val('all');
         // alert('Color chosen - hex: ' + (all && '#' + all.hex || 'none') + ' - alpha: ' + (all && all.a + '%' || 'none'));
           //jQuery(this).attr('rel', all.hex);
           jQuery(this).parent().find(".wp-cta-success-message").remove();
           jQuery(this).parent().find(".new-save-wp-cta").show();
           jQuery(this).parent().find(".new-save-wp-cta-frontend").show();

           //jQuery(this).attr('value', all.hex);
        });
    });

    if (jQuery(".wp-cta-template-selector-container").css("display") == "none"){
        jQuery(".currently_selected").hide(); }
    else {
        jQuery(".currently_selected").show();
    }

    // Add current title of template to selector
    var selected_template = jQuery('#wp_cta_select_template').val();
	//alert(selected_template);
    var selected_template_id = "#" + selected_template;
    var currentlabel = jQuery(".currently_selected");
    jQuery(selected_template_id).parent().addClass("default_template_highlight").prepend(currentlabel);
    jQuery("#wp_cta_metabox_select_template h3").first().append(' - Current Active Template: <strong>' + selected_template + '</strong>')

    jQuery('#wp-cta-change-template-button').live('click', function () {
        jQuery(".wrap").fadeOut(500,function(){
            jQuery('#templates-container').isotope();
            jQuery(".wp-cta-template-selector-container").fadeIn(500, function(){
                jQuery(".currently_selected").show();
                jQuery('#wp-cta-cancel-selection').show();
            });
            jQuery("#template-filter li a").first().click();
        });
    });
    
    /* Move Slug Box
    var slugs = jQuery("#edit-slug-box");
    jQuery('#main-title-area').after(slugs.show());
    */
    // Background Options
    jQuery('.current_lander .background-style').live('change', function () {
        var input = jQuery(".current_lander .background-style option:selected").val();
        if (input == 'color') {
            jQuery('.current_lander tr.background-color').show();
            jQuery('.current_lander tr.background-image').hide();
            jQuery('.background_tip').hide();
        } 
        else if (input == 'default') {
            jQuery('.current_lander tr.background-color').hide();
            jQuery('.current_lander tr.background-image').hide();
            jQuery('.background_tip').hide();
        } 
        else if (input == 'custom') {
            var obj = jQuery(".current_lander tr.background-style td .wp_cta_tooltip");
            obj.removeClass("wp_cta_tooltip").addClass("background_tip").html("Use the custom css block at the bottom of this page to set up custom CSS rules");
            jQuery('.background_tip').show();
        }
        else {
            jQuery('.current_lander tr.background-color').hide();
            jQuery('.current_lander tr.background-image').show();
            jQuery('.background_tip').hide();
        }

    });

    // Check BG options on page load  
    jQuery(document).ready(function () {
        var input2 = jQuery(".current_lander .background-style option:selected").val();
        if (input2 == 'color') {
            jQuery('.current_lander tr.background-color').show();
            jQuery('.current_lander tr.background-image').hide();
        } else if (input2 == 'custom') {
            var obj = jQuery(".current_lander tr.background-style td .wp_cta_tooltip");
            obj.removeClass("wp_cta_tooltip").addClass("background_tip").html("Use the custom css block at the bottom of this page to set up custom CSS rules");
            jQuery('.background_tip').show();
        } else if (input2 == 'default') {
            jQuery('.current_lander tr.background-color').hide();
            jQuery('.current_lander tr.background-image').hide();   
        } else {
            jQuery('.current_lander tr.background-color').hide();
            jQuery('.current_lander tr.background-image').show();
        }
    });

    //Stylize lead's wp-list-table
    var cnt = $("#leads-table-container").contents();
    $("#wp_cta_conversion_log_metabox").replaceWith(cnt);
    
    //remove inputs from wp-list-table
    jQuery('#leads-table-container-inside input').each(function(){
        jQuery(this).remove();
    });

     var post_status = jQuery("#original_post_status").val();
    
    if (post_status === "draft") {
        // jQuery( ".nav-tab-wrapper.a_b_tabs .wp-cta-ab-tab, #tabs-add-variation").hide();
        jQuery(".new-save-wp-cta-frontend").on("click", function(event) {
            event.preventDefault();
            alert("Must publish this page before you can use the visual editor!");
        });
        var subbox = jQuery("#submitdiv");
        jQuery("#wp_cta_ab_display_stats_metabox").before(subbox)
    } else {
        jQuery("#publish").val("Update All");
    }

    // Ajax Saving for metadata
    jQuery('#wp_cta_metabox_select_template input, #wp_cta_metabox_select_template select, #wp_cta_metabox_select_template textarea').on("change keyup", function (e) {
        // iframe content change needs its own change function $("#iFrame").contents().find("#someDiv")
        // media uploader needs its own change function
        var this_id = jQuery(this).attr("id");
        var parent_el = jQuery(this).parent();
        jQuery(parent_el).find(".wp-cta-success-message").remove();
        jQuery(parent_el).find(".new-save-wp-cta").remove();
        var ajax_save_button = jQuery('<span class="button-primary new-save-wp-cta" id="' + this_id + '" style="margin-left:10px">Update</span>');
        //console.log(parent_el);
        jQuery(ajax_save_button).appendTo(parent_el);
    });
    jQuery('#wp-cta-notes-area input').on("change keyup", function (e) {
       var this_id = jQuery(this).attr("id");
        var parent_el = jQuery(this).parent();
        jQuery(parent_el).find(".wp-cta-success-message").remove();
        jQuery(parent_el).find(".new-save-wp-cta").remove();
        var ajax_save_button = jQuery('<span class="button-primary new-save-wp-cta" id="' + this_id + '" style="margin-left:10px">Update</span>');
        //console.log(parent_el);
        jQuery(ajax_save_button).appendTo(parent_el);
    });

        jQuery('#main-title-area input').on("change keyup", function (e) {
        // iframe content change needs its own change function $("#iFrame").contents().find("#someDiv")
        // media uploader needs its own change function
        var this_id = jQuery(this).attr("id");
        var current_view = jQuery("#wp-cta-current-view").text();
        if (current_view !== "0") {
            this_id = this_id + '-' + current_view;
        }
        var parent_el = jQuery(this).parent();
        jQuery(parent_el).find(".wp-cta-success-message").remove();
        jQuery(parent_el).find(".new-save-wp-cta").remove();
        var ajax_save_button = jQuery('<span class="button-primary new-save-wp-cta" id="' + this_id + '" style="margin-left:10px">Update</span>');
        //console.log(parent_el);
        jQuery(ajax_save_button).appendTo(parent_el);
    });


    var nonce_val = wp_cta_post_edit_ui.wp_call_to_action_meta_nonce; // NEED CORRECT NONCE
    jQuery("body").on('click', '.new-save-wp-cta', function () {
        var type_input = jQuery(this).parent().find("input").attr("type");
        var type_select = jQuery(this).parent().find("select");
        jQuery(this).parent().find(".wp-cta-success-message").hide();
        var type_textarea = jQuery(this).parent().find("textarea");
        if (typeof (type_input) != "undefined" && type_input !== null) {
            var type_of_field = type_input;
        } else if (typeof (type_textarea) != "undefined" && type_textarea !== null) {
            var type_of_field = 'textarea';
        } else {
            (typeof (type_select) != "undefined" && type_select)
            var type_of_field = 'select';
        }

        console.log(type_of_field);
        var new_value_meta_input = jQuery(this).parent().find("input").val();
        // console.log(new_value_meta_input); 
        var new_value_meta_select = jQuery(this).parent().find("select").val();
        var new_value_meta_textarea = jQuery(this).parent().find("textarea").val();
        //console.log(new_value_meta_select); 
        var new_value_meta_radio = jQuery(this).parent().find("input:checked").val();
        var new_value_meta_checkbox = jQuery(this).parent().find('input[type="checkbox"]:checked').val();

        // prep data
        if (typeof (new_value_meta_input) != "undefined" && new_value_meta_input !== null && type_of_field == "text") {
            var meta_to_save = new_value_meta_input;
        } else if (typeof (new_value_meta_textarea) != "undefined" && new_value_meta_textarea !== null && type_of_field == "textarea") {
            var meta_to_save = new_value_meta_textarea;
        } else if (typeof (new_value_meta_select) != "undefined" && new_value_meta_select !== null) {
            var meta_to_save = new_value_meta_select;
        } else if (typeof (new_value_meta_radio) != "undefined" && new_value_meta_radio !== null && type_of_field == "radio") {
            var meta_to_save = new_value_meta_radio;
        } else if (typeof (new_value_meta_checkbox) != "undefined" && new_value_meta_checkbox !== null && type_of_field == "checkbox") {
            var meta_to_save = new_value_meta_checkbox;
        } else {
            var meta_to_save = "";
        }

        // if data exists save it
        var this_meta_id = jQuery(this).attr("id");
        var post_id = jQuery("#post_ID").val();

        jQuery.ajax({
            type: 'POST',
            url: wp_cta_post_edit_ui.ajaxurl,
            context: this,
            data: {
                action: 'wp_wp_call_to_action_meta_save',
                meta_id: this_meta_id,
                new_meta_val: meta_to_save,
                page_id: post_id,
                nonce: nonce_val
            },

            success: function (data) {
                var self = this;

                //alert(data);
                // jQuery('.wp-cta-form').unbind('submit').submit();
                //var worked = '<span class="success-message-map">Success! ' + this_meta_id + ' set to ' + meta_to_save + '</span>';
                var worked = '<span class="wp-cta-success-message">Updated!</span>';
                var s_message = jQuery(self).parent();
                jQuery(worked).appendTo(s_message);
                jQuery(self).hide();
                jQuery("#switch-wp-cta").text("0");
                //alert("Changes Saved!");
            },

            error: function (MLHttpRequest, textStatus, errorThrown) {
                alert("Ajax not enabled");
            }
        });

        return false;
        
    });
});