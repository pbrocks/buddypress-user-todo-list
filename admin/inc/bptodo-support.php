<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>      
<div class="bptodo-adming-setting">
    <div class="bptodo-tab-header">
        <h3><?php _e( 'FAQ(s)', BPTODO_TEXT_DOMAIN );?></h3>
    </div>

    <div class="bptodo-admin-settings-block">
        <div id="bptodo-settings-tbl">
            <div class="bptodo-admin-row">
                <div>
                   <button class="bptodo-accordion"><?php _e( 'Does this plugin require any other plugin to work?', BPTODO_TEXT_DOMAIN );?></button>
                    <div class="panel">
                        <p><?php _e( 'As the name of the plugin justifies, this plugin helps manage To-Do List for BuddyPress Members, this plugin requires BuddyPress plugin to be installed and active.', BPTODO_TEXT_DOMAIN );?></p>
                        <p><?php _e( 'You\'ll also get an admin notice and the plugin will become ineffective if the required plugin is not there.', BPTODO_TEXT_DOMAIN );?></p>
                    </div>
                </div>
            </div>
            <div class="bptodo-admin-row">
                <div>
                   <button class="bptodo-accordion"><?php _e( 'Plugin Working?', BPTODO_TEXT_DOMAIN );?></button>
                    <div class="panel">
                        <p><?php _e( 'This plugin creates a menu To-Do in the user profile. User can add a set of tasks and can manage them accordingly.', BPTODO_TEXT_DOMAIN );?></p>
                        <p><?php _e( 'A user can even Export his/her list.', BPTODO_TEXT_DOMAIN );?></p>
                    </div>
                </div>
            </div>
            <div class="bptodo-admin-row">
                <div>
                   <button class="bptodo-accordion"><?php _e( 'How to go for any custom development?', BPTODO_TEXT_DOMAIN );?></button>
                    <div class="panel">
                        <p><?php _e( 'If you need additional help you can contact us for',BPTODO_TEXT_DOMAIN );?> <a href="https://wbcomdesigns.com/contact/" target="_blank" title="Custom Development by Wbcom Designs"><?php _e('Custom Development',BPTODO_TEXT_DOMAIN); ?></a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>