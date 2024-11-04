<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Customer;
use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\DnPorts;
use App\Models\SnPorts;
use Illuminate\Support\Facades\Storage;
class CustomersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(trim($row[0]!="No.")){
            //for Customer Import
            $dn_sn = DnPorts::join('sn_ports','sn_ports.dn_id','=','dn_ports.id')
            ->where('dn_ports.name','=',$row[1])
            ->where('sn_ports.name','=',$row[2])
            ->select('dn_ports.id as dn_id','sn_ports.id as sn_id')
            ->first();
            $township = Township::where('name','=',$row[11])->first();
            $package = Package::where('speed','LIKE','%'.$row[3].'%')
            ->where('type','=',$row[2])
            ->first();
            $status = Status::where('name','LIKE','%'.$row[27].'%')->first();  
            $subcom = User::where('name','LIKE','%'.$row[26].'%')->first();
         
                $cus = Customer::where('ftth_id','=',$row[1])->first();
                if($cus){
                   
                  
                    $cus->ftth_id = $row[1];
                    $cus->package_id = ($package)?$package->id:dd($row[3]);
                    if($row[4] != "")
                    $cus->extra_bandwidth = $row[4];
                    if($row[5] != "")
                    $cus->contract_term = $row[5];
                    if($row[6] != "")
                    $cus->advance_payment = $row[6];
                  //  $customer->advance_payment_day = trim($row[9]);
                    if($row[7] != "")
                    $cus->pppoe_account = $row[7];
                    if($row[8] != "")
                    $cus->pppoe_password = $row[8];
                    if($row[9] != "")
                    $cus->name = $row[9];
                    if($row[10] != "")
                    $cus->address =$row[10];
                    $cus->township_id = ($township)?$township->id:dd($row[11]);
                    $contact = ($row[13])?preg_replace('/\s+/', '', $row[13]):null;//remove space from email
                    if($contact){
                    if( strpos($contact, ',') !== false ) {
                        $phone = explode(",", $contact);
                        $cus->phone_1 = $phone[0];
                        $cus->phone_2 = $phone[1];
                      }else{
                        $cus->phone_1 = $contact;
                      }
                    }
                    if($row[14] != "")
                    $cus->location = $row[14];
                    if($row[15] != "")
                    $cus->fiber_distance = $row[15];
                    $cus->sn_id = ($dn_sn)?$dn_sn->sn_id:null;
                    if($row[19] != "")
                    $cus->splitter_no = $row[19];
                    if($row[20] != "")
                    $cus->installation_date = ($row[20])?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[20]):null;
                    if($row[21] != "")
                    $cus->onu_serial = $row[21];
                    if($row[22] != "")
                    $cus->fc_damaged = $row[22];
                    if($row[23] != "")
                    $cus->fc_used = $row[23];
                    if($row[24] != "")
                    $cus->sale_remark = $row[24];
                    if($row[25] != "")
                    $cus->installation_remark = $row[25];
                    if($row[26] != "")
                    $cus->subcom_id = ($subcom)?$subcom->id:null;
                    if($row[27] != "")
                    $cus->status_id = ($status)?$status->id:dd($row[27]);
                    $cus->deleted = 0;
                    $cus->update();
                    Storage::append('CustomerImport.log', $row[1].' Update !');
                }else{
                    $customer = new Customer();
                    $customer->ftth_id = $row[1];
                    $customer->package_id = ($package)?$package->id:dd($row[3]);
                    $customer->extra_bandwidth = $row[4];
                    $customer->contract_term = $row[5];
                    $customer->advance_payment = $row[6];
                  //  $customer->advance_payment_day = trim($row[9]);
                    $customer->pppoe_account = $row[7];
                    $customer->pppoe_password = $row[8];
                    $customer->name = $row[9];
                    $customer->address =$row[10];
                    $customer->township_id = ($township)?$township->id:dd($row[11]);
                    $contact = ($row[13])?preg_replace('/\s+/', '', $row[13]):null;//remove space from email
                    if($contact){
                    if( strpos($contact, ',') !== false ) {
                        $phone = explode(",", $contact);
                        $customer->phone_1 = $phone[0];
                        $customer->phone_2 = $phone[1];
                      }else{
                        $customer->phone_1 = $contact;
                      }
                    }
                    $customer->location = $row[14];
                    $customer->fiber_distance = $row[15];
                    $customer->sn_id = ($dn_sn)?$dn_sn->sn_id:null;
                    $customer->splitter_no = $row[19];
                    $customer->installation_date = ($row[20])?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[20]):null;
                    $customer->onu_serial = $row[21];
                    $customer->fc_damaged = $row[22];
                    $customer->fc_used = $row[23];
                    $customer->sale_remark = $row[24];
                    $customer->installation_remark = $row[25];
                    $customer->subcom_id = ($subcom)?$subcom->id:null;
                    $customer->status_id = ($status)?$status->id:dd($row[27]);
                    $customer->deleted = 0;
                    $customer->save();
                    Storage::append('CustomerImport.log', $row[1].' Save!');
                    return $customer;
                 }
        }

            // $j = $row[1];
            // for ($i=1; $i <= $j; $i++) { 
            //     $dn =  new DnPorts();
            //         $dn->name= trim($row[0]);
            //         $dn->port   = $i;
            //         $dn->save();
                
            // }
            
        // $customer = Customer::find('ftth_id',$row[1])->first();

        // if($customer){
        //     if(!empty($row[4]))
        //     $customer->name = trim($row[4]);

        //     if(!empty($row[5]))
        //     $customer->address = trim($row[5]);

        //     if(!empty($row[6])){
        //         $township = Township::where('name','=',$row[6])->first();
        //         $customer->township_id = $township->id;
        //     }
           
        //     if(!empty($row[8]))
        //     $customer->phone_1 = trim($row[8]);

        //     if(!empty($row[9]))
        //     $customer->location = trim($row[9]);

        //     if(!empty($row[10]))
        //     $customer->installation_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[10]));

        //     $customer->update();
           
        // }else{

        // }
        



            
        
    }
}
