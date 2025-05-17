<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var Order
     */
    public $order;

    /**
     * La langue pour cet email.
     *
     * @var string
     */
    protected $emailLocale;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, string $locale)
    {
        $this->order = $order;
        $this->emailLocale = $locale;

        $this->sanitizeOrderData();
    }

    /**
     * Sanitize all string data in the order to ensure proper UTF-8 encoding
     */
    protected function sanitizeOrderData()
    {
        if ($this->order->user && $this->order->user->store) {
            $this->order->user->store->name = mb_convert_encoding($this->order->user->store->name, 'UTF-8', 'auto');
            $this->order->user->store->address = mb_convert_encoding($this->order->user->store->address, 'UTF-8', 'auto');
            $this->order->user->store->city = mb_convert_encoding($this->order->user->store->city, 'UTF-8', 'auto');
            $this->order->user->store->zipcode = mb_convert_encoding($this->order->user->store->zipcode, 'UTF-8', 'auto');
        }

        foreach ($this->order->ordersProducts as $orderProduct) {
            if ($orderProduct->product) {
                $orderProduct->product->name = mb_convert_encoding($orderProduct->product->name, 'UTF-8', 'auto');
                $orderProduct->product->description = mb_convert_encoding($orderProduct->product->description, 'UTF-8', 'auto');
            }
        }
    }

    /**
     * Build the message.
     */
    public function build()
    {
        Log::info('Building email with locale: ' . $this->emailLocale);

        $translationsPath = base_path('resources/lang/'.$this->emailLocale.'/emails.php');

        Log::info('Translation path: ' . $translationsPath);

        if (!file_exists($translationsPath)) {
            Log::warning("Translation file not found: {$translationsPath}");
            $translationsPath = base_path('resources/lang/fr/emails.php');
        }

        $translations = require $translationsPath;

        array_walk_recursive($translations, function(&$item) {
            if (is_string($item)) {
                $item = mb_convert_encoding($item, 'UTF-8', 'auto');
            }
        });

        $subject = str_replace(':reference', $this->order->reference, $translations['order']['subject']);

        Log::info('Email subject: ' . $subject);

        $mailable = $this->to($this->order->user->email)
            ->subject($subject)
            ->view('emails.order-confirmation')
            ->with([
                'order' => $this->order,
                'translations' => $translations,
                'locale' => $this->emailLocale,
            ]);

        try {
            $pdf = app(InvoiceService::class)->generatePdfString($this->order, $this->emailLocale);

            $mailable->attachData(
                $pdf,
                "facture-{$this->order->reference}.pdf",
                ['mime' => 'application/pdf']
            );
        } catch (\Exception $e) {
            Log::error('Failed to generate invoice PDF for email: ' . $e->getMessage());
        }

        return $mailable;
    }
}
