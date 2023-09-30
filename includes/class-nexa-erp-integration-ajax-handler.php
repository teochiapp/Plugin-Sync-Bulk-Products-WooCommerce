<?php
// Defining actions to the functions
$ajax_actions = array(
    'mi_endpoint_bulk_create_product' => 'bulk_create',
    'mi_endpoint_bulk_create_from_product' => 'bulk_create_from',
    'mi_endpoint_bulk_update_product' => 'bulk_update',
    'mi_endpoint_bulk_update_from_product' => 'bulk_update_from',
    'mi_endpoint_sync_images_product' => 'sync_images',
);


// Register the functions and actions
foreach ($ajax_actions as $action => $function) {
    if ($action) {
        add_action("wp_ajax_$action", function () use ($function) {
            $plugin = new Products();
            $result = $plugin->$function($_POST['product']);
            wp_send_json($result);
        });
    }
}
