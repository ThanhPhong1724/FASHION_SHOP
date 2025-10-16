<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class VnpayService
{
    private $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    private $vnp_Returnurl = "http://127.0.0.1:8000/checkout/vnpay/return";
    private $vnp_TmnCode = "2QXUI4J4"; // Test merchant code
    private $vnp_HashSecret = "RAOEXHYVSDDIIENYWSLDIIZALQTFZRFA"; // Test secret key

    /**
     * Create VNPay payment URL
     */
    public function createPaymentUrl(Order $order): string
    {
        $vnp_TxnRef = $order->order_number;
        $vnp_OrderInfo = "Thanh toan don hang " . $order->order_number;
        $vnp_OrderType = "other";
        $vnp_Amount = $order->total * 100; // VNPay requires amount in cents
        $vnp_Locale = "vn";
        $vnp_IpAddr = request()->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $this->vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $this->vnp_Url . "?" . $query;
        if (isset($this->vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }

    /**
     * Verify VNPay response
     */
    public function verifyResponse(array $data): array
    {
        $vnp_SecureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash']);

        ksort($data);
        $hashdata = "";
        foreach ($data as $key => $value) {
            if (strlen($hashdata) > 0) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            return [
                'success' => true,
                'order_number' => $data['vnp_TxnRef'] ?? '',
                'amount' => ($data['vnp_Amount'] ?? 0) / 100,
                'response_code' => $data['vnp_ResponseCode'] ?? '',
                'transaction_id' => $data['vnp_TransactionNo'] ?? '',
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid signature'
        ];
    }

    /**
     * Mock VNPay payment for testing
     */
    public function mockPayment(Order $order): array
    {
        // Simulate payment processing
        Log::info('Mock VNPay payment for order: ' . $order->order_number);
        
        // Simulate success (90% success rate for testing)
        $success = rand(1, 10) <= 9;
        
        if ($success) {
            return [
                'success' => true,
                'order_number' => $order->order_number,
                'amount' => $order->total,
                'response_code' => '00',
                'transaction_id' => 'MOCK' . time(),
                'message' => 'Thanh toán thành công'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Thanh toán thất bại (Mock test)'
            ];
        }
    }
}