<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://estudiorochayasoc.com.ar
 * @since      1.0.0
 *
 * @package    Nexa_Erp_Integration
 * @subpackage Nexa_Erp_Integration/admin/partials
 */

require_once dirname(__FILE__) . '/../../includes/class-nexa-erp-integration-products.php';

$nexa_admin = new Products();
$nexa_admin->get_products();

$products = $nexa_admin->get_products();

// Keeping in the session variable
$_SESSION["products"] = $products;

?>
<div class="wrap" id="page" data-product='<?= json_encode($_SESSION["products"]) ?>'>
    <div class="container d-flex mt-3">
        <div class="row">
            <div class="col-12">
                <h3 class="text-bold bold">Nexa ERP | Integrador</h3>
                Total de productos en NEXA: <b><?= count($_SESSION["products"]) ?></b>
            </div>
        </div>
        <div>

        </div>
    </div>
    <hr />


    <div class="container">
        <div class="row mt-3">
            <div class="col-12">
                <button class="button-primary" onclick="do_action('bulk_create');">AGREGAR PRODUCTOS</button>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <button class="button-primary" onclick="do_action('bulk_create_from');">AGREGAR PRODUCTOS DESDE</button>
                <input type="datetime-local" name="Datetime" id="input-datetime-local">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <button class="button-primary" onclick="do_action('bulk_update');">ACTUALIZAR PRODUCTOS</button>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <button class="button-primary" onclick="do_action('bulk_update_from');">ACTUALIZAR PRODUCTOS DESDE</button>
                <input type="datetime-local" name="Datetime2" id="input-datetime-local-update">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <button class="button-primary" onclick="do_action('sync_images');">SINCRONIZAR IMAGENES</button>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="spinner-custom d-none"></div>
            </div>
        </div>
    </div>
</div>

<script>
    var updatedBatches = 1;
    var totalRequests = 0;
    var completedRequests = 0;

    function do_action(option) {
        const products = $("#page").data("product");
        const batchSize = 40;
        const productBatches = chunkArray(products, batchSize);
        const loadingIcon = $(".spinner-custom");
        loadingIcon.removeClass("d-none");

        const processBatch = async (batch, endpoint) => {
            await Promise.all(
                batch.map(async (element) => {
                    await ajax_product(element, endpoint);
                })
            );
        };

        switch (option) {
            case "bulk_create":
                console.log("CREANDO PRODUCTOS");
                const syncBulkCreate = chunkArray(products, batchSize);
                processBatch(syncBulkCreate, "mi_endpoint_bulk_create_product");
                break;

            case "bulk_create_from":
                const datetimeInput = document.getElementById('input-datetime-local');
                const datetimeValue = datetimeInput.value;

                console.log('Valor del elemento datetime:', datetimeValue);
                if (datetimeValue) {
                    ajax_product(datetimeValue, "mi_endpoint_bulk_create_from_product");
                } else {
                    $(".spinner-custom").addClass("d-none");
                }
                break;

            case "bulk_update":
                console.log("ACTUALIZANDO PRODUCTOS");
                const syncUpdateBatches = chunkArray(products, batchSize);
                processBatch(syncUpdateBatches, "mi_endpoint_bulk_update_product");
                break;

            case "bulk_update_from":
                const datetimeInputUpdate = document.getElementById('input-datetime-local-update');
                const datetimeValueUpdate = datetimeInputUpdate.value;

                console.log('Valor del elemento datetime:', datetimeValueUpdate);
                if (datetimeValueUpdate) {
                    ajax_product(datetimeValueUpdate, "mi_endpoint_bulk_create_from_product");
                } else {
                    $(".spinner-custom").addClass("d-none");
                }
                break;

            case "sync_images":
                console.log("SINCRONIZANDO IMÂGENES");
                const syncImageBatches = chunkArray(products, batchSize);
                processBatch(syncImageBatches, "mi_endpoint_sync_images_product");
                break;
        }

    }

    function chunkArray(array, batchSize) {
        if (!Array.isArray(array) || typeof batchSize !== 'number' || batchSize <= 0) {
            return [];
        }

        const result = [];
        for (let startIndex = 0; startIndex < array.length; startIndex += batchSize) {
            const endIndex = startIndex + batchSize;
            const batch = array.slice(startIndex, endIndex);
            result.push(batch);
        }
        return result;
    }


    function ajax_product(element, endpoint) {
        const startTime = performance.now();
        totalRequests++;

        new Promise(function(resolve, reject) {
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: endpoint,
                    product: element
                },
                success: function(response) {
                    resolve(response);
                    console.log('Respuesta del endpoint AJAX:', response);
                },
            });
        }).then(function(response) {
            let endTime = performance.now();
            let durationInSeconds = (endTime - startTime) / 1000;

            console.log('Tiempo transcurrido hasta la creación del lote N1', updatedBatches, "es: ", durationInSeconds.toFixed(2));
            updatedBatches++;
            completedRequests++;

            if (completedRequests === totalRequests) {
                $(".spinner-custom").addClass("d-none");
            }
        }).catch(function(error) {
            console.error('Error en la promesa:', error);
        });
    }
</script>

<style>
    .spinner-custom {
        width: 56px;
        height: 56px;
        display: grid;
        border: 4.5px solid #0000;
        border-radius: 50%;
        border-color: #dbdcef #0000;
        animation: spinner-custom-e04l1k 1s infinite linear;
        position: relative;
        bottom: 104px;
        left: 255px;
    }

    .spinner-custom::before,
    .spinner-custom::after {
        content: "";
        grid-area: 1/1;
        margin: 2.2px;
        border: inherit;
        border-radius: 50%;
    }

    .spinner-custom::before {
        border-color: #332884 #0000;
        animation: inherit;
        animation-duration: 0.5s;
        animation-direction: reverse;
    }

    .spinner-custom::after {
        margin: 8.9px;
    }

    @keyframes spinner-custom-e04l1k {
        100% {
            transform: rotate(1turn);
        }
    }

    .buttons-column {
        align-items: center;
        display: flex;
    }


    .wp-core-ui .button-primary {
        background: #332884;
        border-color: #332884;
        color: #fff;
        width: 220px;
        text-decoration: none;
        text-shadow: none;
        min-width: 200px;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        position: relative;
        overflow: hidden;
        outline: 2px solid #332884;
    }

    .wp-core-ui .button-primary:hover {
        transform: scale(1.05);
        outline: 2px solid #661b77;
        background-color: #661b77;
        box-shadow: 4px 5px 17px -4px #268391;
    }

    .wp-core-ui .button-primary:focus {
        transform: scale(1.05);
        outline: 2px solid #661b77;
        background-color: #661b77;
        box-shadow: 4px 5px 17px -4px #268391;
    }

    .wrap {
        margin: 30px 0px;
    }

    #input-datetime-local, #input-datetime-local-update  {
        margin-left: 30px;
    }
</style>