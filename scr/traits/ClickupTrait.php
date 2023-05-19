<?php

namespace Vikasrinvi\LaravelBugWatcher;

use Illuminate\Support\Facades\Auth;

trait ClickupTrait
{
    public function create($exception)
    {


           $start_date =strtotime(now()) * 1000 ;
            // dd(now(), $start_date);
            $due_date =strtotime(now(+1)) * 1000 ;
          $listId = 900200829019;
          
            $query = array(
              
            );
            $description = "Error Occured on File". $exception->getFile()  ." on line " . $exception->getLine() ;
            $authUserId = Auth::user() ? Auth::user()->id : null;
            if($authUserId){
                $description .= " Auth user Is was $authUserId /n";
            }
            $stackTracked = nl2br($exception->getTraceAsString());
            $description .= $stackTracked;

            $curl = curl_init();

            $payload = array(
              "name" => $exception->getMessage(),
              "description" => $description,
              "assignees" => array(
                
              ),
              "tags" => array(
                "bug"
              ),
              "status" => "BACKLOG",
              "priority" => 3,
              "due_date" => $due_date,
              "due_date_time" => false,
              "time_estimate" => 8640000,
              "start_date" => $start_date,
              "start_date_time" => false,
              "notify_all" => true,
              "parent" => NULL,
              "links_to" => NULL,
              
            );

            curl_setopt_array($curl, [
              CURLOPT_HTTPHEADER => [
                "Authorization: $token",
                "Content-Type: application/json"
              ],
              CURLOPT_POSTFIELDS => json_encode($payload),
              CURLOPT_URL => "https://api.clickup.com/api/v2/list/" . $listId . "/task?" . http_build_query($query),
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_CUSTOMREQUEST => "POST",
            ]);

            $response = curl_exec($curl);
            $error = curl_error($curl);

            curl_close($curl);

}
