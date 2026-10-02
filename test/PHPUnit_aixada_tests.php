<?php
if (version_compare(PHP_VERSION, '8.0.0') < 0) {
    // En la consola (CLI) de PHP 7.4 suele tener un buffer de 0 esto proboca
    // que cuando aixada lanza `session_start();` PHPUnit 9 de el error:
    // - Cannot start session when headers already sent.
    // -> Arancamos la sesión antes que lo haga Aixada si estamos con PHP 7.4
    session_start();
}

// - Simulamos un GET para el test de envio de correos ja que Aixada usa alguno
//   de esos datos para indicar a los destinaterios desde que pagina se ha
//   enviado el mensaje.
$_SERVER = [
    'SERVER_PROTOCOL' => 'HTTP/1.1',
    'SERVER_NAME' => 'localhost',
    'SERVER_PORT' => '80',
    'REQUEST_URI' => '/index.php',
    'REQUEST_METHOD' => 'GET',
    'SCRIPT_NAME' => '/index.php',
    'PHP_SELF'    => '/index.php',
    'HTTP_HOST'   => 'localhost:80',
    'HTTP_SEC_FETCH_DEST' => 'document',
    'HTTP_SEC_FETCH_MODE' => 'navigate',
    'HTTP_SEC_FETCH_SITE' => 'none',
];

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Depends;

/**
 * =============================================================================
 * El juego de pruebas está preparado para que funcione con:
 *      - PHP 7.4 con PHPUnit 9.6.35
 * y con:
 *      - PHP 8.5 con PHPUnit 13.2.6
 * =============================================================================
 */      

#[TestDox('Comprobar algunas partes del código de Aixada con PHPUnit')]
class PHPUnit_aixada_tests extends TestCase
{
    //———————————————————————————— Hojas de cálculo ————————————————————————————

    #[Test]
    #[TestDox('Comprobar importación de hojas de cálculo con PHP >= 7.4 ./external/php74/spreadsheet-reader')]
    public function test_comprobar_importacion_de_hojas_de_calculo(): void
    {  
        $base_folder = dirname(dirname(__FILE__)) . '/';
        require_once $base_folder . 'external/php74/spreadsheet-reader/SpreadsheetReader.php';
        require_once $base_folder . 'external/php74/spreadsheet-reader/php-excel-reader/excel_reader2.php';
        
        $i_sheet_import = function ($file_name) {
            $file_path = 'test/test-spreadsheet-files/' . $file_name;
            try {
                
                $Spreadsheet = new SpreadsheetReader($file_path);

                $Sheets = $Spreadsheet -> Sheets();
                $v_shets = [];
                foreach ($Sheets as $Index => $Name) {
                    $v_shet = [];
                    $Spreadsheet -> ChangeSheet($Index);
                    foreach ($Spreadsheet as $Key => $Row) {
                        $v_row = [];
                        foreach($Row as $k => $v) {
                            if ($v !== '') {
                                $v_row[$k]=$v;
                            }
                        }
                        if (count($v_row) > 0) {
                            $v_shet[$Key]=$v_row;
                        }
                    }
                    if (count($v_shet) > 0) {
                        $v_shets[$Name]=$v_shet;
                    }
                }
                return $v_shets;
            } catch (Exception $e) {
                error_log($e->getMessage());
                return [ $e->getMessage() ];
            }
        };

        $from_excel = [ 'Hoja1' => [
                1 => [ 0 => '1 to 9', 1 => 123456789.0, ],
                2 => [ 0 => 'Currency (Euro)', 1 => '5.43 €', ],
                3 => [ 0 => 'Currency (UK pound) +0,111', 1 => '£5.54', ],
                4 => [ 0 => 'Currency (US dollar) +0,111', 1 => '$5.65', ],
                5 => [ 0 => 'Normal (2 decimal) +0,111', 1 => '5.77', ],
                6 => [ 2 => 'C6', ],
                7 => [ 0 => 'àèìòù', 1 => 'ÀÈÌÒÙ', ],
                8 => [ 0 => 'áéíóú', 1 => 'ÁÉÍÓÚ', ],
                9 => [ 0 => 'aeiou', 1 => 'AEIOU', ],
                10 => [ 0 => 'çñäïü', 1 => 'ÇÑÄÏÜ', ],
                12 => [ 0 => 'Christmas 2026', 1 => '12/25/2026', ],
                13 => [ 0 => 'Last day of the past year', 1 => '12/31/2025', ],
                14 => [ 0 => 'Saint Stephen\'s Day', 1 => '12/26/2026', ],
                16 => [ 4 => 'E16', ] ],
             'Hoja2' => [ 2 => [ 1 => 'B2', ] ] ];
        $from_ods = [ 'Hoja1' => [
                0 => [ 0 => '1 to 9', 1 => '123456789', ],
                1 => [ 0 => 'Currency (Euro)', 1 => '5,43 €', ],
                2 => [ 0 => 'Currency (UK pound) +0,111', 1 => '£5,54', ],
                3 => [ 0 => 'Currency (US dollar) +0,111', 1 => '$5,65', ],
                4 => [ 0 => 'Normal (2 decimal) +0,111', 1 => '5,77', ],
                5 => [ 2 => 'C6', ],
                6 => [ 0 => 'àèìòù', 1 => 'ÀÈÌÒÙ', ],
                7 => [ 0 => 'áéíóú', 1 => 'ÁÉÍÓÚ', ],
                8 => [ 0 => 'aeiou', 1 => 'AEIOU', ],
                9 => [ 0 => 'çñäïü', 1 => 'ÇÑÄÏÜ', ],
                11 => [ 0 => 'Christmas 2026', 1 => '25/12/2026', ],
                12 => [ 0 => 'Last day of the past year', 1 => '31/12/2025', ],
                13 => [ 0 => 'Saint Stephen\'s Day', 1 => '26/12/2026', ],
                15 => [ 4 => 'E16', ] ],
             'Hoja2' => [ 1 => [ 1 => 'B2' ] ] ];
        $from_csv_sheet = [
            0 => [ 0 => '1 to 9', 1 => '123456789', ],
            1 => [ 0 => 'Currency (Euro)', 1 => '5,43 €', ],
            2 => [ 0 => 'Currency (UK pound) +0,111', 1 => '£5,54', ],
            3 => [ 0 => 'Currency (US dollar) +0,111', 1 => '$5,65', ],
            4 => [ 0 => 'Normal (2 decimal) +0,111', 1 => '5,77', ],
            5 => [ 2 => 'C6', ],
            6 => [ 0 => 'àèìòù', 1 => 'ÀÈÌÒÙ', ],
            7 => [ 0 => 'áéíóú', 1 => 'ÁÉÍÓÚ', ],
            8 => [ 0 => 'aeiou', 1 => 'AEIOU', ],
            9 => [ 0 => 'çñäïü', 1 => 'ÇÑÄÏÜ', ],
            11 => [ 0 => 'Christmas 2026', 1 => '12/25/2026', ],
            12 => [ 0 => 'Last day of the past year', 1 => '12/31/2025', ],
            13 => [ 0 => 'Saint Stephen\'s Day', 1 => '12/26/2026', ],
            15 => [ 4 => 'E16', ], ];

        // Provamos los distintos archivos
        $this->assertEquals(['sheet.csv' => $from_csv_sheet ], $i_sheet_import('sheet.csv'));

        $this->assertEquals(['sheet.tsv' => $from_csv_sheet ], $i_sheet_import('sheet.tsv'));

        $this->assertEquals($from_ods, $i_sheet_import('sheet.ods'));

        $this->assertEquals($from_excel, $i_sheet_import('sheet.xls'));

        $this->assertEquals($from_excel, $i_sheet_import('sheet.xlsx'));

    }

