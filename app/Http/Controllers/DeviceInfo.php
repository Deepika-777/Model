<?php

namespace App\Http\Controllers;

use App\Models\detail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DeviceInfo extends Controller
{
    public function getData($id)
    {
        $device_data = [];
        $data = detail::find($id);
        //return $data;
        //return $this->get_device_data( $data);
       
       
         $data['detail'] = $this->get_device_data( $data);
        
        return view('Admin.deviceinfo',compact('data'));
    }


    public function getDataVFM($id, $subid)
    {
        $device_data = [];
        $data = detail::find($id);
        //return $data;
        //return $this->get_device_data( $data);
       
       
         $data['detail'] = $this->get_device_data( $data, $subid );
        
        return view('Admin.deviceinfoVFM',compact('data'));
    }

    public function get_device_data($db_info, $sub_id='sa1'){
       
        $date = Carbon::now();
        //Get date and time
        $date_time = $date->toDateTimeString();

        $ddtn = $db_info['device_details_table'];
        $ds = $db_info['data_summary'];
        //print_r($db_info);

        Config::set("database.connections.".$db_info['db_connection_name'].".database", $db_info['database_name'] );
        DB::purge($db_info['db_connection_name']);

        $query1 = "
            SELECT COUNT(device_id) AS device_count,
            COUNT(distinct client_id ) AS client_count
            FROM $ddtn 
            where visibility = 1
            AND device_id <= 500 
            AND ((existance = '' )OR (existance IS NULL))
        ";
        $query2 = "
            SELECT COUNT(device_id) AS device_count,
            COUNT(distinct client_id ) AS client_count
            FROM $ddtn 
            where visibility = 1
            AND ((existance = '' )OR (existance IS NULL))
        ";

        $query3 = "
        SELECT COUNT(device_id) AS device_count,
        COUNT(distinct meter_no ) AS client_count
        FROM $ddtn 
        where visibility = 1
        ";

        $query4 = "
        SELECT project_name, project_id, COUNT(device_id) 
        FROM $ddtn T1
        JOIN projects T2 ON 
        T2.product_key = T1.project_id 
        WHERE T1.project_id = '$sub_id'
        group BY project_id 
        ";

        if ($db_info['type'] == 1)
        {
            $data1 =  DB::connection($db_info['db_connection_name'])->select($query1);
            $data2 = DB::connection($db_info['db_connection_name'])->select("
            
                    SELECT T1.device_id, T1.client_id, T1.device_name,
                    T2.dt_time,
                    ifNull(color,'table-primary') as color FROM $ddtn T1
                    left JOIN(      
                     SELECT dt_time, device_id , 'table-success' AS color
                     FROM $ds
                     WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
                     AND dt_time <= DATE_SUB('$date_time', INTERVAL 0 MINUTE)
                     AND device_id in ( 
                             SELECT device_id
                             FROM $ddtn
                             where visibility = 1
                             AND device_id <= 500 
                             AND ((existance = '' )OR (existance IS NULL)) )
                               
                 union
                     
                     SELECT dt_time, device_id , 'table-warning' AS color
                     FROM $ds
                     WHERE dt_time < DATE_SUB('$date_time', INTERVAL 30 MINUTE)
                     AND  dt_time >= DATE_SUB('$date_time', INTERVAL 60 MINUTE)
                     AND device_id NOT IN(
                         SELECT device_id  FROM $ds
                         WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
                         AND  dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE)  
                     )
                     AND device_id in ( 
                             SELECT device_id
                             FROM $ddtn
                             where visibility = 1
                             AND device_id <= 500 
                             AND ((existance = '' )OR (existance IS NULL)) ) 
                     
                    union
                     
                     SELECT  MAX(dt_time), MAX(device_id), 'table-danger' AS color 
                     FROM $ds
                     WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 4380 HOUR) 
                     AND  dt_time < DATE_SUB('$date_time', INTERVAL 1 HOUR)
                     AND device_id NOT IN (SELECT device_id FROM $ds
                             WHERE dt_time >=  DATE_SUB('$date_time', INTERVAL 59 MINUTE)
                             AND dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE ))
                     AND device_id in ( 
                             SELECT device_id
                             FROM $ddtn
                             where visibility = 1
                             AND device_id <= 500 
                             AND ((existance = '' )OR (existance IS NULL)) )
                             GROUP BY device_id
                     ) T2
                     ON T1.device_id = T2.device_id
                  where visibility = 1
                  AND T1.device_id <= 500 
                  AND ((existance = '' )OR (existance IS NULL))
                ");
        }    
     
        else if($db_info['type'] == 2)
        {
            $data1 =  DB::connection($db_info['db_connection_name'])->select($query2); 
            $data2 = DB::connection($db_info['db_connection_name'])->select("
            
            SELECT T1.device_id, T1.client_id, T1.device_name,
            T2.dt_time,
            ifNull(color,'table-primary') as color FROM $ddtn T1
            left JOIN(      
             SELECT  dt_time,device_id , 'table-success' AS color
             FROM $ds
             WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
             AND dt_time <= DATE_SUB('$date_time', INTERVAL 0 MINUTE)
             AND device_id in ( 
                     SELECT device_id
                     FROM $ddtn
                     where visibility = 1)
        union
             
             SELECT dt_time, device_id, 'table-warning' AS color
             FROM 
             $ds   WHERE dt_time < DATE_SUB('$date_time', INTERVAL 30 MINUTE)
             AND  dt_time >= DATE_SUB('$date_time', INTERVAL 60 MINUTE)
             AND device_id NOT IN(
                 SELECT device_id  FROM $ds
                 WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
                 AND  dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE)  
             )
             AND device_id in ( 
                     SELECT device_id
                     FROM $ddtn
                     where visibility = 1)
                    
        union
             
             SELECT MAX(dt_time), MAX(device_id), 'table-danger' AS color 
              FROM $ds
             WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 4380 HOUR) 
             AND  dt_time < DATE_SUB('$date_time', INTERVAL 1 HOUR)
             AND device_id NOT IN (SELECT device_id FROM $ds
                     WHERE dt_time >=  DATE_SUB('$date_time', INTERVAL 59 MINUTE)
                     AND dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE ))
             AND device_id in ( 
                     SELECT device_id
                     FROM $ddtn
                     where visibility = 1)
                     GROUP BY device_id
             ) T2
             ON T1.device_id = T2.device_id
          where visibility = 1

        ");
    }
    else if( $db_info['type'] == 3) {
        $data1 =  DB::connection($db_info['db_connection_name'])->select($query3);
        $data2 = DB::connection($db_info['db_connection_name'])->select("
        SELECT  T1.device_id , 
        T2.client_id, 
        T1.room_no,
        T1.floor_id,T1.hostel_id,
        T2.dt_time ,T1.meter_no,
        concat('Room :',room_no,'-',T3.floor_no,'-', T4.hname) as device_name,
        ifNull(color,'table-primary') as color
    
    FROM rooms T1 
               left JOIN(      
                SELECT dt_time, client_id, device_id , 'table-success' AS color
                FROM data_rs485_summary
                WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
                AND dt_time <= DATE_SUB('$date_time', INTERVAL 0 MINUTE)
                AND device_id in ( 
                        SELECT device_id
                        FROM rooms
                        where visibility = 1 )
                          
            union
                
                SELECT dt_time,client_id, device_id , 'table-warning' AS color
                FROM 
                data_rs485_summary
                WHERE dt_time < DATE_SUB('$date_time', INTERVAL 30 MINUTE)
                AND  dt_time >= DATE_SUB('$date_time', INTERVAL 60 MINUTE)
                AND device_id NOT IN(
                    SELECT device_id  FROM data_rs485_summary
                    WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
                    AND  dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE)  
                )
                AND device_id in ( 
                        SELECT device_id
                        FROM rooms
                        where visibility = 1 ) 
                
            union
                
                SELECT dt_time, client_id, device_id, 'table-danger' AS color 
                 FROM data_rs485_summary
                WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 4380 HOUR) 
                AND  dt_time < DATE_SUB('$date_time', INTERVAL 1 HOUR)
                AND device_id NOT IN (SELECT device_id FROM data_rs485_summary
                        WHERE dt_time >=  DATE_SUB('$date_time', INTERVAL 59 MINUTE)
                        AND dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE ))
                AND device_id in ( 
                        SELECT device_id
                        FROM rooms
                        where visibility = 1 )
                ) T2
                ON T1.device_id = T2.device_id
                LEFT JOIN floors T3
                ON T1.floor_id = T3.id
                LEFT JOIN hostels T4
                ON T1.hostel_id = T4.id
               
             where visibility = 1 ");
           
        }

        else if( $db_info['type'] == 4) {
            $data1 =  DB::connection($db_info['db_connection_name'])->select($query4);
            $data2 = DB::connection($db_info['db_connection_name'])->select("
            SELECT dt_time, T1.device_id, T1.project_id, T3.project_name, 
            ifnull(color,'table-primary') AS color ,
            T1.display_device_name as device_name FROM energy_device_details T1
            left JOIN(      
                 SELECT MAX(dt_time) AS dt_time, MAX(device_id) AS device_id, MAX(project_id) AS project_id,
                  'table-success' AS color
             FROM energy_daily_summary T1
             WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
             AND  dt_time <= DATE_SUB('$date_time' , INTERVAL 0 MINUTE)
             AND T1.device_id in ( 
                     SELECT distinct device_id
                     FROM energy_device_details
                     where visibility = 1) 
    
             AND T1.project_id = '$sub_id'
                     GROUP BY T1.device_id, T1.project_id         
                        
         union
             
             SELECT MAX(dt_time) AS dt_time, MAX(device_id) AS device_id, MAX(project_id) AS project_id, 'table-warning' AS color
             FROM 
             energy_daily_summary T1
             WHERE dt_time < DATE_SUB('$date_time', INTERVAL 30 MINUTE)
             AND  dt_time >= DATE_SUB('$date_time', INTERVAL 60 MINUTE)
             AND device_id NOT IN(
                 SELECT device_id  FROM energy_daily_summary
                 WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
                 AND  dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE)  
             ) 
            
             AND T1.device_id in ( 
                     SELECT device_id
                     FROM energy_device_details
                          WHERE visibility = 1 )
                       
            
              AND T1.project_id = '$sub_id'
                     GROUP BY T1.device_id, T1.project_id  
            union
             
             SELECT MAX(dt_time) AS dt_time, MAX(device_id) AS device_id, MAX(project_id) AS project_id, 
             'table-danger' AS color 
             FROM energy_daily_summary T1
             WHERE dt_time < DATE_SUB('$date_time', INTERVAL 1 HOUR)
            AND device_id NOT IN (
                     SELECT device_id FROM energy_daily_summary
                     WHERE dt_time >=  DATE_SUB('$date_time', INTERVAL 59 MINUTE)
                     AND dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE )
                     AND project_id = '$sub_id'
                     ) 
             AND T1.device_id in ( 
                     SELECT device_id
                     FROM energy_device_details
                     where visibility = 1 )
                     
             AND T1.project_id = '$sub_id'
                     GROUP BY T1.device_id, T1.project_id
             ) T2
             ON T1.device_id = T2.device_id AND T1.project_id = T2.project_id
             JOIN projects  T3
                 ON T3.product_key = T1.project_id 
                 
          where visibility = 1
          AND project_status = 1
          AND T1.project_id = '$sub_id'
          ORDER BY T1.device_id
           
        ");
               
        }

        else 
            $data1 =  DB::connection($db_info['db_connection_name'])->select($query3);
            return $data2;
    }  
}
