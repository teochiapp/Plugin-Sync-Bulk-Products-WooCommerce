<?php
class Products
{
    public function get_products()
    {
        $apiUrl = 'http://181.117.206.146:4000/api/Productos';

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



    static function bulk_create_from($datetime)
    {

        $apiUrl = 'http://181.117.206.146:4000/api/Productos';

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


    static function bulk_update_from($datetime)
    {
        $apiUrl = 'http://181.117.206.146:4000/api/Productos';

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

            // Call the bulk_update_existing() function with the decoded response data.
            $responseData = json_decode($response, true);
            $object = new Products();
            $object->bulk_update($responseData);

            if ($responseData) {
                return $responseData;
            } else {
                return "No hay productos nuevos desde esa fecha";
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
            $target_sku = $product_data["codMostrador"];

            if (!empty(!$product_sku) && $product_sku != $target_sku) {
                $new_product = new WC_Product();
                $object = new Products();
                $categories = $object->createUpdateCategory($product_data);
                $new_product->set_name($product_data["descripcion"]);
                $new_product->set_description($product_data["descripcionLarga"]);
                $new_product->set_sku($product_data["codMostrador"]);
                $new_product->set_status($product_data["publicaWeb"] == "true" ? "publish" : "draft");
                $new_product->set_price($product_data["precio"]);
                $new_product->set_regular_price($product_data["precio"]);
                $new_product->set_manage_stock(true);

                if ($product_data["stockWeb"] === "Con Stock") {
                    $new_product->set_stock_status('instock');
                    $new_product->set_stock_quantity(1000);
                } else {
                    $new_product->set_stock_status('outstock');
                    $new_product->set_stock_quantity(0);
                }

                $new_product->set_length($product_data["largo"]);
                $new_product->set_width($product_data["ancho"]);
                $new_product->set_height($product_data["alto"]);
                $new_product->set_weight($product_data["peso"]);

                $new_product->set_category_ids(array_map('intval', $categories));

                $new_product_id = $new_product->save();

                $object->createUpdateattributes($product_data, $new_product_id);

                $products_created[] = "Producto " . $product_data["codMostrador"] . " creado";
            } else {
                $products_created[] = "Producto " . $product_data["codMostrador"] . " no se creó";
            }
        }

        return $products_created;
    }


    static function bulk_update($product_array)
    {
        $products_updated = [];

        foreach ($product_array as $product_data) {
            //Checks if the product is in WooCommerce, then updates it if it is
            $product_sku = wc_get_products(["sku" => $product_data["codMostrador"]]);

            $object = new Products();
            $categories = $object->createUpdateCategory($product_data);

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
                            $product->set_stock_quantity(1000);
                        } else {
                            $product->set_stock_status('outofstock');
                            $product->set_stock_quantity(0);
                        }

                        $product->set_category_ids(array_map('intval', $categories));

                        $product_id = $product->save();

                        $object = new Products();
                        $object->createUpdateattributes($product_data, $product_id);




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

    static function sync_images($product_array)
    {
        $products_updated = [];

        foreach ($product_array as $product_data) {
            //Checks if the product is in WooCommerce, then creates it if its not
            $product_sku = wc_get_products(["sku" => $product_data["codMostrador"]]);

            if (!empty($product_sku)) {
                $target_sku = $product_data["codMostrador"];
                foreach ($product_sku as $product) {
                    if ($product->get_sku() === $target_sku) {
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
                }
            } else {
                $products_updated[] = "Producto " . $product_data["descripcion"] . " falla al actualizar";
            }
        }

        return $products_updated;
    }

    public function createUpdateCategory($product_data)
    {

        $rubros = mb_strtoupper(trim(str_replace(["<", ">"], "", $product_data['rubroWeb'])));
        $tipo = mb_strtoupper(trim(str_replace(["<", ">"], "", $product_data['tipo'])));
        $linea = mb_strtoupper(trim(str_replace(["<", ">"], "", $product_data['linea'])));
        $categories = explode(",", $rubros);
        $categories_to_insert = array();
        foreach ($categories as $category) {
            if (empty($category) || is_null($category)) continue;
            $rubro_individual = trim($category);
            $string_category = '';
            if (!empty($rubro_individual)) $string_category .= $rubro_individual;
            if (!empty($rubro_individual) && !empty($tipo)) $string_category .= " |*| " . $tipo;
            if (!empty($rubro_individual) && !empty($tipo)  && !empty($linea)) $string_category .= " |*| " . $linea;
            $string_category = mb_strtoupper(trim($string_category));
            if (!empty($string_category)) {
                $categories_to_insert[] = $string_category;
            }
        }

        return Products::assignHierarchicalCategoriesToProduct($categories_to_insert);
    }



    // Define una función para asignar categorías jerárquicas con camino completo
    static function assignHierarchicalCategoriesToProduct($category_paths)
    {
        $categories_ids = [];
        foreach ($category_paths as $category_path) {
            $categories = explode(" |*| ", $category_path);
            $parent_id = 0;
            foreach ($categories as $category_name) {
                $category_name = trim($category_name);
                $slug = $parent_id . "-" . urlencode(mb_strtolower(str_replace(" ", "-", $category_name)));
                // Comprueba si la categoría ya existe bajo el padre especificado
                $term = term_exists($category_name, 'product_cat', $parent_id);
                // Si no existe, créala como subcategoría bajo el padre
                if (!$term) {
                    $term = wp_insert_term($category_name, 'product_cat', array('parent' => $parent_id, 'slug' => $slug));
                    $parent_id = $term['term_id']; // Actualiza el parent_id para el siguiente nivel
                } else {
                    $parent_id = $term['term_id']; // Actualiza el parent_id para el siguiente nivel
                }
                $categories_ids[] = $term['term_id'];
            }
        }

        $categories_ids = array_map('intval', $categories_ids);
        return $categories_ids;
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
