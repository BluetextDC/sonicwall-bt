<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<div  class="smls-shortcode-usage-wrapper">
    <ul>
        <li rel="tab-1" class="selected">
            <h4>Shortcode</h4>
            <p class="description">Copy &amp; paste the shortcode directly into any WordPress post or page.</p>
            <div class="smls-shortcode-page-wrap">[smls id="<?php echo $post->ID; ?>"]</div>
        </li>
        <li rel="tab-2">
            <h4>Template Include</h4>
            <p class="description">Copy &amp; paste this code into a template file to include the Smart Logo Showcase within your theme.</p>
            <div class="smls-shortcode-theme-wrap">&lt;?php echo do_shortcode("[smls id='<?php echo $post->ID; ?>']"); ?&gt;</div>
        </li>
    </ul>
</div>
