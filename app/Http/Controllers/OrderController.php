<?php
namespace App\Http\Controllers;

use App\Facades\ResponseFacade;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class OrderController extends Controller
{
    // ... Other methods

    public function index()
    {
        try {

            $orders = Order::orderBy('id', 'desc')->paginate(10);
            return ResponseFacade::success($orders, 'Orders listed successfully');

        } catch (\Exception $exception) {

            Log::error('An exception occurred: ' . $exception->getMessage());
            return ResponseFacade::failure( 'Failed to fetch orders');

        }
    }

    public function show($id)
    {
        try {
            
            $order = Order::findOrFail($id);
            return ResponseFacade::success($order, 'Order fetched successfully');

        } catch (\Exception $exception) {

            Log::error('An exception occurred: ' . $exception->getMessage());
            return ResponseFacade::failure( 'Order not found',404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'customer_id' => 'required|exists:customers,id'
            ]);

            $order = Order::create($validatedData);
            return ResponseFacade::success($order, 'Order created successfully',201);

        } catch (\Exception $exception) {

            Log::error('An exception occurred: ' . $exception->getMessage());
            return ResponseFacade::failure( 'Failed to create order');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            // Check if the order is not paid
            if($order->paid){
                return ResponseFacade::failure( 'Cannot update already paid order');
            }

            $validatedData = $request->validate([
                'customer_id' => 'sometimes|required|exists:customers,id'
            ]);

            $order->update($validatedData);
            return ResponseFacade::success($order, 'Order created successfully');

        } catch (\Exception $exception) {
            Log::error('An exception occurred: ' . $exception->getMessage());
            return ResponseFacade::failure( 'Failed to update order');
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);

            // Check if the order is not paid
            if($order->paid){
                return ResponseFacade::failure( 'Cannot delete already paid order');
            }

            OrderProduct::where('order_id',$id)->delete();
            $order->delete();

            return ResponseFacade::success([], 'Order deleted successfully');
        } catch (\Exception $exception) {

            Log::error('An exception occurred: ' . $exception->getMessage());
            return ResponseFacade::failure( 'Failed to delete order');
        }
    }

    public function addProduct(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            // Check if the order is already paid
            if ($order->paid) {
                return ResponseFacade::failure( 'Cannot add product to a paid order');
            }

            $validatedData = $request->validate([
                'product_id' => 'required|exists:products,id',
            ]);
            if(OrderProduct::where('order_id',$id)->where('product_id',$request->product_id)->exists()){
                return ResponseFacade::failure( 'Product already added to order');
            }
            // Create a new OrderProduct relationship
            $orderProduct = new OrderProduct($validatedData);
            $orderProduct->order_id = $id;
            $orderProduct->product_id = $request->product_id;
            $orderProduct->save();

            return ResponseFacade::success([], 'Product added to order successfully',201);
        } catch (\Exception $exception) {
            Log::error('An exception occurred: ' . $exception->getMessage());
            throw new BadRequestHttpException('Failed to add product to order');
        }
    }
}
