<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestEmailController extends Controller
{
    public function testEmail()
    {
        // Only allow this in development environment
        if (ENVIRONMENT !== 'development') {
            return $this->response->setJSON(['error' => 'Test email only available in development mode']);
        }

        try {
            $emailService = new \App\Libraries\EmailService();
            
            // Test user data
            $testUser = [
                'email' => 'info@boxbites.in', // Send test email to your own email
                'first_name' => 'Test',
                'last_name' => 'User'
            ];
            
            // Test order data
            $testOrder = [
                'order_number' => 'TEST-' . date('YmdHis'),
                'total_amount' => 100.00,
                'subtotal_amount' => 100.00,
                'shipping_amount' => 0.00,
                'tax_amount' => 0.00,
                'discount_amount' => 0.00,
                'created_at' => date('Y-m-d H:i:s'),
                'payment_method' => 'Test Payment',
                'payment_status' => 'pending',
                'payment_method_name' => 'Test Payment Method',
                'shipping_address' => 'Test Address, Test City, Test State, 12345',
                'notes' => 'This is a test order for email verification.'
            ];
            
            // Test order items
            $testOrderItems = [
                [
                    'product_name' => 'Test Product 1',
                    'quantity' => 1,
                    'price' => 50.00,
                    'total' => 50.00
                ],
                [
                    'product_name' => 'Test Product 2',
                    'quantity' => 2,
                    'price' => 25.00,
                    'total' => 50.00
                ]
            ];
            
            log_message('info', 'Starting email test...');

            // Test order confirmation email
            $result1 = $emailService->sendOrderConfirmation($testOrder, $testOrderItems, $testUser);

            // Add delay to prevent SMTP rate limiting
            sleep(2);

            // Test admin notification email
            $result2 = $emailService->sendAdminOrderNotification($testOrder, $testOrderItems, $testUser);

            // Add delay to prevent SMTP rate limiting
            sleep(2);

            // Test welcome email
            $result3 = $emailService->sendWelcomeEmail($testUser);
            
            $results = [
                'order_confirmation' => $result1 ? 'SUCCESS' : 'FAILED',
                'admin_notification' => $result2 ? 'SUCCESS' : 'FAILED',
                'welcome_email' => $result3 ? 'SUCCESS' : 'FAILED'
            ];
            
            log_message('info', 'Email test results: ' . json_encode($results));
            
            return $this->response->setJSON([
                'message' => 'Email test completed',
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Email test error: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => 'Email test failed: ' . $e->getMessage()
            ]);
        }
    }
    
    public function testSMTP()
    {
        // Only allow this in development environment
        if (ENVIRONMENT !== 'development') {
            return $this->response->setJSON(['error' => 'SMTP test only available in development mode']);
        }

        try {
            $emailConfig = new \App\Config\EmailSettings();
            
            $config = [
                'protocol'    => $emailConfig->protocol,
                'SMTPHost'    => $emailConfig->SMTPHost,
                'SMTPUser'    => $emailConfig->SMTPUser,
                'SMTPPass'    => $emailConfig->SMTPPass,
                'SMTPPort'    => $emailConfig->SMTPPort,
                'SMTPCrypto'  => $emailConfig->SMTPCrypto,
                'mailType'    => 'html',
                'charset'     => 'utf-8',
                'newline'     => "\r\n",
                'SMTPTimeout' => 30
            ];
            
            $email = \Config\Services::email();
            $email->initialize($config);
            
            $email->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
            $email->setTo('info@boxbites.in'); // Send to your email
            $email->setSubject('SMTP Test Email - ' . date('Y-m-d H:i:s'));
            $email->setMessage('<h1>SMTP Test</h1><p>This is a test email to verify SMTP configuration.</p><p>Sent at: ' . date('Y-m-d H:i:s') . '</p>');
            
            if ($email->send()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'SMTP test email sent successfully!'
                ]);
            } else {
                $debugInfo = $email->printDebugger(['headers', 'subject', 'body']);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'SMTP test failed',
                    'debug' => $debugInfo
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'SMTP test error: ' . $e->getMessage()
            ]);
        }
    }

    public function testPage()
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Email Test Page</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .container { max-width: 800px; margin: 0 auto; }
                .test-button {
                    background: #007bff;
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    margin: 10px 5px;
                    text-decoration: none;
                    display: inline-block;
                }
                .test-button:hover { background: #0056b3; }
                .result {
                    margin: 20px 0;
                    padding: 15px;
                    border-radius: 5px;
                    display: none;
                }
                .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
                .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
                .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Email Test Page</h1>
                <p>Use the buttons below to test email functionality:</p>

                <button class="test-button" onclick="testSMTP()">Test SMTP Connection</button>
                <button class="test-button" onclick="testEmails()">Test All Email Templates</button>
                <button class="test-button" onclick="testOrderEmail()">Test with Real Order Data</button>
                <button class="test-button" onclick="testAllEmailTypes()">🔍 Comprehensive Email Test</button>

                <div id="result" class="result"></div>

                <h3>Current Configuration:</h3>
                <ul>
                    <li><strong>Environment:</strong> ' . ENVIRONMENT . '</li>
                    <li><strong>Base URL:</strong> ' . base_url() . '</li>
                    <li><strong>SMTP Host:</strong> smtp.hostinger.com</li>
                    <li><strong>SMTP Port:</strong> 587</li>
                    <li><strong>SMTP User:</strong> info@boxbites.in</li>
                    <li><strong>From Email:</strong> info@boxbites.in</li>
                    <li><strong>Admin Email:</strong> info@boxbites.in</li>
                </ul>

                <h3>How to Test:</h3>
                <ol>
                    <li>Click "Test SMTP Connection" first to verify basic email sending works</li>
                    <li>Click "Test All Email Templates" to test order confirmation, admin notification, and welcome emails</li>
                    <li>Check your email inbox (info@boxbites.in) for test emails</li>
                    <li>If emails don\'t arrive, check spam folder</li>
                </ol>

                <h3>Troubleshooting:</h3>
                <ul>
                    <li>Make sure the email account info@boxbites.in exists in your Hostinger control panel</li>
                    <li>Verify the password is correct</li>
                    <li>Check if your server allows outgoing SMTP connections</li>
                    <li>Try both port 587 (TLS) and port 465 (SSL)</li>
                </ul>
            </div>

            <script>
                function showResult(message, type) {
                    const result = document.getElementById("result");
                    result.className = "result " + type;
                    result.innerHTML = message;
                    result.style.display = "block";
                }

                function testSMTP() {
                    showResult("Testing SMTP connection...", "info");

                    fetch("' . base_url('test-smtp') . '")
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showResult("✅ " + data.message, "success");
                            } else {
                                showResult("❌ " + data.message + (data.debug ? "<br><br><strong>Debug Info:</strong><br>" + data.debug : ""), "error");
                            }
                        })
                        .catch(error => {
                            showResult("❌ Error: " + error.message, "error");
                        });
                }

                function testEmails() {
                    showResult("Testing all email templates...", "info");

                    fetch("' . base_url('test-email') . '")
                        .then(response => response.json())
                        .then(data => {
                            if (data.results) {
                                let message = "<strong>Email Test Results:</strong><br>";
                                for (const [type, result] of Object.entries(data.results)) {
                                    const icon = result === "SUCCESS" ? "✅" : "❌";
                                    message += `${icon} ${type.replace("_", " ").toUpperCase()}: ${result}<br>`;
                                }
                                const hasFailures = Object.values(data.results).includes("FAILED");
                                showResult(message, hasFailures ? "error" : "success");
                            } else if (data.error) {
                                showResult("❌ " + data.error, "error");
                            }
                        })
                        .catch(error => {
                            showResult("❌ Error: " + error.message, "error");
                        });
                }

                function testOrderEmail() {
                    showResult("Testing with real order data...", "info");

                    fetch("' . base_url('test-order-email') . '")
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                let message = "<strong>Real Order Email Test Results:</strong><br>";
                                message += `<strong>Order:</strong> ${data.order_number}<br>`;
                                message += `<strong>User:</strong> ${data.user_email}<br>`;
                                message += `<strong>Items:</strong> ${data.order_items_count}<br><br>`;

                                for (const [type, result] of Object.entries(data.results)) {
                                    const icon = result === "SUCCESS" ? "✅" : "❌";
                                    message += `${icon} ${type.replace("_", " ").toUpperCase()}: ${result}<br>`;
                                }
                                const hasFailures = Object.values(data.results).includes("FAILED");
                                showResult(message, hasFailures ? "error" : "success");
                            } else {
                                showResult("❌ " + data.message, "error");
                            }
                        })
                        .catch(error => {
                            showResult("❌ Error: " + error.message, "error");
                        });
                }

                function testAllEmailTypes() {
                    showResult("Running comprehensive email test...", "info");

                    fetch("' . base_url('test-all-emails') . '")
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                let message = "<strong>📧 Comprehensive Email Test Results:</strong><br><br>";

                                // Show summary
                                message += `<strong>Summary:</strong> ${data.summary.passed}/${data.summary.total_tests} tests passed<br><br>`;

                                // Show individual results
                                for (const [type, result] of Object.entries(data.results)) {
                                    const icon = result.success ? "✅" : "❌";
                                    message += `${icon} <strong>${type.replace("_", " ").toUpperCase()}:</strong> ${result.message}<br>`;
                                    if (result.debug) {
                                        message += `&nbsp;&nbsp;&nbsp;&nbsp;Debug: ${result.debug}<br>`;
                                    }
                                }

                                const hasFailures = data.summary.failed > 0;
                                showResult(message, hasFailures ? "error" : "success");
                            } else {
                                showResult("❌ " + data.message, "error");
                            }
                        })
                        .catch(error => {
                            showResult("❌ Error: " + error.message, "error");
                        });
                }
            </script>
        </body>
        </html>';

        return $this->response->setBody($html);
    }

    public function debug()
    {
        $emailConfig = new \App\Config\EmailSettings();

        $info = [
            'Environment' => ENVIRONMENT,
            'Base URL' => base_url(),
            'Current URL' => current_url(),
            'Routes Working' => 'YES - This route is working!',
            'Time' => date('Y-m-d H:i:s'),
            'Email Config' => [
                'SMTP Host' => $emailConfig->SMTPHost,
                'SMTP Port' => $emailConfig->SMTPPort,
                'SMTP User' => $emailConfig->SMTPUser,
                'SMTP Crypto' => $emailConfig->SMTPCrypto,
                'From Email' => $emailConfig->fromEmail,
                'Admin Email' => $emailConfig->adminEmail,
                'Protocol' => $emailConfig->protocol
            ],
            'PHP Mail Functions' => [
                'mail() available' => function_exists('mail') ? 'YES' : 'NO',
                'openssl extension' => extension_loaded('openssl') ? 'YES' : 'NO',
                'curl extension' => extension_loaded('curl') ? 'YES' : 'NO'
            ]
        ];

        return $this->response->setJSON($info);
    }

    public function checkLogs()
    {
        $logPath = WRITEPATH . 'logs';
        $logs = [];

        if (is_dir($logPath)) {
            $files = scandir($logPath);
            foreach ($files as $file) {
                if (strpos($file, 'log-') === 0) {
                    $filePath = $logPath . DIRECTORY_SEPARATOR . $file;
                    $content = file_get_contents($filePath);

                    // Get last 20 lines that contain 'email' or 'Email' or 'order'
                    $lines = explode("\n", $content);
                    $emailLines = [];

                    foreach ($lines as $line) {
                        if (stripos($line, 'email') !== false ||
                            stripos($line, 'order') !== false ||
                            stripos($line, 'smtp') !== false) {
                            $emailLines[] = $line;
                        }
                    }

                    $logs[$file] = array_slice($emailLines, -20); // Last 20 email-related lines
                }
            }
        }

        return $this->response->setJSON([
            'log_path' => $logPath,
            'logs' => $logs
        ]);
    }

    public function testOrderEmail()
    {
        try {
            // Get the most recent order from database
            $orderModel = new \App\Models\OrderModel();
            $order = $orderModel->orderBy('created_at', 'DESC')->first();

            if (!$order) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No orders found in database'
                ]);
            }

            // Get order items
            $orderItemModel = new \App\Models\OrderItemModel();
            $orderItems = $orderItemModel->where('order_id', $order['id'])->findAll();

            // Get user
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($order['user_id']);

            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User not found for order: ' . $order['order_number']
                ]);
            }

            // Test email sending with real order data
            $emailService = new \App\Libraries\EmailService();

            log_message('info', 'Testing email with real order data: ' . json_encode([
                'order_id' => $order['id'],
                'order_number' => $order['order_number'],
                'user_email' => $user['email'],
                'order_items_count' => count($orderItems)
            ]));

            // Test order confirmation email
            $result1 = $emailService->sendOrderConfirmation($order, $orderItems, $user);

            // Add delay
            sleep(2);

            // Test admin notification email
            $result2 = $emailService->sendAdminOrderNotification($order, $orderItems, $user);

            $results = [
                'order_confirmation' => $result1 ? 'SUCCESS' : 'FAILED',
                'admin_notification' => $result2 ? 'SUCCESS' : 'FAILED'
            ];

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Real order email test completed',
                'order_number' => $order['order_number'],
                'user_email' => $user['email'],
                'results' => $results,
                'order_data' => [
                    'id' => $order['id'],
                    'order_number' => $order['order_number'],
                    'total_amount' => $order['total_amount'],
                    'created_at' => $order['created_at'],
                    'user_id' => $order['user_id']
                ],
                'order_items_count' => count($orderItems)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Real order email test error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }

    public function testAllEmailTypes()
    {
        try {
            $results = [];

            // Test 1: Basic SMTP Connection
            $results['smtp_test'] = $this->testBasicSMTP();
            sleep(1);

            // Test 2: Order Confirmation Email (with real order data)
            $results['order_confirmation'] = $this->testOrderConfirmationEmail();
            sleep(1);

            // Test 3: Admin Notification Email
            $results['admin_notification'] = $this->testAdminNotificationEmail();
            sleep(1);

            // Test 4: Welcome Email
            $results['welcome_email'] = $this->testWelcomeEmailOnly();
            sleep(1);

            // Test 5: Order Status Update Email
            $results['status_update'] = $this->testStatusUpdateEmail();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'All email types tested',
                'results' => $results,
                'summary' => [
                    'total_tests' => count($results),
                    'passed' => count(array_filter($results, function($r) { return $r['success']; })),
                    'failed' => count(array_filter($results, function($r) { return !$r['success']; }))
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email testing failed: ' . $e->getMessage()
            ]);
        }
    }

    private function testBasicSMTP()
    {
        try {
            $emailConfig = new \App\Config\EmailSettings();
            $email = \Config\Services::email();

            $config = [
                'protocol'    => $emailConfig->protocol,
                'SMTPHost'    => $emailConfig->SMTPHost,
                'SMTPUser'    => $emailConfig->SMTPUser,
                'SMTPPass'    => $emailConfig->SMTPPass,
                'SMTPPort'    => $emailConfig->SMTPPort,
                'SMTPCrypto'  => $emailConfig->SMTPCrypto,
                'mailType'    => 'html',
                'charset'     => 'utf-8',
                'newline'     => "\r\n",
                'SMTPTimeout' => 30
            ];

            $email->initialize($config);
            $email->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
            $email->setTo($emailConfig->adminEmail);
            $email->setSubject('SMTP Test - ' . date('Y-m-d H:i:s'));
            $email->setMessage('<h3>SMTP Connection Test</h3><p>This email confirms SMTP is working.</p>');

            $result = $email->send();

            return [
                'success' => $result,
                'message' => $result ? 'SMTP connection successful' : 'SMTP connection failed',
                'debug' => $result ? null : $email->printDebugger()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'SMTP test error: ' . $e->getMessage(),
                'debug' => null
            ];
        }
    }
}
