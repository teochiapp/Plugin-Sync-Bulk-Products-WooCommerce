<?php

class Clients
{
    public function add_orders($order)
    {
        $apiUrl = 'http://181.117.206.146:4000/api/Pedidos';

        $state_mapping = array(
            'S' => 'Santa Fe',
            'X' => 'Córdoba',
            'C' => 'Ciudad autónoma de Buenos Aires',
            'K' => 'Catamarca',
            'H' => 'Chaco',
            'U' => 'Chubut'
        );

        //? Order info
        $billing_full_name = $order->get_formatted_billing_full_name();
        $billing_email = $order->get_billing_email();
        $order->get_order_key();
        $id_order = $order->get_id();
        $billing_city = $order->get_billing_city();
        $billing_state = $order->get_billing_state();
        $billing_phone = $order->get_billing_phone();
        $billing_address = $order->get_billing_address_1() . $order->get_billing_address_2();
        $total_discount = $order->get_total_discount();
        $shipping_cost = $order->get_shipping_total();

        if (isset($state_mapping[$billing_state])) {
            $billing_state_full = $state_mapping[$billing_state];
        } else {
            $billing_state_full = $billing_state;
        }

        $timezone = new DateTimeZone('America/Argentina/Buenos_Aires');
        $current_datetime = new DateTime('now', $timezone);
        $formatted_datetime = $current_datetime->format('Y-m-d\TH:i:s.vP');

        //? Get and Loop Over Order Items
        $products = array();

        foreach ($order->get_items() as $item_id => $item) {
            $product_id = $item->get_id();
            $product = $item->get_product();
            $quantity = $item->get_quantity();
            $unit_price = $product->get_price();

            $products[] = array(
                "idProducto" => $product_id,
                "cantidad" => $quantity,
                "precioVenta" => $unit_price,
                "porcDescuento" => 0
            );
        }
        
         //? Add the shipping costs to the array as an element
         $shipping[] = array(
            "idProducto" => 1513,
            "cantidad" => 1,
            "precioVenta" => $shipping_cost,
            "porcDescuento" =>  0
        );
        
        $all_items = array_merge($products, $shipping);
        $all_items_json = json_encode($all_items);

        $jsonData = '{
            "id": ' . $id_order . ',
            "clienteTemporal": {
                "id": "0",
                "nombre": "' . $billing_full_name . '",
                "email": "' . $billing_email . '",
                "localidad": "' . $billing_city . '",
                "provincia": "' . $billing_state_full . '",
                "domicilio": "' . $billing_address . '",
                "telefono": "' . $billing_phone . '"
            },
            "fechaHora": "' . $formatted_datetime . '",
            "items": ' . $all_items_json . ',
            "observacion": "Detalle de la forma de pago",
            "idPagoMP": "id_de_pago_(solo_si_fue_por_MP)"
        }';


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
                $products_str = json_encode($all_items, JSON_PRETTY_PRINT);
                return $responseData  .
                    " Id de Orden: " . $id_order  .
                    " Descuento: " . $total_discount .
                    " Nombre del usuario: " . $billing_full_name .
                    " Nombre del usuario: " . $billing_email .
                    " Ciudad: " . $billing_city .
                    " Direccion: " . $billing_address .
                    " Provincia: " . $billing_state_full .
                    " Telefono: " . $billing_phone .
                    " Fecha de Operacion : " . $formatted_datetime .
                    " Costo de Envio: " . $shipping_cost .
                    var_dump($products_str);
            }
        }

        curl_close($ch);
    }
}
