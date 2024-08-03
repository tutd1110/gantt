<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helper\Mycurl;
use Carbon\Carbon;
use App\Models\GanttTaskModel;
use Illuminate\Support\Facades\Session;

class SyncDataTask extends Command
{

    protected $signature = 'SyncData:task';

    protected $description = 'Sysn data task from work.horusvn.com';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $project_id = 23;
        $dataTasks = $this->getDataTasks($project_id);

        if(!empty($dataTasks)){
            $count = 1;
            foreach ($dataTasks as $key => $task){
                $ganttTaskModel = new GanttTaskModel();

                $ganttTaskModel->project_id = $project_id;
                $ganttTaskModel->task_id = $task['task_id'];
                $ganttTaskModel->name = $task['name'];
                $ganttTaskModel->progress = $task['progress'];
                $ganttTaskModel->description = $task['description'];
                $ganttTaskModel->level = $task['level'];
                $ganttTaskModel->status_alias = $task['status'];
                $ganttTaskModel->status_title = $task['status_title'];
                $ganttTaskModel->depends = ($task['depends']) ? $task['depends'] : null;
                $ganttTaskModel->canWrite = $task['canWrite'];
                $ganttTaskModel->start = $task['start'];
                $ganttTaskModel->end = $task['end'];
                $ganttTaskModel->duration = ($task['duration']) ? $task['duration'] : null;
                $ganttTaskModel->startIsMilestone = $task['startIsMilestone'];
                $ganttTaskModel->endIsMilestone = $task['endIsMilestone'];
                $ganttTaskModel->collapsed = $task['collapsed'];
                $ganttTaskModel->user_id = ($task['user_id']) ? $task['user_id'] : null;
                $ganttTaskModel->hasChild = $task['hasChild'];
                $ganttTaskModel->position = $count++;

                $ganttTaskModel->save();
            }
        }

        echo "Sync data task done"."\n";

