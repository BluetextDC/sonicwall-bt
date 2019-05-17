<?php 
  global $post;
  $menuData = getMenuData($post);

  $anchor_styles = "";

  switch ($menuData->anchor) {
      case 'tl':
          $anchor_styles =  "left: ".$menuData->x."px; top: ".$menuData->y."px;";
          break;
      case 'tr':
          $anchor_styles =  "right: ".$menuData->x."px; top: ".$menuData->y."px;";
          break;
      case 'bl':
          $anchor_styles =  "left: ".$menuData->x."px; bottom: ".$menuData->y."px;";
          break;
      case 'br':
          $anchor_styles =  "right: ".$menuData->x."px; bottom: ".$menuData->y."px;";
          break;
  }
?>
<script>
window.circular_menu_anchor = "<?php echo $menuData->anchor;?>";
</script>
<div id="circular-menu" draggable="true" style="display: none; <?php echo $anchor_styles;?> ">
  <div class="circular-menu-container">
    <nav class='menu'>
      <input class='menu-toggler' id='menu-toggler' type='checkbox'>
      <label for='menu-toggler'>
        <i class="icon-help" style="font-size: 40px;"></i>
      </label>
      <ul>
        <?php foreach ($menuData->links as $link) { 
          if ($link->enabled === "on") {
        ?>
        <li class='menu-item'>
          <a href='<?php echo $link->link;?>' <?php echo $link->lightbox == "on" ? " data-fancybox data-type='iframe' " : "";?> target='<?php echo $link->new_window === 'on' ? '_blank' : ''?>'>
            <i class='<?php echo $link->icon;?>'></i>
            <div class="menu-item-text-container">
              <p class="menu-item-text"><?php echo $link->title;?></p>
            </div>
          </a>
        </li>
        <?php 
          }
        }
        ?>
        
      </ul>
    </nav>
  </div>
</div>
