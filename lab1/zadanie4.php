<?php

function s_push(array &$stos, $val): void {
    array_splice($stos, count($stos), 0, [$val]);
}

function s_pop(array &$stos) {
    if (count($stos) == 0) return null; 
    
    $top = $stos[count($stos) - 1];
    array_splice($stos, -1, 1);
    return $top;
}

function s_peek(array $stos) {
    if (count($stos) == 0) return null;
    return $stos[count($stos) - 1];
}

$wyrazenia_ONP = [
    "5 2 + 3 *",
    "15 7 1 1 + - / 3 * 2 1 1 + + -",
    "4 13 5 / +",
    "2 3 + 4 * 5 -",
    "100 50 25 / -"
];

$napisy_nawiasy = [
    "[{(())}]",
    "((())",
    "{[()]}",
    "([)]",
    ""
];

function waliduj_nawiasy($napis) {
    if ($napis === "") return "OK";
    
    $stos = [];
    $pary = [')' => '(', ']' => '[', '}' => '{'];
    
    for ($i = 0; $i < strlen($napis); $i++) {
        $znak = $napis[$i];
        
        if ($znak == '(' || $znak == '[' || $znak == '{') {
            s_push($stos, $znak);
        } 
        elseif ($znak == ')' || $znak == ']' || $znak == '}') {
            $ostatni = s_pop($stos);
            if ($ostatni !== $pary[$znak]) {
                return "BŁĄD";
            }
        }
    }
    
    if (count($stos) == 0) {
        return "OK";
    } else {
        return "BŁĄD";
    }
}

function oblicz_onp($wyrazenie) {
    $stos = [];
    $tokeny = explode(' ', $wyrazenie);
    
    foreach ($tokeny as $token) {
        if (is_numeric($token)) {
            s_push($stos, $token);
        } else {
            $b = s_pop($stos);
            $a = s_pop($stos);
            
            if ($token == '+') s_push($stos, $a + $b);
            if ($token == '-') s_push($stos, $a - $b);
            if ($token == '*') s_push($stos, $a * $b);
            if ($token == '/') s_push($stos, $a / $b);
        }
    }
    
    return s_pop($stos);
}

echo "<pre style='background:#1e1e24; color:#a1a1aa; padding:15px; border-radius:5px;'>\n";

$bufor = [];
$pos = 0;

for ($i = 0; $i < count($wyrazenia_ONP); $i++) {
    $nawiasy = $napisy_nawiasy[$i];
    $onp = $wyrazenia_ONP[$i];
    
    $status_nawiasow = waliduj_nawiasy($nawiasy);
    $wynik_onp = oblicz_onp($onp);
    
    $bufor[$pos % 5] = $wynik_onp;
    $pos++;
    
    $nr = $i + 1;
    $nawiasy_wyswietl = str_pad('"' . $nawiasy . '":', 13);
    $status_wyswietl = str_pad($status_nawiasow, 6);
    $onp_wyswietl = str_pad('"' . $onp . '"', 33);
    
    echo "[$nr] Nawiasy $nawiasy_wyswietl $status_wyswietl | ONP $onp_wyswietl = $wynik_onp\n";
}

echo "\nBufor cykliczny (ostatnie 5 wyników): [" . implode(', ', $bufor) . "]\n";
echo "</pre>";

?>