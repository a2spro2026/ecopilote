<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche paiement {{ $payment->student?->displayId() }} · {{ config('app.brand') }}</title>
    <style>
        body{font-family:Arial,sans-serif;color:#0f172a;margin:36px;text-transform:uppercase}.header{display:flex;justify-content:space-between;border-bottom:3px solid #2563eb;padding-bottom:18px;margin-bottom:24px}.logo{font-size:22px;font-weight:800}.id{color:#2563eb;font-weight:700}.grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.field{border:1px solid #e2e8f0;border-radius:10px;padding:12px}.label{font-size:10px;text-transform:uppercase;color:#64748b;font-weight:700;margin-bottom:5px}.value{font-size:14px;font-weight:600}@media print{button{display:none}body{margin:20px}}
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="logo">{{ config('app.brand') }}</div>
            <div>Fiche paiement élève</div>
        </div>
        <div class="id">{{ $payment->student?->displayId() }}</div>
    </div>
    <div class="grid">
        @foreach([
            'Date' => $payment->date?->format('d/m/Y') ?: '—',
            'Nom Complet' => $payment->student?->nom_complet ?: '—',
            'Matière' => $matiereLabel,
            'Montant' => \App\Models\StudentPayment::money($payment->montant),
            'Mode' => $payment->modeLabel(),
            'Paiement' => \App\Models\StudentPayment::money($payment->montant_paye),
            'Solde' => \App\Models\StudentPayment::money($payment->solde),
        ] as $label => $value)
            <div class="field"><div class="label">{{ $label }}</div><div class="value">{{ $value }}</div></div>
        @endforeach
    </div>
    <button onclick="window.print()" style="margin-top:24px;padding:10px 18px;border:0;border-radius:8px;background:#2563eb;color:white;font-weight:700;cursor:pointer">Imprimer</button>
    <script>window.addEventListener('load',()=>window.print())</script>
</body>
</html>
