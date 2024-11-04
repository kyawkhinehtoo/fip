<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\DnPorts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class DNUpdate implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row[0] != 'DN Name' ){
            $dn = DnPorts::where('name','=',trim($row[0]))->first();  
             if($dn){
                $dn->location = trim($row[1]);
                $dn->input_dbm = trim($row[2]);
                $dn->update();
                $log = $row[0]." Updated";
                Storage::append('dn_update.log',$log);
              }else{
                $log = $row[0]." Not Found";
                Storage::append('dn_update.log',$log);
             }
     

         
        }
    }
}
