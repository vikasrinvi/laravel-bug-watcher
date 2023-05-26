<?php

namespace Vikasrinvi\LaravelBugWatcher\Interface;


use Illuminate\Support\Facades\Auth;
use Vikasrinvi\LaravelBugWatcher\Interface\BugWatcherInterface;



class TeamworkRepository implements BugWatcherInterface
{

    public function createTask($exception)   
    {
        $token =  config('laravel-bug-watcher.TeamWork.token');

        $projectId =  config('laravel-bug-watcher.TeamWork.project_id');
        $taskListId =  config('laravel-bug-watcher.TeamWork.list_id');

        $description = "Error Occured on File". $exception->getFile()  ." on line " . $exception->getLine() ;
            $authUserId = Auth::user() ? Auth::user()->id : null;
            if($authUserId){
                $description .= " Auth user Is was $authUserId /n";
            }
            $stackTracked = nl2br($exception->getTraceAsString());
            $description .= $stackTracked;
            $start_date =date('Ymd', time()) ;
          $due_date =date('Ymd', strtotime('+1 day')) ;
            $curl = curl_init();

            curl_setopt_array($curl, [
              CURLOPT_URL => "https://vikasscompany6.teamwork.com/tasklists/$taskListId/tasks.json",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode([
                'todo-item' => [
                    'use-defaults' => null,
                    'completed' => null,
                    'content' =>  $exception->getMessage(),
                    'tasklistId' => $taskListId,
                    'notify' => null,
                    'start-date' => $start_date,
                    'due-date' => $due_date,
                    
                    
                    'description' => $description,
                    
                    'progress' => 0,
                    'parentTaskId' => 0,
                    'tagIds' => '1',
                    'everyone-must-do' => null,
                    'predecessors' => [
                            [
                                            
                            ]
                    ],
                    'reminders' => null,
                    'columnId' => 0,
                    
                    
                ]
              ]),
              CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Content-Type: application/json",
                'Authorization: Bearer ' . $token,
              ],
            ]);

            $response = curl_exec($curl); 

            $err = curl_error($curl);

            curl_close($curl);

    }

}
