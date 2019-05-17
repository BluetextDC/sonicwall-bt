<?php

/**
 * Prevent Direct Access
 */
defined( 'ABSPATH' ) or die( "Restricted access!" );

/**
 * Render Settings Tab Content
 */
?>
    <div class="has-sidebar sm-padded">
        <div id="post-body-content" class="has-sidebar-content">
            <div class="meta-box-sortabless">

                <form action="options.php" method="post" enctype="multipart/form-data">
                    <?php settings_fields( SPACEXCHIMP_P011_SETTINGS . '_settings_group' ); ?>

                    <button type="submit" name="submit" id="submit" class="btn btn-info btn-lg button-save-top">
                        <i class="fa fa-save" aria-hidden="true"></i>
                        <span><?php _e( 'Save changes', $text ); ?></span>
                    </button>

                    <?php
                        // Get options from the database
                        $options = get_option( SPACEXCHIMP_P011_SETTINGS . '_settings' );

                        // Set default value if option is empty
                        $snippets = !empty( $options['snippets'] ) ? $options['snippets'] : array();

                        // Cycle
                        $num = "1";
                        if ( !empty( $snippets ) ) {
                            foreach ( $snippets as $snippet ) {
                                foreach ( $snippet as $key => $value ) {

                                    if ( $key == "label" ) {
                                        $label = $value;
                                    }
                                    if ( $key == "content" ) {
                                        $content = $value;
                                        $checked = !empty( $options["snippets"]["function-$num"]["enable"] ) ? 'checked' : '';
                                        ?>
                                            <div class="postbox" id="repeatingSection">
                                                <h3 class="title">
                                                    <input
                                                        type="text"
                                                        name="spacexchimp_p011_settings[snippets][function-<?php echo $num; ?>][label]"
                                                        id="spacexchimp_p011_settings[snippets][function-<?php echo $num; ?>][label]"
                                                        size="50%"
                                                        value="<?php echo $label; ?>"
                                                        placeholder="Title or small description"
                                                    >
                                                    <input
                                                        type="checkbox"
                                                        name="spacexchimp_p011_settings[snippets][function-<?php echo $num; ?>][enable]"
                                                        id="spacexchimp_p011_settings[snippets][function-<?php echo $num; ?>][enable]"
                                                        <?php echo $checked; ?>
                                                        class="control-switch-onoff"
                                                    >
                                                    <span class="not-saved"><?php _e( 'NOT SAVED!', $text ); ?></span>
                                                    <button type="button" class="btn btn-success pull-right showHide">
                                                        <span><?php _e( 'Show', $text ); ?></span>
                                                        <span class="tempHide"><?php _e( 'Hide', $text ); ?></span>
                                                    </button>
                                                    <button type="button" class="btn btn-danger pull-right deleteSection">
                                                        <i class="fa fa-trash-o fa-lg"></i>
                                                    </button>
                                                </h3>
                                                <div class="inside hide">
                                                    <textarea
                                                        name="spacexchimp_p011_settings[snippets][function-<?php echo $num; ?>][content]"
                                                        id="spacexchimp_p011_settings[snippets][function-<?php echo $num; ?>][content]"
                                                        placeholder="<?php _e( 'Enter your PHP function here', $text ); ?>"
                                                    ><?php echo htmlentities( $content ); ?></textarea>
                                                </div>
                                            </div>
                                        <?php
                                        $num++;
                                    }
                                }
                            }
                        } else {
                            ?>
                                <div class="postbox" id="repeatingSection">
                                    <h3 class="title">
                                        <input
                                            type="text"
                                            name="spacexchimp_p011_settings[snippets][function-<?php echo $num; ?>][label]"
                                            id="spacexchimp_p011_settings[snippets][function-<?php echo $num; ?>][label]"
                                            size="50%"
                                            value=""
                                            placeholder="Title or small description"
                                        >
                                        <span class="not-saved"><?php _e( 'NOT SAVED!', $text ); ?></span>
                                    </h3>
                                    <div class="inside">
                                        <textarea
                                            name="spacexchimp_p011_settings[snippets][function-<?php echo $num; ?>][content]"
                                            id="spacexchimp_p011_settings[snippets][function-<?php echo $num; ?>][content]"
                                            placeholder="<?php _e( 'Enter your PHP function here', $text ); ?>"
                                        ></textarea>
                                    </div>
                                </div>
                            <?php
                        }
                    ?>

                    <button type="button" class="btn btn-primary addSection">
                        <?php _e( 'Add another function', $text ); ?>
                    </button>

                    <input type="submit" name="submit" id="submit" class="btn btn-primary saveButton" value="<?php _e( 'Save changes', $text ); ?>">

                </form>

            </div>
        </div>
    </div>
<?php
