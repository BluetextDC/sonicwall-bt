<div id="wpbrain-welcome" class="wrap about-wrap erropix-ui">

    <h1>Welcome to WP Brain</h1>
    <div class="about-text">Congratulations! Your WordPress website got a powerfull logic brain, It's time to teach him what to do and when to take actions
    </div>
    <div class="wp-badge">
        <div>Version <?php echo WPBRAIN_VERSION ?></div>
    </div>

    <h2 class="nav-tab-wrapper">
        <?php foreach ($this->tabs as $tab_slug => $tab): ?>
            <a href="<?php echo $this->get_tab_url($tab_slug) ?>"
               class="nav-tab<?php echo($tab_slug == $this->tab_slug ? ' nav-tab-active' : '') ?>">
                <?php echo $tab['title'] ?>
            </a>
        <?php endforeach ?>
    </h2>

    <div class="about-tab-content">
        <?php $this->render_current_tab() ?>
    </div>
</div><!-- .wrap -->
