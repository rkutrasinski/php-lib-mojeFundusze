<?php

class ParseProfiles
{
    private $xml;

    public function __construct()
    {
        global $cfg;
        $f_name = $cfg['dir']['data'].'/'.$cfg['files']['profiles'];

        if ($hd = fopen($f_name, 'r')) {
            $file = fread($hd,  filesize($f_name));
            $this->xml = new SimpleXMLElement($file);
        }
    }

    public function GetArray()
    {
        $ret = array();
        foreach ($this->xml as $profile) {
            $row = array('fid' => $profile->attributes()->id,
                             'lname' => $profile->lname,
                             'risk' => $profile->risk,
                             'kiid' => $profile->kiid,
                             'report' => $profile->report,
                             'fc' => $profile->fc,
                             'rdate' => $profile->rdate,
                             'assets' => $profile->assets,
                             'family' => $profile->family,
                             'policy' => $profile->policy,
                             'company' => $profile->company,
                             'aclass' => $profile->aclass,
                             'class' => $profile->class,
                             'isin' => $profile->isin,
                             'adate' => $profile->adate,
                            );
            $ret[] = $row;
        }

        return $ret;
    }
}
