<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Services\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Display checkout page
     */
    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.variant.product.media']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        $addresses = Auth::check() ? Auth::user()->addresses : collect();

        return view('checkout.index', compact('cart', 'addresses'));
    }

    /**
     * Process checkout
     */
    public function process(Request $request): JsonResponse
    {
        $validationRules = [
            'saved_address' => 'nullable|exists:addresses,id',
            'shipping_address' => 'required_without:saved_address|array',
            'shipping_address.name' => 'required_without:saved_address|string|max:255',
            'shipping_address.phone' => 'required_without:saved_address|string|max:20',
            'shipping_address.address_line1' => 'required_without:saved_address|string|max:255',
            'shipping_address.city' => 'required_without:saved_address|string|max:100',
            'shipping_address.district' => 'required_without:saved_address|string|max:100',
            'shipping_address.ward' => 'required_without:saved_address|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ];
        
        // Add guest validation rules
        if (!Auth::check()) {
            $validationRules['guest_email'] = 'required|email|max:255';
            $validationRules['guest_phone'] = 'required|string|max:20';
            $validationRules['payment_method'] = 'required|in:vnpay,momo'; // Only prepaid for guests
        } else {
            $validationRules['payment_method'] = 'required|in:cod,bank_transfer,vnpay,momo';
        }
        
        $request->validate($validationRules);

        $cart = $this->getOrCreateCart();
        
        if ($cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống!'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // 1. Create or get shipping address
            if ($request->has('saved_address') && $request->saved_address) {
                // Use saved address
                $shippingAddress = Address::findOrFail($request->saved_address);
            } else {
                // Create new address
                $shippingAddress = $this->createOrGetAddress($request->shipping_address);
            }

            // 2. Create order
            $order = $this->createOrder($cart, $shippingAddress, $request);

            // 3. Handle payment method
            if (in_array($request->payment_method, ['vnpay', 'momo'])) {
                // For prepaid methods, create order items first
                $this->createOrderItems($order, $cart);
                
                // Update order status to pending payment
                $order->update(['status' => 'pending_payment']);
                
                DB::commit();
                
                // Handle VNPay
                if ($request->payment_method === 'vnpay') {
                    $vnpayService = new VnpayService();
                    
                    // For testing, use mock payment
                    $paymentResult = $vnpayService->mockPayment($order);
                    
                    if ($paymentResult['success']) {
                        $order->update([
                            'status' => 'confirmed',
                            'payment_status' => 'paid'
                        ]);
                        
                        // Clear cart
                        $cart->items()->delete();
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Thanh toán thành công!',
                            'order_number' => $order->order_number,
                            'redirect_url' => route('checkout.success', $order)
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => $paymentResult['message']
                        ], 400);
                    }
                }
                
                // Handle MoMo (mock for now)
                if ($request->payment_method === 'momo') {
                    $order->update([
                        'status' => 'confirmed',
                        'payment_status' => 'paid'
                    ]);
                    
                    // Clear cart
                    $cart->items()->delete();
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Thanh toán MoMo thành công!',
                        'order_number' => $order->order_number,
                        'redirect_url' => route('checkout.success', $order)
                    ]);
                }
                
                // Fallback for other prepaid methods
                return response()->json([
                    'success' => false,
                    'message' => 'Phương thức thanh toán không được hỗ trợ.'
                ], 400);
            } else {
                // For COD, create order items and clear cart
                $this->createOrderItems($order, $cart);
                $cart->items()->delete();
                
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Đặt hàng thành công!',
                    'order_number' => $order->order_number,
                    'redirect_url' => route('checkout.success', $order)
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Checkout failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Show checkout success page
     */
    public function success(Order $order): View
    {
        // Verify order belongs to current user
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.variant.product', 'shippingAddress']);

        return view('checkout.success', compact('order'));
    }

    /**
     * Get or create cart for current user/session
     */
    private function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            
            if (!$cart) {
                $cart = Cart::create(['user_id' => Auth::id()]);
            }
        } else {
            $cart = Cart::where('session_id', session()->getId())->first();
            
            if (!$cart) {
                $cart = Cart::create(['session_id' => session()->getId()]);
            }
        }
        
        return $cart->load('items.variant.product');
    }

    /**
     * Create or get address
     */
    private function createOrGetAddress(array $addressData): Address
    {
        if (Auth::check()) {
            // For logged in users, save address to database
            $address = Address::create([
                'user_id' => Auth::id(),
                'name' => $addressData['name'],
                'phone' => $addressData['phone'],
                'address_line1' => $addressData['address_line1'],
                'address_line2' => $addressData['address_line2'] ?? null,
                'city' => $addressData['city'],
                'district' => $addressData['district'],
                'ward' => $addressData['ward'],
                'postal_code' => $addressData['postal_code'] ?? null,
                'is_default' => false,
                'type' => 'other',
            ]);
        } else {
            // For guests, create temporary address (not saved to DB)
            $address = new Address([
                'user_id' => null,
                'name' => $addressData['name'],
                'phone' => $addressData['phone'],
                'address_line1' => $addressData['address_line1'],
                'address_line2' => $addressData['address_line2'] ?? null,
                'city' => $addressData['city'],
                'district' => $addressData['district'],
                'ward' => $addressData['ward'],
                'postal_code' => $addressData['postal_code'] ?? null,
                'is_default' => false,
                'type' => 'other',
            ]);
            $address->save(); // Save to get real ID
        }

        return $address;
    }

    /**
     * Create order
     */
    private function createOrder(Cart $cart, Address $shippingAddress, Request $request): Order
    {
        $orderNumber = $this->generateOrderNumber();
        
        $orderData = [
            'user_id' => Auth::check() ? Auth::id() : null,
            'order_number' => $orderNumber,
            'status' => 'pending',
            'subtotal' => $cart->total,
            'discount' => 0, // TODO: Implement coupon system
            'shipping_fee' => 0, // Free shipping for now
            'tax' => 0,
            'total' => $cart->total,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'shipping_address_id' => $shippingAddress->id,
            'notes' => $request->notes,
        ];
        
        // Add guest information if not authenticated
        if (!Auth::check()) {
            $orderData['guest_email'] = $request->guest_email;
            $orderData['guest_phone'] = $request->guest_phone;
        }

        return Order::create($orderData);
    }

    /**
     * Create order items and update stock
     */
    private function createOrderItems(Order $order, Cart $cart): void
    {
        foreach ($cart->items as $cartItem) {
            $variant = $cartItem->variant;
            
            // Check stock
            if ($variant->stock < $cartItem->quantity) {
                throw new \Exception("Không đủ hàng cho sản phẩm: {$variant->product->name}");
            }

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_variant_id' => $variant->id,
                'product_name' => $variant->product->name,
                'variant_details' => [
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'sku' => $variant->sku,
                ],
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->price,
                'subtotal' => $cartItem->subtotal,
            ]);

            // Update stock
            $variant->decrement('stock', $cartItem->quantity);
            $variant->product->increment('sales_count', $cartItem->quantity);
        }
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}