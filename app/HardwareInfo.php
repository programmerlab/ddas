<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HardwareInfo extends Model
{
    protected $table = 'hardware_info';
    protected $primaryKey = 'id';
    protected $fillable = ['no_of_sim','device_id','os','user_id','brand','country','manufacturer','model','notification_id','device_name','os_version','timezone','latitude','longitude'];
    public $data;
    
    
    public function Mysave(){
        if($this->data && count($this->data) > 0){
            
            $hardwareModel = HardwareInfo::where(array('device_id' => $this->data['device_id']))->first();
            if($hardwareModel){
                
                $hardwareModel->fill($this->data);
                $hardwareModel->save();
                
            }else{
                
                HardwareInfo::create($this->data);
            }
        }
    }
}
