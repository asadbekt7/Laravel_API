@php
    $lang = $lang ?? 'ru';
    $lang = in_array($lang, ['uz', 'ru', 'en'], true) ? $lang : 'ru';

    $T = [
        'uz' => [
            'title' => 'QABUL AKTI', 'from' => 'sana', 'supplier' => 'Yetkazib beruvchi',
            'contract' => 'Shartnoma', 'invoice' => 'Hisob-faktura', 'proxy' => 'Ishonchnoma',
            'accepted' => 'Qabul qildi', 'no' => '№',
            'h_name' => 'Mahsulot nomi', 'h_unit' => "O'lch. bir.", 'h_qty' => 'Miqdor',
            'h_price' => "Narxi, so'm", 'h_sum' => "Summa, so'm",
            'total' => 'Jami', 'in_words' => 'Summa yozuv bilan', 'delivered' => 'Topshirdi', 'received' => 'Qabul qildi',
        ],
        'ru' => [
            'title' => 'ПРИЁМНЫЙ АКТ', 'from' => 'от', 'supplier' => 'Поставщик',
            'contract' => 'Договор', 'invoice' => 'Счёт-фактура', 'proxy' => 'Доверенность',
            'accepted' => 'Принял', 'no' => '№',
            'h_name' => 'Наименование товара', 'h_unit' => 'Ед. изм.', 'h_qty' => 'Кол-во',
            'h_price' => 'Цена, сум', 'h_sum' => 'Сумма, сум',
            'total' => 'Итого', 'in_words' => 'Сумма прописью', 'delivered' => 'Сдал', 'received' => 'Принял',
        ],
        'en' => [
            'title' => 'RECEIVING ACT', 'from' => 'dated', 'supplier' => 'Supplier',
            'contract' => 'Contract', 'invoice' => 'Invoice', 'proxy' => 'Power of attorney',
            'accepted' => 'Accepted by', 'no' => '#',
            'h_name' => 'Product name', 'h_unit' => 'Unit', 'h_qty' => 'Qty',
            'h_price' => 'Price, sum', 'h_sum' => 'Amount, sum',
            'total' => 'Total', 'in_words' => 'Amount in words', 'delivered' => 'Delivered by', 'received' => 'Received by',
        ],
    ][$lang];

    $money = fn ($v) => number_format((float) $v, 2, ',', ' ');
    $dash  = fn ($v) => ($v === null || $v === '') ? '—' : $v;
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4; margin: 12mm 12mm 14mm 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: "Times New Roman", serif; font-size: 12px; color: #000; }

        .title-row { position: relative; margin: 6px 0 14px; text-align: center; }
        .title-row .title { font-weight: bold; font-size: 15px; letter-spacing: 0.4px; }
        .title-row .date { position: absolute; right: 0; top: 2px; font-weight: bold; }

        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.meta td { padding: 3px 6px; vertical-align: middle; }
        table.meta .lbl { width: 26%; border: 0; }
        table.meta .val { border: 1px solid #000; font-weight: bold; }

        table.items { width: 100%; border-collapse: collapse; }
        table.items th, table.items td { border: 1px solid #000; padding: 4px 5px; }
        table.items th { text-align: center; font-weight: bold; vertical-align: middle; }
        table.items td { vertical-align: top; }
        table.items td.c { text-align: center; vertical-align: middle; }
        table.items td.r { text-align: right; vertical-align: middle; white-space: nowrap; }
        .total-row td { font-weight: bold; }
        .total-row .total-lbl { text-align: right; }
        .words-row td { font-weight: bold; padding: 5px; }

        table.signs { width: 100%; margin-top: 30px; border-collapse: collapse; }
        table.signs td { padding: 16px 6px 0; font-weight: bold; vertical-align: bottom; width: 50%; }
        .sign-line { display: inline-block; min-width: 170px; border-bottom: 1px solid #000; margin-left: 8px; }
    </style>
</head>
<body>

    {{-- ===== Sarlavha + sana ===== --}}
    <div class="title-row">
        <span class="title">{{ $T['title'] }} № {{ $act->aktNumber }}</span>
        <span class="date">@if ($act->aktDate) {{ $T['from'] }} {{ $act->aktDate }} @endif</span>
    </div>

    {{-- ===== Meta ===== --}}
    <table class="meta">
        <tr>
            <td class="lbl">{{ $T['supplier'] }}</td>
            <td class="val" colspan="3">{{ $dash($act->supplierName) }}</td>
        </tr>
        <tr>
            <td class="lbl">{{ $T['contract'] }}</td>
            <td class="val">№ {{ $dash($act->contractNumber) }}</td>
            <td class="lbl" style="width:14%;">{{ $T['from'] }}</td>
            <td class="val">{{ $dash($act->contractDate) }}</td>
        </tr>
        <tr>
            <td class="lbl">{{ $T['invoice'] }}</td>
            <td class="val">№ {{ $dash($act->hisobFaktura) }}</td>
            <td class="lbl" style="width:14%;">{{ $T['from'] }}</td>
            <td class="val">{{ $dash($act->hisobFakturaDate) }}</td>
        </tr>
        <tr>
            <td class="lbl">{{ $T['proxy'] }}</td>
            <td class="val">№ {{ $dash($act->ishonchnomaNumber) }}</td>
            <td class="lbl" style="width:14%;">{{ $T['from'] }}</td>
            <td class="val">{{ $dash($act->ishonchnomaDate) }}</td>
        </tr>
        <tr>
            <td class="lbl">{{ $T['accepted'] }}</td>
            <td class="val" colspan="3">{{ $dash($act->assigneeName) }}</td>
        </tr>
    </table>

    {{-- ===== Asosiy jadval ===== --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:4%;">{{ $T['no'] }}</th>
                <th>{{ $T['h_name'] }}</th>
                <th style="width:9%;">{{ $T['h_unit'] }}</th>
                <th style="width:10%;">{{ $T['h_qty'] }}</th>
                <th style="width:16%;">{{ $T['h_price'] }}</th>
                <th style="width:18%;">{{ $T['h_sum'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($act->items as $item)
                <tr>
                    <td class="c">{{ $item->rowNumber }}</td>
                    <td>{{ $item->productName }}</td>
                    <td class="c">{{ $item->unitName }}</td>
                    <td class="c">{{ $item->quantity }}</td>
                    <td class="r">{{ $money($item->price) }}</td>
                    <td class="r">{{ $money($item->sum) }}</td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td class="total-lbl" colspan="5">{{ $T['total'] }}:</td>
                <td class="r">{{ $money($act->totalSum) }}</td>
            </tr>
            @if ($act->totalSumInWords)
                <tr class="words-row">
                    <td colspan="6">{{ $T['in_words'] }}: {{ $act->totalSumInWords }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- ===== Imzolar ===== --}}
    <table class="signs">
        <tr>
            <td>{{ $T['delivered'] }} <span class="sign-line"></span></td>
            <td>{{ $T['received'] }} <span class="sign-line"></span></td>
        </tr>
    </table>

</body>
</html>
