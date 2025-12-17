<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Tạo một đối tượng message mới.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Lấy envelope message (Tiêu đề và địa chỉ).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận Đơn hàng Thành công - #' . $this->order->ma,
        );
    }

    /**
     * Lấy định nghĩa nội dung message.
     */
    public function content(): Content
    {
        // Trỏ đến view Blade sẽ dùng làm nội dung email
        return new Content(
            view: 'emails.order_confirmation',
            with: [
                'order' => $this->order,
            ]
        );
    }

    /**
     * Lấy các phần đính kèm cho message.
     */
    public function attachments(): array
    {
        return [];
    }
}