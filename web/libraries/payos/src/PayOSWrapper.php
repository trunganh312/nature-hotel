<?php
namespace Lib\PayOS;

use PayOS\PayOS;
use Exception;

class PayOSWrapper
{
    private $payOS;
    
    public function __construct(
        string $clientId, 
        string $apiKey, 
        string $checksumKey
    ) {
        $this->payOS = new PayOS($clientId, $apiKey, $checksumKey);
    }

    /**
     * Tạo payment link cho booking
     */
    public function createBookingPayment(
        $orderCode,
        $amount,
        $description,
        $returnUrl,
        $cancelUrl,
        $items,
        $timeExpired
    ): array {
        try {
            // Validate input theo yêu cầu PayOS
            if(!is_numeric($orderCode) || $orderCode <= 0 || $orderCode > 9007199254740991) {
                throw new Exception("Mã đơn hàng không hợp lệ");
            }
            
            if(strlen($description) > 25) {
                $description = substr($description, 0, 22) . '...';
            }
    
            $paymentData = [
                "orderCode" => (int)$orderCode,
                "amount" => (int)$amount,
                "description" => $description,
                "cancelUrl" => $cancelUrl,
                "returnUrl" => $returnUrl,
                "items" => $items,
                "expiredAt" => $timeExpired
            ];
            
            return $this->payOS->createPaymentLink($paymentData);
            
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Xác minh webhook
     */
    public function verifyWebhook(array $data): array
    {
        try {
            return $this->payOS->verifyPaymentWebhookData($data);
        } catch (Exception $e) {
            throw new Exception("WEBHOOK_ERROR: " . $e->getMessage());
        }
    }

    // Lấy thông tin payment
    public function getPaymentLinkInformation(string|int $code):array {
        try {
            return $this->payOS->getPaymentLinkInformation($code);
        } catch (Exception $e) {
            return [];
        }
    }
}