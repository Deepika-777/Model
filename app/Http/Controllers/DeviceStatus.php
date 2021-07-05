<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\detail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeviceStatus extends Controller
{
    public function getData()
    {
        $device_data = [];
        $data = detail::where('type','!=',4)->orderBy('sequence')->get();
        //return $data;
        foreach($data as $key=>$value){
            //$device_data[$value['database_name']]= $this->get_device_data($value);
            $data[$key]['detail'] = $this->get_device_data($value);
        }
       // return $data;
        return view('Admin.status',compact('data'));
    }
    
    public function getDataVFM()
    {
        $device_data = [];
        $data = detail::where('type',4)->get();
        foreach($data as $key=>$value){
            //$device_data[$value['database_name']]= $this->get_device_data($value);
            $data[$key]['detail'] = $this->get_device_data($value);
        }
        //return $data;
        return view('Admin.tables',compact('data'));
    }
    
    public function get_device_data($db_info){
       
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
            group BY project_id 
    
        ";

        if ($db_info['type'] == 1)
        {
            $data1 =  DB::connection($db_info['db_connection_name'])->select($query1);
            $data2 = DB::connection($db_info['db_connection_name'])->select("
            
                    SELECT T1.device_id, T2.client_id, T1.device_name,
                    T2.dt_time,
                    ifNull(color,'badge-primary') as color, ifNull(text,'Data is not present') as text,
                     ifNull(status, 'primary-tooltip') AS status FROM $ddtn T1
                    left JOIN(      
                     SELECT MAX(dt_time) AS dt_time, MAX(device_id) AS device_id, 'badge-success' AS color, 'Device is live at' AS text, 
                     'success-tooltip' AS status, client_id
                     FROM $ds
                     WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
                     AND dt_time <= DATE_SUB('$date_time', INTERVAL 0 MINUTE)
                     AND device_id in ( 
                             SELECT device_id
                             FROM $ddtn
                             where visibility = 1
                             AND device_id <= 500 
                             AND ((existance = '' )OR (existance IS NULL)) )
                             GROUP BY device_id
                               
                 union
                     
                     SELECT dt_time, device_id , 'badge-warning' AS color, 'Data not received from' AS text,
                    'warning-tooltip' AS status, client_id
                     FROM 
                     $ds
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
                     
                     SELECT  MAX(dt_time) AS dt_time, MAX(device_id) AS device_id, 'badge-danger' AS color,
                     'Data not received from' AS text, 'danger-tooltip' AS status, client_id 
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
            
            SELECT T1.device_id, T2.client_id, T1.device_name,
            T2.dt_time,
            ifNull(color,'badge-primary') as color, ifNull(text,'Data is not present') as text, 
            ifNull(status, 'primary-tooltip') AS status FROM $ddtn T1
            left JOIN(      
             SELECT  MAX(dt_time) AS dt_time, MAX(device_id) AS device_id, 'badge-success' AS color, 
             'Device is live at' AS text, 'success-tooltip' AS status, client_id
             FROM $ds
             WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
             AND dt_time <= DATE_SUB('$date_time', INTERVAL 0 MINUTE)
             AND device_id in ( 
                     SELECT device_id
                     FROM $ddtn
                     where visibility = 1)
                     GROUP BY device_id
         union
             
             SELECT dt_time, device_id, 'badge-warning' AS color, 'Data not received from' AS text, 
             'warning-tooltip' AS status, client_id
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
             
             SELECT MAX(dt_time), MAX(device_id), 'badge-danger' AS color, 'Data not received from' AS text,
            'danger-tooltip' AS status, client_id 
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
        concat('Room :',room_no,'-',T3.floor_no,'-', T4.hname,'-', T1.meter_no ) as device_name,
        ifNull(color,'badge-primary') as color, ifNull(text,'Data is not present') as text, 
        ifNull(status, 'primary-tooltip') AS status
    
    FROM rooms T1 
               left JOIN(     
                SELECT MAx(dt_time) AS dt_time, MAX(client_id) AS client_id, MAX(device_id) AS device_id, 'badge-success' AS color,
                'Device is live at' AS text, 'success-tooltip' AS status
                FROM data_rs485_summary
                WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
                AND dt_time <= DATE_SUB('$date_time', INTERVAL 0 MINUTE)
                AND device_id in ( 
                        SELECT device_id
                        FROM rooms
                        where visibility = 1 )
                        GROUP BY device_id
                          
            union
                
                SELECT dt_time,client_id, device_id , 'badge-warning' AS color, 'Data not received from' AS text,
                'warning-tooltip' AS status
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
                
                SELECT MAX(dt_time) AS dt_time, MAX(client_id) AS client_id, MAX(device_id) AS device_id, 'badge-danger' AS color,
                'Data not received from' AS text, 'danger-tooltip' AS status 
                 FROM data_rs485_summary
                WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 4380 HOUR) 
                AND  dt_time < DATE_SUB('$date_time', INTERVAL 1 HOUR)
                AND device_id NOT IN (SELECT device_id FROM data_rs485_summary
                        WHERE dt_time >=  DATE_SUB('$date_time', INTERVAL 59 MINUTE)
                        AND dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE ))
                AND device_id in ( 
                        SELECT device_id
                        FROM rooms
                        where visibility = 1)
                        GROUP BY device_id
                ) T2
                ON T1.device_id = T2.device_id
                LEFT JOIN floors T3
                ON T1.floor_id = T3.id
                LEFT JOIN hostels T4
                ON T1.hostel_id = T4.id
               
             where visibility = 1 
             ORDER BY T1.hostel_id ");
            
           
        }

        else if( $db_info['type'] == 4) {
            $data1 =  DB::connection($db_info['db_connection_name'])->select($query4);
            $data2 = DB::connection($db_info['db_connection_name'])->select(" SELECT dt_time, 
            T1.device_id, T1.project_id, T3.project_name, 
            ifnull(color,'badge-primary') AS color, ifNull(text,'Data is not present') as text, 
            ifNull(status, 'primary-tooltip') AS status,
            T1.display_device_name as device_name FROM energy_device_details T1
            left JOIN(      
                 SELECT MAX(dt_time) AS dt_time, MAX(device_id) AS device_id, MAX(project_id) AS project_id, 
                 'badge-success' AS color, 
                 'Device is live at' AS text, 'success-tooltip' AS status
             FROM energy_daily_summary T1
             WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
             AND  dt_time <= DATE_SUB('$date_time' , INTERVAL 0 MINUTE)
             AND T1.device_id in ( 
                     SELECT distinct device_id
                     FROM energy_device_details
                     where visibility = 1) 
    
             AND T1.project_id in ( 
                     SELECT distinct project_id
                     FROM energy_device_details
                     where visibility = 1)
                     GROUP BY T1.device_id, T1.project_id
                   
                   
                        
         union
             
             SELECT MAX(dt_time) AS dt_time, MAX(device_id) AS device_id, MAX(project_id) AS project_id, 
             'badge-warning' AS color,
             'Data not received from' AS text, 'warning-tooltip' AS status
             FROM 
             energy_daily_summary T1
             WHERE dt_time < DATE_SUB('$date_time', INTERVAL 30 MINUTE)
             AND  dt_time >= DATE_SUB('$date_time', INTERVAL 60 MINUTE)
            /* AND device_id NOT IN(
                 SELECT device_id  FROM energy_daily_summary
                 WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
                 AND  dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE)  
             ) */
             AND T1.project_id NOT IN(
                 SELECT project_id  FROM energy_daily_summary
                 WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
                 AND  dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE)  
             )
             AND T1.device_id in ( 
                     SELECT device_id
                     FROM energy_device_details
                          WHERE visibility = 1 )
                       
                     
              AND T1.project_id in ( 
                     SELECT project_id
                     FROM energy_device_details
                     where visibility = 1)
                     GROUP BY T1.device_id, T1.project_id
                          
                  
            union
             
             SELECT MAX(dt_time) AS dt_time, MAX(device_id) AS device_id, MAX(project_id) AS project_id,
              'badge-danger' AS color, 
             'Data not received from' AS text, 'danger-tooltip' AS status 
              FROM energy_daily_summary T1
             WHERE /*dt_time >= DATE_SUB('$date_time', INTERVAL 4380 HOUR) 
             AND */ dt_time < DATE_SUB('$date_time', INTERVAL 1 HOUR)
            AND concat(T1.device_id, '-', T1.project_id) NOT IN (
                 SELECT  concat(device_id, '-', project_id) as id FROM energy_daily_summary
                     WHERE dt_time >=  DATE_SUB('$date_time', INTERVAL 59 MINUTE)
                     AND dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE )
                     
                     )                      
            /*AND T1.project_id NOT IN (
                 SELECT project_id FROM energy_daily_summary
                     WHERE dt_time >=  DATE_SUB('$date_time', INTERVAL 59 MINUTE)
                     AND dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE ))   */          
                     
             AND T1.device_id in ( 
                     SELECT device_id
                     FROM energy_device_details
                     where visibility = 1 )
                     
             AND T1.project_id in ( 
                     SELECT project_id
                     FROM energy_device_details
                     where visibility = 1 )
            GROUP BY T1.device_id, T1.project_id
             ) T2
             ON T1.device_id = T2.device_id AND T1.project_id = T2.project_id
             JOIN projects  T3
                 ON T3.product_key = T1.project_id  
          where visibility = 1
          AND  project_status = 1
          ORDER BY T1.device_id
    
      ");
        $dt = new detail();
      return [
        'count'=>$data1,
        'data'=>$dt->transpose($data2),
    ];
}  
      
        else
            $data1 =  DB::connection($db_info['db_connection_name'])->select($query3);

            return [
                'count'=>$data1,
                'data'=>$data2,
                
                
            ];

           }    
           public function transpose($vdata){
            $arr =[];
            foreach($vdata as $key=>$value){
                $arr[$value->project_id][] = $value;
            }
            return $arr;

        }
}
