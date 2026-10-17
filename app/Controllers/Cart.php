<?php
namespace App\Controllers;

class Cart extends BaseController
{
    public function addToCart($carrito, $id, $nombre, $precio)
    {
        $cart = session()->get($carrito) ?? [];

        $product = [
            'id' => $id,
            'name' => $nombre,
            'price' => $precio,
            'quantity' => 1
        ];

        // Verifica si el producto ya está en el carrito y actualiza la cantidad si es necesario
        $found = false;
        foreach ($cart as &$item) {
            if ($item['id'] === $id) {
                $item['quantity']++;
                $found = true;
                break;
            }
        }

        // Si no se encontró el producto en el carrito, agrégalo
        if (!$found) {
            $cart[] = $product;
        }

        session()->set($carrito, $cart);

        // Prepara una respuesta JSON
        $response = [
            'status' => 'success',
            'message' => 'Producto agregado.'
        ];

        return $response;
    }

    public function removeFromCart($carrito, $id)
    {
        $cart = session()->get($carrito);

        // Buscar el índice del producto en el carrito
        $productIndex = null;
        foreach ($cart as $index => $product) {
            if ($product['id'] === $id) {
                $productIndex = $index;
                break;
            }
        }

        if ($productIndex !== null) {
            // Eliminar el producto del carrito
            unset($cart[$productIndex]);
            session()->set($carrito, array_values($cart)); // Reindexar el array
        }

        // Prepara una respuesta JSON
        $response = [
            'status' => 'success',
            'message' => 'Producto eliminado.'
        ];

        return $response;
    }

    public function clearCart($carrito)
    {
        session()->remove($carrito);

        // Prepara una respuesta JSON
        $response = [
            'status' => 'success',
            'message' => 'Carrito limpiado'
        ];

        return $response;
    }

    public function getTotalPrice($carrito)
    {
        $cart = session()->get($carrito);
        $totalPrice = 0;

        if (!empty($cart)) {
            foreach ($cart as $product) {
                $totalPrice += $product['price'] * $product['quantity'];
            }
        }

        return $totalPrice;
    }

    public function updateCantidad($carrito, $id, $cantidad)
    {
        $cart = session()->get($carrito);

        foreach ($cart as &$product) {
            if ($product['id'] === $id) {
                $product['quantity'] = $cantidad;
                break;
            }
        }

        session()->set($carrito, $cart);

        $response = [
            'status' => 'success',
            'message' => 'Cantidad actualizado.'
        ];

        return $response;
    }

    public function updatePrice($carrito, $id, $new_price)
    {
        $cart = session()->get($carrito);

        foreach ($cart as &$product) {
            if ($product['id'] === $id) {
                $product['price'] = $new_price;
                break;
            }
        }

        session()->set($carrito, $cart);

        $response = [
            'status' => 'success',
            'message' => 'Precio actualizado.'
        ];

        return $response;
    }
}