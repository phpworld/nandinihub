<?php
namespace App\Libraries;
use CodeIgniter\Email\Email;

class EmailService
{
    protected $email;
    protected $emailConfig;

    public function __construct()
    {
        $this->email = \Config\Services::email();
        $this->emailConfig = new \App\Config\EmailSettings();

        // Configure email settings from config
        $config = [
            'protocol'    => $this->emailConfig->protocol,
            'SMTPHost'    => $this->emailConfig->SMTPHost,
            'SMTPUser'    => $this->emailConfig->SMTPUser,
            'SMTPPass'    => $this->emailConfig->SMTPPass,
            'SMTPPort'    => $this->emailConfig->SMTPPort,
            'SMTPCrypto'  => $this->emailConfig->SMTPCrypto,
            'mailType'    => $this->emailConfig->mailType,
            'charset'     => $this->emailConfig->charset,
            'newline'     => $this->emailConfig->newline,
            'SMTPTimeout' => 30,
            'wordWrap'    => true,
            'wrapChars'   => 76
        ];

        // Log email configuration for debugging
        log_message('info', 'Email configuration: ' . json_encode([
            'protocol' => $config['protocol'],
            'SMTPHost' => $config['SMTPHost'],
            'SMTPUser' => $config['SMTPUser'],
            'SMTPPort' => $config['SMTPPort'],
            'SMTPCrypto' => $config['SMTPCrypto']
        ]));

        $this->email->initialize($config);
    }