        return Command::SUCCESS;
    }

    public function getDataTasks($project_id){
        $dataTask = $this->getTaskByProject($project_id);

        $ganttTasks = [];
        $status = config('const.status');

        foreach ($dataTask as $key => $item){
            if(empty($item['name'])){
                continue;
            }
            $temp = [];
            $temp['task_id'] = $item['id'];
            $temp['name'] = $item['name'];
            $temp['progress'] = $item['progress'];
            $temp['description'] = $item['description'];
            $temp['level'] = $item['level'];
            $temp['status'] = (isset($status[$item['status']])) ? $status[$item['status']]['alias'] : "STATUS_UNDEFINED";
            $temp['status_title'] = (isset($status[$item['status']])) ? $status[$item['status']]['title'] : "STATUS_UNDEFINED";
            $temp['depends'] = "";

            $temp['canWrite'] = true;
            $temp['start'] = $item['start_time_milisecond'];

            $temp['end'] = $item['end_time_milisecond'];
            $temp['startIsMilestone'] = false;
            $temp['endIsMilestone'] = false;
            $temp['collapsed'] = false;
            $temp['user_id'] = $item['user_id'];
            $temp['hasChild'] = false;
            if(!empty($item['grandchildren'])){
                $temp['hasChild'] = true;
            }

            $this->cleanTaskArray($item);

            if((empty($item['start_time']) || empty($item['end_time'])) && empty($item['grandchildren'])){
                continue;
            }

            if((empty($item['start_time']) || empty($item['end_time'])) && !empty($item['grandchildren'])){
                $this->updateTimes($item);
                $temp['start'] = strtotime($item['start_time']) * 1000;
                $temp['end'] = strtotime($item['end_time']) * 1000;
            }

            $temp['duration'] = "";
            if($item['start_time'] && $item['end_time']) {
                $formatted_dt1 = Carbon::parse($item['start_time']);
                $formatted_dt2 = Carbon::parse($item['end_time']);
                $date_diff = $formatted_dt1->diffInDays($formatted_dt2);
                $temp['duration'] = $date_diff;
            }

            $ganttTasks[] = $temp;
        }

        return $ganttTasks;
    }

    public function getTaskByProject($project_id){
        $url_api = env('WORK_HORUS').'/api/task/get-task-by-project';
        //$access_token = session('access_token');
        $access_token = "161|oUEexV9qWcSsCyKGOK1NgskKfeynWkTSC2rRBIse76c27597";
        $params = [
            'project_id' => $project_id
        ];
        $result = Mycurl::postCurl($url_api, $access_token, $params);

        return $result;
    }

    public function findMinMaxDates($tasks, &$minDate, &$maxDate) {
        foreach ($tasks as $key => &$task) {
            // Kiểm tra và xử lý ngày bắt đầu
            if(empty($task['start_time']) && empty($task['end_time']) && empty($task['grandchildren'])){
                unset($tasks[$key]);
                // continue;
            }
            if (!empty($task['start_time'])) {
                $startTime = Carbon::createFromFormat('d-m-Y',$task['start_time']);
                if ($minDate === null || $startTime < $minDate) {
                    $minDate = $startTime;
                }
            }

            // Kiểm tra và xử lý ngày kết thúc
            if (!empty($task['end_time'])) {
                $endTime = Carbon::createFromFormat('d-m-Y',$task['end_time']);
                if ($maxDate === null || $endTime > $maxDate) {
                    $maxDate = $endTime;
                }
            }

            // Đệ quy tìm kiếm trong các cấp grandchildren sâu hơn
            if (!empty($task['grandchildren'])) {
                $this->findMinMaxDates($task['grandchildren'], $minDate, $maxDate);
            }
        }
    }

    public function updateTimes(&$task) {
        $minStartTime = null;
        $maxEndTime = null;

        if (isset($task['grandchildren']) && !empty($task['grandchildren'])) {

            // Đệ quy để tìm start_time nhỏ nhất và end_time lớn nhất trong grandchildren
            $this->findMinMaxDates($task['grandchildren'], $minStartTime, $maxEndTime);

            foreach ($task['grandchildren'] as &$child) {
                // Đệ quy cập nhật times cho grandchildren
                $this->updateTimes($child);
            }

            // Cập nhật start_time của cấp cha nếu nó là null
            if ($task['start_time'] === null && $minStartTime !== null) {
                $task['start_time'] = Carbon::parse($minStartTime)->format('d-m-Y');
            }

            // Cập nhật end_time của cấp cha nếu nó là null
            if ($task['end_time'] === null && $maxEndTime !== null) {
                $task['end_time'] = Carbon::parse($maxEndTime)->format('d-m-Y');
            }
        }
    }

    public function removeNullTasks(&$task) {
        if (isset($task['grandchildren']) && !empty($task['grandchildren'])) {
            foreach ($task['grandchildren'] as $key => &$child) {
                // Gọi đệ quy cho grandchildren
                $this->removeNullTasks($child);

                // Kiểm tra và loại bỏ grandchildren nếu start_time, end_time và grandchildren đều null
                if (empty($child['start_time']) && empty($child['end_time']) && empty($child['grandchildren']) ) {
                    unset($task['grandchildren'][$key]);
                }
            }

            // Kiểm tra và loại bỏ cấp hiện tại nếu tất cả grandchildren đều null
            if (empty($task['grandchildren'])) {
                $task['grandchildren'] = null;
            } else {
                // Sắp xếp lại chỉ số mảng
                $task['grandchildren'] = array_values($task['grandchildren']);
            }
        }

        // Nếu start_time, end_time và grandchildren của cấp hiện tại đều null, loại bỏ cấp này
        if ($task['start_time'] === null && $task['end_time'] === null && $task['grandchildren'] === null) {
            $task = null;
        }
    }

    public function cleanTaskArray(&$task) {
        $this->removeNullTasks($task);

        // Nếu task bị loại bỏ (null), cần loại bỏ nó khỏi mảng cha
        if ($task === null) {
            return false;
        }
        return true;
    }

}
