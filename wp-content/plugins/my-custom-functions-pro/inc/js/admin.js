/*
 * Plugin JavaScript and jQuery code for the admin pages of website
 *
 * @package     My Custom Functions PRO
 * @author      Arthur Gareginyan
 * @link        https://www.spacexchimp.com
 * @copyright   Copyright (c) 2016-2018 Space X-Chimp. All Rights Reserved.
 */


jQuery(document).ready(function($) {

    // Remove the 'successful' message after 3 seconds
    if ('.updated') {
        setTimeout(function() {
            $('.updated').fadeOut();
        }, 3000);
    }

    // Add questions and answers into spoilers and color them in different colors
    $('.panel-group .panel').each(function(i) {
         $('.question-' + (i+1) ).appendTo( $('h4', this) );
         $('.answer-' + (i+1) ).appendTo( $('.panel-body', this) );

         if ( $(this).find('h4 div').hasClass('question-red') ) {
             $(this).addClass('panel-danger');
         } else {
             $(this).addClass('panel-info');
         }
    });

    // Enable switches (on/off)
    $('.control-switch-onoff').checkboxpicker({
        reverse: true,
        offLabel: 'OFF',
        onLabel: 'ON'
    });

    // Delete a repeating section
    $('.deleteSection').click(function (e) {
        e.preventDefault();
        var current_field = $(this).parent('h3').parent('div');
        var other_fields = current_field.siblings('#repeatingSection');
        if (other_fields.length === 0) {
            alert("Cannot delete the last function. If you do not require this function then leave it blank.");
            return;
        }
        var answer = confirm("Are you sure you want to delete this function?");
        if (answer == true) {
            event.preventDefault();
            current_field.slideUp('fast', function () {
                current_field.remove();
            });
        }
    });

    // Show/Hide entry of field and change text of button
    $('.showHide').click(function (e) {
        e.preventDefault();
        $(this).parent().next('div').toggleClass('show');
        $('span', this).toggle();

        // Refresh CodeMirror editor
        $('.CodeMirror').each(function(index, elements) {
            elements.CodeMirror.refresh();
        });
    });

    // Warning about a not saved changes
    $('textarea').each(function() {
        var elem = $(this);

        // Save current value of element
        elem.data('oldVal', elem.val());

        // Monitor changes in the value
        elem.bind("propertychange change click keyup input paste", function(event) {

            // If value has changed
            if (elem.data('oldVal') != elem.val()) {

                // Updated stored value
                elem.data('oldVal', elem.val());

                // Show message
                $(this).closest('.postbox').children('h3').children('.not-saved').show();
            }
        });
    });

});
