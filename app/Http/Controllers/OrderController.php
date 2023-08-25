<?php
namespace App\Http\Controllers;

use App\Facades\ResponseFacade;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Services\SuperPaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{

    public function index()
    {
        try {

            $orders = Order::with('customer:id,first_name')->orderBy('id', 'desc')->paginate(10);
            return ResponseFacade::success($orders, 'Orders listed successfully');

        } catch (\Exception $exception) {

            Log::error('An exception occurred: ' . $exception->getMessage());
            return ResponseFacade::failure( 'Failed to fetch orders');

        }
    }

    public function show($id)
    {
        try {
            
            $order = Order::with('customer:id,first_name,last_name,email,phone')->findOrFail($id);
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

        } 
        catch (ValidationException $exception) {
            Log::error('An exception occurred: ' . $exception->getMessage());
            $errors = $exception->validator->errors();
            return ResponseFacade::validationfailed( $errors);
        }
        catch (\Exception $exception) {

            Log::error('An exception occurred: ' . $exception->getMessage());
            return ResponseFacade::failure( $exception->getMessage());
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
                'customer_id' => 'required|exists:customers,id'
            ]);

            $order->update($validatedData);
            return ResponseFacade::success($order, 'Order updated successfully');

        } 
        catch (ValidationException $exception) {
            Log::error('An exception occurred: ' . $exception->getMessage());
            $errors = $exception->validator->errors();
            return ResponseFacade::validationfailed( $errors);
        }
        catch (\Exception $exception) {
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
            $product = Product::findOrFail($request->product_id);

            // Create a new OrderProduct relationship
            $orderProduct = new OrderProduct($validatedData);
            $orderProduct->order_id = $id;
            $orderProduct->product_id = $request->product_id;
            $orderProduct->price = $product->price; // this might be useful in future by reducing joins
            $orderProduct->save();

            // order master table
            $order->product_count = $order->product_count + 1;
            $order->total_amount = $product->price;
            $order->update();

            return ResponseFacade::success([], 'Product added to order successfully',201);
        }
        catch (ValidationException $exception) {
            Log::error('An exception occurred: ' . $exception->getMessage());
            $errors = $exception->validator->errors();
            return ResponseFacade::validationfailed( $errors);
        } catch (\Exception $exception) {
            Log::error('An exception occurred: ' . $exception->getMessage());
            throw new BadRequestHttpException('Failed to add product to order');
        }
    }

    public function orderProducts()
    {
        try {

            $orderProducts = OrderProduct::with('product:id,productname')->select('id','product_id','price','created_at')->orderBy('created_at', 'desc')->paginate(10);
            return ResponseFacade::success($orderProducts, 'Order Products listed successfully');

        } catch (\Exception $exception) {

            Log::error('An exception occurred: ' . $exception->getMessage());
            return ResponseFacade::failure( 'Failed to fetch Order Products');

        }
    }
    public function payOrder(Request $request, $id)
    {
        try {
            $order = Order::with('customer')->findOrFail($id);

            // Check if the order is already paid
            if ($order->paid) {
                return ResponseFacade::failure('Order is already paid', 422);
            }
            $email = $order->customer->email;
            $value = $order->total_amount;

            // Create a new PaymentService instance
            $paymentService = new SuperPaymentService();

            // Attempt to make the payment
            $paymentResult = $paymentService->makePayment($id, $email, $value );

            if ($paymentResult) {
                // Update the order status to paid
                $order->paid = true;
                $order->save();

                return ResponseFacade::success(['order' => $order], 'Order payment successful');
            } else {
                return ResponseFacade::failure('Payment failed. Insufficient funds', 422);
            }
        } catch (ModelNotFoundException $exception) {
            Log::error('An exception occurred: ' . $exception->getMessage());
            return ResponseFacade::notFound('Order not found');
        } catch (\Exception $exception) {
            Log::error('An exception occurred: ' . $exception->getMessage());
            return ResponseFacade::failure('Failed to process payment', 500);
        }
    }
}
