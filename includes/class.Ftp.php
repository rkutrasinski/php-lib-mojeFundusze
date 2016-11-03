<?php

class Ftp
{
    protected $conn;
    private $host;
    private $user;
    private $pass;

    public function __construct($host = '', $user = '', $pass = '')
    {
        global $cfg;
        (isset($cfg['ftp']['host'])) ? $this->host = $cfg['ftp']['host'] : $this->host = $host;
        (isset($cfg['ftp']['user'])) ? $this->user = $cfg['ftp']['user'] : $this->host = $user;
        (isset($cfg['ftp']['pass'])) ? $this->pass = $cfg['ftp']['pass'] : $this->host = $pass;

        $this->conn = ftp_connect($this->host);

        if (ftp_login($this->conn, $this->user, $this->pass)) {
            return true;
        } else {
            throw new Exception("Can't login to ftp");
        }
    }

    private function ExtractFiles($file)
    {
        global $cfg;
        $zip = new ZipArchive();
        $res = $zip->open($file);
        if ($res === true) {
            for ($i = 0; $i < $zip->numFiles; ++$i) {
                $zip->extractTo($cfg['dir']['data'].'/ARCH/', array($zip->getNameIndex($i)));
                echo $zip->getNameIndex($i)."\n";
            }
            $zip->close();

            return true;
        }
    }

    public function getHistAllFiles()
    {
        global $cfg;
        ftp_pasv($this->conn, true);
        ftp_chdir($this->conn, '/');

        return ftp_get($this->conn, $cfg['dir']['data'].'/ARCH/all.zip', '!all.zip', FTP_BINARY);
    }

    public function getUmbrellaFile()
    {
        global $cfg;

        ftp_pasv($this->conn, true);
        ftp_chdir($this->conn, '/');

        return ftp_get($this->conn, $cfg['dir']['data'].'/'.$cfg['files']['umbrella'], $cfg['files']['umbrella'], FTP_ASCII);
    }

    public function getHistFile($ID)
    {
        global $cfg;
        ftp_chdir($this->conn, '/ARCH');
        ftp_get($this->conn, $cfg['dir']['data'].'/'.'ARCH/'.$ID.'.txt', $ID.'.txt', FTP_ASCII);
    }

    public function getCurrPrices()
    {
        global $cfg;
        ftp_pasv($this->conn, true);
        ftp_chdir($this->conn, '/');

        return ftp_get($this->conn, $cfg['dir']['data'].'/'.$cfg['files']['prices'], $cfg['files']['prices'], FTP_ASCII);
    }

    public function getIndex()
    {
        global $cfg;

        ftp_pasv($this->conn, true);
        ftp_chdir($this->conn, '/');

        return ftp_get($this->conn, $cfg['dir']['data'].'/'.$cfg['files']['index'], $cfg['files']['index'], FTP_ASCII);
    }

    public function getProfiles()
    {
        global $cfg;
        ftp_pasv($this->conn, true);
        ftp_chdir($this->conn, '/');

        return ftp_get($this->conn, $cfg['dir']['data'].'/'.$cfg['files']['profiles'], $cfg['files']['profiles'], FTP_ASCII);
    }

    public function gotoDocDirectory()
    {
        ftp_chdir($this->conn, '/FILES');
        ftp_pasv($this->conn, true);
    }

    public function getDocument($filename)
    {
        global $cfg;
        ftp_pasv($this->conn, true);

        $res = (ftp_get($this->conn, $cfg['dir']['data'].'/FILES/'.$filename.'.pdf', $filename.'.pdf', FTP_BINARY));
        if ($res) {
            return $res;
        } else {
            throw new Exception('Download failed: '.$filename);
        }
    }
}
