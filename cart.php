<?php
require_once 'cart_functions.php';
session_start();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);
            $customizationOptions = $_POST['customization_options'] ?? null;

            if ($productId <= 0) {
                $response['message'] = 'Invalid product ID.';
            } else {
                $result = addToCart($productId, $quantity, $customizationOptions);
                if ($result === true) {
                    $response['success'] = true;
                    $response['message'] = 'Product added to cart successfully.';
                    $response['cart_count'] = getCartItemCount();
                } else {
                    $response['message'] = $result;
                }
            }
            break;

        case 'update':
            $cartItemId = (int)($_POST['cart_item_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 0);

            if ($cartItemId <= 0) {
                $response['message'] = 'Invalid cart item ID.';
            } else {
                $result = updateCartItem($cartItemId, $quantity);
                if ($result === true) {
                    $response['success'] = true;
                    $response['message'] = 'Cart updated successfully.';
                    $response['cart_count'] = getCartItemCount();
                    $response['cart_total'] = number_format(getCartTotal(), 2);
                } else {
                    $response['message'] = $result;
                }
            }
            break;

        case 'remove':
            $cartItemId = (int)($_POST['cart_item_id'] ?? 0);

            if ($cartItemId <= 0) {
                $response['message'] = 'Invalid cart item ID.';
            } else {
                removeFromCart($cartItemId);
                $response['success'] = true;
                $response['message'] = 'Item removed from cart.';
                $response['cart_count'] = getCartItemCount();
                $response['cart_total'] = number_format(getCartTotal(), 2);
            }
            break;

        case 'clear':
            clearCart();
            $response['success'] = true;
            $response['message'] = 'Cart cleared.';
            $response['cart_count'] = 0;
            $response['cart_total'] = '0.00';
            break;

        default:
            $response['message'] = 'Invalid action.';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'count':
            $response['success'] = true;
            $response['cart_count'] = getCartItemCount();
            break;

        case 'total':
            $response['success'] = true;
            $response['cart_total'] = number_format(getCartTotal(), 2);
            break;

        default:
            $response['message'] = 'Invalid action.';
    }
}

echo json_encode($response);
?>