@php
    $lang = $lang ?? 'ru';
    $lang = in_array($lang, ['uz', 'ru', 'en'], true) ? $lang : 'ru';

    $T = [
        'uz' => [
            'title' => 'Qabul akti',
            'from' => 'dan',
            'invoice' => 'Nakladnoy - Hisob-faktura',
            'accepted' => 'Qabul qildi',
            'supplier_from' => 'Kimdan',
            'contract' => 'Shartnoma bo‘yicha',
            'proxy' => 'Ishonchnoma orqali',
            'no' => '№',
            'h_name' => 'Nomi',
            'h_unit' => "O'lch.bir",
            'h_qty' => 'Miqdor',
            'h_price' => 'Narxi',
            'h_sum' => 'Summa',
            'total' => 'Jami',
            'delivered' => 'Topshirdi',
            'received' => 'Qabul qildi',
        ],

        'ru' => [
            'title' => 'Приёмный акт',
            'from' => 'от',
            'invoice' => 'Накладная - Счёт - Фактура',
            'accepted' => 'Принял',
            'supplier_from' => 'От кого',
            'contract' => 'По счёт По договору',
            'proxy' => 'Через к Дов',
            'no' => '№',
            'h_name' => 'Наименование',
            'h_unit' => 'Ед.изм',
            'h_qty' => 'Кол-во',
            'h_price' => 'Цена',
            'h_sum' => 'Сумма',
            'total' => 'Итого',
            'delivered' => 'Сдал',
            'received' => 'Принял',
        ],

        'en' => [
            'title' => 'Receiving act',
            'from' => 'dated',
            'invoice' => 'Delivery note - Invoice',
            'accepted' => 'Accepted by',
            'supplier_from' => 'From',
            'contract' => 'Under contract',
            'proxy' => 'By power of attorney',
            'no' => '#',
            'h_name' => 'Description',
            'h_unit' => 'Unit',
            'h_qty' => 'Qty',
            'h_price' => 'Price',
            'h_sum' => 'Amount',
            'total' => 'Total',
            'delivered' => 'Delivered',
            'received' => 'Accepted',
        ],
    ][$lang];

    $money = fn ($v) => number_format((float) $v, 2, ',', ' ');
    $dash = fn ($v) => ($v === null || $v === '') ? '—' : $v;

    $qty = function ($v) {
        return number_format((float) $v, 2, ',', ' ');
    };
