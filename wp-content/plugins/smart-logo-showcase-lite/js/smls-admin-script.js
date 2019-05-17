(function($) {

    /**
     * Add logo functionality
     */
    $(function() {
        $('.smls-color-picker').wpColorPicker();

        /*
         * Add logo
         */
        $('body').on('click', '.smls-add-logo-button', function() {
            var image = wp.media({
                title: 'Insert Logo',
                button: {text: 'Insert Logo'},
                library: {type: 'image'},
                multiple: "toggle"
            }).open()
                    .on('select', function() {
                        var uploaded_image = image.state().get('selection');
                        uploaded_image.map(function(attachment) {
                            attachment = attachment.toJSON();
                            var image_id = attachment.id;
                            var image_url = attachment.url;
                            var data = {
                                'action': 'smls_logo_detail',
                                '_wpnonce': smls_backend_js_params.ajax_nonce,
                                'image_url': image_url,
                                'image_id': image_id
                            };
                            $.ajax({
                                url: smls_backend_js_params.ajax_url,
                                data: data,
                                type: "POST",
                                success: function(response) {
                                    $(".smls-add-append-wrap").append(response);
                                    $('.smls-add-append-wrap').sortable({
                                        handle: ".smls-move-logo",
                                        containment: "parent"
                                    });
                                }
                            });
                        });
                    });
        });
        $('.smls-add-append-wrap').sortable({
            handle: ".smls-move-logo",
            containment: "parent"
        });

        /**
         * logo Item Remove
         *
         */

        $('body').on('click', '.smls-delete-logo', function() {
            var delete_logo = confirm('Are you sure you want to delete this logo?');
            if (delete_logo) {
                $(this).closest('.smls-each-logo-item').fadeOut(500, function() {
                    $(this).remove();
                });
            }
        });
        /*
         *toggle for logo
         */

        $('body').on('click', '.smls-logo-slideToggle', function() {
            $(this).closest('.smls-add-logo-showcase').find('.smls-logo-slideTogglebox').slideToggle();
            $(this).find('i').toggleClass('fa-angle-down fa-angle-up');
        });
        /*
         *Upload image configuration For editing the main image
         */
        $('body').on('click', '.smls-edit-logo', function(e) {
            e.preventDefault();
            var btnClicked = $(this);
            var image = wp.media({
                title: 'Insert Logo',
                button: {text: 'Insert Logo'},
                library: {type: 'image'},
                multiple: false
            }).open()
                    .on('select', function(e) {
                        var uploaded_image = image.state().get('selection').first();
                        console.log(uploaded_image);
                        var image_url = uploaded_image.toJSON().url;
                        $(btnClicked).closest('.smls-logo-image-preview').find('.smls-logo-image').attr('src', image_url);
                        $(btnClicked).closest('.smls-each-logo-item').find('.smls-logo-image-url').val(image_url);
                        $(btnClicked).closest('.smls-each-logo-item').find('.smls-logo-save-trigger').click();
                    });
        });

        /**
         * Popup close
         *
         */
        $('body').on('click', '.smls-close-popup', function() {
            $(this).closest('.smls-each-logo-item').find('.smls-logo-save-trigger').click();
            $(this).closest('.smls-each-logo-item').find('.smls-add-logo-option-wrap').fadeOut(500);
        });
        /**
         * Show settings configuration section
         *
         */
        $('body').on('click', '.smls-settings-logo', function() {
            $(this).closest('.smls-each-logo-item').find('.smls-add-logo-option-wrap').fadeIn(500);
        });
        /*
         * Grid layout show and hide
         */
        $('.smls-grid-template').change(function() {
            if ($(this).val() === 'template-1' || $(this).val() === 'template-2') {
                $('.smls-extra-effects-wrap').show();
                $('.smls-overlay-note').show();
                $('.smls-grid-border-wrap').show();
            } else {
                $('.smls-extra-effects-wrap').show();
                $('.smls-overlay-note').show();
                $('.smls-grid-border-wrap').hide();
            }
        });
        var selected_grid_type = $(".smls-grid-template option:selected").val();
        if (selected_grid_type == "template-1" || selected_grid_type == "template-2")
        {
            $('.smls-extra-effects-wrap').show();
            $('.smls-overlay-note').show();
            $('.smls-grid-border-wrap').show();
        } else {
            $('.smls-extra-effects-wrap').show();
            $('.smls-overlay-note').show();
            $('.smls-grid-border-wrap').hide();
        }

        /**
         * Layout type show & hide configuration
         *
         */

        $(".smls-layout-type").change(function() {
            if ($(this).val() === "grid")
            {
                $(".smls-grid-setting-wrap").show();
                $(".smls-carousel-setting-section").hide();
                $(".smls-pager-setting-wrapper").hide();
                $(".smls-slider-setting-wrap").hide();

            } else if ($(this).val() === "carousel")
            {
                $(".smls-grid-setting-wrap").hide();
                $(".smls-carousel-setting-section").show();
                $(".smls-pager-setting-wrapper").show();
                $(".smls-slider-setting-wrap").show();
                $('.smls-count-slide-wrap').show();
                $('.smls-carousel-margin-wrap').show();

            } else {
                $(".smls-grid-setting-wrap").hide();
                $(".smls-carousel-setting-section").hide();
                $(".smls-pager-setting-wrapper").hide();
                $(".smls-slider-setting-wrap").hide();
                $('.smls-count-slide-wrap').hide();
                $('.smls-carousel-margin-wrap').hide();
            }
        });
        var selected_layout_type = $(".smls-layout-type option:selected").val();
        if (selected_layout_type === "grid")
        {
            $(".smls-grid-setting-wrap").show();
            $(".smls-carousel-setting-section").hide();
            $(".smls-pager-setting-wrapper").hide();
            $(".smls-slider-setting-wrap").hide();
        } else if (selected_layout_type === "carousel")
        {
            $(".smls-grid-setting-wrap").hide();
            $(".smls-carousel-setting-section").show();
            $(".smls-pager-setting-wrapper").show();
            $(".smls-slider-setting-wrap").show();
            $('.smls-count-slide-wrap').show();
            $('.smls-carousel-margin-wrap').show();
        } else {
            $(".smls-grid-setting-wrap").hide();
            $(".smls-carousel-setting-section").hide();
            $(".smls-pager-setting-wrapper").hide();
            $(".smls-slider-setting-wrap").hide();
            $('.smls-count-slide-wrap').hide();
            $('.smls-carousel-margin-wrap').hide();
        }

        /*
         * toggle form for grid settings
         */
        $('body').on('click', '.smls-grid-toogle-outer-wrap', function() {
            $(this).closest('.smls-grid-setting-wrap').find('.smls-inner-toogle-grid').slideToggle();
            $(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');
        });
        $('body').on('click', '.smls-tooltip-outer-wrap', function() {
            $(this).closest('.smls-tooltip-main-wrapper').find('.smls-tooltip-inner-wrap').slideToggle();
            $(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');
        });


        $('body').on('click', '.smls-carousel-outer-wrap', function() {
            $(this).closest('.smls-carousel-setting-section').find('.smls-carousel-inner-wrap').slideToggle();
            $(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');
        });
        $('body').on('click', '.smls-pager-outer-wrap', function() {
            $(this).closest('.smls-pager-setting-wrapper').find('.smls-pager-inner-wrap').slideToggle();
            $(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');
        });
        $('body').on('click', '.smls-slider-outer-wrap', function() {
            $(this).closest('.smls-slider-setting-wrap').find('.smls-slider-inner-wrap').slideToggle();
            $(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');
        });


//checked for external link
        $('body').on('click', '.smls-logo-external-link-info', function() {
            if ($(this).is(':checked')) {
                $(this).closest('.smls-option-field').find('.smls-logo-external-link-value').val('1');
                $(this).closest('.smls-add-logo-option-wrap').find('.smls-external-link-wrap').show();
            } else
            {
                $(this).closest('.smls-option-field').find('.smls-logo-external-link-value').val('0');
                $(this).closest('.smls-add-logo-option-wrap').find('.smls-external-link-wrap').hide();
            }
        });
        $('.smls-image-effect-type').change(function() {
            if ($(this).val() === "hover")
            {

                $('.smls-hover-setting-wrap').show();
            } else
            {
                $('.smls-hover-setting-wrap').hide();
            }
        });
        var selected_effect = $(".smls-image-effect-type option:selected").val();
        if (selected_effect === "hover")
        {
            $('.smls-hover-setting-wrap').show();
        } else {
            $('.smls-hover-setting-wrap').hide();
        }

        /*
         * Title settings
         */

        $('.smls-view-title-type').change(function() {
            if ($(this).val() === "title_tooltip")
            {

                $('.smls-tooltip-main-wrapper').show();
            } else
            {
                $('.smls-tooltip-main-wrapper').hide();
            }
        });
        var selected_title = $(".smls-view-title-type option:selected").val();
        if (selected_title === "title_tooltip")
        {
            $('.smls-tooltip-main-wrapper').show();
        } else {
            $('.smls-tooltip-main-wrapper').hide();
        }

        /*
         * Carousel layout show and hide
         */
        $('.smls-carousel-template').change(function() {
            if ($(this).val() === 'template-1') {
                $('.smls-hover-outer-wrap').show();
                $('.smls-overlay-note').show();
                $('.smls-car-border-color').hide();
            } else {
                $('.smls-hover-outer-wrap').show();
                $('.smls-overlay-note').show();
                $('.smls-car-border-color').show();
            }
        });
        var selected_car = $(".smls-carousel-template option:selected").val();
        if (selected_car === "template-1") {
            $('.smls-hover-outer-wrap').show();
            $('.smls-overlay-note').show();
            $('.smls-car-border-color').hide();
        } else {
            $('.smls-hover-outer-wrap').show();
            $('.smls-overlay-note').show();
            $('.smls-car-border-color').show();
        }

        $('.smls-arrow-type').change(function() {
            if ($(this).val() === 'type-1') {
                $('.smls-arrow-hover-color.smls-color-picker').val('rgba(71, 71, 71, 0.7)');
                $('.smls-arrow-color.smls-color-picker').val('#474747');
                $('.smls-arrow-hover-wrap').show();
            } else if ($(this).val() === 'type-2') {
                $('.smls-arrow-hover-color.smls-color-picker').val('#f6881f');
                $('.smls-arrow-color.smls-color-picker').val('#bcbcbc');
                $('.smls-arrow-hover-wrap').show();
            } else if ($(this).val() === 'type-3') {
                $('.smls-arrow-hover-color.smls-color-picker').val('#f24831');
                $('.smls-arrow-color.smls-color-picker').val('#e8e8e8');
                $('.smls-arrow-hover-wrap').show();
            } else if ($(this).val() === 'type-4') {
                $('.smls-arrow-hover-color.smls-color-picker').val('#e8e8e8');
                $('.smls-arrow-color.smls-color-picker').val('#cccccc');
                $('.smls-arrow-hover-wrap').show();
            } else {
                $('.smls-arrow-color.smls-color-picker').val('#75be08');
                $('.smls-arrow-hover-wrap').hide();
            }
        });
        var selected_arrow = $(".smls-arrow-type option:selected").val();
        if (selected_arrow === "type-5") {

            $('.smls-arrow-hover-wrap').hide();
        } else {
            $('.smls-arrow-hover-wrap').show();
        }

//grid template preview
        $(".smls-grid-common").first().addClass("grid-active");
        $('.smls-grid-template').on('change', function() {
            var template_value = $(this).val();
            var array_break = template_value.split('-');
            var current_id = array_break[1];
            $('.smls-grid-common').hide();
            $('#smls-grid-demo-' + current_id).show();
        });
        var grid_view = $(".smls-grid-template option:selected").val();
        var array_break = grid_view.split('-');
        var current_id1 = array_break[1];
        $('.smls-grid-common').hide();
        $('#smls-grid-demo-' + current_id1).show();
        //carousel template preview
        $(".smls-carousel-common").first().addClass("carousel-active");
        $('.smls-carousel-template').on('change', function() {
            var template_value = $(this).val();
            var array_break = template_value.split('-');
            var current_id = array_break[1];
            $('.smls-carousel-common').hide();
            $('#smls-carousel-demo-' + current_id).show();
        });
        var carousel_view = $(".smls-carousel-template option:selected").val();
        var array_break = carousel_view.split('-');
        var current_id1 = array_break[1];
        $('.smls-carousel-common').hide();
        $('#smls-carousel-demo-' + current_id1).show();

        //arrow template preview
        $(".smls-arrow-common").first().addClass("arrow-active");
        $('.smls-arrow-type').on('change', function() {
            var template_value = $(this).val();
            var array_break = template_value.split('-');
            var current_id = array_break[1];
            $('.smls-arrow-common').hide();
            $('#smls-arrow-demo-' + current_id).show();
        });
        var arrow_view = $(".smls-arrow-type option:selected").val();
        var array_break = arrow_view.split('-');
        var current_id1 = array_break[1];
        $('.smls-arrow-common').hide();
        $('#smls-arrow-demo-' + current_id1).show();

        //tooltip template preview
        $(".smls-tooltip-common").first().addClass("tooltip-active");
        $('.smls-tooltip-template').on('change', function() {
            var template_value = $(this).val();
            var array_break = template_value.split('-');
            var current_id = array_break[1];
            $('.smls-tooltip-common').hide();
            $('#smls-tooltip-demo-' + current_id).show();
        });
        var tooltip_view = $(".smls-tooltip-template option:selected").val();
        var array_break = tooltip_view.split('-');
        var current_id1 = array_break[1];
        $('.smls-tooltip-common').hide();
        $('#smls-tooltip-demo-' + current_id1).show();



        /*
         * Carousel pager show hide
         */
        $('.smls-carousel-pager').change(function() {
            if ($(this).val() === 'true') {

                $('.smls-pager-hide-wrap').show();
            } else {

                $('.smls-pager-hide-wrap').hide();
            }
        });
        var selected_car_pager = $(".smls-carousel-pager option:selected").val();
        if (selected_car_pager === "true") {
            $('.smls-pager-hide-wrap').show();
        } else {

            $('.smls-pager-hide-wrap').hide();
        }

        /*
         * Carousel controls arrow show hide
         */
        $('.smls-controls-type').change(function() {
            if ($(this).val() === 'arrow') {

                $('.smls-controls-true-wrap').show();
            } else {

                $('.smls-controls-true-wrap').hide();
            }
        });
        var selected_car_control = $(".smls-controls-type option:selected").val();
        if (selected_car_control === "arrow") {
            $('.smls-controls-true-wrap').show();
        } else {

            $('.smls-controls-true-wrap').hide();
        }
        /*
         * Carousel controls show hide
         */
        $('.smls-carousel-controls').change(function() {
            if ($(this).val() === 'true') {

                $('.smls-car-control-type-wrap').show();
            } else {

                $('.smls-car-control-type-wrap').hide();
            }
        });
        var selected_car_control = $(".smls-carousel-controls option:selected").val();
        if (selected_car_control === "true") {
            $('.smls-car-control-type-wrap').show();
        } else {

            $('.smls-car-control-type-wrap').hide();
        }
        $('body').on('click', '.smls-logo-save-trigger', function() {
            var logo_serialized_detail = $(this).closest('.smls-add-logo-option-wrap').find('input').serialize();
            $(this).closest('.smls-each-logo-item').find('.smls_logo_details_data').val(logo_serialized_detail);
        });
    });
}(jQuery));
