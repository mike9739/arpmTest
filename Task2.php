<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        $orderData = [];

        foreach ($orders as $order) {
            $customer = $order->customer;
            $items = $order->items;
            $totalAmount = 0;
            $itemsCount = 0;

            foreach ($items as $item) {
                //we can remove this variable since is not used anywhere
                $product = $item->product;
                $totalAmount += $item->price * $item->quantity;
                $itemsCount++;
            }
            //too many queries here, we can reduce the number of queries by using eager loading

            $lastAddedToCart = CartItem::where('order_id', $order->id)
                ->orderByDesc('created_at')
                ->first()
                ->created_at ?? null; //we can use the optional() helper function to avoid the null check here https://laravel.com/docs/12.x/helpers#method-optional

                //this query is not needed, we can use the $order variable to check if the order is completed
            $completedOrderExists = Order::where('id', $order->id)
                ->where('status', 'completed')
                ->exists();

            $orderData[] = [
                'order_id' => $order->id,
                'customer_name' => $customer->name,
                'total_amount' => $totalAmount,
                'items_count' => $itemsCount,
                'last_added_to_cart' => $lastAddedToCart,
                'completed_order_exists' => $completedOrderExists,
                'created_at' => $order->created_at,
            ];
        }

        //this method is not efficient, it can be refactored using the data that we already have in the $orderData array
        usort($orderData, function($a, $b) {

            $aCompletedAt = Order::where('id', $a['order_id'])
                ->where('status', 'completed')
                ->orderByDesc('completed_at')
                ->first()
                ->completed_at ?? null;

            $bCompletedAt = Order::where('id', $b['order_id'])
                ->where('status', 'completed')
                ->orderByDesc('completed_at')
                ->first()
                ->completed_at ?? null;

            return strtotime($bCompletedAt) - strtotime($aCompletedAt);
        });

        return view('orders.index', ['orders' => $orderData]);
    }

    public function optimizedIndex(){
        // The first query is ok , but we can reduce the number of queries by using eager loading
        // see more at https://laravel.com/docs/12.x/eloquent-relationships#eager-loading-multiple-relationships
        $orders = Order::with('customer','items.product','cartItems');
        //then we will map all then orders to format the data as we want

        foreach($orders as $order) {
            $totalAmount = $order->items->sum(function($item){
                return $item->price * $item->quantity;
            });

            $itemsCount = $order->items->count();
            $lastAddedToCart = optional($order->cartItems->sortByDesc('created_at')->first())->created_at;
            $completedOrderExists = $order->status === 'completed';
            $orderData[] = [
                'order_id' => $order->id,
                'customer_name' => $order->customer->name,
                'total_amount' => $totalAmount,
                'items_count' => $itemsCount,
                'last_added_to_cart' => $lastAddedToCart,
                'completed_order_exists' => $completedOrderExists,
                'created_at' => $order->created_at,
            ];
        }

        usort($orderData, function ($a, $b) {
            return strtotime($b['completed_order_exists'] ?: 0) - strtotime($a['completed_order_exists'] ?: 0);
        });

        return view('orders.index', ['orders' => $orderData]);

    }

}
