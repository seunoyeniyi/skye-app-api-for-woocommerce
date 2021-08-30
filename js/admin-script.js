jQuery(function($) {
    // FOR BANNER ONE UPLAOD
    // on upload button click
	// $('body').on( 'click', '.banner-upload-btn', function(e){

	// 	e.preventDefault();

	// 	var button = $(this),
	// 	custom_uploader = wp.media({
	// 		title: 'Insert image',
	// 		library : {
	// 			// uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
	// 			type : 'image'
	// 		},
	// 		button: {
	// 			text: 'Use this image' // button label text
	// 		},
	// 		multiple: false
	// 	}).on('select', function() { // it also has "open" and "close" events
	// 		var attachment = custom_uploader.state().get('selection').first().toJSON();
	// 		button.html('<img style="width: auto; heigth: auto; max-height: 200px; border: 1px solid #dfdfdf;" src="' + attachment.url + '">');
    //         var parent = button.closest("table");
	// 		parent.find(".banner-remove-btn").show();
	// 		parent.find("#banner-image-value").val(attachment.id);
    //         button.removeClass("button");
    //         button.removeClass("button-primary");
	// 	}).open();
	
	// });

	//handle on click to [select]
	// $('body').on('change', '.banner-click-to', function(e) {
	// 	var parent = $(this).closest("table");
	// 	switch($(this).val()) {
	// 		case "category":
	// 			parent.find(".banner-category").show();
	// 			parent.find("#banner-url-container").hide();
	// 			break;
	// 		case "url":
	// 			parent.find(".banner-category").hide();
	// 			parent.find("#banner-url-container").show();
	// 			break;
	// 		default:
	// 			parent.find(".banner-category").hide();
	// 			parent.find("#banner-url-container").hide();
	// 			break;
	// 	}
	// });

	// on remove button click
	// $('body').on('click', '.banner-remove-btn', function(e){
	// 	e.preventDefault();

	// 	var button = $(this);
	// 	var parent = button.closest("table");
	// 	parent.find("#banner-image-value").val(""); // emptying the hidden field
	// 	parent.find(".banner-upload-btn").html('Upload image');
    //     parent.find(".banner-upload-btn").addClass("button");
    //     parent.find(".banner-upload-btn").addClass("button-primary");
	// 	button.hide();
	// });




	// on upload button click
	$('body').on( 'click', '#skye-app-select-image, #skye-app-change-image', function(e){

		e.preventDefault();

		
		var custom_uploader = wp.media({
			title: 'Insert image',
			library : {
				// uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
				type : 'image'
			},
			button: {
				text: 'Use this image' // button label text
			},
			multiple: false
		}).on('select', function() { // it also has "open" and "close" events
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			$("#skye-app-banner-form #image").val(attachment.id);
			$("a#skye-app-change-image").html('<img style="width: auto; heigth: auto; max-height: 200px; border: 1px solid #dfdfdf;" src="' + attachment.url + '">');
            $("#skye-app-select-image").hide();
			$("#skye-app-remove-image").show();
		}).open();
	
	});

	// on remove button click
	$('body').on('click', '#skye-app-remove-image', function(e){
		e.preventDefault();

		$("#skye-app-banner-form #image").val("");
		$("a#skye-app-change-image").html("");
		$("#skye-app-select-image").show();
		$("#skye-app-remove-image").hide();
	});
});