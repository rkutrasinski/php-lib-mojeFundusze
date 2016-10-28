<?php


class ParseIdx {
        private $indexFunds = array();
        function __construct()
            {
            global $cfg;
                   
            if ($hd = fopen($cfg['dir']['data']."/".$cfg['files']['index'], "r")) {
                $file = fread($hd,  filesize($cfg['dir']['data']."/".$cfg['files']['index']));
                $rows = explode("\n",$file);
                foreach ($rows as $line)
                    {
                    $cols = explode(";",$line);
                    if (substr_count($line, ';')>2)
                        {
                        $this->indexFunds[] = array(
                                                    'fid' => $cols[0],
                                                    'sname' => $cols[1],
                                                    'currency' => $cols[2],
                                                    'umbrella' => $cols[3]
                                                    );
                        }
                    }
                fclose ($hd);
                }    
            }
        public function getIndexArray()
                {
                return $this->indexFunds;
                }
    }
                
                
            
        

