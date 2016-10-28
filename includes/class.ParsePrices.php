<?php


class ParsePrices {
        private $prices = array();
        
        function __construct($fid = null)
            {
            global $cfg;
            
            if (isset($fid))
                { $f_name = $cfg['dir']['data']."/".$fid;
                  $f_id = preg_replace("/[^0-9]/","",$fid);
                
                }
            else
                { $f_name = $cfg['dir']['data']."/".$cfg['files']['prices'];}
            
            if ($hd = fopen($f_name, "r")) {
                $file = fread($hd,  filesize($f_name));
                $rows = explode("\n",$file);
                
                foreach ($rows as $line)
                    {
                    if (substr_count($line, ';')>2)
                        {
                        $cols = explode(";",$line);
                        if (isset($fid))
                            {
                            if (substr_count($line, ';')>5)                            
                                {
                                $this->prices[] = array('fid' => $f_id,
                                             'date' => $cols[1],
                                             'price' => $cols[5]
                                            );
                                }
                            }
                        else
                            {
                            if (substr_count($line, ';')>2)
                                {
                                $this->prices[] = array('fid' => $cols[0],
                                             'date' => $cols[1],
                                             'price' => $cols[2]
                                            );                                    
                                }
                            }
                        }
                        
                    }
                fclose ($hd);
                }    
            }
        public function getIndexArray()
            {
                return $this->prices;
            }
        
        
                
        

}
