<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Tableau des matières · {{ config('app.brand') }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #0f172a; margin: 36px; text-transform: uppercase; }
        .header { display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 3px solid #2563eb; padding-bottom: 18px; margin-bottom: 24px; }
        .logo { font-size: 22px; font-weight: 800; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px 8px; text-align: center; }
        th { background: #eff6ff; color: #1e3a8a; font-size: 10px; }
        td.subject { text-align: left; font-weight: 700; }
        .flag { display: inline-flex; width: 22px; height: 16px; margin-right: 8px; vertical-align: middle; border: 1px solid #cbd5e1; border-radius: 3px; overflow: hidden; align-items: center; justify-content: center; }
        .flag svg { width: 100%; height: 100%; display: block; }
        @media print { button { display: none; } body { margin: 20px; } }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="logo">{{ config('app.brand') }}</div>
            <div>Tableau des matières</div>
        </div>
        <div>{{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Matière</th>
                <th>Nbrs Profs</th>
                <th>Nbrs Étudiant</th>
                <th>Nbrs H/mois</th>
                <th>Revenue</th>
                <th>Paiement</th>
                <th>Bénéfice</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($matieres as $m)
                <tr>
                    <td class="subject">
                        @if (! empty($m['flag']))
                            <span class="flag">@include('admin.subjects.flag', ['code' => $m['flag']])</span>
                        @elseif (($m['icon'] ?? null) === 'math')
                            <span class="flag" style="border:0;font-weight:800;line-height:16px;text-align:center">√x</span>
                        @elseif (($m['icon'] ?? null) === 'science')
                            <span class="flag" style="border:0;font-weight:800;line-height:16px;text-align:center">⚗</span>
                        @elseif (($m['icon'] ?? null) === 'leaf')
                            <span class="flag" style="border:0;font-weight:800;line-height:16px;text-align:center">🌿</span>
                        @elseif (($m['icon'] ?? null) === 'globe')
                            <span class="flag" style="border:0;font-weight:800;line-height:16px;text-align:center">🌍</span>
                        @elseif (($m['icon'] ?? null) === 'code')
                            <span class="flag" style="border:0;font-weight:800;line-height:16px;text-align:center">&lt;/&gt;</span>
                        @endif
                        {{ \App\Support\SubjectAbbreviation::display($m['nom']) }}
                    </td>
                    <td>{{ $m['profs'] }}</td>
                    <td>{{ $m['etudiants'] }}</td>
                    <td>{{ number_format($m['heures_mois'], 0, ',', ' ') }} h</td>
                    <td>{{ number_format($m['revenue'], 0, ',', ' ') }} MAD</td>
                    <td>{{ number_format($m['paiement'], 0, ',', ' ') }} MAD</td>
                    <td>{{ number_format($m['benefice'], 0, ',', ' ') }} MAD</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <button onclick="window.print()" style="margin-top:24px;padding:10px 18px;border:0;border-radius:8px;background:#2563eb;color:white;font-weight:700;cursor:pointer">Imprimer</button>
</body>
</html>
