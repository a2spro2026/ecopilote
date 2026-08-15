<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Teacher\AuthController as TeacherAuthController;
use App\Http\Controllers\Teacher\WorkspaceController;
use App\Models\StudentApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/categories', 'pages.categories')->name('categories');
Route::view('/activites', 'pages.activites')->name('activites');

Route::get('/portail-profs', [TeacherAuthController::class, 'showLogin'])->name('portail.profs');
Route::view('/portail-etudiant', 'pages.portail-etudiant')->name('portail.etudiant');
Route::redirect('/portail-parents', '/portail-etudiant');

Route::post('/portail-profs', [TeacherAuthController::class, 'login'])->name('portail.profs.login');
Route::post('/portail-profs/inscription', [TeacherAuthController::class, 'register'])->name('portail.profs.register');
Route::post('/espace-prof/deconnexion', [TeacherAuthController::class, 'logout'])->name('teacher.logout');

Route::middleware('teacher.auth')->prefix('espace-prof')->group(function () {
    Route::get('/', [WorkspaceController::class, 'bureau'])->name('teacher.bureau');
    Route::get('/seances', [WorkspaceController::class, 'seances'])->name('teacher.seances');
    Route::get('/classes', [WorkspaceController::class, 'classes'])->name('teacher.classes');
    Route::get('/classes/{classe}', [WorkspaceController::class, 'classeShow'])->whereNumber('classe')->name('teacher.classes.show');
    Route::get('/eleves', [WorkspaceController::class, 'eleves'])->name('teacher.eleves');
    Route::get('/eleves/{eleve}', [WorkspaceController::class, 'eleveShow'])->whereNumber('eleve')->name('teacher.eleves.show');
    Route::get('/bibliotheque', [WorkspaceController::class, 'bibliotheque'])->name('teacher.bibliotheque');
    Route::get('/exercices', [WorkspaceController::class, 'exercices'])->name('teacher.exercices');
    Route::get('/archives', [WorkspaceController::class, 'archives'])->name('teacher.archives');
    Route::get('/suivi', [WorkspaceController::class, 'suivi'])->name('teacher.suivi');
    Route::get('/notifications', [WorkspaceController::class, 'notifications'])->name('teacher.notifications');
    Route::get('/profil', [WorkspaceController::class, 'profil'])->name('teacher.profil');
    Route::get('/salle', [WorkspaceController::class, 'salle'])->name('teacher.salle');
    Route::get('/seance-terminee', [WorkspaceController::class, 'terminer'])->name('teacher.seance.terminee');
});

Route::post('/portail-etudiant', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    return back()->with('status', 'Identifiants reçus — prêt à être connecté au système ECOPILOTE.');
})->name('portail.etudiant.login');

Route::post('/portail-etudiant/inscription', function (Request $request) {
    $data = $request->validate([
        'nom_complet' => ['required', 'string', 'max:120'],
        'contact' => ['required', 'string', 'max:120'],
        'contact_tuteur' => ['required', 'string', 'max:120'],
        'ville' => ['required', 'string', 'max:120'],
        'niveau_scolaire' => ['required', 'string', 'max:120'],
        'matiere' => ['required', 'string', 'max:120'],
        'type_cours' => ['required', 'in:individuel,en_groupe'],
    ]);

    StudentApplication::create([
        ...$data,
        'etat' => StudentApplication::ETAT_EN_ATTENTE,
    ]);

    return back()->with('status', 'Inscription envoyée. Notre équipe vous recontactera bientôt.');
})->name('portail.etudiant.register');

Route::prefix('administration')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.attempt');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('auth')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/classes', [ClassController::class, 'index'])->name('admin.page.classes');
        Route::get('/classes/nouvelle', [ClassController::class, 'create'])->name('admin.classes.create');
        Route::get('/classes/{classe}', [ClassController::class, 'show'])->whereNumber('classe')->name('admin.classes.show');
        Route::post('/classes', [ClassController::class, 'store'])->name('admin.classes.store');

        Route::get('/candidatures-profs', [TeacherController::class, 'applications'])->name('admin.page.candidatures-profs');
        Route::get('/professeurs', [TeacherController::class, 'index'])->name('admin.page.professeurs');
        Route::get('/professeurs/{professeur}', [TeacherController::class, 'show'])->name('admin.teachers.show');
        Route::get('/professeurs/{professeur}/modifier', [TeacherController::class, 'edit'])->name('admin.teachers.edit');
        Route::put('/professeurs/{professeur}', [TeacherController::class, 'update'])->name('admin.teachers.update');
        Route::post('/professeurs/{professeur}/suspendre', [TeacherController::class, 'suspend'])->name('admin.teachers.suspend');
        Route::post('/candidatures-profs/{application}/valider', [TeacherController::class, 'validateApplication'])->name('admin.teachers.applications.validate');
        Route::post('/candidatures-profs/{application}/en-attente', [TeacherController::class, 'pendingApplication'])->name('admin.teachers.applications.pending');
        Route::post('/candidatures-profs/{application}/suspendre', [TeacherController::class, 'suspendApplication'])->name('admin.teachers.applications.suspend');

        Route::get('/demandes-eleves', [StudentController::class, 'applications'])->name('admin.page.demandes-eleves');
        Route::get('/eleves', [StudentController::class, 'index'])->name('admin.page.eleves');
        Route::get('/eleves/{eleve}', [StudentController::class, 'show'])->name('admin.students.show');
        Route::get('/eleves/{eleve}/modifier', [StudentController::class, 'edit'])->name('admin.students.edit');
        Route::put('/eleves/{eleve}', [StudentController::class, 'update'])->name('admin.students.update');
        Route::post('/eleves/{eleve}/suspendre', [StudentController::class, 'suspend'])->name('admin.students.suspend');
        Route::post('/demandes-eleves/{application}/valider', [StudentController::class, 'validateApplication'])->name('admin.students.applications.validate');
        Route::post('/demandes-eleves/{application}/en-attente', [StudentController::class, 'pendingApplication'])->name('admin.students.applications.pending');
        Route::post('/demandes-eleves/{application}/suspendre', [StudentController::class, 'suspendApplication'])->name('admin.students.applications.suspend');

        Route::get('/matieres', [SubjectController::class, 'index'])->name('admin.page.matieres');
        Route::get('/niveaux', [LevelController::class, 'index'])->name('admin.page.niveaux');
        Route::get('/salles-actives', [RoomController::class, 'index'])->name('admin.page.salles-actives');

        foreach (AdminController::pageKeys() as $key) {
            if (in_array($key, ['classes', 'candidatures-profs', 'professeurs', 'demandes-eleves', 'eleves', 'matieres', 'niveaux', 'salles-actives'], true)) {
                continue;
            }
            Route::get("/{$key}", [AdminController::class, 'page'])
                ->defaults('key', $key)
                ->name("admin.page.{$key}");
        }
    });
});
