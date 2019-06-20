<div class="wrap erropix-ui">
    <h2><?php _e("WP Brain Settings", 'wpbrain') ?></h2>

    <div id="wpbrain_settings">
        <h2 class="nav-tab-wrapper">
            <?php if (count($this->tabs) > 1): ?>
                <?php foreach ($this->tabs as $tab_slug => $tab): ?>
                    <a href="<?php echo $this->get_tab_url($tab_slug) ?>"
                       class="nav-tab<?php echo($tab_slug == $this->tab_slug ? ' nav-tab-active' : '') ?>">
                        <?php echo $tab['title'] ?>
                    </a>
                <?php endforeach ?>
            <?php endif ?>
        </h2>
        <div class="nav-tab-content">
            <?php $this->render_current_tab() ?>
        </div>
    </div>

    <div style="display:none;">
        <?php wp_editor('', uniqid(), array('quicktags' => 0, 'media_buttons' => 0, 'teeny' => 1)) ?>
    </div>

    <script type="text/javascript">
        !function ($) {
            // Open chosen select on focus
            $(document).on('focus', '.chosen-select > select', function (e) {
                var $this = $(this);
                setTimeout(function () {
                    $this.trigger('chosen:open');
                }, 0);
            });

            // Dirty forms notification before window close
            $(window).on('load', function () {
                setTimeout(function () {
                    $('form[data-ays]').ays();
                }, 100);
            });
        }(jQuery);
    </script>
</div><!-- .wrap -->
