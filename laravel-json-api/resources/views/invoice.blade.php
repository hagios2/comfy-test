<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Invoice</title>

    <style>
      @page default {
        size: a4;
        margin: 4cm;
      }

      *, *::before, *::after {
        box-sizing: border-box;
        color: inherit;
        font-family: inherit;
        line-height: 1.35;
        position: relative;
      }

      html {
        color: #363e41;
        font-size: 14px;
      }

      table {
        border: 0px;
        border-collapse: collapse;
        width: 100%;
      }

      .page {
        page: default;
      }

      .page--break {
        page-break-after: always;
      }

      .page::after {
        bottom: -0.75cm;
        color: #5b5f7a;
        content: "Page " counter(page);
        font-size: 0.875rem;
        position: absolute;
        right: -0.25cm;
      }

      .page::before {
        content: "ISSUED INVOICE";
        font-size: 4.5rem;
        font-weight: bold;
        height: 10%;
        left: 0%;
        opacity: 0.075;
        position: absolute;
        top: 25%;
        text-align: center;
        transform: rotate(-30deg);
        transform-origin: center;
        width: 100%;
      }

      .page__logo {
        bottom: -0.75cm;
        left: 85px;
        position: absolute;
        width: 4.25rem;
      }

      .page__logo__text {
        bottom: -0.50cm;
        left: 0px;
        position: absolute;
      }

      .table-header {
        background-color: #a6abce69;
        border: none;
        padding: 0.75rem 1.25rem;
      }

      .table-cell {
        border: none;
        padding: 0.75rem 1.25rem;
      }
    </style>
  </head>

  <body>
    <div class="page">
      <span class="page__logo__text" style="">Powered By</span>
      <img class="page__logo" src="{{public_path('images/soltech.jpeg')}}" alt="Solutech Hub Logo"></img>

      <table>
        <tbody>
          <tr>
            <td>
              <img style="max-height: 7.0rem; width: 7.0rem;" src="{{public_path('images/ST-Lg.jpg')}}" alt="Logo">
            </td>

            <td style="text-align: right;">
              <div style="color: black; font-size: 2rem; font-weight: bold;">INVOICE</div>
              <div>
                <span style="color: black; font-weight: bold;">Invoice No: </span>
                <span>{{$invoice->invoice_no}}</span>
              </div>
{{--              <div>--}}
{{--                <span style="color: black; font-weight: bold;">Payment Ref: </span>--}}
{{--                <span>{{$transactionId}}</span>--}}
{{--              </div>--}}
              <div>
                  <span style="color: black; font-weight: bold;">Issued Date: </span>
                  <span>{{$invoice->issue_date->format('D, d F Y')}}</span>
              </div>
              <div>
                  <span style="color: black; font-weight: bold;">Due Date: </span>
                  <span>{{$invoice->due_date->format('D, d F Y')}}</span>
              </div>
{{--              <div>--}}
{{--                <span style="color: black; font-weight: bold;">Paid At: </span>--}}
{{--                <span>{{$settledAt}}</span>--}}
{{--              </div>--}}
            </td>
          </tr>
        </tbody>
      </table>

      <div style="color: black; font-size: 1.25rem; font-weight: bold;">{{$invoice->invoice_name}}</div>

      <table style="margin-top: 2.5rem">
        <tbody>
          <tr>
            <td style="width: 50%;">
              <span style="color: black; font-weight: bold;">From:</span>

              <div style="margin-top: 0.75rem;">Samuel Tengey (PhD)</div>
              <div>sam@samueltengey.com</div>
              <div>Accra Leadership Center, Golden Glades St,<br>East Airport, Accra, Ghana</div>
              <div>+233 265 084 067</div>
            </td>
            <td style="width: 50%;">
              <span style="color: black; font-weight: bold;">Billed to:</span>

              <div style="margin-top: 0.75rem;">{{$invoice->customer?->name}}</div>
               <div>{{$invoice->customer->email}}</div>
              <div>{{$invoice->customer->address}}</div>
              <div>{{$invoice->customer->phone}}</div>
            </td>
          </tr>
        </tbody>
      </table>

      <table style="margin-top: 2.5rem;">
        <thead>
          <tr>
            <th class="table-header">Item</th>
            <th class="table-header">Quantity</th>
            <th class="table-header" style="text-align: right;">Unit price ({{$invoice->currency}})</th>
            <th class="table-header" style="text-align: right;">Subtotal ({{$invoice->currency}})</th>
          </tr>
        </thead>

        <tbody>
             @foreach($invoice->invoiceItems as $item)
                 <tr>
                    <td class="table-cell" style="text-align: center;">{{$item->item}}</td>
                    <td class="table-cell" style="text-align: center;">{{$item->quantity}}</td>
                    <td class="table-cell" style="text-align: right;">{{$item->unit_price}}</td>
                    <td class="table-cell" style="text-align: right;">{{$item->subtotal}}</td>
                 </tr>
             @endforeach
             <tr>
                <td class="table-cell" style="border-top: 1px solid #a6abce69;"></td>
                <td class="table-cell" style="border-top: 1px solid #a6abce69;"></td>
{{--                <td class="table-cell" style="border-top: 1px solid #a6abce69; font-weight: bold; text-align: right;">Subtotal</td>--}}
{{--                <td class="table-cell" style="border-top: 1px solid #a6abce69; font-weight: bold; text-align: right;">{{$invoice->subtotal}}</td>--}}
             </tr>
{{--             @if ( !blank($invoice->discount_type))--}}
{{--                  @if( strtolower($invoice->discount_type) === 'flat')--}}
{{--                        <tr>--}}
{{--                            <td class="table-cell"></td>--}}
{{--                            <td class="table-cell"></td>--}}
{{--                            <td class="table-cell" style="font-weight: bold; text-align: right;">Discount ({{ $invoice->currency }} {{ $invoice->discount_value }})</td>--}}
{{--                            <td class="table-cell" style="font-weight: bold; text-align: right;"> - {{ $invoice->discount_amount }}</td>--}}
{{--                        </tr>--}}
{{--                  @elseif( strtolower($invoice->discount_type) === 'percentage')--}}
{{--                        <tr>--}}
{{--                            <td class="table-cell"></td>--}}
{{--                            <td class="table-cell"></td>--}}
{{--                            <td class="table-cell" style="font-weight: bold; text-align: right;">Discount ({{ $invoice->discount_value }})</td>--}}
{{--                            <td class="table-cell" style="font-weight: bold; text-align: right;"> - {{ $invoice->discount_amount }}</td>--}}
{{--                        </tr>--}}
{{--                  @endif--}}
{{--             @endif--}}
{{--             @if ( ! blank($invoice->taxes) )--}}
{{--                 @foreach ($invoice->taxes as $tax)--}}
{{--                    <tr>--}}
{{--                         <td class="table-cell"></td>--}}
{{--                         <td class="table-cell"></td>--}}
{{--                         <td class="table-cell" style="font-weight: bold; text-align: right;">{{ $tax->name }} ({{ $tax->percentage }} %)</td>--}}
{{--                         <td class="table-cell" style="font-weight: bold; text-align: right;">{{ $tax->pivot->tax_amount }}</td>--}}
{{--                    </tr>--}}
{{--                @endforeach--}}
{{--                 <tr>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td style="padding: 0.5rem 0.75rem; text-align: right;">Amount After Tax</td>--}}
{{--                    <td style="border: 1px solid #5b5f7a; padding: 0.5rem 0.75rem; text-align: right;">{{ $invoice->amount_after_tax }}</td>--}}
{{--                </tr>--}}
{{--             @endif--}}
             <tr>
                <td class="table-cell" style="border-bottom: 1px solid #a6abce69;"></td>
                <td class="table-cell" style="border-bottom: 1px solid #a6abce69;"></td>
                <td class="table-cell" style="border-bottom: 1px solid #a6abce69; font-size: 1.5rem; font-weight: bold; text-align: right;">Total</td>
                <td class="table-cell" style="border-bottom: 1px solid #a6abce69; font-size: 1.5rem; font-weight: bold; text-align: right;">{{strtoupper($invoice->currency)}} {{number_format($invoice->total, 2)}}</td>
             </tr>
        </tbody>
      </table>
        <table style="margin-top: 2.5rem">
        <tbody>
          <tr>
            <td style="width: 50%;">
              <span style="color: black; font-size: 20px; font-weight: bold;">Account Details:</span>

              <div>Zenith Bank - Cape Coast</div>
              <div>Dr. Samuel Tengey</div>
              <div>Account Number: 4011416320</div>
              <div>Mobile Money: +233 265 084 067</div>
            </td>
            <td style="width: 50%;">
                <span style="color: black; font-size: 20px; font-weight: bold;">Account Details:</span>

              <div>Fidelity Bank</div>
              <div>Dr. Samuel Tengey</div>
              <div>Account Number: 2030210918713</div>
              <div>Accra Central Post Bank</div>
    </td>
          </tr>
        </tbody>
      </table>
    </div>
  </body>
</html>
