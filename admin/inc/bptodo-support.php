<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>      
<div class="bptodo-adming-setting">
    <div class="bptodo-tab-header">
        <h3><?php _e( 'Have some questions?', 'wc-price-quotes' );?></h3>
    </div>

    <div class="bptodo-admin-settings-block">
        <div id="bptodo-settings-tbl">
            <div class="bptodo-admin-row">
                <div>
                   <button class="bptodo-accordion">
                    <?php _e( 'How can we send an enquiry for any product?', 'wc-price-quotes' );?>
                    </button>
                    <div class="panel">
                        <p> 
                            <?php _e( 'See the products from the store and go to its single description page. There at the bottom you’ll see an enquiry form, submitting which will forward your details to the product author.', 'wc-price-quotes' );?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bptodo-admin-row">
                <div>
                   <button class="bptodo-accordion">
                    <?php _e( 'How can I (admin) quote a price for any product?', 'wc-price-quotes' );?>
                    </button>
                    <div class="panel">
                        <p>
                            <?php _e( 'In the admin section, you’ll see a submenu page just under the woocommerce, <strong>“Price Quotes”</strong>. On that page, you’ll have 2 options to quote the prices.', 'wc-price-quotes' );
                            ?>
                        </p>
                        <p>
                            <?php _e( 'First option is for all the products, enabling which will make all the products on the site as not purchasable.', 'wc-price-quotes' );
                            ?>
                        </p>
                        <p>
                            <?php _e( 'Second option is for selected products where you’ll get to select products from a list, and the prices for those products will be quoted.', 'wc-price-quotes' );
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>