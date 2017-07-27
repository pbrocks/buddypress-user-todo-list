<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>      
<div class="bptodo-adming-setting">
    <div class="bptodo-tab-header">
        <h3><?php _e( 'Have some questions?', BPTODO_TEXT_DOMAIN );?></h3>
    </div>

    <div class="bptodo-admin-settings-block">
        <div id="bptodo-settings-tbl">
            <div class="bptodo-admin-row">
                <div>
                   <button class="bptodo-accordion"><?php _e( 'Plugin Working?', BPTODO_TEXT_DOMAIN );?></button>
                    <div class="panel">
                        <p><?php _e( 'This plugin creates a menu <strong>Todo</strong> in the user profile. He can add a set of tasks and can manage them accordingly.', BPTODO_TEXT_DOMAIN );?></p>
                        <p><?php _e( 'A user can even <strong>Export</strong> his/her list.', BPTODO_TEXT_DOMAIN );?></p>
                    </div>
                </div>
            </div>

            <div class="bptodo-admin-row">
                <div>
                   <button class="bptodo-accordion"><?php _e( 'Any plugin dependency?', BPTODO_TEXT_DOMAIN );?></button>
                    <div class="panel">
                        <p><?php _e( 'As the name suggests, this plugin requires <strong>BuddyPress</strong> to be installed and active.', BPTODO_TEXT_DOMAIN );?></p>
                    </div>
                </div>
            </div>

            <div class="bptodo-admin-row">
                <div>
                   <button class="bptodo-accordion"><?php _e( 'How to go for any custom development?', BPTODO_TEXT_DOMAIN );?></button>
                    <div class="panel">
                        <p><?php _e( 'If you need additional help you can contact us for <a href="https://wbcomdesigns.com/contact/" target="_blank" title="Custom Development by Wbcom Designs">Custom Development</a>.', BPTODO_TEXT_DOMAIN );?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>