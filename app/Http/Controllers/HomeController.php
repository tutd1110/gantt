<?php

namespace App\Http\Controllers;

use App\Helper\Mycurl;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\GanttTaskModel;

class HomeController extends Controller
{
    public function index(Request $request){

        $requestData = $request->all();
        $project_id = 0;
        if($requestData){
            $project_id = $requestData['projectId'];
        }

        $getSelectBoxs = $this->getSelectBoxs();
        $listProject = [];
        if($getSelectBoxs){
            $listProject = $getSelectBoxs['projects'];
        }

        $dataGanttTasks = [];
        $getProject = [];
        if($project_id) {
            $getProject = $this->getProjectById($project_id);
            $getGanttTasks = GanttTaskModel::query()
                ->where('project_id', '=', $project_id)
                ->orderBy('id', 'asc')
                ->get();

            if ($getGanttTasks) {
                foreach ($getGanttTasks as $item) {
                    $temp = [];
                    $temp['id'] = $item['id'];
                    $temp['name'] = $item['name'];
                    $temp['progress'] = $item['progress'];
                    $temp['description'] = $item['description'];
                    $temp['level'] = $item['level'];
                    $temp['status'] = $item['status_alias'];
                    $temp['status_title'] = $item['status_title'];
                    $temp['depends'] = ($item['depends']) ? $item['depends'] : "";

                    $temp['canWrite'] = $item['canWrite'];
                    $temp['start'] = $item['start'];

                    $temp['end'] = $item['end'];
                    $temp['startIsMilestone'] = $item['startIsMilestone'];
                    $temp['endIsMilestone'] = $item['endIsMilestone'];
                    $temp['collapsed'] = $item['collapsed'];
                    $temp['user_id'] = $item['user_id'];
                    $temp['duration'] = $item['duration'];

                    $assigs = [];
                    if ($item['user_id']) {
                        $assigs = [["id" => "tmp_" . $item['id'], "resourceId" => $item['user_id']]];
                    }
                    $temp['assigs'] = $assigs;
                    $temp['hasChild'] = $item['hasChild'];

                    $dataGanttTasks[] = $temp;
                }
            }
        }

        $status = config('const.status');
        $getListUsers = $this->getListUsers();
        $dataUsers= [];
        if($getListUsers){
            foreach ($getListUsers as $user){
                $temp = [];
                $temp['id'] = $user['id'];
                $temp['name'] = $user['fullname'];
                $dataUsers[] = $temp;
            }
        }

        $dataResources =  json_encode($dataUsers, true);
        $taskGanttJson = json_encode($dataGanttTasks, true);

        return view('home.index')
            ->with('taskGanttJson', $taskGanttJson)
            ->with('dataResources', $dataResources)
            ->with('status', $status)
            ->with('getProject', $getProject)
            ->with('listProject', $listProject)
            ;
    }

    public function getListUsers(){
        $url_api = env('WORK_HORUS').'/api/task/get-list-user';
        $access_token = session('access_token');

        $result = Mycurl::getCurl($url_api, $access_token);

        return $result;
    }

    public function getProjectById($id){
        $url_api = env('WORK_HORUS').'/api/project/get_project_by_id';
        $params = [
            'id' => $id
        ];
        $access_token = session('access_token');
        $result = Mycurl::getCurl($url_api, $access_token, $params);

        return $result;
    }

    public function getSelectBoxs(){
        $url_api = env('WORK_HORUS').'/api/project/get_select_boxes';

        $access_token = session('access_token');
        $result = Mycurl::getCurl($url_api, $access_token);

        return $result;
    }

}
