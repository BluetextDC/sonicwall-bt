<div class="item">
    <?php
    $o = new wpdreamsCustomSelect("resultstype", __('Results layout type', 'ajax-search-pro'), array(
        'selects'=>array(
            array('option' => __('Vertical Results', 'ajax-search-pro'), 'value' => 'vertical'),
            array('option' => __('Horizontal Results', 'ajax-search-pro'), 'value' => 'horizontal'),
            array('option' => __('Isotopic Results', 'ajax-search-pro'), 'value' => 'isotopic'),
            array('option' => __('Polaroid style Results', 'ajax-search-pro'), 'value' => 'polaroid')
        ),
        'value'=>$sd['resultstype']
    ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<p class='infoMsg'>
    <?php echo __('If you are using <b>Polaroid</b> layout type, then <b>block</b> position is highly recommended!', 'ajax-search-pro'); ?>
</p>
<div class="item">
    <?php
    $o = new wpdreamsCustomSelect("resultsposition", __('Results layout position', 'ajax-search-pro'), array(
        'selects'=>array(
            array('option' => 'Hover - over content', 'value' => 'hover'),
            array('option' => 'Block - pushes content', 'value' => 'block')
        ),
        'value'=>$sd['resultsposition']
    ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showauthor", __('Show author in results?', 'ajax-search-pro'), $sd['showauthor']);
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsCustomSelect("author_field", __('Author field', 'ajax-search-pro'),
		array(
			'selects' => array(
				array('option' => 'Display name', 'value' => 'display_name'),
				array('option' => 'Login name', 'value' => 'user_login')
			),
			'value' => $sd['author_field']
		));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showdate", __('Show date in results?', 'ajax-search-pro'), $sd['showdate']);
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item item-flex-nogrow item-conditional" style="flex-wrap: wrap;">
    <?php
        $o = new wpdreamsYesNo("custom_date", __('Use custom date format?', 'ajax-search-pro'),
            $sd['custom_date']);
        $params[$o->getName()] = $o->getData();
    ?>
    <?php
        $o = new wpdreamsText("custom_date_format", __(' format', 'ajax-search-pro'),
            $sd['custom_date_format']);
        $params[$o->getName()] = $o->getData();
    ?>
    <div class='descMsg' style="min-width: 100%;
    flex-wrap: wrap;
    flex-basis: auto;
    flex-grow: 1;
    box-sizing: border-box;">
        <?php echo __('If turned OFF, it will use WordPress defaults. Default custom value: <b>Y-m-d H:i:s</b>', 'ajax-search-pro'); ?>
    </div>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showdescription", __('Show description (content) in results?', 'ajax-search-pro'), $sd['showdescription']);
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsTextSmall("descriptionlength", __('Description (content) length', 'ajax-search-pro'), $sd['descriptionlength']);
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">
        <?php echo __('Content length in characters.', 'ajax-search-pro'); ?>
    </p>
</div>
<div class="item item item-flex-nogrow" style="flex-wrap: wrap;">
    <?php
    $o = new wpdreamsYesNo("description_context", __('Display the description context?', 'ajax-search-pro'), $sd['description_context']);
    $params[$o->getName()] = $o->getData();

    $o = new wpdreamsTextSmall("description_context_depth", __(' ..depth', 'ajax-search-pro'), $sd['description_context_depth']);
    $params[$o->getName()] = $o->getData();
    ?><div>characters.</div>
    <div class='descMsg' style="min-width: 100%;
    flex-wrap: wrap;
    flex-basis: auto;
    flex-grow: 1;
    box-sizing: border-box;">
        <?php echo __('Will display the description from around the search phrase, not from the beginning.', 'ajax-search-pro'); ?>
    </div>
</div>
<script>
    jQuery(function($) {
        $('select[name="more_results_action"]').change(function(){
            if ($(this).val() == 'ajax') {
                $('input[name="more_redirect_url"]').parent().parent().css("display", "none");
            } else {
                $('input[name="more_redirect_url"]').parent().parent().css("display", "block");
            }
        });
        $('select[name="more_results_action"]').change();
    });
</script>