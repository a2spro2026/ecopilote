@extends('admin.layout')

@section('title', 'Fiche paiement')
@section('heading', 'Fiche paiement')
@section('subtitle', 'Versements et soldes des élèves')

@section('content')
@php
    $showForm = $errors->any();
@endphp

@if (session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
        <ul class="list-disc space-y-1 pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div id="fichePaiementTable" class="{{ $showForm ? 'hidden' : '' }} w-full min-h-[calc(100vh-10rem)] overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Fiche paiement</h2>
            <p class="text-sm text-slate-500"><span id="paiementCount">{{ $payments->count() }}</span> ligne(s)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" id="fichePaiementAdd" class="rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20">
                Ajouter
            </button>
            <a href="{{ route('admin.page.eleves') }}" data-window-close class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                Fermer
            </a>
        </div>
    </div>

    <div class="grid gap-2 border-b border-slate-200 px-5 py-3 dark:border-slate-800 sm:grid-cols-3 sm:px-6">
        <label class="block">
            <span class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Mois</span>
            <select id="filterMois" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                @foreach ($months as $month)
                    <option value="{{ $month['value'] }}" @selected($month['value'] === $currentMonth)>{{ $month['label'] }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Nom</span>
            <input id="filterNom" type="search" placeholder="Rechercher un nom…"
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
        </label>
        <label class="block">
            <span class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Montant</span>
            <input id="filterMontant" type="search" placeholder="Ex. 1200"
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
        </label>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="ep-table min-w-[1280px] w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[9%]">
                <col class="w-[8%]">
                <col class="w-[16%]">
                <col class="w-[9%]">
                <col class="w-[10%]">
                <col class="w-[10%]">
                <col class="w-[10%]">
                <col class="w-[10%]">
                <col class="w-[18%]">
            </colgroup>
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="!px-1">ID</th>
                    <th>Nom Complet</th>
                    <th>Matière</th>
                    <th>Montant</th>
                    <th>Mode</th>
                    <th>Paiement</th>
                    <th>Solde</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($payments as $p)
                    @php $eleve = $p->student; @endphp
                    <tr class="paiement-row hover:bg-slate-50/80 dark:hover:bg-slate-800/40"
                        data-mois="{{ $p->date?->format('Y-m') }}"
                        data-nom="{{ $eleve?->nom_complet }}"
                        data-montant="{{ $p->montant }} {{ $p->montant_paye }}">
                        <td class="text-[12px] text-slate-600 dark:text-slate-300">{{ $p->date?->format('d/m/Y') ?: '—' }}</td>
                        <td class="!px-1 text-[11px] font-semibold text-slate-900 dark:text-white">{{ $eleve?->displayId() ?: '—' }}</td>
                        <td class="truncate font-medium text-slate-800 dark:text-slate-100" title="{{ $eleve?->nom_complet }}">{{ $eleve?->nom_complet ?: '—' }}</td>
                        <td class="text-[12px] font-semibold text-slate-700 dark:text-slate-200" title="{{ $p->matiere }}">{{ \App\Support\SubjectAbbreviation::display($p->matiere) }}</td>
                        <td class="font-semibold text-slate-800 dark:text-slate-100">{{ \App\Models\StudentPayment::money($p->montant) }}</td>
                        <td class="text-slate-600 dark:text-slate-300">{{ $p->modeLabel() }}</td>
                        <td class="font-semibold text-emerald-700 dark:text-emerald-300">{{ \App\Models\StudentPayment::money($p->montant_paye) }}</td>
                        <td class="font-semibold {{ (float) $p->solde > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-800 dark:text-slate-100' }}">{{ \App\Models\StudentPayment::money($p->solde) }}</td>
                        <td>
                            <div class="flex flex-nowrap items-center justify-center gap-1.5">
                                <button type="button" data-view-payment="{{ $p->id }}" title="Voir" aria-label="Voir"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    </svg>
                                </button>
                                <button type="button" data-edit-payment="{{ $p->id }}" title="Modifier" aria-label="Modifier"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white transition hover:bg-blue-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                    </svg>
                                </button>
                                <a href="{{ route('admin.students.payments.print', $p) }}" target="_blank" rel="noopener" title="Imprimer" aria-label="Imprimer"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-white transition hover:bg-emerald-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829h10.56M6.72 17.443h10.56M6.72 21h10.56A1.72 1.72 0 0 0 19 19.28V9.5H5v9.78A1.72 1.72 0 0 0 6.72 21ZM7 5V3h10v2m2 0H5a3 3 0 0 0-3 3v5h3V9.5h14V13h3V8a3 3 0 0 0-3-3Z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="paiementNone">
                        <td colspan="9" class="!py-14 text-center text-sm text-slate-500">Aucune fiche paiement. Cliquez sur Ajouter pour enregistrer un versement.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p id="paiementEmpty" class="hidden px-6 py-14 text-center text-sm text-slate-500">Aucun résultat pour ces filtres.</p>
</div>

<form id="fichePaiementPanel" method="POST" action="{{ route('admin.students.payments.store') }}"
      class="{{ $showForm ? '' : 'hidden' }} min-h-[calc(100vh-8rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    @csrf
    <input type="hidden" name="payment_id" id="paymentId" value="{{ old('payment_id') }}">

    <div class="border-b border-slate-200 bg-gradient-to-r from-blue-600 to-emerald-500 px-6 py-5 text-white dark:border-slate-800">
        <h2 id="fichePaiementPanelTitle" class="text-lg font-extrabold" style="font-family:'Poppins',sans-serif;">Nouvelle fiche paiement</h2>
        <p class="mt-1 text-sm text-blue-50">Saisissez la date, l’élève, le montant dû et le versement.</p>
    </div>

    <div class="grid gap-6 p-6 sm:p-8 sm:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Date</label>
            <input id="paymentDate" name="date" type="date" value="{{ old('date', now()->format('Y-m-d')) }}" required
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Élève</label>
            <select id="paymentStudent" name="student_id" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                <option value="">Sélectionner…</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>
                        {{ $student->displayId() }} · {{ $student->nom_complet }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Matière</label>
            <select id="paymentSubject" name="matiere" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                <option value="">Sélectionner…</option>
                @foreach ($matieres as $matiere)
                    <option value="{{ $matiere }}" @selected(old('matiere') === $matiere)>{{ \App\Support\SubjectAbbreviation::display($matiere) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Mode</label>
            <select id="paymentMode" name="mode_paiement" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                <option value="">Sélectionner</option>
                <option value="virement" @selected(old('mode_paiement') === 'virement')>Virement</option>
                <option value="cheque" @selected(old('mode_paiement') === 'cheque')>Chèque</option>
                <option value="especes" @selected(old('mode_paiement') === 'especes')>Espèces</option>
                <option value="versement" @selected(old('mode_paiement') === 'versement')>Versement</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Montant</label>
            <input id="paymentDue" type="number" name="montant" value="{{ old('montant') }}" min="0" step="0.01" required
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Paiement</label>
            <input id="paymentPaid" type="number" name="montant_paye" value="{{ old('montant_paye') }}" min="0" step="0.01" required
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Solde</label>
            <input id="paymentBalance" type="text" readonly
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-800 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <button type="button" id="fichePaiementCancel" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Fermer</button>
        <button type="submit" class="rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20">Valider</button>
    </div>
</form>

<section id="fichePaiementView" class="hidden min-h-[calc(100vh-8rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 bg-gradient-to-r from-slate-800 to-blue-600 px-6 py-5 text-white dark:border-slate-800">
        <h2 class="text-lg font-extrabold" style="font-family:'Poppins',sans-serif;">Fiche paiement</h2>
        <p class="mt-1 text-sm text-blue-50">Consultez le versement, imprimez-le ou revenez au tableau.</p>
    </div>
    <dl id="viewPaymentFields" class="grid gap-3 p-6 sm:grid-cols-2 sm:p-8"></dl>
    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <button type="button" id="viewPaymentClose" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Fermer</button>
        <a id="viewPaymentPrint" href="#" target="_blank" rel="noopener" class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">Imprimer</a>
    </div>
</section>
@endsection

@push('scripts')
@php
    $paymentsPayload = $payments->map(fn ($payment) => [
        'id' => $payment->id,
        'studentId' => $payment->student_id,
        'date' => $payment->date?->format('Y-m-d'),
        'dateLabel' => $payment->date?->format('d/m/Y'),
        'code' => $payment->student?->displayId(),
        'name' => $payment->student?->nom_complet,
        'matiere' => $payment->matiere,
        'matiereLabel' => \App\Support\SubjectAbbreviation::display($payment->matiere),
        'montant' => $payment->montant,
        'montantLabel' => \App\Models\StudentPayment::money($payment->montant),
        'mode' => $payment->mode_paiement,
        'modeLabel' => $payment->modeLabel(),
        'paid' => $payment->montant_paye,
        'paidLabel' => \App\Models\StudentPayment::money($payment->montant_paye),
        'solde' => $payment->solde,
        'soldeLabel' => \App\Models\StudentPayment::money($payment->solde),
        'printUrl' => route('admin.students.payments.print', $payment),
    ])->values();

    $studentsPayload = $students->map(fn ($student) => [
        'id' => $student->id,
        'subjects' => array_values(array_filter(array_map('trim', preg_split('/[,;\/|]+/', (string) $student->matiere) ?: []))),
        'montant' => $student->paiement,
        'mode' => $student->mode_paiement,
    ])->values();
@endphp
<script>
(() => {
    const payments = @json($paymentsPayload);
    const students = @json($studentsPayload);
    const table = document.getElementById('fichePaiementTable');
    const panel = document.getElementById('fichePaiementPanel');
    const viewPanel = document.getElementById('fichePaiementView');
    const hiddenId = document.getElementById('paymentId');
    const title = document.getElementById('fichePaiementPanelTitle');
    const studentSelect = document.getElementById('paymentStudent');
    const subjectSelect = document.getElementById('paymentSubject');
    const dueInput = document.getElementById('paymentDue');
    const paidInput = document.getElementById('paymentPaid');
    const balanceInput = document.getElementById('paymentBalance');
    const allSubjectOptions = [...subjectSelect.options].map(opt => ({ value: opt.value, label: opt.textContent }));

    const fold = value => String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();

    const money = value => {
        const amount = Number(value);
        return Number.isFinite(amount) ? amount.toFixed(2) : '';
    };

    const updateBalance = () => {
        const due = Number(dueInput.value || 0);
        const paid = Number(paidInput.value || 0);
        balanceInput.value = money(due - paid);
    };

    const fillSubjects = (student, selected) => {
        const studentSubjects = student?.subjects || [];
        const wanted = studentSubjects.filter(name => allSubjectOptions.some(opt => opt.value === name));
        const allowed = wanted.length ? wanted : allSubjectOptions.map(opt => opt.value).filter(Boolean);
        subjectSelect.innerHTML = allSubjectOptions
            .filter(opt => !opt.value || allowed.includes(opt.value))
            .map(opt => `<option value="${opt.value}">${opt.label}</option>`)
            .join('');
        if (selected && allowed.includes(selected)) {
            subjectSelect.value = selected;
        }
    };

    const applyStudentDefaults = (keepSubject = '') => {
        const student = students.find(item => item.id === Number(studentSelect.value));
        fillSubjects(student, keepSubject);
        if (!hiddenId.value && student) {
            if (student.montant != null && student.montant !== '') {
                dueInput.value = money(student.montant);
            }
            if (student.mode) {
                document.getElementById('paymentMode').value = student.mode;
            }
        }
        updateBalance();
    };

    const resetForm = () => {
        hiddenId.value = '';
        title.textContent = 'Nouvelle fiche paiement';
        document.getElementById('paymentDate').value = new Date().toISOString().slice(0, 10);
        studentSelect.value = '';
        document.getElementById('paymentMode').value = '';
        dueInput.value = '';
        paidInput.value = '';
        fillSubjects(null, '');
        updateBalance();
    };

    const loadPayment = payment => {
        hiddenId.value = payment.id;
        title.textContent = 'Modifier la fiche paiement';
        document.getElementById('paymentDate').value = payment.date ?? '';
        studentSelect.value = String(payment.studentId ?? '');
        document.getElementById('paymentMode').value = payment.mode ?? '';
        dueInput.value = money(payment.montant);
        paidInput.value = money(payment.paid);
        const student = students.find(item => item.id === Number(payment.studentId));
        fillSubjects(student, payment.matiere);
        updateBalance();
    };

    const escapeHtml = value => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');

    const field = (label, value) => `
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800">
            <dt class="text-xs text-slate-500">${escapeHtml(label)}</dt>
            <dd class="text-sm font-medium text-slate-800 dark:text-slate-100">${escapeHtml(value || '—')}</dd>
        </div>`;

    const openView = payment => {
        document.getElementById('viewPaymentFields').innerHTML = [
            field('Date', payment.dateLabel),
            field('ID', payment.code),
            field('Nom Complet', payment.name),
            field('Matière', payment.matiereLabel),
            field('Montant', payment.montantLabel),
            field('Mode', payment.modeLabel),
            field('Paiement', payment.paidLabel),
            field('Solde', payment.soldeLabel),
        ].join('');
        document.getElementById('viewPaymentPrint').href = payment.printUrl;
        table.classList.add('hidden');
        panel.classList.add('hidden');
        viewPanel.classList.remove('hidden');
    };

    const closeView = () => {
        viewPanel.classList.add('hidden');
        table.classList.remove('hidden');
    };

    const openPanel = payment => {
        viewPanel.classList.add('hidden');
        if (payment) {
            loadPayment(payment);
        } else {
            resetForm();
        }
        table.classList.add('hidden');
        panel.classList.remove('hidden');
        document.getElementById('paymentDate').focus();
    };

    const closePanel = () => {
        panel.classList.add('hidden');
        table.classList.remove('hidden');
    };

    const applyFilters = () => {
        const mois = document.getElementById('filterMois')?.value || '';
        const nom = fold(document.getElementById('filterNom')?.value);
        const montant = fold(document.getElementById('filterMontant')?.value).replace(',', '.');
        const rows = [...document.querySelectorAll('.paiement-row')];
        let visible = 0;

        rows.forEach(row => {
            const matchMois = !mois || row.dataset.mois === mois;
            const matchNom = !nom || fold(row.dataset.nom).includes(nom);
            const matchMontant = !montant || fold(row.dataset.montant).includes(montant);
            const show = matchMois && matchNom && matchMontant;
            row.classList.toggle('hidden', !show);
            if (show) visible += 1;
        });

        const countEl = document.getElementById('paiementCount');
        const emptyEl = document.getElementById('paiementEmpty');
        const noneEl = document.getElementById('paiementNone');
        if (countEl) countEl.textContent = String(visible);
        emptyEl?.classList.toggle('hidden', visible !== 0 || rows.length === 0);
        noneEl?.classList.toggle('hidden', rows.length === 0 ? false : true);
    };

    document.getElementById('fichePaiementAdd')?.addEventListener('click', () => openPanel(null));
    document.getElementById('fichePaiementCancel')?.addEventListener('click', closePanel);
    document.getElementById('viewPaymentClose')?.addEventListener('click', closeView);
    studentSelect.addEventListener('change', () => applyStudentDefaults(subjectSelect.value));
    dueInput.addEventListener('input', updateBalance);
    paidInput.addEventListener('input', updateBalance);
    document.getElementById('filterMois')?.addEventListener('change', applyFilters);
    document.getElementById('filterNom')?.addEventListener('input', applyFilters);
    document.getElementById('filterMontant')?.addEventListener('input', applyFilters);

    document.querySelectorAll('[data-view-payment]').forEach(button => {
        button.addEventListener('click', () => {
            const payment = payments.find(item => item.id === Number(button.dataset.viewPayment));
            if (payment) openView(payment);
        });
    });

    document.querySelectorAll('[data-edit-payment]').forEach(button => {
        button.addEventListener('click', () => {
            const payment = payments.find(item => item.id === Number(button.dataset.editPayment));
            if (payment) openPanel(payment);
        });
    });

    const oldId = Number(hiddenId.value);
    if (oldId) {
        const payment = payments.find(item => item.id === oldId);
        if (payment) loadPayment(payment);
    } else {
        updateBalance();
    }

    applyFilters();
})();
</script>
@endpush