    public function sendOrderConfirmation($order, $orderItems, $user)
    {
        try {
            log_message('info', 'Attempting to send order confirmation email to: ' . $user['email'] . ' for order: ' . $order['order_number']);

            // Reinitialize email service to avoid conflicts
            $this->reinitializeEmail();

            $this->email->setFrom($this->emailConfig->fromEmail, $this->emailConfig->fromName);
            $this->email->setTo($user['email']);
            $subject = str_replace('{order_number}', $order['order_number'], $this->emailConfig->templates['order_confirmation']['subject']);
            $this->email->setSubject($subject);

            $message = $this->generateOrderConfirmationEmail($order, $orderItems, $user);
            $this->email->setMessage($message);

            log_message('info', 'Email prepared, attempting to send...');

            if ($this->email->send()) {
                log_message('info', 'Order confirmation email sent successfully to: ' . $user['email']);
                return true;
            } else {
                $debugInfo = $this->email->printDebugger(['headers', 'subject', 'body']);
                log_message('error', 'Failed to send order confirmation email. Debug info: ' . $debugInfo);
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', 'Email service error in sendOrderConfirmation: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    private function reinitializeEmail()
    {
        // Create a fresh email instance to avoid conflicts
        $this->email = \Config\Services::email();

        $config = [
            'protocol'    => $this->emailConfig->protocol,
            'SMTPHost'    => $this->emailConfig->SMTPHost,
            'SMTPUser'    => $this->emailConfig->SMTPUser,
            'SMTPPass'    => $this->emailConfig->SMTPPass,
            'SMTPPort'    => $this->emailConfig->SMTPPort,
            'SMTPCrypto'  => $this->emailConfig->SMTPCrypto,
            'mailType'    => $this->emailConfig->mailType,
            'charset'     => $this->emailConfig->charset,
            'newline'     => $this->emailConfig->newline,
            'SMTPTimeout' => 30,
            'wordWrap'    => true,
            'wrapChars'   => 76
        ];

        $this->email->initialize($config);
    }

    public function sendOrderStatusUpdate($order, $user, $oldStatus, $newStatus)
    {
        try {
            $this->email->setFrom($this->emailConfig->fromEmail, $this->emailConfig->fromName);
            $this->email->setTo($user['email']);
            $subject = str_replace('{order_number}', $order['order_number'], $this->emailConfig->templates['status_update']['subject']);
            $this->email->setSubject($subject);

            $message = $this->generateOrderStatusUpdateEmail($order, $user, $oldStatus, $newStatus);
            $this->email->setMessage($message);

            if ($this->email->send()) {
                log_message('info', 'Order status update email sent to: ' . $user['email']);
                return true;
            } else {
                log_message('error', 'Failed to send order status update email: ' . $this->email->printDebugger());
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', 'Email service error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendWelcomeEmail($user)
    {
        try {
            log_message('info', 'Attempting to send welcome email to: ' . $user['email']);

            // Reinitialize email service to avoid conflicts
            $this->reinitializeEmail();

            $this->email->setFrom($this->emailConfig->fromEmail, $this->emailConfig->fromName);
            $this->email->setTo($user['email']);
            $this->email->setSubject($this->emailConfig->templates['welcome']['subject']);

            $message = $this->generateWelcomeEmail($user);
            $this->email->setMessage($message);

            log_message('info', 'Welcome email prepared, attempting to send...');

            if ($this->email->send()) {
                log_message('info', 'Welcome email sent successfully to: ' . $user['email']);
                return true;
            } else {
                $debugInfo = $this->email->printDebugger(['headers', 'subject', 'body']);
                log_message('error', 'Failed to send welcome email. Debug info: ' . $debugInfo);
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', 'Email service error in sendWelcomeEmail: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    public function sendAdminOrderNotification($order, $orderItems, $user)
    {
        try {
            log_message('info', 'Attempting to send admin notification email to: ' . $this->emailConfig->adminEmail . ' for order: ' . $order['order_number']);

            // Reinitialize email service to avoid conflicts
            $this->reinitializeEmail();

            $this->email->setFrom($this->emailConfig->fromEmail, $this->emailConfig->fromName);
            $this->email->setTo($this->emailConfig->adminEmail);
            $subject = str_replace('{order_number}', $order['order_number'], $this->emailConfig->templates['admin_notification']['subject']);
            $this->email->setSubject($subject);

            $message = $this->generateAdminOrderNotificationEmail($order, $orderItems, $user);
            $this->email->setMessage($message);

            log_message('info', 'Admin email prepared, attempting to send...');

            if ($this->email->send()) {
                log_message('info', 'Admin order notification sent successfully for order: ' . $order['order_number']);
                return true;
            } else {
                $debugInfo = $this->email->printDebugger(['headers', 'subject', 'body']);
                log_message('error', 'Failed to send admin order notification. Debug info: ' . $debugInfo);
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', 'Email service error in sendAdminOrderNotification: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    private function generateOrderConfirmationEmail($order, $orderItems, $user)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Order Confirmation</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #ff6b35, #f7931e); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .order-details { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
                .item { border-bottom: 1px solid #eee; padding: 10px 0; }
                .total { font-weight: bold; font-size: 18px; color: #ff6b35; }
                .footer { text-align: center; padding: 20px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🕉️ Nandini Hub</h1>
                    <h2>Order Confirmation</h2>
                </div>
                
                <div class="content">
                    <p>Dear ' . esc($user['first_name']) . ',</p>
                    <p>Thank you for your order! We have received your order and it is being processed.</p>
                    
                    <div class="order-details">
                        <h3>Order Details</h3>
                        <p><strong>Order Number:</strong> ' . esc($order['order_number']) . '</p>
                        <p><strong>Order Date:</strong> ' . date('F j, Y', strtotime($order['created_at'])) . '</p>
                        <p><strong>Payment Method:</strong> ' . ucfirst($order['payment_method']) . '</p>
                        
                        <h4>Items Ordered:</h4>';
        
        foreach ($orderItems as $item) {
            $html .= '
                        <div class="item">
                            <strong>' . esc($item['product_name']) . '</strong><br>
                            Quantity: ' . $item['quantity'] . ' × ₹' . number_format($item['price'], 2) . ' = ₹' . number_format($item['total'], 2) . '
                        </div>';
        }
        
        $html .= '
                        <div class="total">
                            <p>Total Amount: $' . number_format($order['total_amount'], 2) . '</p>
                        </div>
                    </div>
                    
                    <p>Your order will be shipped to:</p>
                    <div class="order-details">
                        ' . nl2br(esc($order['shipping_address'])) . '
                    </div>
                    
                    <p>We will send you another email when your order ships.</p>
                    <p>Thank you for choosing Microdose Mushroom for your wellness needs!</p>
                </div>
                
                <div class="footer">
                    <p>Microdose Mushroom - Premium Microdose Products Online</p>
                    <p>For any queries, contact us at info@microdosemushroom.com</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    private function generateAdminOrderNotificationEmail($order, $orderItems, $user)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>New Order Notification</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .order-details { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
                .item { border-bottom: 1px solid #eee; padding: 10px 0; }
                .total { font-weight: bold; font-size: 18px; color: #dc3545; }
                .customer-info { background: #e9ecef; padding: 15px; margin: 15px 0; border-radius: 5px; }
                .footer { text-align: center; padding: 20px; color: #666; }
                .urgent { background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🚨 New Order Alert</h1>
                    <h2>Order #' . esc($order['order_number']) . '</h2>
                </div>

                <div class="content">
                    <div class="urgent">
                        <strong>⚡ Action Required:</strong> A new order has been placed and requires your attention.
                    </div>

                    <div class="customer-info">
                        <h3>Customer Information</h3>
                        <p><strong>Name:</strong> ' . esc($user['first_name'] . ' ' . $user['last_name']) . '</p>
                        <p><strong>Email:</strong> ' . esc($user['email']) . '</p>
                        <p><strong>Phone:</strong> ' . esc($user['phone'] ?? 'Not provided') . '</p>
                    </div>

                    <div class="order-details">
                        <h3>Order Details</h3>
                        <p><strong>Order Number:</strong> ' . esc($order['order_number']) . '</p>
                        <p><strong>Order Date:</strong> ' . date('F j, Y \a\t g:i A', strtotime($order['created_at'])) . '</p>
                        <p><strong>Payment Status:</strong> ' . ucfirst($order['payment_status']) . '</p>
                        <p><strong>Payment Method:</strong> ' . esc($order['payment_method_name'] ?? 'Not specified') . '</p>

                        <h4>Items Ordered:</h4>';

        foreach ($orderItems as $item) {
            $html .= '
                        <div class="item">
                            <strong>' . esc($item['product_name']) . '</strong><br>
                            Quantity: ' . $item['quantity'] . ' × $' . number_format($item['price'], 2) . ' = $' . number_format($item['total'], 2) . '
                        </div>';
        }

        $html .= '
                        <div class="total">
                            <p>Subtotal: $' . number_format($order['subtotal_amount'], 2) . '</p>
                            <p>Shipping: $' . number_format($order['shipping_amount'], 2) . '</p>
                            <p>Tax: $' . number_format($order['tax_amount'], 2) . '</p>
                            ' . ($order['discount_amount'] > 0 ? '<p>Discount: -$' . number_format($order['discount_amount'], 2) . '</p>' : '') . '
                            <p><strong>Total Amount: $' . number_format($order['total_amount'], 2) . '</strong></p>
                        </div>
                    </div>

                    <div class="order-details">
                        <h4>Shipping Address:</h4>
                        ' . nl2br(esc($order['shipping_address'])) . '
                    </div>

                    ' . (!empty($order['notes']) ? '<div class="order-details"><h4>Customer Notes:</h4><p>' . nl2br(esc($order['notes'])) . '</p></div>' : '') . '

                    <div class="urgent">
                        <p><strong>Next Steps:</strong></p>
                        <ul>
                            <li>Review and confirm the order in admin panel</li>
                            <li>Verify payment if required</li>
                            <li>Prepare items for shipping</li>
                            <li>Update order status as needed</li>
                        </ul>
                    </div>
                </div>

                <div class="footer">
                    <p>Nandini Hub Admin Panel</p>
                    <p><a href="' . base_url('admin/orders') . '">View Order in Admin Panel</a></p>
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    private function generateOrderStatusUpdateEmail($order, $user, $oldStatus, $newStatus)
    {
        $statusMessages = [
            'pending' => 'Your order is being processed.',
            'processing' => 'Your order is being prepared for shipment.',
            'shipped' => 'Your order has been shipped and is on its way!',
            'delivered' => 'Your order has been delivered. Thank you for shopping with us!',
            'cancelled' => 'Your order has been cancelled as requested.'
        ];

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Order Status Update</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #ff6b35, #f7931e); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .status-update { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; text-align: center; }
                .status { font-size: 24px; font-weight: bold; color: #ff6b35; text-transform: uppercase; }
                .footer { text-align: center; padding: 20px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🕉️ Nandini Hub</h1>
                    <h2>Order Status Update</h2>
                </div>
                
                <div class="content">
                    <p>Dear ' . esc($user['first_name']) . ',</p>
                    <p>Your order status has been updated.</p>
                    
                    <div class="status-update">
                        <p><strong>Order Number:</strong> ' . esc($order['order_number']) . '</p>
                        <div class="status">' . ucfirst($newStatus) . '</div>
                        <p>' . ($statusMessages[$newStatus] ?? 'Your order status has been updated.') . '</p>
                    </div>
                    
                    <p>You can track your order anytime by visiting your account on our website.</p>
                    <p>Thank you for choosing Microdose Mushroom!</p>
                </div>
                
                <div class="footer">
                    <p>Microdose Mushroom - Premium Microdose Products Online</p>
                    <p>For any queries, contact us at info@microdosemushroom.com</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    private function generateWelcomeEmail($user)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Welcome to Microdose Mushroom</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #ff6b35, #f7931e); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .welcome-box { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; text-align: center; }
                .features { display: flex; justify-content: space-around; margin: 20px 0; }
                .feature { text-align: center; flex: 1; padding: 10px; }
                .footer { text-align: center; padding: 20px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🍄 Welcome to Microdose Mushroom</h1>
                    <p>Your Wellness Journey Begins Here</p>
                </div>
                
                <div class="content">
                    <div class="welcome-box">
                        <h2>Namaste ' . esc($user['first_name']) . '!</h2>
                        <p>Thank you for joining the Microdose Mushroom family. We are delighted to have you with us on your wellness journey.</p>
                    </div>
                    
                    <h3>What makes Microdose Mushroom special?</h3>
                    <div class="features">
                        <div class="feature">
                            <h4>🔥 Premium Quality</h4>
                            <p>Premium microdose products sourced from trusted suppliers</p>
                        </div>
                        <div class="feature">
                            <h4>🚚 Fast Delivery</h4>
                            <p>Quick and secure delivery to your doorstep</p>
                        </div>
                        <div class="feature">
                            <h4>🛡️ Trusted Service</h4>
                            <p>24/7 customer support and easy returns</p>
                        </div>
                    </div>
                    
                    <p>Start exploring our wide range of:</p>
                    <ul>
                        <li>Premium Microdose Capsules</li>
                        <li>Dried Magic Mushrooms</li>
                        <li>Mushroom Chocolates & Edibles</li>
                        <li>Wellness & Therapeutic Products</li>
                        <li>And much more...</li>
                    </ul>
                    
                    <div class="welcome-box">
                        <p><strong>Special Offer:</strong> Get free shipping on your first order above ₹500!</p>
                        <a href="' . base_url() . '" style="background: #ff6b35; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Start Shopping</a>
                    </div>
                </div>
                
                <div class="footer">
                    <p>Microdose Mushroom - Premium Microdose Products Online</p>
                    <p>For any queries, contact us at info@microdosemushroom.com</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}
