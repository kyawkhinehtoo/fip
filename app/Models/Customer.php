<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ftth_id',
        'name',
        'nrc',
        'phone_1',
        'phone_2',
        'address',
        'location',
        'order_date',
        'installation_date',
        'prefer_install_date',
        'sale_channel',
        'sale_remark',
        'township_id',
        'package_id',
        'sale_person_id',
        'status_id',
        'subcom_id',
        'sn_id',
        'dn_id',
        'pop_id',
        'splitter_no',
        'fiber_distance',
        'installation_remark',
        'fc_used',
        'fc_damaged',
        'onu_serial',
        'onu_power',
        'contract_term',
        'foc',
        'foc_period',
        'advance_payment',
        'advance_payment_day',
        'extra_bandwidth',
        'bundles',
        'created_at',
        'updated_at',
        'deleted',
        'pppoe_account',
        'pppoe_password',
        'currency',
        'email'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'ftth_id' => 'string',
        'name' => 'string',
        'nrc' => 'string',
        'phone_1' => 'string',
        'phone_2' => 'string',
        'address' => 'string',
        'location' => 'string',
        'order_date' => 'date',
        'installation_date' => 'date',
        'sale_channel' => 'string',
        'sale_remark' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'dob',
        'order_date',
        'installation_date',
        'prefer_install_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    // Scopes...

    // Functions ...

    // Relations 
    // public function township()
    // {
    //     return $this->hasOne(Township::class);
    // }
    public function township()
    {
        return $this->belongsTo(Township::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function getTableColumns()
    {
        $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $column_array = array();
        foreach ($columns as $key => $value) {
            array_push($column_array, ['id' => $key, 'name' => $value]);
        }
        return $column_array;
    }
}
