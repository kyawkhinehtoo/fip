<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\SnPorts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class SNUpdate implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row[0] != 'All' ){
            $sn = SnPorts::Join('dn_ports','sn_ports.dn_id','=','dn_ports.id')
                    ->where('dn_ports.name','=',trim($row[1]))
                    ->where('sn_ports.name','=',trim($row[2]))
                    ->select('sn_ports.*')
                    ->first();  
             if($sn){
                $sn->location = trim($row[3]);
                $sn->input_dbm = trim($row[4]);
                $sn->update();
                $log = $row[0]." Updated";
                Storage::append('sn_update.log',$log);
              }else{
                $log = $row[0]." Not Found";
                Storage::append('sn_update.log',$log);
             }
     

         
        }
    }
}
