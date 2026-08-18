<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Tableau des niveaux · {{ config('app.brand') }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #0f172a; margin: 36px; text-transform: uppercase; }
        .header { display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 3px solid #2563eb; padding-bottom: 18px; margin-bottom: 24px; }
        .logo { font-size: 22px; font-weight: 800; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { border: 1px solid #e2e8f0; padding: 12px 10px; text-align: center; }
        th { background: #eff6ff; color: #1e3a8a; font-size: 11px; }
        td.level { text-align: left; font-weight: 700; }
        @media print { button { display: none; } body { margin: 20px; } }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="logo">{{ config('app.brand') }}</div>
            <div>Tableau des niveaux</div>
        </div>
        <div>{{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Niveaux</th>
                <th>Nbrs Étudiant</th>
                <th>Nbrs Profs</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($niveaux as $n)
                <tr>
                    <td class="level">{{ $n['nom'] }}</td>
                    <td>{{ $n['etudiants'] }}</td>
                    <td>{{ $n['profs'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <button onclick="window.print()" style="margin-top:24px;padding:10px 18px;border:0;border-radius:8px;background:#2563eb;color:white;font-weight:700;cursor:pointer">Imprimer</button>
</body>
</html>
