<?php
namespace src\Services;

class Sennet
{
    private $secret = SENNET_SECRET;
    private $app_id = SENNET_APP_ID;
    private $base_url = DOMAIN_SENNET;

    /**
     * Send a JSON response and exit
     * 
     * @param array $data Response data
     * @param int $statusCode HTTP status code
     * @return void
     */
    private function sendResponse($data, $statusCode = 200) 
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * Make an HTTP request to the Sennet API
     * 
     * @param string $path API endpoint path
     * @param array $option Request options
     * @param string $signature Request signature
     * @return mixed Response from API
     */
    private function request($path, $option = [], $signature = '')
    {
        extract($option);

        if (!isset($method)) {
            $method = 'GET';
        }

        // Initialize curl
        $ch = curl_init($this->base_url . $path);

        // Set common headers and options
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-APP-ID: ' . $this->app_id, // Add app ID header
            'X-SIGNATURE: ' . $signature,
        ]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            if (isset($post_data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
        }

        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        
        // Execute request
        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);

        // Handle response based on HTTP code
        if ($httpCode >= 400) {
            $response = json_decode($output, true);
            $this->sendResponse([
                'error' => STATUS_ACTIVE,
                'message' => $response['message'] ?? 'Request failed',
                'error_code' => $response['error_code'] ?? 'REQUEST_FAILED'
            ], $httpCode);
        }

        return $output;
    }

    /**
     * Request to hold rooms
     * 
     * @param array $data Booking data
     * @return array Response from Sennet API
     */
    public function requestHoldRoom($data)
    {
        // Validate required fields
        $requiredFields = ['booking_id', 'hotel_id', 'rooms', 'checkin', 'checkout', 'time'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $this->sendResponse([
                    'error' => STATUS_ACTIVE,
                    'message' => "Missing required field: {$field}",
                    'error_code' => 'MISSING_FIELD'
                ], 400);
            }
        }

        $data['type'] = 'hold';
        $json_data = json_encode($data);
        $data_encode = base64_encode($json_data);
        $signature = hash_hmac('sha256', $data_encode, $this->secret);
        
        try {
            $response = $this->request('/api/ota/hold_room', [
                'method' => 'POST',
                'post_data' => $data_encode
            ], $signature);
            
            return json_decode($response, true);
        } catch (\Exception $e) {
            $this->sendResponse([
                'error' => STATUS_ACTIVE,
                'message' => 'Failed to hold room: ' . $e->getMessage(),
                'error_code' => 'HOLD_ROOM_FAILED'
            ], 500);
        }
    }

    /**
     * Send booking data to Sennet
     * 
     * @param array $data Booking data
     * @return array Response from Sennet API
     */
    public function sendBookingSennet($data)
    {
        $json_data = json_encode($data);
        $data_encode = base64_encode($json_data);
        $signature = hash_hmac('sha256', $data_encode, $this->secret);
        
        try {
            $response = $this->request('/api/ota/create_booking', [
                'method' => 'POST',
                'post_data' => $data_encode
            ], $signature);
            
            return json_decode($response, true);
        } catch (\Exception $e) {
            $this->sendResponse([
                'error' => STATUS_ACTIVE,
                'message' => 'Failed to create booking: ' . $e->getMessage(),
                'error_code' => 'CREATE_BOOKING_FAILED'
            ], 500);
        }
    }

    /**
     * Request to unhold rooms
     * 
     * @param array $data Booking data
     * @return array Response from Sennet API
     */
    public function requestUnHoldRoom($data)
    {
        // Validate required fields
        $requiredFields = ['booking_id', 'hotel_id'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $this->sendResponse([
                    'error' => STATUS_ACTIVE,
                    'message' => "Missing required field: {$field}",
                    'error_code' => 'MISSING_FIELD'
                ], 400);
            }
        }

        $data['type'] = 'unhold';
        $json_data = json_encode($data);
        $data_encode = base64_encode($json_data);
        $signature = hash_hmac('sha256', $data_encode, $this->secret);
        
        try {
            $response = $this->request('/api/ota/hold_room', [
                'method' => 'POST',
                'post_data' => $data_encode
            ], $signature);
            
            return json_decode($response, true);
        } catch (\Exception $e) {
            $this->sendResponse([
                'error' => STATUS_ACTIVE,
                'message' => 'Failed to unhold room: ' . $e->getMessage(),
                'error_code' => 'UNHOLD_ROOM_FAILED'
            ], 500);
        }
    }
} 