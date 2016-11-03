<?php

require_once 'includes/config.php';
require_once 'includes/class.Database.php';
require_once 'includes/class.ftp.php';
require_once 'includes/class.ParsePrices.php';
require_once 'includes/class.ParseProfiles.php';
require_once 'includes/class.ParseCsv.php';

class MojeFunduszeClient
{
    private $db;
    private $ftp;

    public function __construct()
    {
        $this->db = Database::instance();
        $this->db->log('Begin Update', 1);

        try {
            $this->ftp = new Ftp();
        } catch (Exception $e) {
            $this->db->log($e->getMessage(), 0);
        }
    }

    private function UpdateFromZip($file)
    {
        global $cfg;
        try {
            $zip = new ZipArchive();
            $res = $zip->open($file);
            if ($res === true) {
                for ($i = 0; $i < $zip->numFiles; ++$i) {
                    $zip->extractTo($cfg['dir']['data'], array($zip->getNameIndex($i)));
                    $this->db->log('Extracting file '.$zip->getNameIndex($i), 2);
                    $pr = new ParsePrices($zip->getNameIndex($i));
                    $this->db->insert('f_prices', $pr->getIndexArray());
                }
                $zip->close();

                return true;
            }
        } catch (Exception $e) {
            $this->db->log($e->getMessage(), 0);
        }
    }

    public function updateAllPrices()
    {
        global $cfg;
        try {
            if ($this->ftp->getHistAllFiles()) {
                $this->UpdateFromZip($cfg['dir']['data'].'/ARCH/all.zip');
            }
        } catch (Exception $e) {
            $this->db->log($e->getMessage(), 0);
        }
    }

    public function updateUmbrella()
    {
        $this->db->log('Update Umbrella', 1);
        global $cfg;
        try {
            if ($this->ftp->getUmbrellaFile()) {
                $csv = new ParseCsv($cfg['dir']['data'].'/'.$cfg['files']['umbrella'], array('uid', 'name'));
                $this->db->insert('index_umbrella', ($csv->getArray()));
                //var_dump($csv->getArray());
            }
        } catch (Exception $e) {
            $this->db->log($e->getMessage(), 0);
        }
    }

    public function updateIndex()
    {
        $this->db->log('Update Index', 1);
        global $cfg;
        try {
            if ($this->ftp->getIndex()) {
                $csv = new ParseCsv($cfg['dir']['data'].'/'.$cfg['files']['index'], array('fid', 'sname', 'currency', 'umbrella'));
                $this->db->insert('index_funds', ($csv->getArray()));
            }
        } catch (Exception $e) {
            $this->db->log($e->getMessage(), 0);
        }
    }

    public function updateCurrentPrices()
    {
        $this->db->log('Update Current Prices', 1);
        try {
            if ($this->ftp->getCurrPrices()) {
                $prices = new ParsePrices();
                $this->db->insert('f_prices', $prices->getIndexArray());
            }
        } catch (Exception $e) {
            $this->db->log($e->getMessage(), 0);
        }
    }

    public function updateProfiles()
    {
        $this->db->log('Update Profiles', 1);
        try {
            if ($this->ftp->getProfiles()) {
                $profiles = new ParseProfiles();
                $this->db->insert('index_funds', $profiles->GetArray());
            }
        } catch (Exception $e) {
            $this->db->log($e->getMessage(), 0);
        }
    }

    private function updateDocs($tableName)
    {
        $this->db->log("Update Documents from table $tableName", 1);
        $arFiles = ($this->db->select('SELECT `filename` from `'.$tableName.'` WHERE `downloaded`="N"'));
        $this->ftp->gotoDocDirectory();
        foreach ($arFiles as $arFile) {
            try {
                if ($this->ftp->getDocument($arFile[0])) {
                    $this->db->update($tableName, array('downloaded' => 'Y'), array('filename' => $arFile[0]));
                }
            } catch (Exception $e) {
                $this->db->log($e->getMessage(), 0);
            }
        }
    }

    public function updateDocuments()
    {
        $this->updateDocs('index_kiids');
        $this->updateDocs('index_fc');
        $this->updateDocs('index_reports');
    }
}
