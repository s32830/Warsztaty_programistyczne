<?php
session_start();

if (!isset($_SESSION['zadania'])) {
    $_SESSION['zadania'] = [];
}

$tytul = $kategoria = $opis = $priorytet = $status = $data_wykonania = $czas = $lokalizacja = $osoba = "";
$zasoby = [];
$bledy = [];
$sukces = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add') {
        $tytul = htmlspecialchars($_POST['tytul'] ?? '');
        $kategoria = htmlspecialchars($_POST['kategoria'] ?? '');
        $opis = htmlspecialchars($_POST['opis'] ?? '');
        $priorytet = htmlspecialchars($_POST['priorytet'] ?? '');
        $status = htmlspecialchars($_POST['status'] ?? '');
        $data_wykonania = htmlspecialchars($_POST['data_wykonania'] ?? '');
        $czas = htmlspecialchars($_POST['czas'] ?? '');
        $lokalizacja = htmlspecialchars($_POST['lokalizacja'] ?? '');
        $osoba = htmlspecialchars($_POST['osoba'] ?? '');
        $zasoby = $_POST['zasoby'] ?? [];

        if (empty($tytul)) $bledy[] = 'Tytuł zadania jest wymagany.';
        if (empty($kategoria)) $bledy[] = 'Kategoria jest wymagana.';
        if (empty($priorytet)) $bledy[] = 'Priorytet jest wymagany.';
        if (empty($data_wykonania)) $bledy[] = 'Data wykonania jest wymagana.';

        if (empty($bledy)) {
            $_SESSION['zadania'][] = [
                'tytul' => $tytul,
                'kategoria' => $kategoria,
                'opis' => $opis,
                'priorytet' => $priorytet,
                'status' => $status,
                'data_wykonania' => $data_wykonania,
                'czas' => $czas,
                'lokalizacja' => $lokalizacja,
                'osoba' => $osoba,
                'zasoby' => $zasoby
            ];
            $sukces = "Zadanie '" . $tytul . "' zostało pomyślnie dodane!";
            $messageType = 'success';
            
            $tytul = $kategoria = $opis = $priorytet = $status = $data_wykonania = $czas = $lokalizacja = $osoba = "";
            $zasoby = [];
        } else {
            $messageType = 'error';
        }
    } 
    elseif ($action === 'delete_single' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        if (isset($_SESSION['zadania'][$id])) {
            array_splice($_SESSION['zadania'], $id, 1);
            $sukces = "Zadanie zostało usunięte.";
            $messageType = 'info';
        }
    } 
    elseif ($action === 'delete_all') {
        $_SESSION['zadania'] = [];
        $sukces = "Lista zadań została wyczyszczona.";
        $messageType = 'info';
    }
}

$stat_total = count($_SESSION['zadania']);
$stat_priorytet = ['wysoki' => 0, 'sredni' => 0, 'niski' => 0];
$stat_status = ['nowe' => 0, 'w_trakcie' => 0, 'zakonczone' => 0, 'brak' => 0];

