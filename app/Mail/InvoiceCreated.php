<?php
namespace App\Mail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice; // Import the Invoice model

class InvoiceCreated extends Mailable
{
    use Queueable, SerializesModels;
    
    public $invoice; // Add the invoice property

    /**
     * Create a new message instance.
     *
     * @param Invoice $invoice
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice; // Assign the invoice
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $filePath = $this->invoice->file_path;
        
    
        return $this->subject('Nouvelle facture créée')
                    ->view('emails.invoice_created')
                    ->attach(storage_path('app/public/'.$filePath))
                    ->with([
                        'invoice' => $this->invoice,
                    ]);
    }
}
