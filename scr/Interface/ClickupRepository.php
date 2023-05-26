<?php

namespace Vikasrinvi\LaravelBugWatcher\Interface;


use Illuminate\Support\Facades\Auth;
use Vikasrinvi\LaravelBugWatcher\Interface\BugWatcherInterface;



class ClickupRepository implements BugWatcherInterface
{

    public function createTask($exception)   
    {
        $token =  config('laravel-bug-watcher.ClickUp.token');
        $teamName =  config('laravel-bug-watcher.ClickUp.team_name');
        $folderName =  config('laravel-bug-watcher.ClickUp.folder_name');
        $listName =  config('laravel-bug-watcher.ClickUp.list_name');

        $curl = curl_init();

        curl_setopt_array($curl, [
          CURLOPT_HTTPHEADER => [
            "Authorization: $token"
          ],
          CURLOPT_URL => "https://api.clickup.com/api/v2/team",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        if(array_key_exists('teams', $response)){
            $team = collect($response['teams'])->where('name', $teamName)->first();
            if($team){
                $teamId = $team['id'];
            }
        }


        $teamId = $teamId;
        $query = array(
          "archived" => "false"
        );

        $curl = curl_init();

        curl_setopt_array($curl, [
          CURLOPT_HTTPHEADER => [
            "Authorization: $token"
          ],
          CURLOPT_URL => "https://api.clickup.com/api/v2/team/$teamId/folder",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        if(array_key_exists('folders', $response)){
            $folder = collect($response['folders'])->where('name', $folderName)->first();
            if($folder && array_key_exists('lists', $folder)){

                $list = collect($folder['lists'])->where('name', $listName)->first();

                if(!$list){
                    $list = collect($folder['lists'])->where('name', 'General')->first();
                }

                if($list){
                    $listId = $list['id'];
                }


            }
        }
        if($listId){
          $start_date =strtotime(now()) * 1000 ;
          $due_date =strtotime(now(+1)) * 1000 ;

          $listId = $listId;
          
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
              CURLOPT_URL => "https://api.clickup.com/api/v2/list/$listId/task?" . http_build_query($query),
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_CUSTOMREQUEST => "POST",
            ]);
            $response = curl_exec($curl);

        $error = curl_error($curl);

        curl_close($curl);
        }

    }

}