foreach ($_SESSION['zadania'] as $z) {
    if (!empty($z['priorytet'])) $stat_priorytet[$z['priorytet']]++;
    if (!empty($z['status'])) $stat_status[$z['status']]++;
    else $stat_status['brak']++;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Menedżer Zadań</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #333;
            padding: 24px;
        }

        header {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 3px solid #3498db;
        }
        header h1 { font-size: 1.8rem; color: #2c3e50; }

        main { max-width: 960px; margin: 0 auto; }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
            margin-bottom: 28px;
        }
        .card h2 { font-size: 1.15rem; margin-bottom: 20px; color: #2c3e50; }

        .alert {
            border-left: 4px solid;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: .9rem;
        }
        .alert ul { margin: 6px 0 0 18px; }
        .alert-error   { border-color: #e74c3c; background: #fdecea; color: #c0392b; }
        .alert-success { border-color: #27ae60; background: #eafaf1; color: #1e8449; }
        .alert-info    { border-color: #3498db; background: #ebf5fb; color: #1a6fa8; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 24px;
        }
        .full-width { grid-column: 1 / -1; }

        .form-group { display: flex; flex-direction: column; gap: 6px; }

        label { font-size: .85rem; font-weight: bold; color: #555; }
        label .req { color: #e74c3c; }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            padding: 9px 11px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: .95rem;
            width: 100%;
            transition: border-color .2s, box-shadow .2s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52,152,219,.15);
        }
        textarea { resize: vertical; min-height: 90px; }

        .checkboxes { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 4px; }
        .checkboxes label { font-weight: normal; display: flex; align-items: center; gap: 5px; }

        .btn-row { display: flex; gap: 12px; justify-content: flex-end; margin-top: 8px; }
        button {
            padding: 10px 22px;
            border: none;
            border-radius: 5px;
            font-size: .95rem;
            cursor: pointer;
            transition: opacity .2s;
        }
        button:hover { opacity: .82; }
        .btn-primary { background: #3498db; color: #fff; }
        .btn-reset   { background: #e0e0e0; color: #333; }
        .btn-danger  { background: #e74c3c; color: #fff; padding: 5px 12px; font-size: .82rem; }
        .btn-outline { background: transparent; border: 1px solid #e74c3c; color: #e74c3c;
                       padding: 7px 16px; font-size: .88rem; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: #fff;
            border-radius: 8px;
            padding: 18px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            text-align: center;
        }
        .stat-card .stat-number { font-size: 2rem; font-weight: bold; color: #3498db; }
        .stat-card .stat-label  { font-size: .8rem; color: #888; margin-bottom: 10px; }
        .stat-card .stat-rows   { font-size: .85rem; text-align: left; margin-top: 10px; border-top: 1px solid #eee; padding-top: 8px; }
        .stat-rows span { display: flex; justify-content: space-between; padding: 2px 0; }
        .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .dot-red    { background: #e74c3c; }
        .dot-yellow { background: #f39c12; }
        .dot-green  { background: #27ae60; }
        .dot-blue   { background: #3498db; }
        .dot-gray   { background: #95a5a6; }

        .table-actions { display: flex; justify-content: flex-end; margin-bottom: 12px; }
        .task-table { width: 100%; border-collapse: collapse; font-size: .88rem; overflow-x: auto; display: block;}
        @media (min-width: 601px) { .task-table { display: table; } }
        
        .task-table th {
            background: #3498db; color: #fff;
            text-align: left; padding: 10px 12px;
        }
        .task-table td { padding: 9px 12px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .task-table tr:last-child td { border-bottom: none; }
        .task-table tr:nth-child(even) td { background: #f9f9f9; }

        .badge {
            display: inline-block; padding: 2px 9px;
            border-radius: 12px; font-size: .78rem; font-weight: bold;
        }
        .badge-wysoki { background: #fdecea; color: #c0392b; }
        .badge-sredni { background: #fef9e7; color: #b7770d; }
        .badge-niski  { background: #eafaf1; color: #1e8449; }

        .empty-info { text-align: center; color: #aaa; padding: 24px; font-style: italic; }

        footer { text-align: center; margin-top: 32px; font-size: .8rem; color: #aaa; }

        @media (max-width: 600px) {
            .form-grid, .stats-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: 1; }
            .btn-row { flex-direction: column; }
            .task-table { font-size: .76rem; }
            .task-table th, .task-table td { padding: 7px 8px; }
        }
    </style>
</head>
<body>

<header>
    <h1>Rozbudowany Menedżer Zadań</h1>
</header>

<main>

    <div class="card">
        <h2>Dodaj nowe zadanie</h2>

        <?php if (!empty($bledy)): ?>
            <div class="alert alert-error">
                <strong>⚠ Popraw następujące błędy:</strong>
                <ul>
                    <?php foreach ($bledy as $blad): ?>
                        <li><?= $blad ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($sukces !== ''): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= $sukces ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <input type="hidden" name="action" value="add">
            <div class="form-grid">

                <div class="form-group full-width">
                    <label for="tytul">Tytuł zadania: <span class="req">*</span></label>
                    <input type="text" id="tytul" name="tytul" value="<?= $tytul ?>" placeholder="Wpisz tytuł zadania">
                </div>

                <div class="form-group full-width">
                    <label for="opis">Opis zadania:</label>
                    <textarea id="opis" name="opis" placeholder="Opcjonalny opis..."><?= $opis ?></textarea>
                </div>

                <div class="form-group">
                    <label for="priorytet">Priorytet: <span class="req">*</span></label>
                    <select id="priorytet" name="priorytet">
                        <option value="">Wybierz priorytet</option>
                        <option value="wysoki" <?= $priorytet === 'wysoki' ? 'selected' : '' ?>>Wysoki</option>
                        <option value="sredni" <?= $priorytet === 'sredni' ? 'selected' : '' ?>>Średni</option>
                        <option value="niski"  <?= $priorytet === 'niski' ? 'selected' : '' ?>>Niski</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="">Wybierz status</option>
                        <option value="nowe"       <?= $status === 'nowe' ? 'selected' : '' ?>>Nowe</option>
                        <option value="w_trakcie"  <?= $status === 'w_trakcie' ? 'selected' : '' ?>>W trakcie</option>
                        <option value="zakonczone" <?= $status === 'zakonczone' ? 'selected' : '' ?>>Zakończone</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="kategoria">Kategoria: <span class="req">*</span></label>
                    <select id="kategoria" name="kategoria">
                        <option value="">Wybierz kategorię</option>
                        <option value="domowe" <?= $kategoria === 'domowe' ? 'selected' : '' ?>>Domowe</option>
                        <option value="praca"  <?= $kategoria === 'praca' ? 'selected' : '' ?>>Praca</option>
                        <option value="nauka"  <?= $kategoria === 'nauka' ? 'selected' : '' ?>>Nauka</option>
                        <option value="hobby"  <?= $kategoria === 'hobby' ? 'selected' : '' ?>>Hobby</option>
                        <option value="inne"   <?= $kategoria === 'inne' ? 'selected' : '' ?>>Inne</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="data_wykonania">Data wykonania: <span class="req">*</span></label>
                    <input type="date" id="data_wykonania" name="data_wykonania" value="<?= $data_wykonania ?>">
                </div>

                <div class="form-group">
                    <label for="czas">Szacowany czas (minuty):</label>
                    <input type="number" id="czas" name="czas" min="1" value="<?= $czas ?>" placeholder="np. 60">
                </div>

                <div class="form-group">
                    <label for="lokalizacja">Lokalizacja:</label>
                    <input type="text" id="lokalizacja" name="lokalizacja" value="<?= $lokalizacja ?>" placeholder="np. Dom, Biuro">
                </div>

                <div class="form-group">
                    <label for="osoba">Osoba przypisana:</label>
                    <input type="text" id="osoba" name="osoba" value="<?= $osoba ?>" placeholder="Imię i nazwisko">
                </div>

                <div class="form-group full-width">
                    <label>Potrzebne zasoby:</label>
                    <div class="checkboxes">
                        <?php 
                        $lista_zasobow = ['komputer' => 'Komputer', 'internet' => 'Internet', 'telefon' => 'Telefon', 'samochod' => 'Samochód', 'ksiazka' => 'Książka', 'narzedzia' => 'Narzędzia', 'dokumenty' => 'Dokumenty', 'inne' => 'Inne'];
                        foreach ($lista_zasobow as $val => $label): 
                        ?>
                            <label>
                                <input type="checkbox" name="zasoby[]" value="<?= $val ?>" <?= in_array($val, $zasoby) ? 'checked' : '' ?>>
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <div class="btn-row">
                <button type="reset" class="btn-reset">Wyczyść</button>
                <button type="submit" class="btn-primary">Dodaj zadanie</button>
            </div>
        </form>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $stat_total ?></div>
            <div class="stat-label">Łącznie zadań</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Według priorytetu</div>
            <div class="stat-rows">
                <span><span><span class="dot dot-red"></span>Wysoki</span><strong><?= $stat_priorytet['wysoki'] ?></strong></span>
                <span><span><span class="dot dot-yellow"></span>Średni</span><strong><?= $stat_priorytet['sredni'] ?></strong></span>
                <span><span><span class="dot dot-green"></span>Niski</span><strong><?= $stat_priorytet['niski'] ?></strong></span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Według statusu</div>
            <div class="stat-rows">
                <span><span><span class="dot dot-blue"></span>Nowe</span><strong><?= $stat_status['nowe'] ?></strong></span>
                <span><span><span class="dot dot-yellow"></span>W trakcie</span><strong><?= $stat_status['w_trakcie'] ?></strong></span>
                <span><span><span class="dot dot-green"></span>Zakończone</span><strong><?= $stat_status['zakonczone'] ?></strong></span>
                <span><span><span class="dot dot-gray"></span>Brak</span><strong><?= $stat_status['brak'] ?></strong></span>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Lista zadań (<?= $stat_total ?>)</h2>

        <?php if (!empty($_SESSION['zadania'])): ?>
            <div class="table-actions">
                <form method="POST" action="index.php" onsubmit="return confirm('Czy na pewno chcesz usunąć wszystkie zadania?')">
                    <input type="hidden" name="action" value="delete_all">
                    <button type="submit" class="btn-outline">Wyczyść wszystkie</button>
                </form>
            </div>

            <table class="task-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tytuł</th>
                        <th>Kategoria</th>
                        <th>Priorytet</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Czas</th>
                        <th>Osoba</th>
                        <th>Akcja</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['zadania'] as $i => $task): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= $task['tytul'] ?></strong></td>
                            <td><?= ucfirst($task['kategoria']) ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower($task['priorytet']) ?>">
                                    <?= ucfirst($task['priorytet']) ?>
                                </span>
                            </td>
                            <td><?= !empty($task['status']) ? ucfirst(str_replace('_', ' ', $task['status'])) : '-' ?></td>
                            <td><?= $task['data_wykonania'] ?></td>
                            <td><?= !empty($task['czas']) ? $task['czas'] . ' min' : '-' ?></td>
                            <td><?= !empty($task['osoba']) ? $task['osoba'] : '-' ?></td>
                            <td>
                                <form method="POST" action="index.php" onsubmit="return confirm('Usunąć to zadanie?')" style="margin:0;">
                                    <input type="hidden" name="action" value="delete_single">
                                    <input type="hidden" name="id" value="<?= $i ?>">
                                    <button type="submit" class="btn-danger">Usuń</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="empty-info">Brak zadań. Dodaj pierwsze zadanie powyżej.</p>
        <?php endif; ?>
    </div>

</main>

<footer>
    <p>Menedżer Zadań &mdash; &copy; <?= date('Y') ?></p>
</footer>

</body>
</html>