@endphp

    <!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: A4;
            margin: 18mm 17mm 18mm 17mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;

            font-family: "Times New Roman", Times, serif;
            font-size: 15px;
            line-height: 1.18;

            color: #000;
            background: #fff;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .header {
            width: 100%;
            margin-top: 2mm;
            margin-bottom: 3px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: 0;
            padding: 0;
            vertical-align: bottom;
        }

        .header-title {
            text-align: right;
            width: 42%;
            padding-right: 25px !important;
        }

        .header-number {
            width: 18%;
            text-align: center;
        }

        .header-date-label {
            width: 8%;
            text-align: center;
        }

        .header-date {
            width: 32%;
            text-align: left;
        }

        /*
        |--------------------------------------------------------------------------
        | META TEXT
        |--------------------------------------------------------------------------
        */

        .meta {
            margin-top: 3px;
            margin-bottom: 14px;
            font-size: 15px;
        }

        .meta-line {
            margin: 0 0 5px 0;
            white-space: nowrap;
        }

        .meta-line.invoice {
            padding-left: 17px;
        }

        .meta-line strong {
            font-weight: 700;
        }

        .invoice-number {
            display: inline-block;
            margin-left: 100px;
            font-weight: bold;
        }

        /*
        |--------------------------------------------------------------------------
        | ITEMS TABLE
        |--------------------------------------------------------------------------
        */

        table.items {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 2px solid #000;
        }

        table.items th,
        table.items td {
            border: 1px solid #000;
            padding: 5px 7px;
        }

        table.items th {
            font-size: 14px;
            line-height: 1;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        table.items td {
            font-size: 14px;
            vertical-align: middle;
        }

        .col-no {
            width: 9%;
        }

        .col-name {
            width: 34%;
        }

        .col-unit {
            width: 13%;
        }

        .col-qty {
            width: 12%;
        }

        .col-price {
            width: 16%;
        }

        .col-sum {
            width: 16%;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
            white-space: nowrap;
        }

        .number {
            text-align: center;
            font-weight: bold;
        }

        .product-name {
            line-height: 1.18;
        }

        /*
        |--------------------------------------------------------------------------
        | ROW HEIGHT
        |--------------------------------------------------------------------------
        */

        .item-row td {
            min-height: 42px;
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        .total-row td {
            font-weight: bold;
            padding-top: 3px !important;
            padding-bottom: 3px !important;
        }

        .total-label {
            text-align: center;
            font-size: 15px !important;
        }

        .total-value {
            text-align: right;
            font-size: 15px !important;
        }

        .words-row td {
            padding: 4px 8px !important;
            font-size: 14px !important;
            text-align: center;
            font-weight: bold;
            line-height: 1.1;
        }

        /*
        |--------------------------------------------------------------------------
        | SIGNATURES
        |--------------------------------------------------------------------------
        */

        .signatures {
            width: 100%;
            margin-top: 23px;
            border-collapse: collapse;
        }

        .signatures td {
            border: 0;
            width: 50%;
            padding: 0;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        .sign-line {
            display: inline-block;
            width: 145px;
            margin-left: 5px;
            border-bottom: 1px solid #000;
            transform: translateY(-3px);
        }
    </style>
</head>

<body>

{{-- ============================================================
    HEADER
============================================================ --}}

<div class="header">
    <table class="header-table">
        <tr>
            <td class="header-title">
                {{ $T['title'] }}
            </td>

            <td class="header-number">
                № {{ $dash($act->aktNumber) }}
            </td>

            <td class="header-date-label">
                {{ $T['from'] }}
            </td>

            <td class="header-date">
                {{ $dash($act->aktDate) }}г.
            </td>
        </tr>
    </table>
</div>


{{-- ============================================================
    META INFORMATION
============================================================ --}}

<div class="meta">

    {{-- Накладная - Счёт - Фактура --}}
    <div class="meta-line invoice">
        {{ $T['invoice'] }} №

        <span class="invoice-number">
                {{ $dash($act->hisobFaktura) }}
            </span>

        &nbsp;&nbsp;{{ $T['from'] }}&nbsp;

        {{ $dash($act->hisobFakturaDate) }} г.
    </div>


    {{-- Принял Зав.Складом --}}
    <div class="meta-line">
        {{ $T['accepted'] }}

        @if(!empty($act->assigneePosition))
            {{ $act->assigneePosition }}
        @endif

        &nbsp;

        {{ $dash($act->assigneeName) }}
    </div>


    {{-- От кого --}}
    <div class="meta-line">
        {{ $T['supplier_from'] }}

        <strong>
            "{{ $dash($act->supplierName) }}"
        </strong>
    </div>


    {{-- По договору --}}
    <div class="meta-line">
        {{ $T['contract'] }}

        № <strong>{{ $dash($act->contractNumber) }}</strong>

        &nbsp;&nbsp;{{ $T['from'] }}&nbsp;

        {{ $dash($act->contractDate) }}
    </div>


    {{-- Через доверенность --}}
    <div class="meta-line">
        {{ $T['proxy'] }}

        № {{ $dash($act->ishonchnomaNumber) }}

        {{ $T['from'] }}

        {{ $dash($act->ishonchnomaDate) }}г.

        @if(!empty($act->proxyPersonName))
            &nbsp;&nbsp;{{ $act->proxyPersonName }}
        @endif
    </div>

</div>


{{-- ============================================================
    ITEMS
============================================================ --}}

<table class="items">

    <thead>
    <tr>
        <th class="col-no">
            {{ $T['no'] }}
        </th>

        <th class="col-name">
            {{ $T['h_name'] }}
        </th>

        <th class="col-unit">
            {{ $T['h_unit'] }}
        </th>

        <th class="col-qty">
            {{ $T['h_qty'] }}
        </th>

        <th class="col-price">
            {{ $T['h_price'] }}
        </th>

        <th class="col-sum">
            {{ $T['h_sum'] }}
        </th>
    </tr>
    </thead>

    <tbody>

    @foreach ($act->items as $item)

        <tr class="item-row">

            <td class="number">
                {{ $item->rowNumber ?? $loop->iteration }}
            </td>

            <td class="product-name">
                {{ $item->productName }}
            </td>

            <td class="center">
                {{ $item->unitName }}
            </td>

            <td class="center">
                {{ $qty($item->quantity) }}
            </td>

            <td class="right">
                {{ $money($item->price) }}
            </td>

            <td class="right">
                {{ $money($item->sum) }}
            </td>

        </tr>

    @endforeach


    {{-- Итого --}}
    <tr class="total-row">

        <td colspan="5" class="total-label">
            {{ $T['total'] }}:
        </td>

        <td class="total-value">
            {{ $money($act->totalSum) }}
        </td>

    </tr>


    {{-- Сумма прописью --}}
    @if($act->totalSumInWords)

        <tr class="words-row">
            <td colspan="6">
                {{ $act->totalSumInWords }}
            </td>
        </tr>

    @endif

    </tbody>

</table>


{{-- ============================================================
    SIGNATURES
============================================================ --}}

<table class="signatures">
    <tr>

        <td>
            {{ $T['delivered'] }}
            <span class="sign-line"></span>
        </td>

        <td>
            {{ $T['received'] }}
            <span class="sign-line"></span>
        </td>

    </tr>
</table>

</body>
</html>
