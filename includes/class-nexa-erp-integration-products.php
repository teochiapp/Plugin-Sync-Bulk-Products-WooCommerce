<?php
class Products
{
    public function get_products()
    {
        $apiUrl = 'http://oficina.nexa.com.ar:4019/api/Productos';

        $jsonData = json_encode(array());

        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error al realizar la solicitud: ' . curl_error($ch);
        } else {
            $responseData = json_decode($response, true);
            if ($responseData === null) {
                echo 'Error al decodificar la respuesta JSON.';
            } else {
                //Handling response
                return $responseData;
            }
        }

        curl_close($ch);
    }

    static function bulk_create($product_array)
    {
        $products_created = [];

        foreach ($product_array as $product_data) {
            //Checks if the product is in WooCommerce, then creates it if its not
            $product_sku = wc_get_products(["sku" => $product_data["codMostrador"]]);
            $category_id = get_term_by('name', $product_data["tipo"], 'product_cat')->term_id;
            $target_sku = $product_data["codMostrador"];

            if ( !empty(!$product_sku) && $product_sku != $target_sku) {
                $new_product = new WC_Product();
                $new_product->set_name($product_data["descripcion"]);
                $new_product->set_description($product_data["descripcionLarga"]);
                $new_product->set_sku($product_data["codMostrador"]);
                $new_product->set_status($product_data["publicaWeb"] == "true" ? "publish" : "draft");
                $new_product->set_price($product_data["precio"]);
                $new_product->set_regular_price($product_data["precio"]);
                $new_product->set_manage_stock(true);

                if ($product_data["stockWeb"] === "Con Stock") {
                    $new_product->set_stock_status('instock');
                    $new_product->set_stock_quantity(10);
                } else {
                    $new_product->set_stock_status('outstock');
                    $new_product->set_stock_quantity(0);
                }

                $new_product->set_length($product_data["largo"]);
                $new_product->set_width($product_data["ancho"]);
                $new_product->set_height($product_data["alto"]);
                $new_product->set_weight($product_data["peso"]);

                $new_product_id = $new_product->save();

                $object = new Products();
                $object->createUpdateattributes($product_data, $new_product_id);
                $object->createUpdateCategory($new_product_id, $product_data, $category_id);

                $products_created[] = "Producto " . $product_data["codMostrador"] . " creado";
            } else {
                $products_created[] = "Producto " . $product_data["codMostrador"] . " no se creó";
            }
        }

        return $products_created;
    }

    static function bulk_create_from($datetime)
    {

        $apiUrl = 'http://oficina.nexa.com.ar:4019/api/Productos';

        $apiUrl .= '?posterioresA=' . urlencode($datetime);

        $jsonData = json_encode(array());

        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error al realizar la solicitud: ' . curl_error($ch);
        } else {
            $responseData = json_decode($response, true);
            $object = new Products();
            $object->bulk_create($responseData);

            if ($responseData) {
                return $responseData;
            } else {
                return "No hay productos nuevos desde esa fecha";
            }
        }

        curl_close($ch);
    }

     static function bulk_update($product_array)
    {
        $products_updated = [];

        foreach ($product_array as $product_data) {
            //Checks if the product is in WooCommerce, then updates it if it is
            $product_sku = wc_get_products(["sku" => $product_data["codMostrador"]]);
            $category_id = get_term_by('name', $product_data["tipo"], 'product_cat')->term_id;

            if (!empty($product_sku)) {
                $target_sku = $product_data["codMostrador"];
                foreach ($product_sku as $product) {
                    if ($product->get_sku() === $target_sku) {
                        $product->set_status($product_data["publicaWeb"] == "true" ? "publish" : "draft");
                        $product->set_price($product_data["precio"]);
                        $product->set_name($product_data["descripcion"]);
                        $product->set_description($product_data["descripcionLarga"]);
                        $product->set_regular_price($product_data["precio"]);
                        $product->set_length($product_data["largo"]);
                        $product->set_width($product_data["ancho"]);
                        $product->set_height($product_data["alto"]);
                        $product->set_weight($product_data["peso"]);

                        if ($product_data["stockWeb"] === "Con Stock") {
                            $product->set_stock_status('instock');
                            $product->set_stock_quantity(10);
                        } else {
                            $product->set_stock_status('outofstock');
                            $product->set_stock_quantity(0);
                        }

                        $product_id = $product->save();

                        $object = new Products();
                        $object->createUpdateattributes($product_data, $product_id);
                        $object->createUpdateCategory($product_id, $product_data, $category_id);

                        $products_updated[] = "Producto " . $product_data["descripcion"] . " actualizado con el precio de: " . $product_data["urlFoto"];
                        break;
                    }
                }
            } else {
                $products_updated[] = "Producto " . $product_data["descripcion"] . " falló al actualizar";
            }
        }

        return $products_updated;
    }

    static function bulk_update_from($datetime)
    {

        $apiUrl = 'http://oficina.nexa.com.ar:4019/api/Productos';

        $apiUrl .= '?posterioresA=' . urlencode($datetime);

        $jsonData = json_encode(array());

        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error al realizar la solicitud: ' . curl_error($ch);
        } else {
            $responseData = json_decode($response, true);
            foreach ($responseData as $product_data) {
                //Checks if the product is in WooCommerce, then updates it if it is
                $product_sku = wc_get_products(["sku" => $product_data["codMostrador"]]);
                $category_id = get_term_by('name', $product_data["tipo"], 'product_cat')->term_id;
    
       
                    $target_sku = $product_data["codMostrador"];
                    foreach ($product_sku as $product) {
                        if ($product->get_sku() === $target_sku) {
                            $product->set_status($product_data["publicaWeb"] == "true" ? "publish" : "draft");
                            $product->set_price($product_data["precio"]);
                            $product->set_name($product_data["descripcion"]);
                            $product->set_description($product_data["descripcionLarga"]);
                            $product->set_regular_price($product_data["precio"]);
                            $product->set_length($product_data["largo"]);
                            $product->set_width($product_data["ancho"]);
                            $product->set_height($product_data["alto"]);
                            $product->set_weight($product_data["peso"]);
    
                            if ($product_data["stockWeb"] === "Con Stock") {
                                $product->set_stock_status('instock');
                                $product->set_stock_quantity(10);
                            } else {
                                $product->set_stock_status('outofstock');
                                $product->set_stock_quantity(0);
                            }
    
                            $product_id = $product->save();
    
                            $object = new Products();
                            $object->createUpdateattributes($product_data, $product_id);
                            $object->createUpdateCategory($product_id, $product_data, $category_id);
    
                            $products_updated[] = "Producto " . $product_data["descripcion"] . " actualizado con el precio de: " . $product_data["urlFoto"];
                            break;
                        }
                    }
                
            }

            if ($responseData) {
                return $responseData;
            } else {
                return "No hay productos nuevos desde esa fecha";
            }
        }

        curl_close($ch);
    }

    static function sync_images($product_array)
    {
        $products_updated = [];

        foreach ($product_array as $product_data) {
            //Checks if the product is in WooCommerce, then creates it if its not
            $product_sku = wc_get_products(["sku" => $product_data["codMostrador"]]);

            if ($product_sku) {
                foreach ($product_sku as $product) {
                    $image_data = file_get_contents($product_data["urlFoto"]);

                    if ($image_data !== false) {
                        // Unique archive name for image
                        $filename = md5($product_data["codMostrador"]) . '.jpg';

                        $file_path = wp_upload_dir()["path"] . '/' . $filename;

                        file_put_contents($file_path, $image_data);

                        $attachment_id = wp_insert_attachment(
                            array(
                                'post_title' => $filename,
                                'post_mime_type' => 'image/jpeg',
                                'post_content' => '',
                                'post_status' => 'inherit',
                            ),
                            $file_path
                        );

                        if (!is_wp_error($attachment_id)) {
                            set_post_thumbnail($product->get_id(), $attachment_id);

                            $products_updated[] = "Producto " . $product_data["descripcion"] . " actualizado con la imagen: " . $product_data["urlFoto"];
                        } else {
                            $products_updated[] = "Producto " . $product_data["descripcion"] . " error al asignar la imagen";
                        }
                    } else {
                        $products_updated[] = "Producto " . $product_data["descripcion"] . " no se pudo obtener la imagen desde la URL " . $product_data["urlFoto"];
                    }
                }
            } else {
                $products_updated[] = "Producto " . $product_data["descripcion"] . " falla al actualizar";
            }
        }

        return $products_updated;
    }

    static function createUpdateCategory($product_id, $product_data, $category_id)
    {
        if ($product_id && !is_wp_error($product_id)) {
            wp_set_object_terms($product_id, $category_id, 'product_cat');
        }

        $category_child = get_term_by('name', $product_data["familia"], 'product_cat');

        if (!$category_child) {
            // Creates the child category
            $category_child_args = array(
                'parent' => $category_id,
                'name' => $product_data["familia"],
                'slug' => sanitize_title($product_data["familia"])
            );

            $category_child_id = wp_insert_term($product_data["familia"], 'product_cat', $category_child_args);

            if (!is_wp_error($category_child_id)) {
                $category_child_id = $category_child_id['term_id'];
            }
        } else {
            $category_child_id = $category_child->term_id;
        }

        wp_set_object_terms($product_id, array($category_id, $category_child_id), 'product_cat');

        $category_grandchild = get_term_by('name', $product_data["linea"], 'product_cat');

        if (!$category_grandchild) {
            // Creates the third-level child category
            $category_grandchild_args = array(
                'parent' => $category_child_id,
                'name' => $product_data["linea"],
                'slug' => sanitize_title($product_data["linea"])
            );

            $category_grandchild_id = wp_insert_term($product_data["linea"], 'product_cat', $category_grandchild_args);

            if (!is_wp_error($category_grandchild_id)) {
                $category_grandchild_id = $category_grandchild_id['term_id'];
            }
        } else {
            $category_grandchild_id = $category_grandchild->term_id;
        }

        wp_set_object_terms($product_id, array($category_id, $category_child_id, $category_grandchild_id), 'product_cat');
    }

    static function createUpdateattributes($product_data, $new_product_id)
    {
        $attributes = array(
            array("name" => "Diametro", "options" => array($product_data["diametro"]), "position" => 1, "visible" => 1, "variation" => 1),
            array("name" => "Altura Total", "options" => array($product_data["alturaTotal"]), "position" => 2, "visible" => 1, "variation" => 1),
            array("name" => "Diametro Boca", "options" => array($product_data["diametroBoca"]), "position" => 3, "visible" => 1, "variation" => 1)
        );
        if ($attributes) {
            $productAttributes = array();
            foreach ($attributes as $attribute) {
                $attr = wc_sanitize_taxonomy_name(stripslashes($attribute["name"]));
                $attr = 'pa_' . $attr;
                if ($attribute["options"]) {
                    foreach ($attribute["options"] as $option) {
                        wp_set_object_terms($new_product_id, $option, $attr, true);
                    }
                }
                $productAttributes[sanitize_title($attr)] = array(
                    'name' => sanitize_title($attr),
                    'value' => $attribute["options"],
                    'position' => $attribute["position"],
                    'is_visible' => $attribute["visible"],
                    'is_variation' => $attribute["variation"],
                    'is_taxonomy' => '1'
                );
            }
            update_post_meta($new_product_id, '_product_attributes', $productAttributes);
        }
    }
}
