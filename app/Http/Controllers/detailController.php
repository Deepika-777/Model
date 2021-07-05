<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\detail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class detailController extends Controller
{
    
    public function getData()
    {
        $device_data = [];
        $data = detail::where('type','!=',4)->orderBy('sequence')->get();
        foreach($data as $key=>$value){
            //$device_data[$value['database_name']]= $this->get_device_data($value);
            $data[$key]['detail'] = $this->get_device_data($value);
        }
        return view('Admin.dashboard',compact('data'));
    }

    public function getDataVFM()
    {
        $device_data = [];
        $data = detail::where('type','=',4)->get();
        foreach($data as $key=>$value){
            //$device_data[$value['database_name']]= $this->get_device_data($value);
            $data[$key]['detail'] = $this->get_device_data($value);
        }
        //return $data;
        return view('Admin.icons',compact('data'));
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
                SELECT  count( DISTINCT T1.device_id) AS device_count,
                    COUNT( distinct client_id)  AS client_count
                    FROM(
                        SELECT distinct  device_id AS device_id,
                             client_id  AS client_id
                    FROM $ds T1
                    where device_id IN( 
                                SELECT device_id FROM $ddtn
                                where visibility = 1
                                AND device_id <= 500 
                            AND ((existance = '' )OR (existance IS NULL))
                        )
                       and DATE >= DATE_SUB('$date_time' ,interval 180 day )
                        GROUP BY T1.device_id
                        ) T1
    ";
    $query2 = "
        SELECT COUNT(distinct T1.device_id) AS device_count,
        COUNT(distinct client_id ) AS client_count
        FROM (
              select distinct device_id as device_id,
                 client_id as client_id
        from $ds T1        
        where device_id in(
                    select device_id from $ddtn
                    where visibility = 1
            )
            and DATE >= DATE_SUB('$date_time' ,interval 30 day )
            group by T1.device_id
            ) T1
    ";

    $query3 = "
        SELECT COUNT( T1.device_id) AS device_count,
        COUNT( DISTINCT  client_id  ) AS client_count
        FROM (
            SELECT  device_id as device_id,
                client_id as client_id,
                    ROW_NUMBER()  OVER (PARTITION BY device_id ORDER BY dt_time DESC) AS row_number
            from $ds T1        
            where device_id in(
                    select device_id from $ddtn
                    where visibility = 1
            )
            
        ) T1 
        WHERE ROW_NUMBER = 1
        ";

    $query4 = "
     
    SELECT COUNT(distinct T1.device_id) AS device_count,
    COUNT(distinct project_id ) AS client_count,
    T1.project_id
   
    from $ddtn  T1  
     where visibility = 1      
     group by T1.project_id
     order by T1.project_id

    ";


    if($db_info['type'] == 1){
        $data1 =  DB::connection($db_info['db_connection_name'])->select($query1); 
        $query_status1 = DB::connection($db_info['db_connection_name'])->select("SELECT T1.date,
            on_device_count, yellow_device_count, 
            on_client_count, yellow_client_count,
            (".$data1[0]->device_count." - (on_device_count + yellow_device_count)) as  off_device_count,
            (".$data1[0]->client_count." - (on_client_count + yellow_client_count)) as  off_client_count
            FROM(
            SELECT '$date_time' AS date , count(distinct device_id) AS on_device_count,
            count(distinct client_id) AS on_client_count FROM $ds
            WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
            AND dt_time <= DATE_SUB('$date_time', INTERVAL 0 MINUTE)
            AND device_id in ( 
                    SELECT device_id
                    FROM $ddtn 
                    where visibility = 1
                    AND device_id <= 500 
                    AND ((existance = '' )OR (existance IS NULL)) )
            ) T1
            JOIN 
            (
            SELECT '$date_time' AS date,  count(distinct device_id) AS yellow_device_count,
            count(distinct client_id) AS yellow_client_count FROM 
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
            )T2
            on T1.date = T2.date
            JOIN
            (
            SELECT '$date_time' AS date,   count(distinct device_id) AS off_device_count, 
            count(distinct client_id) AS off_client_count FROM $ds
            WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 24 HOUR) 
       
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
            ) T3
            on T2.date = T3.date
            ");
    }
        
    else if($db_info['type'] == 2){
        $data1 =  DB::connection($db_info['db_connection_name'])->select($query2);  
        $query_status1 = DB::connection($db_info['db_connection_name'])->select("SELECT T1.date,
        on_device_count, yellow_device_count, 
        on_client_count, yellow_client_count,
            (".$data1[0]->device_count." - (on_device_count + yellow_device_count)) as  off_device_count,
            (".$data1[0]->client_count." - (on_client_count + yellow_client_count)) as  off_client_count
        FROM(
        SELECT '$date_time' AS date , count(distinct device_id) AS on_device_count,
        count(distinct client_id) AS on_client_count  FROM $ds
        WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
        AND dt_time <= DATE_SUB('$date_time', INTERVAL 0 MINUTE)
        AND device_id in ( 
                SELECT device_id
                FROM $ddtn 
                where visibility = 1)
        ) T1
        JOIN 
        (
        SELECT '$date_time' AS date,  count(distinct device_id) AS yellow_device_count ,
        count(distinct client_id) AS yellow_client_count FROM 
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
                where visibility = 1)
        )T2
        on T1.date = T2.date
        JOIN
        (
        SELECT '$date_time' AS date,   count(distinct device_id) AS off_device_count ,
        count(distinct client_id) AS off_client_count FROM $ds
        WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 24 HOUR) 
        AND  dt_time < DATE_SUB('$date_time', INTERVAL 1 HOUR)
        AND device_id NOT IN (SELECT device_id FROM $ds
                WHERE dt_time >=  DATE_SUB('$date_time', INTERVAL 59 MINUTE)
                AND dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE ))
        AND device_id in ( 
                SELECT device_id
                FROM $ddtn 
                where visibility = 1)
        ) T3
        on T2.date = T3.date
        ");
    }
    else if( $db_info['type'] == 3) {

        $data1 =  DB::connection($db_info['db_connection_name'])->select($query3);  
        $query_status1 = DB::connection($db_info['db_connection_name'])->select("SELECT T1.date,
        on_device_count, yellow_device_count, 
        on_client_count, yellow_client_count,
            (".$data1[0]->device_count." - (on_device_count + yellow_device_count)) as  off_device_count,
            (".$data1[0]->client_count." - (on_client_count + yellow_client_count)) as  off_client_count
        FROM(
        SELECT '$date_time' AS date , count(distinct device_id) AS on_device_count,
        count(distinct client_id) AS on_client_count  FROM $ds
        WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
        AND dt_time <= DATE_SUB('$date_time', INTERVAL 0 MINUTE)
        AND device_id in ( 
                SELECT device_id
                FROM $ddtn 
                where visibility = 1)
        ) T1
        JOIN 
        (
        SELECT '$date_time' AS date,  count(distinct device_id) AS yellow_device_count ,
        count(distinct client_id) AS yellow_client_count FROM 
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
                where visibility = 1)
        )T2
        on T1.date = T2.date
        JOIN
        (
        SELECT '$date_time' AS date,   count(distinct device_id) AS off_device_count ,
        count(distinct client_id) AS off_client_count FROM $ds
        WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 24 HOUR) 
        AND  dt_time < DATE_SUB('$date_time', INTERVAL 1 HOUR)
        AND device_id NOT IN (SELECT device_id FROM $ds
                WHERE dt_time >=  DATE_SUB('$date_time', INTERVAL 59 MINUTE)
                AND dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE )) 
        AND device_id in ( 
                SELECT device_id
                FROM $ddtn 
                where visibility = 1)
        ) T3
        on T2.date = T3.date
        ");

    }

    else if( $db_info['type'] == 4) {
        $data1 =  DB::connection($db_info['db_connection_name'])->select($query4);  
        $query_status1 = DB::connection($db_info['db_connection_name'])->select("SELECT T1.date,
       ifnull(on_device_count, 0) as on_device_count, 
       ifnull(yellow_device_count, 0) as yellow_device_count, 
       ifnull(on_client_count, 0) as on_client_count, 
       ifnull( yellow_client_count, 0) as yellow_client_count,
        0 as off_device_count,
        0 as off_client_count,
        T3.product_key as project_id,
        T3.project_name
        FROM(
        SELECT '$date_time' AS date , count(distinct device_id) AS on_device_count,
        count(distinct project_id) AS on_client_count, project_id  FROM $ds
        WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
        AND dt_time <= DATE_SUB('$date_time', INTERVAL 0 MINUTE)
        AND device_id in ( 
                SELECT device_id
                FROM $ddtn 
                where visibility = 1)
                
      	AND project_id in ( 
                SELECT project_id
                FROM $ddtn 
                where visibility = 1)
                group BY project_id
        ) T1
        
       LEFT JOIN 
        (
        SELECT '$date_time' AS date,  count(distinct device_id) AS yellow_device_count ,
        count(distinct project_id) AS yellow_client_count, project_id FROM $ds
        WHERE dt_time < DATE_SUB('$date_time', INTERVAL 30 MINUTE)
        AND  dt_time >= DATE_SUB('$date_time', INTERVAL 60 MINUTE)
        AND device_id NOT IN(
            SELECT device_id  FROM $ddtn 
            WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
            AND  dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE)  
        )
        AND project_id NOT IN(
            SELECT project_id  FROM $ddtn 
            WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 30 MINUTE)
            AND  dt_time <  DATE_SUB('$date_time', INTERVAL 0 MINUTE)  
        )
        AND device_id in ( 
                SELECT device_id
                FROM $ddtn  
                where visibility = 1)
                
      	AND project_id in ( 
                SELECT project_id
                FROM $ddtn 
                where visibility = 1) 
					 group BY project_id      
        )T2
        ON 	T1.date = T2.date
		  and T1.project_id = T2.project_id
        LEFT JOIN
        ( 
        SELECT '$date_time' AS date, count(distinct device_id) AS off_device_count ,
        count(distinct project_id) AS off_client_count, project_id FROM $ds
        WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 24 HOUR) 
        AND  dt_time < DATE_SUB('$date_time', INTERVAL 1 HOUR)
        AND device_id NOT IN (SELECT device_id FROM $ddtn 
                WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 59 MINUTE)
                AND dt_time < DATE_SUB('$date_time', INTERVAL 0 MINUTE ))

        AND project_id NOT IN (
            SELECT project_id FROM $ddtn 
            WHERE dt_time >= DATE_SUB('$date_time', INTERVAL 59 MINUTE)
            AND dt_time < DATE_SUB('$date_time', INTERVAL 0 MINUTE ))

        AND device_id in ( 
                SELECT device_id
                FROM $ddtn  
                where visibility = 1)
                
        AND device_id in ( 
                SELECT project_id
                FROM $ddtn  
                where visibility = 1) 
                group BY project_id
					        
        ) T3
         on T2.date = T3.date
         AND T2.project_id = T3.project_id
         right join (
             select distinct project_id FROM $ddtn  
                where visibility = 1
         ) T4
         ON T4.project_id = T1.project_id
         JOIN projects T3
             ON T3.product_key = T4.project_id 
         order by T3.product_key
        ");

        $dt = new detail();
        return [
        'count'=>$dt->transpose($data1),
        'data'=>$dt->transpose($query_status1),
        ];

    }

    else 
        $data1 =  DB::connection($db_info['db_connection_name'])->select($query3);

    $data1[0]->off_device_count = $query_status1[0]->off_device_count;
    $data1[0]->on_device_count = $query_status1[0]->on_device_count;
    $data1[0]->yellow_device_count = $query_status1[0]->yellow_device_count;

    $data1[0]->off_client_count = $query_status1[0]->off_client_count;
    $data1[0]->on_client_count = $query_status1[0]->on_client_count;
    $data1[0]->yellow_client_count = $query_status1[0]->yellow_client_count;

    $data1[0]->date = $query_status1[0]->date;
    return $data1;
                                    
    }
}
