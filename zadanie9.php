<?php

$dane = [];
$historia = [];

function wypiszTablice($arr) {
    echo "[" . implode(", ", $arr) . "]\n";
}

while (true) {
    $linia = readline(">> ");
    if ($linia === false || trim($linia) === '') continue;

    $czesci = explode(' ', trim($linia), 3);
    $polecenie = strtolower($czesci[0]);
    $sukces = false;

    switch ($polecenie) {
        case 'push':
            if (!isset($czesci[1])) {
                echo "Brak argumentu dla: push\n";
            } else {
                $wartosc = isset($czesci[2]) ? $czesci[1] . " " . $czesci[2] : $czesci[1];
                $dane[] = $wartosc;
                wypiszTablice($dane);
                $sukces = true;
            }
            break;

        case 'pop':
            if (empty($dane)) {
                echo "Tablica jest pusta.\n";
            } else {
                $ostatni = array_pop($dane);
                echo "Usunięto: $ostatni\n";
                wypiszTablice($dane);
            }
            $sukces = true;
            break;

        case 'insert':
            if (!isset($czesci[1]) || !isset($czesci[2])) {
                echo "Brak argumentu dla: insert\n";
            } else {
                $idx = (int)$czesci[1];
                $val = $czesci[2];
                array_splice($dane, $idx, 0, [$val]);
                wypiszTablice($dane);
                $sukces = true;
            }
            break;

        case 'delete':
            if (!isset($czesci[1])) {
                echo "Brak argumentu dla: delete\n";
            } else {
                $idx = (int)$czesci[1];
                array_splice($dane, $idx, 1);
                wypiszTablice($dane);
                $sukces = true;
            }
            break;

        case 'sort':
            sort($dane);
            wypiszTablice($dane);
            $sukces = true;
            break;

        case 'rsort':
            rsort($dane);
            wypiszTablice($dane);
            $sukces = true;
            break;

        case 'filter':
            if (!isset($czesci[1]) || !isset($czesci[2])) {
                echo "Brak argumentu dla: filter\n";
            } else {
                $op = $czesci[1];
                $val = $czesci[2];
                $nowe_dane = [];
                foreach ($dane as $item) {
                    $pasuje = false;
                    switch ($op) {
                        case '>': $pasuje = ($item > $val); break;
                        case '<': $pasuje = ($item < $val); break;
                        case '>=': $pasuje = ($item >= $val); break;
                        case '<=': $pasuje = ($item <= $val); break;
                        case '==': $pasuje = ($item == $val); break;
                        case '!=': $pasuje = ($item != $val); break;
                    }
                    if ($pasuje) {
                        $nowe_dane[] = $item;
                    }
                }
                $dane = $nowe_dane;
                wypiszTablice($dane);
                $sukces = true;
            }
            break;

        case 'unique':
            $dane = array_values(array_unique($dane));
            wypiszTablice($dane);
            $sukces = true;
            break;

        case 'reverse':
            $dane = array_reverse($dane);
            wypiszTablice($dane);
            $sukces = true;
            break;

        case 'chunk':
            if (!isset($czesci[1])) {
                echo "Brak argumentu dla: chunk\n";
            } else {
                $n = (int)$czesci[1];
                if ($n > 0) {
                    $chunks = array_chunk($dane, $n);
                    foreach ($chunks as $i => $ch) {
                        echo "Chunk " . ($i + 1) . ": [" . implode(", ", $ch) . "]\n";
                    }
                }
                $sukces = true;
            }
            break;

        case 'slice':
            if (!isset($czesci[1]) || !isset($czesci[2])) {
                echo "Brak argumentu dla: slice\n";
            } else {
                $od = (int)$czesci[1];
                $ile = (int)$czesci[2];
                $fragment = array_slice($dane, $od, $ile);
                wypiszTablice($fragment);
                $sukces = true;
            }
            break;

        case 'stats':
            if (empty($dane)) {
                echo "Tablica jest pusta.\n";
            } else {
                $suma = 0;
                $min = $dane[0];
                $max = $dane[0];
                foreach ($dane as $item) {
                    $suma += $item;
                    if ($item < $min) $min = $item;
                    if ($item > $max) $max = $item;
                }
                $srednia = $suma / count($dane);
                echo "Suma: $suma | Średnia: $srednia | Min: $min | Max: $max\n";
            }
            $sukces = true;
            break;

        case 'show':
            wypiszTablice($dane);
            $sukces = true;
            break;

        case 'reset':
            $dane = [];
            wypiszTablice($dane);
            $sukces = true;
            break;

        case 'save':
            echo json_encode($dane) . "\n";
            $sukces = true;
            break;

        case 'history':
            foreach ($historia as $i => $h) {
                echo ($i + 1) . ": $h\n";
            }
            $sukces = true;
            break;

        case 'help':
            echo "Dostępne polecenia: push, pop, insert, delete, sort, rsort, filter, unique, reverse, chunk, slice, stats, show, reset, save, history, help, exit\n";
            $sukces = true;
            break;

        case 'exit':
            exit(0);

        default:
            echo "Nieznane polecenie: $polecenie\n";
            break;
    }

    if ($sukces) {
        $historia[] = trim($linia);
        $historia = array_slice($historia, -10);
    }
}
?>