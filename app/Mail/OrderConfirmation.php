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
     * The language for this email.
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
            Log::info('Starting PDF generation for order: ' . $this->order->reference);

            $invoiceService = app(InvoiceService::class);
            $pdf = $invoiceService->generatePdfString($this->order, $this->emailLocale);

            if (empty($pdf)) {
                throw new \Exception('Generated PDF is empty');
            }

            Log::info('PDF generated successfully, size: ' . strlen($pdf) . ' bytes');

            $fileName = $invoiceService->getFileName($this->order, $this->emailLocale);
            Log::info('Attaching PDF with filename: ' . $fileName);

            $mailable->attachData(
                $pdf,
                $fileName,
                ['mime' => 'application/pdf']
            );

            Log::info('PDF attached successfully to email');
        } catch (\Exception $e) {
            Log::error('Failed to generate or attach invoice PDF: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        return $mailable;
    }
}
