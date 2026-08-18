<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche {{ $professeur->displayId() }} · {{ config('app.brand') }}</title>
    <style>
        body{font-family:Arial,sans-serif;color:#0f172a;margin:36px;text-transform:uppercase}.header{display:flex;justify-content:space-between;border-bottom:3px solid #7c3aed;padding-bottom:18px;margin-bottom:24px}.logo{font-size:22px;font-weight:800}.id{color:#7c3aed;font-weight:700}.profile{display:flex;gap:24px;align-items:center;margin-bottom:28px}.photo{width:120px;height:120px;border:1px solid #cbd5e1;border-radius:16px;object-fit:cover;display:flex;align-items:center;justify-content:center;color:#94a3b8}.grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.field{border:1px solid #e2e8f0;border-radius:10px;padding:12px}.label{font-size:10px;text-transform:uppercase;color:#64748b;font-weight:700;margin-bottom:5px}.value{font-size:14px;font-weight:600}@media print{button{display:none}body{margin:20px}}
    </style>
</head>
<body>
    <div class="header"><div><div class="logo">{{ config('app.brand') }}</div><div>Fiche professeur</div></div><div class="id">{{ $professeur->displayId() }}</div></div>
    <div class="profile">
        @if($professeur->photo_url)<img src="{{ $professeur->photo_url }}" alt="" class="photo">@else<div class="photo">Photo</div>@endif
        <div><h1>{{ $professeur->nom_complet }}</h1><p>{{ $professeur->statutLabel() }} · {{ $professeur->etatLabel() }}</p></div>
    </div>
    <div class="grid">
        @foreach([
            'Contact' => $professeur->contact,
            'Ville' => $professeur->ville,
            'Statut' => $professeur->statutLabel(),
            'Matière' => $professeur->matiere,
            'Mode' => $professeur->paiementLabel(),
            'Paiement' => $professeur->montantDisplay(),
            'Échéance' => $professeur->periodePaiementLabel(),
            'Login' => $professeur->login,
            'Mot de passe' => $professeur->access_password,
        ] as $label => $value)
            <div class="field"><div class="label">{{ $label }}</div><div class="value">{{ $value }}</div></div>
        @endforeach
    </div>
    <button onclick="window.print()" style="margin-top:24px;padding:10px 18px;border:0;border-radius:8px;background:#7c3aed;color:white;font-weight:700;cursor:pointer">Imprimer</button>
    <script>window.addEventListener('load',()=>window.print())</script>
</body>
</html>
