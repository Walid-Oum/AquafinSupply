<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */

public function run(): void
{

    $antwerpen = Location::create([
        'name' => 'Aquafin Antwerpen',
        'city' => 'Antwerpen',
        'postal_code' => '2000',
        'latitude' => 51.2205000,
        'longitude' => 4.4003000,
    ]);

    $gent = Location::create([
        'name' => 'Aquafin Gent',
        'city' => 'Gent',
        'postal_code' => '9000',
        'latitude' => 51.0543000,
        'longitude' => 3.7174000,
    ]);

    $brussel = Location::create([
        'name' => 'Aquafin Brussel',
        'city' => 'Brussel',
        'postal_code' => '1000',
        'latitude' => 50.8503000,
        'longitude' => 4.3517000,
    ]);

    // 1. De vaste Admin
    \App\Models\User::factory()->create([
        'name' => 'Aquafin Admin',
        'email' => 'admin@aquafinsupply.be',
        'password' => bcrypt('Aquafin2026!'),
        'role' => 'admin',
        'location_id' => $antwerpen->id,
    ]);

    // 2. De vaste Magazijn
    \App\Models\User::factory()->create([
        'name' => 'Magazijn Medewerker',
        'email' => 'magazijn@aquafinsupply.be',
        'password' => bcrypt('Magazijn2026!'),
        'role' => 'magazijn',
        'location_id' => $brussel->id,
    ]);

    // 3. De vaste Technieker
    \App\Models\User::factory()->create([
        'name' => 'Technieker App',
        'email' => 'technieker@aquafinsupply.be',
        'password' => bcrypt('Technieker2026!'),
        'role' => 'technieker',
        'location_id' => $gent->id,
    ]);








    $materials = [
            // ==================== BEVESTIGINGSMATERIAAL ====================
            ['name' => 'Bout M6', 'category' => 'Bevestigingsmateriaal', 'description' => 'Inox A2', 'stock' => 200, 'is_active' => true],
            ['name' => 'Bout M8', 'category' => 'Bevestigingsmateriaal', 'description' => 'Inox A2/A4, verzinkt', 'stock' => 150, 'is_active' => true],
            ['name' => 'Bout M10', 'category' => 'Bevestigingsmateriaal', 'description' => 'Inox A2/A4, verzinkt', 'stock' => 120, 'is_active' => true],
            ['name' => 'Bout M12', 'category' => 'Bevestigingsmateriaal', 'description' => 'Inox A2/A4, verzinkt', 'stock' => 100, 'is_active' => true],
            ['name' => 'Bout M16', 'category' => 'Bevestigingsmateriaal', 'description' => 'Inox A2/A4, verzinkt', 'stock' => 80, 'is_active' => true],
            ['name' => 'Zeskantmoeren', 'category' => 'Bevestigingsmateriaal', 'description' => 'Verschillende maten', 'stock' => 300, 'is_active' => true],
            ['name' => 'Borgmoeren', 'category' => 'Bevestigingsmateriaal', 'description' => 'Zelfborgend', 'stock' => 250, 'is_active' => true],
            ['name' => 'Flensmoeren', 'category' => 'Bevestigingsmateriaal', 'description' => 'Met flens', 'stock' => 200, 'is_active' => true],
            ['name' => 'Sluitringen', 'category' => 'Bevestigingsmateriaal', 'description' => 'Verschillende maten', 'stock' => 500, 'is_active' => true],
            ['name' => 'Veerringen', 'category' => 'Bevestigingsmateriaal', 'description' => 'Verschillende maten', 'stock' => 500, 'is_active' => true],
            ['name' => 'Tandringen', 'category' => 'Bevestigingsmateriaal', 'description' => 'Verschillende maten', 'stock' => 400, 'is_active' => true],
            ['name' => 'Ankerbouten', 'category' => 'Bevestigingsmateriaal', 'description' => 'Voor beton', 'stock' => 60, 'is_active' => true],
            ['name' => 'Chemische ankers', 'category' => 'Bevestigingsmateriaal', 'description' => 'Hilti HIT', 'stock' => 40, 'is_active' => true],
            ['name' => 'Keilbouten', 'category' => 'Bevestigingsmateriaal', 'description' => 'Voor beton', 'stock' => 50, 'is_active' => true],
            ['name' => 'Draadstangen', 'category' => 'Bevestigingsmateriaal', 'description' => 'M6 t.e.m. M16', 'stock' => 100, 'is_active' => true],
            ['name' => 'Inslagmoeren', 'category' => 'Bevestigingsmateriaal', 'description' => 'Voor plaatmateriaal', 'stock' => 150, 'is_active' => true],
            ['name' => 'Tapbouten', 'category' => 'Bevestigingsmateriaal', 'description' => 'Zeskantkop', 'stock' => 120, 'is_active' => true],
            ['name' => 'Zeskantkopschroeven', 'category' => 'Bevestigingsmateriaal', 'description' => 'Verschillende maten', 'stock' => 200, 'is_active' => true],
            ['name' => 'Inbusbouten', 'category' => 'Bevestigingsmateriaal', 'description' => 'Verschillende maten', 'stock' => 180, 'is_active' => true],
            ['name' => 'Torxschroeven', 'category' => 'Bevestigingsmateriaal', 'description' => 'Verschillende maten', 'stock' => 200, 'is_active' => true],
            ['name' => 'Kruiskopschroeven', 'category' => 'Bevestigingsmateriaal', 'description' => 'Verschillende maten', 'stock' => 250, 'is_active' => true],
            ['name' => 'Zelftappende vijzen', 'category' => 'Bevestigingsmateriaal', 'description' => 'Voor metaal', 'stock' => 300, 'is_active' => true],
            ['name' => 'Parkervijzen', 'category' => 'Bevestigingsmateriaal', 'description' => 'Voor hout', 'stock' => 200, 'is_active' => true],
            ['name' => 'Spaanplaatschroeven', 'category' => 'Bevestigingsmateriaal', 'description' => 'Voor spaanplaat', 'stock' => 150, 'is_active' => true],
            ['name' => 'Slangenklemmen', 'category' => 'Bevestigingsmateriaal', 'description' => 'Div. diameters', 'stock' => 300, 'is_active' => true],

            // ==================== PBM (PERSOONLIJKE BESCHERMINGSMIDDELEN) ====================
            ['name' => 'Veiligheidshelm', 'category' => 'PBM', 'description' => 'Met kinband', 'stock' => 50, 'is_active' => true],
            ['name' => 'Oordoppen', 'category' => 'PBM', 'description' => 'Wegwerp', 'stock' => 200, 'is_active' => true],
            ['name' => 'Gehoorkappen', 'category' => 'PBM', 'description' => 'Instelbaar', 'stock' => 30, 'is_active' => true],
            ['name' => 'Veiligheidsbril', 'category' => 'PBM', 'description' => 'Gelaatsscherm', 'stock' => 40, 'is_active' => true],
            ['name' => 'Stofmaskers FFP2', 'category' => 'PBM', 'description' => 'FFP2', 'stock' => 150, 'is_active' => true],
            ['name' => 'Stofmaskers FFP3', 'category' => 'PBM', 'description' => 'FFP3', 'stock' => 100, 'is_active' => true],
            ['name' => 'Werkhandschoenen snijvast', 'category' => 'PBM', 'description' => 'Snijvast', 'stock' => 80, 'is_active' => true],
            ['name' => 'Werkhandschoenen chemisch', 'category' => 'PBM', 'description' => 'Chemisch resistent', 'stock' => 60, 'is_active' => true],
            ['name' => 'Werkhandschoenen elektrisch', 'category' => 'PBM', 'description' => 'Elektrisch geïsoleerd', 'stock' => 40, 'is_active' => true],
            ['name' => 'Veiligheidsschoenen S3', 'category' => 'PBM', 'description' => 'S3, antistatisch, stalen tip', 'stock' => 30, 'is_active' => true],
            ['name' => 'Werklaarzen PVC', 'category' => 'PBM', 'description' => 'PVC, met stalen zool', 'stock' => 25, 'is_active' => true],
            ['name' => 'Regenjas', 'category' => 'PBM', 'description' => 'Waterdicht', 'stock' => 30, 'is_active' => true],
            ['name' => 'Regenbroek', 'category' => 'PBM', 'description' => 'Waterdicht', 'stock' => 30, 'is_active' => true],
            ['name' => 'Fluovest', 'category' => 'PBM', 'description' => 'EN ISO 20471', 'stock' => 50, 'is_active' => true],
            ['name' => 'Overall brandvertragend', 'category' => 'PBM', 'description' => 'Brandvertragend, antistatisch', 'stock' => 20, 'is_active' => true],
            ['name' => 'Valharnas', 'category' => 'PBM', 'description' => 'Veiligheidsharnas', 'stock' => 15, 'is_active' => true],
            ['name' => 'Valbeveiligingslijn', 'category' => 'PBM', 'description' => 'Lifeline', 'stock' => 15, 'is_active' => true],
            ['name' => 'Karabijnhaken', 'category' => 'PBM', 'description' => 'Voor valbeveiliging', 'stock' => 30, 'is_active' => true],
            ['name' => 'Gasdetectiemeter', 'category' => 'PBM', 'description' => 'O₂, CH₄, H₂S, CO', 'stock' => 10, 'is_active' => true],
            ['name' => 'EHBO-kit', 'category' => 'PBM', 'description' => 'Compleet', 'stock' => 20, 'is_active' => true],

            // ==================== GEREEDSCHAP ====================
            ['name' => 'Dopsleutelset metrisch', 'category' => 'Gereedschap', 'description' => 'Metrisch', 'stock' => 15, 'is_active' => true],
            ['name' => 'Dopsleutelset inch', 'category' => 'Gereedschap', 'description' => 'Inch', 'stock' => 10, 'is_active' => true],
            ['name' => 'Ringsleutelset', 'category' => 'Gereedschap', 'description' => 'Verschillende maten', 'stock' => 20, 'is_active' => true],
            ['name' => 'Steeksleutelset', 'category' => 'Gereedschap', 'description' => 'Verschillende maten', 'stock' => 20, 'is_active' => true],
            ['name' => 'Momentsleutel', 'category' => 'Gereedschap', 'description' => 'Instelbaar', 'stock' => 12, 'is_active' => true],
            ['name' => 'Inbussleutelset', 'category' => 'Gereedschap', 'description' => 'Los en in set', 'stock' => 25, 'is_active' => true],
            ['name' => 'Schroevendraaierset plat', 'category' => 'Gereedschap', 'description' => 'Plat', 'stock' => 30, 'is_active' => true],
            ['name' => 'Schroevendraaierset kruiskop', 'category' => 'Gereedschap', 'description' => 'Kruiskop', 'stock' => 30, 'is_active' => true],
            ['name' => 'Schroevendraaierset Torx', 'category' => 'Gereedschap', 'description' => 'Torx', 'stock' => 20, 'is_active' => true],
            ['name' => 'Geïsoleerde schroevendraaiers', 'category' => 'Gereedschap', 'description' => 'Geïsoleerd', 'stock' => 15, 'is_active' => true],
            ['name' => 'Combinatietang', 'category' => 'Gereedschap', 'description' => '200mm', 'stock' => 25, 'is_active' => true],
            ['name' => 'Waterpomptang', 'category' => 'Gereedschap', 'description' => 'Verstelbaar', 'stock' => 20, 'is_active' => true],
            ['name' => 'Kniptang', 'category' => 'Gereedschap', 'description' => 'Voor kabel', 'stock' => 18, 'is_active' => true],
            ['name' => 'Punttang', 'category' => 'Gereedschap', 'description' => 'Fijn werk', 'stock' => 18, 'is_active' => true],
            ['name' => 'Krimptang', 'category' => 'Gereedschap', 'description' => 'Voor kabelschoenen', 'stock' => 12, 'is_active' => true],
            ['name' => 'Kabelstripper', 'category' => 'Gereedschap', 'description' => 'Voor elektriciteit', 'stock' => 15, 'is_active' => true],
            ['name' => 'Hamer', 'category' => 'Gereedschap', 'description' => 'Klauwhamer', 'stock' => 20, 'is_active' => true],
            ['name' => 'Kunststofhamer', 'category' => 'Gereedschap', 'description' => 'Zachte slag', 'stock' => 15, 'is_active' => true],
            ['name' => 'Moker', 'category' => 'Gereedschap', 'description' => 'Zware slag', 'stock' => 10, 'is_active' => true],
            ['name' => 'Breekijzer', 'category' => 'Gereedschap', 'description' => '60cm', 'stock' => 15, 'is_active' => true],
            ['name' => 'Haakse slijper', 'category' => 'Gereedschap', 'description' => '125mm', 'stock' => 8, 'is_active' => true],
            ['name' => 'Accuboormachine', 'category' => 'Gereedschap', 'description' => '18V', 'stock' => 10, 'is_active' => true],
            ['name' => 'Klopboormachine', 'category' => 'Gereedschap', 'description' => 'Inclusief boren', 'stock' => 6, 'is_active' => true],
            ['name' => 'Schroefmachine', 'category' => 'Gereedschap', 'description' => 'Accu', 'stock' => 8, 'is_active' => true],
            ['name' => 'Slagmoersleutel', 'category' => 'Gereedschap', 'description' => 'Pneumatisch of accu', 'stock' => 5, 'is_active' => true],
            ['name' => 'Waterpas', 'category' => 'Gereedschap', 'description' => '80cm', 'stock' => 15, 'is_active' => true],
            ['name' => 'Laserwaterpas', 'category' => 'Gereedschap', 'description' => 'Zelfnivellerend', 'stock' => 5, 'is_active' => true],
            ['name' => 'Meetlint', 'category' => 'Gereedschap', 'description' => '5m', 'stock' => 25, 'is_active' => true],
            ['name' => 'Rolmeter', 'category' => 'Gereedschap', 'description' => '50m', 'stock' => 10, 'is_active' => true],
            ['name' => 'Spanningstester', 'category' => 'Gereedschap', 'description' => 'Spanningszoeker', 'stock' => 15, 'is_active' => true],
            ['name' => 'Multimeter', 'category' => 'Gereedschap', 'description' => 'Digitaal', 'stock' => 10, 'is_active' => true],

            // ==================== TECHNISCHE ONDERHOUDSMATERIALEN ====================
            ['name' => 'Smeervet foodgrade', 'category' => 'Technisch onderhoud', 'description' => 'Foodgrade', 'stock' => 20, 'is_active' => true],
            ['name' => 'Smeervet EP2', 'category' => 'Technisch onderhoud', 'description' => 'EP2', 'stock' => 25, 'is_active' => true],
            ['name' => 'Smeervet lithium', 'category' => 'Technisch onderhoud', 'description' => 'Lithium', 'stock' => 20, 'is_active' => true],
            ['name' => 'O-ringen set', 'category' => 'Technisch onderhoud', 'description' => 'Div. maten en types', 'stock' => 15, 'is_active' => true],
            ['name' => 'Pakkingen papier', 'category' => 'Technisch onderhoud', 'description' => 'Papier', 'stock' => 30, 'is_active' => true],
            ['name' => 'Pakkingen rubber', 'category' => 'Technisch onderhoud', 'description' => 'Rubber', 'stock' => 30, 'is_active' => true],
            ['name' => 'Pakkingen EPDM', 'category' => 'Technisch onderhoud', 'description' => 'EPDM', 'stock' => 25, 'is_active' => true],
            ['name' => 'PTFE tape', 'category' => 'Technisch onderhoud', 'description' => 'Voor draadverbindingen', 'stock' => 50, 'is_active' => true],
            ['name' => 'Loctite', 'category' => 'Technisch onderhoud', 'description' => 'Draadborging', 'stock' => 30, 'is_active' => true],
            ['name' => 'PVC slang', 'category' => 'Technisch onderhoud', 'description' => 'Div. diameters', 'stock' => 50, 'is_active' => true],
            ['name' => 'PE slang', 'category' => 'Technisch onderhoud', 'description' => 'Div. diameters', 'stock' => 40, 'is_active' => true],
            ['name' => 'Persslang', 'category' => 'Technisch onderhoud', 'description' => 'Hydrauliek', 'stock' => 20, 'is_active' => true],
            ['name' => 'PVC-fittingen', 'category' => 'Technisch onderhoud', 'description' => 'Bochten, T-stukken', 'stock' => 60, 'is_active' => true],
            ['name' => 'Koppeling Geka', 'category' => 'Technisch onderhoud', 'description' => 'Slangkoppeling', 'stock' => 40, 'is_active' => true],
            ['name' => 'Koppeling Gardena', 'category' => 'Technisch onderhoud', 'description' => 'Tuinwater', 'stock' => 35, 'is_active' => true],
            ['name' => 'Camlock koppeling', 'category' => 'Technisch onderhoud', 'description' => 'Industrieel', 'stock' => 25, 'is_active' => true],
            ['name' => 'V-snaren', 'category' => 'Technisch onderhoud', 'description' => 'Verschillende maten', 'stock' => 30, 'is_active' => true],
            ['name' => 'Kettingen', 'category' => 'Technisch onderhoud', 'description' => 'Verschillende lengtes', 'stock' => 20, 'is_active' => true],
            ['name' => 'Kabels M16-M32', 'category' => 'Technisch onderhoud', 'description' => 'M16 tot M32', 'stock' => 40, 'is_active' => true],
            ['name' => 'Wartels M16-M32', 'category' => 'Technisch onderhoud', 'description' => 'M16 tot M32', 'stock' => 40, 'is_active' => true],
            ['name' => 'Aansluitdozen', 'category' => 'Technisch onderhoud', 'description' => 'Elektrisch', 'stock' => 20, 'is_active' => true],
            ['name' => 'Pneumatische koppelingen', 'category' => 'Technisch onderhoud', 'description' => 'Voor perslucht', 'stock' => 30, 'is_active' => true],
            ['name' => 'Trillingsdempers', 'category' => 'Technisch onderhoud', 'description' => 'Rubber', 'stock' => 25, 'is_active' => true],

            // ==================== AQUAFIN/RIOLERING GERELATEERDE TOOLS ====================
            ['name' => 'Putdekselhaak', 'category' => 'Aquafin tools', 'description' => 'Mangatopener', 'stock' => 15, 'is_active' => true],
            ['name' => 'Rioolcamera', 'category' => 'Aquafin tools', 'description' => 'Inspectiecamera', 'stock' => 5, 'is_active' => true],
            ['name' => 'Gasdetectietoestel H₂S', 'category' => 'Aquafin tools', 'description' => 'H₂S', 'stock' => 8, 'is_active' => true],
            ['name' => 'Gasdetectietoestel CO', 'category' => 'Aquafin tools', 'description' => 'CO', 'stock' => 8, 'is_active' => true],
            ['name' => 'Ontstoppingsveer', 'category' => 'Aquafin tools', 'description' => 'Handmatig', 'stock' => 10, 'is_active' => true],
            ['name' => 'Hogedrukreiniger', 'category' => 'Aquafin tools', 'description' => 'Voor riolen', 'stock' => 4, 'is_active' => true],
            ['name' => 'Slangenwagen', 'category' => 'Aquafin tools', 'description' => 'Op wielen', 'stock' => 8, 'is_active' => true],
            ['name' => 'Dompelpomp', 'category' => 'Aquafin tools', 'description' => 'Onderwater', 'stock' => 6, 'is_active' => true],
            ['name' => 'Rioolstop', 'category' => 'Aquafin tools', 'description' => 'Opblaasbaar', 'stock' => 12, 'is_active' => true],
            ['name' => 'Vlotterschakelaar', 'category' => 'Aquafin tools', 'description' => 'Voor pomp', 'stock' => 10, 'is_active' => true],
            ['name' => 'Ultrasoon niveaumeting', 'category' => 'Aquafin tools', 'description' => 'Ultrasoon', 'stock' => 5, 'is_active' => true],
            ['name' => 'Radar niveaumeting', 'category' => 'Aquafin tools', 'description' => 'Radar', 'stock' => 3, 'is_active' => true],
            ['name' => 'Staalnamepot', 'category' => 'Aquafin tools', 'description' => 'Voor watermonsters', 'stock' => 30, 'is_active' => true],
            ['name' => 'Monsternameapparatuur', 'category' => 'Aquafin tools', 'description' => 'Automatisch', 'stock' => 4, 'is_active' => true],

            // ==================== DIVERSEN / VERBRUIKSGOEDEREN ====================
            ['name' => 'Tie-wraps', 'category' => 'Verbruiksgoederen', 'description' => 'Verschillende maten', 'stock' => 500, 'is_active' => true],
            ['name' => 'Kabelschoenen', 'category' => 'Verbruiksgoederen', 'description' => 'Verschillende maten', 'stock' => 300, 'is_active' => true],
            ['name' => 'Markeringstape', 'category' => 'Verbruiksgoederen', 'description' => 'Waarschuwingsband', 'stock' => 50, 'is_active' => true],
            ['name' => 'Siliconenkit', 'category' => 'Verbruiksgoederen', 'description' => 'Waterdicht', 'stock' => 40, 'is_active' => true],
            ['name' => 'Lijm', 'category' => 'Verbruiksgoederen', 'description' => 'Montagelijm', 'stock' => 30, 'is_active' => true],
            ['name' => 'Reinigingsdoekjes', 'category' => 'Verbruiksgoederen', 'description' => 'Rags', 'stock' => 100, 'is_active' => true],
            ['name' => 'WD-40', 'category' => 'Verbruiksgoederen', 'description' => 'Spray', 'stock' => 50, 'is_active' => true],
            ['name' => 'Contactspray', 'category' => 'Verbruiksgoederen', 'description' => 'Voor elektronica', 'stock' => 30, 'is_active' => true],
            ['name' => 'Kettingspray', 'category' => 'Verbruiksgoederen', 'description' => 'Voor kettingen', 'stock' => 25, 'is_active' => true],
            ['name' => 'Duct tape', 'category' => 'Verbruiksgoederen', 'description' => 'Sterk plakband', 'stock' => 40, 'is_active' => true],
            ['name' => 'Isolatietape', 'category' => 'Verbruiksgoederen', 'description' => 'Zwart', 'stock' => 60, 'is_active' => true],
            ['name' => 'Batterijen AA', 'category' => 'Verbruiksgoederen', 'description' => 'AA', 'stock' => 100, 'is_active' => true],
            ['name' => 'Batterijen AAA', 'category' => 'Verbruiksgoederen', 'description' => 'AAA', 'stock' => 100, 'is_active' => true],
            ['name' => 'Accu\'s 18V', 'category' => 'Verbruiksgoederen', 'description' => 'Voor machines', 'stock' => 20, 'is_active' => true],
            ['name' => 'Fles perslucht', 'category' => 'Verbruiksgoederen', 'description' => 'Ademlucht', 'stock' => 10, 'is_active' => true],
        ];

        foreach ($materials as $material) {
            $material['created_at'] = now();
            $material['updated_at'] = now();
            DB::table('materials')->insert($material);
        }
}



}
