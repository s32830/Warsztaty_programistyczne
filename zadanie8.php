<?php

$rekordy = [
    ["id"=>1,  "imie"=>"anna",    "wiek"=>"25",  "email"=>"anna@test.com",    "wynik"=>92.5],
    ["id"=>2,  "imie"=>"Bartosz", "wiek"=>"abc", "email"=>"bartosz@test.com", "wynik"=>78.0],
    ["id"=>3,  "imie"=>"celina",  "wiek"=>"31",  "email"=>"celina@test.com",  "wynik"=>105.0],
    ["id"=>4,  "imie"=>"Dawid",   "wiek"=>"45",  "email"=>"",                 "wynik"=>66.5],
    ["id"=>5,  "imie"=>"EWA",     "wiek"=>"28",  "email"=>"ewa@test.com",     "wynik"=>88.0],
    ["id"=>6,  "imie"=>"filip",   "wiek"=>"130", "email"=>"filip@test.com",   "wynik"=>74.0],
    ["id"=>7,  "imie"=>"Grażyna", "wiek"=>"52",  "email"=>"anna@test.com",    "wynik"=>91.0],
    ["id"=>8,  "imie"=>"Henryk",  "wiek"=>"19",  "email"=>"henryk@test.com",  "wynik"=>-5.0],
    ["id"=>9,  "imie"=>"irena",   "wiek"=>"37",  "email"=>"irena@test.com",   "wynik"=>83.5],
    ["id"=>10, "imie"=>"JANEK",   "wiek"=>"22",  "email"=>"janek@test.com",   "wynik"=>55.0],
    ["id"=>11, "imie"=>"Kasia",   "wiek"=>"29",  "email"=>"kasia@test.com",   "wynik"=>97.0],
    ["id"=>12, "imie"=>"Leon",    "wiek"=>"41",  "email"=>"leon@test.com",    "wynik"=>62.0],
    ["id"=>13, "imie"=>"Marta",   "wiek"=>"0",   "email"=>"marta@test.com",   "wynik"=>79.5],
    ["id"=>14, "imie"=>"norbert", "wiek"=>"33",  "email"=>"norbert@test.com", "wynik"=>86.0],
    ["id"=>15, "imie"=>"Ola",     "wiek"=>"26",  "email"=>"ola@test.com",     "wynik"=>91.0],
];

function waliduj(array $dane): array {
    $wynik = ['valid' => [], 'rejected' => []];
    
    foreach ($dane as $r) {
        $wiek = $r['wiek'];
        $score = $r['wynik'];
        $email = trim($r['email']);
        
        if (!preg_match('/^-?\d+$/', (string)$wiek) || (int)$wiek < 1 || (int)$wiek > 120) {
            $wynik['rejected'][] = ['rekord' => $r, 'powod' => "nieprawidłowy wiek '$wiek'"];
        } 
        elseif (!is_numeric($score) || (float)$score < 0.0 || (float)$score > 100.0) {
            $sformatowany_wynik = number_format((float)$score, 1, '.', '');
            $wynik['rejected'][] = ['rekord' => $r, 'powod' => "wynik poza zakresem [0-100]: $sformatowany_wynik"];
        } 
        elseif ($email === '') {
            $wynik['rejected'][] = ['rekord' => $r, 'powod' => "pusty email"];
        } 
        else {
            $wynik['valid'][] = $r;
        }
    }
    
    return $wynik;
}

function transformuj(array $dane): array {
    $czyste = [];
    $odrzucone = []; 
    $widziane_emaile = [];
    
    foreach ($dane as $r) {
        $email = trim($r['email']);
        
        if (isset($widziane_emaile[$email])) {
            $odrzucone[] = ['rekord' => $r, 'powod' => "duplikat email '$email'"];
            continue;
        }
        $widziane_emaile[$email] = true;
        
        $r['imie'] = ucfirst(strtolower($r['imie']));
        $r['wiek'] = (int)$r['wiek'];
        $r['wynik'] = (float)$r['wynik'];
        
        $czyste[] = $r;
    }
    
    return ['dane' => $czyste, 'odrzucone' => $odrzucone];
}

$etap_walidacji = waliduj($rekordy);
$etap_transformacji = transformuj($etap_walidacji['valid']);

$finalna_baza = $etap_transformacji['dane'];

$wszystkie_odrzucone = array_merge($etap_walidacji['rejected'], $etap_transformacji['odrzucone']);

usort($wszystkie_odrzucone, function($a, $b) {
    return $a['rekord']['id'] <=> $b['rekord']['id'];
});

echo "<pre style='background:#1e1e24; color:#a1a1aa; padding:15px; border-radius:5px;'>\n";

echo "=== Etap E: Walidacja ===\n";
echo "Odrzucone rekordy (" . count($wszystkie_odrzucone) . "):\n";
foreach ($wszystkie_odrzucone as $odrzut) {
    $id = $odrzut['rekord']['id'];
    $imie = $odrzut['rekord']['imie'];
    $powod = $odrzut['powod'];
    echo " - ID $id ($imie): $powod\n";
}
echo "\n";

$statystyki = [
    'A' => ['suma' => 0, 'ile' => 0],
    'B' => ['suma' => 0, 'ile' => 0],
    'C' => ['suma' => 0, 'ile' => 0],
    'D' => ['suma' => 0, 'ile' => 0]
];

foreach ($finalna_baza as &$r) {
    $wynik = $r['wynik'];
    
    if ($wynik >= 90) $ocena = 'A';
    elseif ($wynik >= 75) $ocena = 'B';
    elseif ($wynik >= 60) $ocena = 'C';
    else $ocena = 'D';
    
    $r['ocena'] = $ocena;
    $statystyki[$ocena]['suma'] += $wynik;
    $statystyki[$ocena]['ile']++;
}
unset($r);

echo "=== Etap L: Finalna baza (" . count($finalna_baza) . " rekordów) ===\n";
printf("%-10s | %-4s | %-20s | %-5s | %s\n", "Imię", "Wiek", "Email", "Wynik", "Ocena");
echo str_repeat("-", 58) . "\n";

foreach ($finalna_baza as $r) {
    printf("%-10s | %4d | %-20s | %5.1f | %s\n", $r['imie'], $r['wiek'], $r['email'], $r['wynik'], $r['ocena']);
}

echo "\nRozkład ocen:\n";
foreach (['A', 'B', 'C', 'D'] as $o) {
    $ile = $statystyki[$o]['ile'];
    if ($ile > 0) {
        $srednia = $statystyki[$o]['suma'] / $ile;
        printf("<span style='color:#81a1c1;'>%s:</span> %d studentów, średnia: <span style='color:#bf616a;'>%.1f%%</span>\n", $o, $ile, $srednia);
    }
}

echo "</pre>";

?>