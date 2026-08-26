@php
    use App\Support\NumberToWordsUz;
    use Illuminate\Support\Str;

    $lang = $lang ?? 'uz';
    $lang = in_array($lang, ['uz', 'ru', 'en'], true) ? $lang : 'uz';

    $T = [
        'uz' => [
            'approve'  => 'Tasdiqlayman:',
            'org1'     => 'Jahon iqtisodiyoti va diplomatiya universiteti',
            'org2'     => "Moliya va xo'jalik masalalari bo'yicha rektor o'rinbosari",
            'title'    => '%s-son YUK XATI (TALABNOMA)',
            'date_suf' => 'y.',
            'm_recipient' => 'Tashkilot — oluvchining nomi',
            'm_dept'      => "Tarkibiy bo'limi",
            'm_sender'    => "Tashkilot (jo'natuvchi)",
            'm_sender_v'  => 'Markaziy ombor',
            'm_person'    => 'Moddiy javobgar shaxs',
            'h_name'   => "Moddiy qiymatliklarning nomi, navi va o'lchami",
            'h_unit'   => "O'lch. birligi",
            'h_acc'    => 'Buxgalteriya yozuvi',
            'h_debit'  => 'debet', 'h_credit' => 'kredit',
            'h_qty'    => 'Miqdor', 'h_req' => 'talab qilingan', 'h_given' => 'berilgan',
            'h_price'  => "Narxi, so'm", 'h_sum' => "Summa, so'm",
            'total'    => 'Jami:',
            's_chief'  => 'Bosh hisobchi', 's_acc' => 'Hisobchi', 's_recv' => 'Oldim', 's_deliv' => 'Topshirdim',

            'appr_title' => 'Tasdiqlash holati',
            'appr_fio' => 'F.I.O.', 'appr_position' => 'Lavozim',
            'appr_status' => 'Holat', 'appr_date' => 'Sana',
            'r_recipient' => 'Qabul qiluvchi', 'r_sender' => 'Yuboruvchi',
            'r_level' => "%d-daraja imzolovchi",
            'st_pending' => 'Kutilmoqda', 'st_active' => "Ko'rib chiqilmoqda",
            'st_approved' => 'Tasdiqladi', 'st_rejected' => 'Rad etdi',
            'st_sent' => 'Yubordi', 'st_received' => 'Qabul qildi',
        ],
        'ru' => [
            'approve'  => 'Утверждаю:',
            'org1'     => 'Университет мировой экономики и дипломатии',
            'org2'     => 'Проректор по финансово-хозяйственным вопросам',
            'title'    => 'НАКЛАДНАЯ (ТРЕБОВАНИЕ) № %s',
            'date_suf' => 'г.',
            'm_recipient' => 'Организация-получатель',
            'm_dept'      => 'Структурное подразделение',
            'm_sender'    => 'Организация (отправитель)',
            'm_sender_v'  => 'Центральный склад',
            'm_person'    => 'Материально ответственное лицо',
            'h_name'   => 'Наименование, сорт и размер ТМЦ',
            'h_unit'   => 'Ед. изм.',
            'h_acc'    => 'Бухгалтерская запись',
            'h_debit'  => 'дебет', 'h_credit' => 'кредит',
            'h_qty'    => 'Количество', 'h_req' => 'затребовано', 'h_given' => 'отпущено',
            'h_price'  => 'Цена, сум', 'h_sum' => 'Сумма, сум',
            'total'    => 'Итого:',
            's_chief'  => 'Главный бухгалтер', 's_acc' => 'Бухгалтер', 's_recv' => 'Получил', 's_deliv' => 'Сдал',

            'appr_title' => 'Статус подтверждения',
            'appr_fio' => 'Ф.И.О.', 'appr_position' => 'Должность',
            'appr_status' => 'Статус', 'appr_date' => 'Дата',
            'r_recipient' => 'Получатель', 'r_sender' => 'Отправитель',
            'r_level' => 'Подписант %d-го уровня',
            'st_pending' => 'Ожидает', 'st_active' => 'На рассмотрении',
            'st_approved' => 'Подтвердил', 'st_rejected' => 'Отклонил',
            'st_sent' => 'Отправил', 'st_received' => 'Принял',
        ],
        'en' => [
            'approve'  => 'Approved:',
            'org1'     => 'University of World Economy and Diplomacy',
            'org2'     => 'Vice-Rector for Financial and Economic Affairs',
            'title'    => 'WAYBILL (REQUISITION) № %s',
            'date_suf' => '',
            'm_recipient' => 'Recipient organization',
            'm_dept'      => 'Department',
            'm_sender'    => 'Organization (sender)',
            'm_sender_v'  => 'Central warehouse',
            'm_person'    => 'Responsible person',
            'h_name'   => 'Name, grade and size of goods',
            'h_unit'   => 'Unit',
            'h_acc'    => 'Accounting entry',
            'h_debit'  => 'debit', 'h_credit' => 'credit',
            'h_qty'    => 'Quantity', 'h_req' => 'requested', 'h_given' => 'issued',
            'h_price'  => 'Price, sum', 'h_sum' => 'Amount, sum',
            'total'    => 'Total:',
            's_chief'  => 'Chief accountant', 's_acc' => 'Accountant', 's_recv' => 'Received', 's_deliv' => 'Delivered',

            'appr_title' => 'Approval status',
            'appr_fio' => 'Full name', 'appr_position' => 'Position',
            'appr_status' => 'Status', 'appr_date' => 'Date',
            'r_recipient' => 'Recipient', 'r_sender' => 'Sender',
            'r_level' => 'Level %d signer',
            'st_pending' => 'Pending', 'st_active' => 'In review',
            'st_approved' => 'Approved', 'st_rejected' => 'Rejected',
            'st_sent' => 'Sent', 'st_received' => 'Accepted',
        ],
    ][$lang];

    $MONTHS = [
        'uz' => [1 => 'yanvar', 2 => 'fevral', 3 => 'mart', 4 => 'aprel', 5 => 'may', 6 => 'iyun',
                 7 => 'iyul', 8 => 'avgust', 9 => 'sentabr', 10 => 'oktabr', 11 => 'noyabr', 12 => 'dekabr'],
        'ru' => [1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля', 5 => 'мая', 6 => 'июня',
                 7 => 'июля', 8 => 'августа', 9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'],
        'en' => [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
                 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'],
    ][$lang];

    $docDate = $batch->completed_at ?? $batch->created_at;
    $dateStr = null;
    if ($docDate) {
        $d = $docDate->format('d');
        $mo = $MONTHS[(int) $docDate->format('n')];
        $y = $docDate->format('Y');
        $dateStr = $lang === 'en'
            ? trim("{$mo} {$d}, {$y}")
            : trim("“{$d}” {$mo} {$y} {$T['date_suf']}");
    }

    $recipient = $batch->entries->first();

    $total = 0;
    foreach ($batch->entries as $it) {
        $total += (float) ($it->warehouse->product_price ?? 0) * (int) $it->quantity;
    }

    $money = fn ($v) => number_format((float) $v, 2, ',', ' ');
    $dash  = fn ($v) => ($v === null || $v === '') ? '—' : $v;

    $accountantRoles = [$T['s_chief'], $T['s_acc']];

    $approvals = $batch->signers->values()->map(function ($signer, int $index) use ($accountantRoles, $T) {
        return [
            'name'     => $signer->user?->full_name ?: $signer->user?->name,
            'position' => $signer->role_label
                ?: ($accountantRoles[$index] ?? sprintf($T['r_level'], $signer->level)),
            'status'   => $signer->status?->value,
            'at'       => $signer->responded_at,
        ];
    });

    $approvals->push([
        'name'     => $recipient->full_name ?? null,
        'position' => $T['r_recipient'],
        'status'   => $batch->completed_at ? 'received' : null,
        'at'       => $batch->completed_at,
    ]);

    $approvals->push([
        'name'     => $batch->createdBy?->full_name ?: $batch->createdBy?->name,
        'position' => $T['r_sender'],
        'status'   => $batch->created_at ? 'sent' : null,
        'at'       => $batch->created_at,
    ]);
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4; margin: 12mm 12mm 14mm 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: "Times New Roman", serif; font-size: 12px; color: #000; }

        .approve { width: 58%; margin-left: auto; text-align: center; line-height: 1.35; }
        .approve .lead { text-align: right; margin-bottom: 2px; }
        .approve .org { font-weight: bold; }
        .approve-line { border: 0; border-top: 1px solid #000; margin: 6px 0 0; }

        .title-row { position: relative; margin: 18px 0 12px; text-align: center; }
        .title-row .title { font-weight: bold; font-size: 14px; }
        .title-row .date { position: absolute; right: 0; top: 0; font-weight: bold; }

        .meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .meta td { padding: 3px 6px; vertical-align: middle; }
        .meta .lbl { width: 32%; border: 0; }
        .meta .val { border: 1px solid #000; font-weight: bold; }

        table.items { width: 100%; border-collapse: collapse; }
        table.items th, table.items td { border: 1px solid #000; padding: 4px 5px; }
        table.items th { text-align: center; font-weight: bold; vertical-align: middle; }
        table.items td { vertical-align: top; }
        table.items td.c { text-align: center; vertical-align: middle; }
        table.items td.r { text-align: right; vertical-align: middle; white-space: nowrap; }
        .total-row td { font-weight: bold; }
        .total-row .total-lbl { text-align: left; }
        .words-row td { font-weight: bold; text-align: center; padding: 5px; }

        .signs { width: 100%; margin-top: 26px; border-collapse: collapse; }
        .signs td { padding: 14px 6px 0; font-weight: bold; vertical-align: bottom; width: 50%; }
        .sign-line { display: inline-block; min-width: 160px; border-bottom: 1px solid #000; margin-left: 8px; }

        .approvals-block { margin-top: 22px; page-break-inside: avoid; }
        .approvals-title { margin-bottom: 6px; text-align: center; font-weight: bold; font-size: 13px; }
        table.approvals { width: 100%; border-collapse: collapse; table-layout: fixed; border: 1px solid #000; }
        table.approvals th, table.approvals td { border: 1px solid #000; padding: 5px 7px; font-size: 12px; vertical-align: middle; }
        table.approvals th { text-align: center; font-weight: bold; }
        .appr-no { width: 6%; text-align: center; }
        .appr-fio { width: 29%; }
        .appr-position { width: 31%; }
        .appr-status { width: 16%; text-align: center; font-style: italic; }
        .appr-date { width: 18%; padding-left: 9px !important; padding-right: 9px !important; text-align: center; white-space: nowrap; line-height: 1.3; }
    </style>
</head>
<body>

    {{-- ===== Tasdiqlash bloki ===== --}}
    <div class="approve">
        <div class="lead">{{ $T['approve'] }}</div>
        <div class="org">{{ $T['org1'] }}</div>
        <div class="org">{{ $T['org2'] }}</div>
        <hr class="approve-line">
    </div>

    {{-- ===== Sarlavha + sana ===== --}}
    <div class="title-row">
        <span class="title">{{ sprintf($T['title'], $batch->id) }}</span>
        <span class="date">@if ($dateStr){{ $dateStr }}@endif</span>
    </div>

    {{-- ===== Meta ===== --}}
    <table class="meta">
        <tr>
            <td class="lbl">{{ $T['m_recipient'] }}</td>
            <td class="val">{{ $recipient->full_name ?? '' }}</td>
        </tr>
        <tr>
            <td class="lbl">{{ $T['m_dept'] }}</td>
            <td class="val">{{ $recipient->department ?? '' }}&nbsp;</td>
        </tr>
        <tr>
            <td class="lbl">{{ $T['m_sender'] }}</td>
            <td class="val">{{ $T['m_sender_v'] }}</td>
        </tr>
        <tr>
            <td class="lbl">{{ $T['m_person'] }}</td>
            <td class="val">{{ $batch->createdBy->full_name ?? '' }}</td>
        </tr>
    </table>

    {{-- ===== Asosiy jadval ===== --}}
    <table class="items">
        <thead>
            <tr>
                <th rowspan="2" style="width:3.5%;">№</th>
                <th rowspan="2" style="width:27%;">{{ $T['h_name'] }}</th>
                <th rowspan="2" style="width:7%;">{{ $T['h_unit'] }}</th>
                <th colspan="2" style="width:16%;">{{ $T['h_acc'] }}</th>
                <th colspan="2" style="width:16%;">{{ $T['h_qty'] }}</th>
                <th rowspan="2" style="width:13%;">{{ $T['h_price'] }}</th>
                <th rowspan="2" style="width:13%;">{{ $T['h_sum'] }}</th>
            </tr>
            <tr>
                <th>{{ $T['h_debit'] }}</th>
                <th>{{ $T['h_credit'] }}</th>
                <th>{{ $T['h_req'] }}</th>
                <th>{{ $T['h_given'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($batch->entries as $i => $item)
                @php
                    $price = (float) ($item->warehouse->product_price ?? 0);
                    $line  = $price * (int) $item->quantity;
                @endphp
                <tr>
                    <td class="c">{{ $i + 1 }}</td>
                    <td>{{ $item->name ?? $item->warehouse->name ?? '—' }}</td>
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
                <td class="total-lbl" colspan="8">{{ $T['total'] }}</td>
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
             <td>{{ $T['s_chief'] }} <span class="sign-line"></span></td>
             <td>{{ $T['s_acc'] }} <span class="sign-line"></span></td>
         </tr>
         <tr>
             <td>{{ $T['s_recv'] }} <span class="sign-line"></span></td>
             <td>{{ $T['s_deliv'] }} <span class="sign-line"></span></td>
         </tr>
     </table>

    {{-- ===== Tasdiqlash holati ===== --}}

    {{-- ===== Imzolar (tasdiqlash holati jadvali) ===== --}}
    {{--@if ($approvals->isNotEmpty())
        <div class="approvals-block">
            <div class="approvals-title">{{ $T['appr_title'] }}</div>

            <table class="approvals">
                <thead>
                    <tr>
                        <th class="appr-no">№</th>
                        <th class="appr-fio">{{ $T['appr_fio'] }}</th>
                        <th class="appr-position">{{ $T['appr_position'] }}</th>
                        <th class="appr-status">{{ $T['appr_status'] }}</th>
                        <th class="appr-date">{{ $T['appr_date'] }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($approvals as $approval)
                        <tr>
                            <td class="appr-no">{{ $loop->iteration }}</td>
                            <td class="appr-fio">{{ $dash($approval['name']) }}</td>
                            <td class="appr-position">{{ $dash($approval['position']) }}</td>
                            <td class="appr-status">
                                {{ $approval['status'] ? ($T['st_'.$approval['status']] ?? $approval['status']) : '—' }}
                            </td>
                            <td class="appr-date">
                                @if ($approval['at'])
                                    {{ $approval['at']->format('d.m.Y') }}
                                    <div>{{ $approval['at']->format('H:i') }}</div>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif--}}

</body>
</html>
