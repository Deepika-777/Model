<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detail extends Model
{
    use HasFactory;
    public function transpose($vdata){
        $arr =[];
        foreach($vdata as $key=>$value){
            $arr[$value->project_id][] = $value;
        }
        return $arr;

    }
}
