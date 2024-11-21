<?php

namespace App\Jobs;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Invoice $invoice)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

//        $taxes = collect();
//        $nonVatTaxes = $this->quotation->tax()->whereNotIn('name', ['VAT', 'VAT FLAT'])->get();

//        foreach ($nonVatTaxes as $tax) {
//            $taxes->push([
//                'name' => $tax->name,
//                'percentage' => $tax->percentage,
//                'amount' => ($tax->percentage * $this->quotation->sub_total) / 100
//            ]);
//        }

//        $vatTaxes = $this->quotation->tax()->whereIn('name', ['VAT', 'VAT FLAT'])->get();

//        foreach ($vatTaxes as $tax) {
//            $taxes->push([
//                'name' => $tax->name,
//                'percentage' => $tax->percentage,
//                'amount' => (($this->quotation->sub_total + $taxes->sum('amount')) * $tax->percentage) / 100
//            ]);
//        }

        $file = Pdf::loadView('invoice', [
            'invoice' => $this->invoice,
        ])->setPaper('A4');

        $path = "invoices/".str_replace('/', '-', $this->invoice->invoice_no) .'.pdf';
        $file->save(storage_path("app/public/{$path}"));
        $this->invoice->update(['path' => '/storage/' . $path]);
    }
}
