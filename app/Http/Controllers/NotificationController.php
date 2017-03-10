<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Device;
use App\Http\Controllers\Controller;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class NotificationController extends Controller {

    public function getIndex(Request $request) {
        $this->authorize('Notification');
        $user_role = auth()->user()->user_type;
        try {
            $request->flash();
            $devices = Device::where('active', 1)->get();

//            $start_date = date('Y-m-t 00:00:00', time());
//            $start_end = date('Y-m-t 23:59:59', time());
//            $start_date = $request->has('start_date') ? $request->old('start_date') : $start_date;
//            $end_date = $request->has('end_date') ? $request->old('end_date') : $start_end;

            $users = User::join('devices', 'users.id', '=', 'devices.user_id')
                    ->select('users.id', 'users.name', 'users.email', 'users.thumbnail', 'users.status', 'users.user_type');
//
            if ($request->has('key')) {
                $keyword = $request->get('key');
                $keyword = preg_replace('/\s\s+/', ' ', trim($keyword));
                $users = $users->whereRaw("users.name LIKE '%$keyword%'");
            }
//
            if ($request->has('status')) {
                $status = strip_tags($request->get('status'));
                $users = $users->where('users.status', $status);
            }
//            
            if ($request->has('user_type')) {
                $type = strip_tags($request->get('user_type'));
                $users = $users->where('users.user_type', $type);
            }
//            
            if ($request->has('device')) {
                $device = strip_tags($request->get('device'));
                $users = $users->where('devices.deviceid', $device);
            }

            $users = $users->orderBy('users.name', 'desc');
            $users = $users->groupBy('users.id');
            $request_all = $request->all();
            $users = $users->paginate(10);

            if (!empty($users)) {
                return view('childs.user.notification')->with('users', $users)->with('request_all', $request_all)->with('devices', $devices);
            } else {
                echo 'no data';
            }
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function postPushText(Request $request) {
        try {
            $devices = array();
            $data_devices = array();

            $date = date('Y-m-d H:i:s');
            $start_date = $request->has('start_date') ? strip_tags($request->get('start_date')) : $start_date;
//            $end_date = $request->has('end_date') ? strip_tags($request->get('end_date')) : $end_date;

            $title = $request->has('title') ? strip_tags($request->get('title')) : '';
            $content = $request->has('content') ? strip_tags($request->get('content')) : '';

            $device = $request->has('device') ? (int) strip_tags($request->get('device')) : 0;
            $strID = $request->has('id') ? strip_tags($request->get('id')) : '';

            $arrID = array_filter(explode(',', $strID));
            for ($i = 0; $i < count($arrID); $i++) {
                $devices = Device::select('token', 'user_id', 'devicename');
                $user_id = (int) $arrID[$i];
                if ($user_id > 0) {
                    $devices = $devices->where('user_id', $user_id);
                }
                if ($device > 0) {
                    $devices = $devices->where('id', $device);
                }
                $devices = $devices->where('active', 1)->get();

                foreach ($devices as $value) {
                    if (isset($value->token)) {
                        $data_devices[] = $value->token;
                    }
                }
            }
            $time_cur = strtotime($date);
            $start_job = strtotime($start_date);
            $time_delay = (int) ($start_job - $time_cur);
            
            if (!empty($data_devices)) {
//                $this->processPush($data_devices, $title, $content);
                $job = new \App\Jobs\JobCacheArray($data_devices, $title, $content, []);
                $job->delay($time_delay);
                $this->dispatch($job);
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    function processPush($devices, $title, $content) {
        $optionBuiler = new OptionsBuilder();
        $optionBuiler->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($content)->setSound('default');

        $option = $optionBuiler->build();
        $notification = $notificationBuilder->build();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['action' => 'Push text']);
        $data_get = $dataBuilder->build();
        if (isset($data_get)) {
            FCM::sendTo($devices, $option, $notification, $data_get);
        } else {
            FCM::sendTo($devices, $option, $notification, $data_get);
        }
    }

}
