<?php
$ra = new RARequests();
$categories = $ra->get_ra_categories();

if ($categories && $categories[0] && $categories[0]->value && $categories[0]->value == "Alerts")
{
    array_shift($categories);
}

$lang_home_url = apply_filters( 'wpml_home_url', get_option( 'home' ) );

if (!(substr($lang_home_url, -strlen("/")) === "/"))
{
    $lang_home_url = $lang_home_url."/";
}

$i = 0;
foreach ($categories as $category)
{
    ?>
<div class="one-quarter"><a class="ra_cat_search" href="<?php echo $lang_home_url;?>support/knowledge-base/<?php echo $ra->build_category_slug($category);?>" id="<?php echo $ra->build_category_slug($category);?>"><?php echo $category->value;?></a></div>
    <?php
    $i++;
}
?>
