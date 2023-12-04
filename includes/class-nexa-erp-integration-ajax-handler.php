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


add_action('init', 'programar_tarea_wp_cron_crear_productos_nuevos');

function programar_tarea_wp_cron_crear_productos_nuevos()
{
    if (!wp_next_scheduled('crear_productos_nuevos')) {
        wp_schedule_event(time(), 'hourly', 'crear_productos_nuevos');
    }
}

// Function that will run when the WP Cron task is triggered

add_action('crear_productos_nuevos', 'ejecutar_agregar_productos_nuevos');

function ejecutar_agregar_productos_nuevos()
{
    $actual_date = new DateTime();
    $date_formatted = $actual_date->format('Y-m-d');
    $plugin = new Products();
    $plugin->bulk_create_from($date_formatted);
}

add_action('init', 'programar_tarea_wp_cron_actualizar_productos_nuevos');

function programar_tarea_wp_cron_actualizar_productos_nuevos()
{
    if (!wp_next_scheduled('actualizar_productos_nuevos')) {
        wp_schedule_event(time(), 'hourly', 'actualizar_productos_nuevos');
    }
}

// Function that will run when the WP Cron task is triggered

add_action('actualizar_productos_nuevos', 'ejecutar_actualizar_productos_nuevos');

function ejecutar_actualizar_productos_nuevos()
{
    $actual_date = new DateTime();
    $date_formatted = $actual_date->format('Y-m-d');
    $plugin = new Products();
    $plugin->bulk_update_from($date_formatted);
}



