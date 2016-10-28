<?php


class ParseCsv {
        private $indexFunds = array();
        function __construct($file, array $db_cols)
            {
            $num_cols=sizeof($db_cols);
            if ($hd = fopen($file, "r")) {
                $file = fread($hd,  filesize($file));
                
                $rows = explode("\n",$file);
                    foreach ($rows as $line)
                        {
                    
                        $newLine = array();
                        $cols = explode(";",$line);
                        if (sizeof($cols)>=$num_cols)
                            {
                            $i = 0;
                            foreach ($db_cols as $db_col)
                                {
                                $newLine[$db_col]=$cols[$i];
                                $i++;
                                }
                            $this->indexFunds[] = $newLine;
                            }
                        }
                fclose ($hd);                    
                }
            }    
            
        public function getArray()
                {
                return $this->indexFunds;
                }
    }