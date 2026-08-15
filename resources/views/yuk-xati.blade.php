@php
    use App\Support\NumberToWordsUz;
    use Illuminate\Support\Str;

    $months = [1 => 'январ', 2 => 'феврал', 3 => 'март', 4 => 'апрел', 5 => 'май', 6 => 'июн',
               7 => 'июл', 8 => 'август', 9 => 'сентябр', 10 => 'октябр', 11 => 'ноябр', 12 => 'декабр'];
    $docDate = $batch->completed_at ?? $batch->created_at;

    // Jami summa (нархи × берилган)
    $total = 0;
    foreach ($batch->items as $it) {
        $total += (float) ($it->warehouse->product_price ?? 0) * (int) $it->quantity;
    }

    $money = fn ($v) => number_format((float) $v, 2, ',', ' ');
@endphp
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4; margin: 12mm 12mm 14mm 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: "Times New Roman", serif; font-size: 12px; color: #000; }

        /* ===== Header (o'ng tomonda tasdiqlash bloki) ===== */
        .approve { width: 58%; margin-left: auto; text-align: center; line-height: 1.35; }
        .approve .lead { text-align: right; margin-bottom: 2px; }
        .approve .org { font-weight: bold; }
        .approve-line { border: 0; border-top: 1px solid #000; margin: 6px 0 0; }

        /* ===== Title ===== */
        .title-row { position: relative; margin: 18px 0 12px; text-align: center; }
        .title-row .title { font-weight: bold; font-size: 14px; }
        .title-row .date { position: absolute; right: 0; top: 0; font-weight: bold; }

        /* ===== Meta (chap yorliq + o'ng qutili qiymat) ===== */
        .meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .meta td { padding: 3px 6px; vertical-align: middle; }
        .meta .lbl { width: 32%; border: 0; }
        .meta .val { border: 1px solid #000; font-weight: bold; }

        /* ===== Asosiy jadval ===== */
        table.items { width: 100%; border-collapse: collapse; }
        table.items th, table.items td { border: 1px solid #000; padding: 4px 5px; }
        table.items th { text-align: center; font-weight: bold; vertical-align: middle; }
        table.items td { vertical-align: top; }
        table.items td.c { text-align: center; vertical-align: middle; }
        table.items td.r { text-align: right; vertical-align: middle; white-space: nowrap; }
        .total-row td { font-weight: bold; }
        .total-row .total-lbl { text-align: left; }
        .words-row td { font-weight: bold; text-align: center; padding: 5px; }

        /* ===== Imzolar ===== */
        .signs { width: 100%; margin-top: 26px; border-collapse: collapse; }
        .signs td { padding: 14px 6px 0; font-weight: bold; vertical-align: bottom; width: 50%; }
        .sign-line { display: inline-block; min-width: 160px; border-bottom: 1px solid #000; margin-left: 8px; }
    </style>
</head>
<body>

    {{-- ===== Tasdiqlash bloki ===== --}}
    <div class="approve">
        <div class="lead">Тасдиқлайман:</div>
        <div class="org">Жаҳон иқтисодиёти ва дипломатия университети</div>
        <div class="org">Молия ва хўжалик масалалари бўйича ректор ўринбосари</div>
        <hr class="approve-line">
    </div>

    {{-- ===== Sarlavha + sana ===== --}}
    <div class="title-row">
        <span class="title">{{ $batch->id }} сонли ЮК ХАТИ (ТАЛАБНОМА)</span>
        <span class="date">
            @if ($docDate)
                “{{ $docDate->format('d') }}” {{ $months[(int) $docDate->format('n')] }} {{ $docDate->format('Y') }} й.
            @endif
        </span>
    </div>

    {{-- ===== Meta ===== --}}
    <table class="meta">
        <tr>
            <td class="lbl">Ташкилот - олувчининг номи</td>
            <td class="val">{{ $batch->staff->full_name ?? $batch->staff->name ?? '' }}</td>
        </tr>
        <tr>
            <td class="lbl">Таркибий бўлими</td>
            <td class="val">&nbsp;</td>
        </tr>
        <tr>
            <td class="lbl">Ташкилот (жўнатувчи)</td>
            <td class="val">Марказий омбор</td>
        </tr>
        <tr>
            <td class="lbl">Моддий жавобгар шахс</td>
            <td class="val">{{ $batch->createdBy->full_name ?? '' }}</td>
        </tr>
    </table>

    {{-- ===== Asosiy jadval ===== --}}
    <table class="items">
        <thead>
            <tr>
                <th rowspan="2" style="width:3.5%;">№</th>
                <th rowspan="2" style="width:27%;">Моддий қийматликлар-нинг<br>номи, нави ва ўлчами</th>
                <th rowspan="2" style="width:7%;">Ўл. бир-<br>лиги</th>
                <th colspan="2" style="width:16%;">Бухгалтерия ёзуви</th>
                <th colspan="2" style="width:16%;">Микдор</th>
                <th rowspan="2" style="width:13%;">Нархи, сўм</th>
                <th rowspan="2" style="width:13%;">Сумма, сўм</th>
            </tr>
            <tr>
                <th>дебет</th>
                <th>кредит</th>
                <th>талаб килин-<br>ган</th>
                <th>берил-<br>ган</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($batch->items as $i => $item)
                @php
                    $price = (float) ($item->warehouse->product_price ?? 0);
                    $line  = $price * (int) $item->quantity;
                @endphp
                <tr>
                    <td class="c">{{ $i + 1 }}</td>
                    <td>{{ $item->warehouse->name ?? '—' }}</td>
                    <td class="c">{{ $item->warehouse->unit->name ?? '' }}</td>
                    <td class="c">{{ $item->debit ?? '' }}</td>
                    <td class="c">{{ $item->kredit ?? '' }}</td>
                    <td class="c">{{ $item->talab_qilingan ?? '' }}</td>
                    <td class="c">{{ $item->quantity }}</td>
                    <td class="r">{{ $money($price) }}</td>
                    <td class="r">{{ $money($line) }}</td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td class="total-lbl" colspan="8">Итого:</td>
                <td class="r">{{ $money($total) }}</td>
            </tr>
            <tr class="words-row">
                <td colspan="9">{{ Str::ucfirst(NumberToWordsUz::money($total)) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ===== Imzolar ===== --}}
    <table class="signs">
        <tr>
            <td>Бош ҳисобчи <span class="sign-line"></span></td>
            <td>Хисобчи <span class="sign-line"></span></td>
        </tr>
        <tr>
            <td>Олдим <span class="sign-line"></span></td>
            <td>Топширдим <span class="sign-line"></span></td>
        </tr>
    </table>

</body>
</html>
