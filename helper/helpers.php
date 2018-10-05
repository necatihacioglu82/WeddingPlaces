<?php 

namespace WeddingPlaces;

class Helpers
{
    public function Read_Csv($path, $search = '')
    {
        $headers = [];
        $result  = [];
        $row = 0;

        if (($handle = fopen($path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                $num = count($data);

                if ($row==1)
                {
                    // get headers
                    for ($c=0; $c < $num; $c++) {
                        $headers[$c] = $data[$c];
                    }
                }
                else
                {
                    // get values
                    if ( $search=="" || strpos($data[0], $search) !== false )
                    {
                        $add = [];
                        for ($c=0; $c < $num; $c++) {
                            // headers count control
                            if (count($headers) > $c)
                                $add[$headers[$c]] = $data[$c];
                        }
                        array_push($result, $add);
                    }
                }
             }
            fclose($handle);
         }

         return $result;
    }

    public function Write_Csv($path, $line) {
        try {
            if (!file_exists($path))
                return Array("success" => false, "exception" => "Dosya bulunamadı!");

            $handle = fopen($path, "a");
            fputcsv($handle, $line);
            fclose($handle);

            return Array("success" => true);
          }
          catch(\Exception $e) {
            return Array("success" => false, "exception" => $e);
          }
    }

    public function Update_Csv($path, $old_city, $old_place, $new_city, $new_place) {
        $i = 0;
        $row_update = false;
        $newdata = [];
        $handle = fopen($path, "r");

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

            // row find
            if ( $row_update == false && (trim($data[0])==trim($old_city) && trim($data[1]) == trim($old_place) ) ) {
                $newdata[$i][0] = $new_city;
                $newdata[$i][1] = $new_place;
                $row_update = true;
                $i++;
                continue;
            }
            $newdata[$i][0] = $data[0];
            $newdata[$i][1] = $data[1];
            $i++;
        }

        if ($row_update == true) {
            $fp = fopen($path, 'w');
            foreach ($newdata as $rows) {
                fputcsv($fp, $rows);
            }
            fclose($fp);
        }

        return true;
    }

    public function Delete_Csv($path, $city, $place) {
        $i = 0;
        $delete_update = false;
        $newdata = [];
        $handle = fopen($path, "r");

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

            // row find
            if ( $delete_update == false && (trim($data[0])==trim($city) && trim($data[1]) == trim($place)) ) {
                $delete_update = true;
                $i++;
                continue;
            }
            $newdata[$i][0] = $data[0];
            $newdata[$i][1] = $data[1];
            $i++;
        }

        if ($delete_update == true) {
            $fp = fopen($path, 'w');
            foreach ($newdata as $rows) {
                fputcsv($fp, $rows);
            }
            fclose($fp);
        }

        return true;
    }

    public function Array_OrderBy()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
                }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

    public function ApiLimit($second, $req_limit) {
        // read, client txt file
        $path = __DIR__ . "\\..\\public\\last_call\\" . $this->getUserIP() . ".txt";
        $txt_array = [];
        if (file_exists ( $path )) {
            $file_lines = file($path);
            foreach ($file_lines as $line) {
                array_push($txt_array, $line);
            }
        }

        $text = "";
        $now = strtotime(date("Y-m-d h:i:s"));
        $row_num = 0;
        foreach($txt_array as $key => $value) {
            if ( $now - str_replace("\r\n", "", $value) < $second ) {
                $text .= $value;
                $row_num++;
            }
        }

        if ( $row_num >= $req_limit )
            return Array("success" => false, "exception" => "No permission. Client request limit: 10 times in a minute");

        $text .= strtotime(date("Y-m-d h:i:s"))."\r\n";
        $fh = fopen($path, "w");
        fwrite($fh, $text);
        fclose($fh);
        return Array("success" => true);
    }

    public function getUserIP() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return str_replace(':', '_', $ipaddress);
    }
}