    //——————————————————————————————— Correo ———————————————————————————————————
    // - Como servidor de SMTP local de pruevas se debe ejecutar
    //      [mailpit](https://mailpit.axllent.org/) desde linia de mandatos del
    //      sistema.
    // - Para acceder al SMTP:  http://localhost:1025
    // - Para ver correos:      http://localhost:8025

    #[Test]
    #[TestDox('Comprobar el envio de correos')]
    public function test_enviar_un_email(): string
    {
        $date = new DateTime();
        $email_sent_on = "=>" . $date->format('Y-m-d H:i:s.u') . "<= ";
        
        $this->define_root();
        require_once __ROOT__ . "php/utilities/general.php";
        $this->put_config([
            'admin_email' => "test@dummy_origin.com",
            'email_SMTP_pswd' => 'no_nesesario',
            'email_safe_replyTo' => true,
            'email_SMTP_host' => 'localhost',
            'email_SMTP_port' => 1025,
            'email_SMTP_encryption' => '',
            'email_SMTP_verifyCert' => false
        ]);
        
        $toEmail = 'some-user@dummy_destination.com'; 
        $options = [
            'cc' => 'another-user@dummy_destination.com,yet-another-user@dummy_destination.com',
            'bcc' => 'and-another-user@dummy_destination.com'
        ];

        $testUTF = 'áàéèïíoóòüúçñ ÁÀÉÈÏÍOÓÒÜÚÇÑ €=EUR';

        $subject = $email_sent_on . "IT'S A TEST: ". $testUTF;

        $messageHTML = "<b>Is a test using PHP v." . PHP_VERSION . "</b><br>
            Test utf-8: " . $testUTF . "<br><br>
            Options:<br><pre style='margin: 0 0 0 3em'>" . 
                'toEmail => ' . $toEmail ."\n" .
                var_export($options, true). 
            "</pre>
            Config:<br><pre style='margin: 0 0 0 3em'>" . var_export(array(
                'coop_name' => get_config('coop_name'),
                'admin_email' => get_config('admin_email'),
                'email_SMTP_host' => get_config('email_SMTP_host'),
                'email_SMTP_pswd' => '(hidden)',
                'email_SMTP_port' => get_config('email_SMTP_port'),
                'email_SMTP_encryption' => get_config('email_SMTP_encryption'),
                'email_SMTP_verifyCert' => get_config('email_SMTP_verifyCert')
                ), true) . "</pre>";

        $email_was_sent = send_mail($toEmail, $subject, $messageHTML, $options);
        ob_end_flush();
        $this->assertEquals(true, $email_was_sent);

        return $email_sent_on;
    }

    #[Test]
    #[TestDox('Comprobar la recepción de un correo')]
    /**
     * @depends test_enviar_un_email
     */
    #[Depends('test_enviar_un_email')]
    public function test_recepcion_del_email(string $email_sent_on): void
    {
        $json = @file_get_contents('http://localhost:8025/api/v1/messages');

        if ($json === false) {
            throw new RuntimeException('No se puede conectar com Mailpit.');
        }
        $data = json_decode($json, true);

        $found = false;
        foreach ($data['messages'] as $k => $m) {
            // Se usa str_contains() ya que Aixada pone un prefijo con nombre coop
            if ( str_contains($m['Subject'],$email_sent_on) ) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Se ha recivido correctamente el correo.');
    }

    //————————————————————————————————— Aixada —————————————————————————————————

    #[Test]
    #[TestDox('Comprobar función DBWrap->get_error() de ./php/inc/database.php')]
    public function test_comprobar_funcion_get_error_de_DBWrap(): void
    {
        // Instanciamos la db de Aixada
        $db_aixada = $this->get_db();

        // Ejecutar en vacio
        $errorMsg = $db_aixada->get_error();
        $this->assertEmpty($errorMsg);

        // Ejecitar una SELECT, no responde error.
        $rs = $db_aixada->Execute('SELECT id FROM aixada_user where id=1;');
        $errorMsg = $db_aixada->get_error();
        $this->assertEmpty($errorMsg);

        $error_occurred = false;
        try {
            $rs = $db_aixada->Execute('SELECT id FROM aixada_userr where id=1;');
        } catch (Exception $e) {
            $error_occurred = true;
            $errorMsg = $db_aixada->get_error();
            // error_log('$errorMsg ="' . $errorMsg . '"');
            $this->assertNotEmpty($errorMsg);
            $this->assertEquals("Table 'lacistella2.aixada_userr' doesn't exist", $errorMsg);
        }
        $this->assertEquals(true, $error_occurred);
    }

    #[Test]
    #[TestDox('Comprobar valor DBWrap->current_query_SQL de ./php/inc/database.php vía ./php/lib/table_with_ref.php')]
    public function test_comprobar_valor_de_current_query_SQL_en_DBWrap(): void
    {
        // Instanciamos la db de Aixada
        $db_aixada = $this->get_db();

        require_once __ROOT__ . 'php/lib/table_with_ref.php';

        $fkm = new foreign_key_manager("aixada_version");
        $this->assertEquals('SELECT * FROM aixada_version LIMIT 1', $db_aixada->current_query_SQL);

        $error_occurred = false;
        try {
            $fkm = new foreign_key_manager("aixada_userr");
        } catch (Exception $e) {
            $error_occurred = true;
            // error_log('$db_aixada->current_query_SQL ="' . $db_aixada->current_query_SQL . '"');
            $this->assertEquals('SHOW CREATE TABLE aixada_userr', $db_aixada->current_query_SQL);
        }
        $this->assertEquals(true, $error_occurred);
    }

    //———————————————— Funciones internas para los test —————————————————————————

    // Definir __ROOT__ y DS tal como lo hace Aixada
    private function define_root()
    {
        if(! defined('__ROOT__')) {
            $f_root = dirname(dirname(__FILE__)). '/';

            // Los modulos de Aixada usan las constantes DS i __ROOT__
            define('DS', DIRECTORY_SEPARATOR);
            define('__ROOT__', $f_root);
        }
    }

    // Abrir la base de datos y mantener una buena gestión output buffers.
    private function get_db()
    {
        $already_required = defined('__ROOT__');

        if(! $already_required) {
            $this->define_root();

            // database.php ya se encarga de hacer los require_once que necesiste
            require_once __ROOT__ . "php/inc/database.php";
        }

        $db = DBWrap::get_instance();

        // php/inc/database.php abre con ob_start();
        // Lo cerramos para que PHPUnit concluya bien
        if(! $already_required) {
            ob_end_flush();
        }

        return $db;
    }

    // Trick to force some config parameters
    function put_config($forced_cfg) {
        $cfg = configuration_vars::get_instance();
        foreach ($forced_cfg as $k => $v) {
            $cfg->$k = $v;
        }
    }
}
