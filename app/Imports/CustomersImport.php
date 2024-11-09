<?php

namespace App\Imports;

use App\Models\BundleEquiptment;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Customer;
use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\DnPorts;
use App\Models\SnPorts;
use App\Models\Subcom;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToModel, WithHeadingRow
{
  /**
   * @param array $row
   *
   * @return \Illuminate\Database\Eloquent\Model|null
   */
  public function model(array $row)
  {

    $township_id = (trim($row['township'])  != "") ? Township::where('name', trim($row['township']))->first() : null;
    $package_id = (trim($row['original_package_plan'])  != "") ? Package::where('name', trim($row['original_package_plan']))->first() : null;
    $sale_person_id = (trim($row['create_person'])  != "") ? User::where('name', trim($row['create_person']))->first() : null;
    $status_id = (trim($row['installation_date'])  != "") ? 2 : 1;
    $subcom_id = (trim($row['installation_team'])  != "") ? Subcom::where('name', trim($row['installation_team']))->first() : null;
    $sn_id = (trim($row['dn_sn'])  != "") ? SnPorts::where('sn_ports.name', trim($row['dn_sn']))->first() : null;
    $pop_id = (trim($row['dn_sn'])  != "") ? 1 : null;
    $pop_device_id = (trim($row['dn_sn'])  != "") ? 1 : null;
    $bundle = (trim($row['ont_model'])  != "") ? BundleEquiptment::where('name', trim($row['ont_model']))->first() : null;
    $project_id = 1;


    $cus = Customer::where('ftth_id', '=', trim($row['onu_idcustomer_id']))->first();
    if ($cus) {
      $cus->ftth_id = trim($row['onu_idcustomer_id']);
      $cus->name = trim($row['user_name']);
      $cus->phone_1 = trim($row['contactnumber']);
      $cus->address = trim($row['full_address']);
      $cus->location = trim($row['lat_long']);

      $cus->order_date = (trim($row['installation_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['installation_date'])) : null;
      $cus->installation_date = (trim($row['installation_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['installation_date'])) : null;
      $cus->prefer_install_date = (trim($row['installation_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['installation_date'])) : null;
      $cus->service_activation_date = (trim($row['service_activation_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['service_activation_date'])) : null;

      $cus->sale_channel = 'D2D';
      $cus->sale_remark = trim($row['remark']);
      $cus->township_id = ($township_id) ? $township_id->id : null;
      $cus->package_id = ($package_id) ? $package_id->id : null;
      $cus->sale_person_id = ($sale_person_id) ? $sale_person_id->id : null;
      $cus->status_id = $status_id;
      $cus->subcom_id = ($subcom_id) ? $subcom_id->id : null;
      $cus->sn_id = ($sn_id) ? $sn_id->id : null;
      $cus->pop_id = $pop_id;
      $cus->onu_serial = trim($row['serial_no_onu']);
      $cus->deleted = 0;
      $cus->pppoe_account = trim($row['pppoe_user_name']);
      $cus->pppoe_password = trim($row['pppoe_password']);
      $cus->customer_type = 1;
      $cus->bundle = ($bundle) ? $bundle->id : null;
      $cus->project_id = $project_id;
      $cus->vlan = trim($row['vlan']);
      $cus->wlan_ssid = trim($row['onu_wlan_ssid']);
      $cus->wlan_password = trim($row['onu_wlan_password']);
      $cus->pop_device_id = $pop_device_id;
      $cus->gpon_ontid = trim($row['onu_id']);
      //$cus->email =trim($row['']);
      //$cus->splitter_no =trim($row['']);
      //$cus->social_account =trim($row['']);
      $cus->update();
      Storage::append('CustomerImport.log', trim($row['onu_idcustomer_id']) . ' Update !');
    } else {
      $customer = new Customer();
      $customer->ftth_id = trim($row['onu_idcustomer_id']);
      $customer->name = trim($row['user_name']);
      $customer->phone_1 = trim($row['contactnumber']);
      $customer->address = trim($row['full_address']);
      $customer->location = trim($row['lat_long']);

      $customer->order_date = (trim($row['installation_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['installation_date'])) : null;
      $customer->installation_date = (trim($row['installation_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['installation_date'])) : null;
      $customer->prefer_install_date = (trim($row['installation_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['installation_date'])) : null;
      $customer->service_activation_date = (trim($row['service_activation_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['service_activation_date'])) : null;

      $customer->sale_channel = 'D2D';
      $customer->sale_remark = trim($row['remark']);
      $customer->township_id = ($township_id) ? $township_id->id : null;
      $customer->package_id = ($package_id) ? $package_id->id : null;
      $customer->sale_person_id = ($sale_person_id) ? $sale_person_id->id : null;
      $customer->status_id = $status_id;
      $customer->subcom_id = ($subcom_id) ? $subcom_id->id : null;
      $customer->sn_id = ($sn_id) ? $sn_id->id : null;
      $customer->pop_id = $pop_id;
      $customer->onu_serial = trim($row['serial_no_onu']);
      $customer->deleted = 0;
      $customer->pppoe_account = trim($row['pppoe_user_name']);
      $customer->pppoe_password = trim($row['pppoe_password']);
      $customer->customer_type = 1;
      $customer->bundle = ($bundle) ? $bundle->id : null;
      $customer->project_id = $project_id;
      $customer->vlan = trim($row['vlan']);
      $customer->wlan_ssid = trim($row['onu_wlan_ssid']);
      $customer->wlan_password = trim($row['onu_wlan_password']);
      $customer->pop_device_id = $pop_device_id;
      $customer->gpon_ontid = trim($row['onu_id']);

      //$customer->email =trim($row['']);
      //$customer->splitter_no =trim($row['']);
      //$customer->social_account =trim($row['']);
      $customer->save();
      Storage::append('CustomerImport.log', trim($row['onu_idcustomer_id']) . ' Save!');
      return $customer;
    }
  }
}