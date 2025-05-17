<?php

namespace App\Jobs;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Le nombre de tentatives maximum pour le job.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Le nombre de secondes à attendre avant de retenter.
     *
     * @var array
     */
    public $backoff = [60, 300, 600]; // 1min, 5min, 10min

    /**
     * La locale pour la facture
     *
     * @var string
     */
    private string $locale;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Order $order,
        string $locale
    ) {
        $this->locale = $locale;
        Log::info("Job created with locale: {$locale} for order: {$order->reference}");
    }

    /**
     * Execute the job.
     */
    public function handle(InvoiceService $invoiceService)
    {
        Log::info('Starting to process order confirmation email for order: ' . $this->order->reference . ' with locale: ' . $this->locale);

        try {
            $this->order->load(['ordersProducts.product', 'user.store']);

            if ($this->order->user && $this->order->user->store) {
                $this->order->user->store->name = mb_convert_encoding($this->order->user->store->name, 'UTF-8', 'auto');
                $this->order->user->store->address = mb_convert_encoding($this->order->user->store->address, 'UTF-8', 'auto');
                $this->order->user->store->city = mb_convert_encoding($this->order->user->store->city, 'UTF-8', 'auto');
            }

            Log::info('Order relations loaded successfully');

            Log::info('Sending email to: ' . $this->order->user->email);
            $mail = new OrderConfirmation($this->order, $this->locale);

            Mail::to($this->order->user->email)
                ->send($mail);

            Log::info('Email sent successfully');

        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation email in queue: ' . $e->getMessage());
            Log::error('Error details: ' . $e->getTraceAsString());
            Log::error('Order details: ' . json_encode([
                    'order_id' => $this->order->id,
                    'reference' => $this->order->reference,
                    'user_email' => $this->order->user->email ?? 'no email',
                    'has_store' => isset($this->order->user->store),
                    'has_products' => $this->order->ordersProducts->isNotEmpty(),
                    'locale' => $this->locale,
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Job failed finally after all retries for order: ' . $this->order->reference);
        Log::error('Final error: ' . $exception->getMessage());
    }
}
