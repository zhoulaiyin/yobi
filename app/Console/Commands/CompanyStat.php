<?php

namespace App\Console\Commands;

use Mail;

use App\Http\Models\User\User;

use Illuminate\Console\Command;

use App\Service\TaskScheduleService;

use App\Http\Models\Company\BusinessOpportunity;

use App\Http\Models\Company\CompanyCommunication;

use App\Http\Models\Company\Company;

class CompanyStat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:stat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '客户管理反馈预警任务';

    /**
     * 任务id
     *
     * @var int
     */
    protected $id = 5;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        //检查任务
        if (false === TaskScheduleService::check($this->id)) {
            return false;
        }

        $time = date('Y-m-d H:i:s',strtotime('+1 day'));

        //获取客户管理信息
        $company_obj = Company::all();

        if ( $company_obj ) {

            $company_obj = $company_obj->toArray();

            foreach ($company_obj as $v) {

                $next_time = '';
                if ( $v['next_start_time'] == 1 ) {
                    $next_time = date('Y-m-d H:i:s',strtotime('+3 day',strtotime($v['createTime'])));
                } elseif ( $v['next_start_time'] == 2 ) {
                    $next_time = date('Y-m-d H:i:s',strtotime('+7 day',strtotime($v['createTime'])));
                } elseif ( $v['next_start_time'] == 3 ) {
                    $next_time = date('Y-m-d H:i:s',strtotime('+15 day',strtotime($v['createTime'])));
                } elseif ( $v['next_start_time']== 4 ) {
                    $next_time = date('Y-m-d H:i:s',strtotime('+28 day',strtotime($v['createTime'])));
                } elseif ( $v['next_start_time'] == 5 ) {
                    $next_time = date('Y-m-d H:i:s',strtotime('+1 months',strtotime($v['createTime'])));
                }

                $status = 1;  // 1.已完成 2.未完成
                if ( date('Y-m-d H:i:s') > $next_time && !empty( $next_time ) ) {

                    $communication = CompanyCommunication::where([['company_id','=',$v['company_id']],['created_at','>',$next_time]])->get()->toArray();

                    if ( !$communication ) {
                        $status = 2;
                    }
                }


                Company::where(['company_id'=>$v['company_id']])->update(['status'=>$status]);  //更新状态


                //获取拜访记录反馈结束时间
                $company_communication = CompanyCommunication::where([
                    ['created_at','>',date('Y-m-d H:i:s',strtotime('-35 day'))],
                    ['company_id','=',$v['company_id']]
                ])->orderBy('id','DESC')
                    ->limit(1)
                    ->get()
                    ->toArray();

                if ( $company_communication ) {

                    $next_start_time = '';
                    if ( $company_communication[0]['next_start_time'] == 1 ) {
                        $next_start_time = date('Y-m-d H:i:s',strtotime('+3 day',strtotime($company_communication[0]['created_at'])));
                    } elseif ( $company_communication[0]['next_start_time'] == 2 ) {
                        $next_start_time = date('Y-m-d H:i:s',strtotime('+7 day',strtotime($company_communication[0]['created_at'])));
                    } elseif ( $company_communication[0]['next_start_time'] == 3 ) {
                        $next_start_time = date('Y-m-d H:i:s',strtotime('+15 day',strtotime($company_communication[0]['created_at'])));
                    } elseif ( $company_communication[0]['next_start_time'] == 4 ) {
                        $next_start_time = date('Y-m-d H:i:s',strtotime('+28 day',strtotime($company_communication[0]['created_at'])));
                    } elseif ( $company_communication[0]['next_start_time'] == 5 ) {
                        $next_start_time = date('Y-m-d H:i:s',strtotime('+1 months',strtotime($company_communication[0]['created_at'])));
                    }

                    if ( $time >= $next_start_time && date('Y-m-d H:i:s') <= $next_start_time && !empty( $next_start_time ) ) {

                        //查询操作人姓名
                        $operate_person = User::find( $company_communication[0]['creator'] );

                        $company = Company::find($company_communication[0]['company_id']);

                        $data = [
                            'email' => $operate_person['email'], //邮件
                            'user_name' => $operate_person['trueName'], //用户姓名
                            'operate' => '售前记录', //操作
                            'operate_person' => $operate_person['trueName'], //操作人
                            'company_name' => $company->company_name,
                        ];

                        //发送邮件
                        $this->saleEmail( $data , $company_communication[0] );

                        //保存日志
                        TaskScheduleService::addLog( $this->id ,1 , '售前记录反馈时间预警邮件发送成功');
                    }
                }
            }
        }

        //获取客户管理信息
        $company = Company::where([['createTime','>', date('Y-m-d H:i:s',strtotime('-35 day'))]])->get()->toArray();

        if ( $company ) {

            foreach ( $company as $v ) {

                $feedback_time = '';
                if ( $v['feedback_time']== 1 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+3 day',strtotime($v['createTime'])));
                } elseif ( $v['feedback_time'] == 2 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+7 day',strtotime($v['createTime'])));
                } elseif ( $v['feedback_time'] == 3 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+15 day',strtotime($v['createTime'])));
                } elseif ( $v['feedback_time']== 4 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+28 day',strtotime($v['createTime'])));
                } elseif ( $v['feedback_time']== 5 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+1 months',strtotime($v['createTime'])));
                }

                if ( $time >= $feedback_time && date('Y-m-d H:i:s') <= $feedback_time && !empty( $feedback_time )) {

                    //查询收件人信息
                    $user_data = User::find( $v['notice_user_id'] );

                    //查询操作人姓名
                    $operate_person = User::find( $v['creator'] );

                    $v['client_name'] = $v['company_name'];

                    $data = [
                        'email' => $user_data['email'], //邮件
                        'user_name' => $user_data['trueName'], //用户姓名
                        'operate' => '商机分派', //操作
                        'operate_person' => $operate_person['trueName'] //操作人
                    ];

                    //发送邮件
                    $this->opportunityEmail( $data , $v );

                    //保存日志
                    TaskScheduleService::addLog( $this->id ,1 , '商机分派预警邮件发送成功');
                }

            }
        }


        //获取商机反馈结束时间
        $business_opportunity = BusinessOpportunity::where([['source','>',1],['created_at','>', date('Y-m-d H:i:s',strtotime('-35 day'))]])->get()->toArray();

        if ( $business_opportunity ) {

            foreach ( $business_opportunity as $value ) {

                $feedback_time = '';
                if ( $value['feedback_time']== 1 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+3 day',strtotime($value['created_at'])));
                } elseif ( $value['feedback_time'] == 2 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+7 day',strtotime($value['created_at'])));
                } elseif ( $value['feedback_time'] == 3 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+15 day',strtotime($value['created_at'])));
                } elseif ( $value['feedback_time']== 4 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+28 day',strtotime($value['created_at'])));
                } elseif ( $value['feedback_time']== 5 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+1 months',strtotime($value['created_at'])));
                }

                if ( $time >= $feedback_time && date('Y-m-d H:i:s') <= $feedback_time && !empty( $feedback_time )) {

                    //查询收件人信息
                    $user_data = User::find( $value['notice_user_id'] );

                    //查询操作人姓名
                    $operate_person = User::find( $value['creator'] );

                    $data = [
                        'email' => $user_data['email'], //邮件
                        'user_name' => $user_data['trueName'], //用户姓名
                        'operate' => '添加商机', //操作
                        'operate_person' => $operate_person['trueName'] //操作人
                    ];

                    //发送邮件
                    $this->opportunityEmail( $data , $value );

                    //保存日志
                    TaskScheduleService::addLog( $this->id ,1 , '添加商机预警邮件发送成功');
                }
            }

        }


        //获取拜访记录反馈结束时间
        $company_communication_obj = CompanyCommunication::where([['created_at','>', date('Y-m-d H:i:s',strtotime('-35 day'))]])->get()->toArray();

        if ( $company_communication_obj ) {

            foreach ( $company_communication_obj as $item ) {

                $feedback_time = '';
                if ( $item['feedback_time']== 1 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+3 day',strtotime($item['created_at'])));
                } elseif ( $item['feedback_time'] == 2 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+7 day',strtotime($item['created_at'])));
                } elseif ( $item['feedback_time'] == 3 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+15 day',strtotime($item['created_at'])));
                } elseif ( $item['feedback_time']== 4 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+28 day',strtotime($item['created_at'])));
                } elseif ( $item['feedback_time']== 5 ) {
                    $feedback_time = date('Y-m-d H:i:s',strtotime('+1 months',strtotime($item['created_at'])));
                }

                if ( $time >= $feedback_time && date('Y-m-d H:i:s') <= $feedback_time && !empty( $feedback_time ) &&!empty( $item['notice_user_id'] ) && $item['problem_status'] == 0 ) {

                    //查询收件人信息
                    $user_data = User::find( $item['notice_user_id'] );

                    //查询操作人姓名
                    $operate_person = User::find( $item['creator'] );

                    $company = Company::find($item['company_id']);

                    $data = [
                        'email' => $user_data['email'], //邮件
                        'user_name' => $user_data['trueName'], //用户姓名
                        'operate' => '售前记录', //操作
                        'operate_person' => $operate_person['trueName'], //操作人
                        'company_name' => $company->company_name
                    ];

                    //发送邮件
                    $this->saleEmail( $data , $item );

                    //保存日志
                    TaskScheduleService::addLog( $this->id ,1 , '售前记录跟进时间预警邮件发送成功');
                }
            }
        }


        //保存日志
        TaskScheduleService::addLog( $this->id ,1 , '当前没有需要处理的预警邮件');


        return true;

    }

    /**
     * 商机管理发送邮件通知
     * @param $data
     * @param $opportunity_data
     */
    private  function opportunityEmail($data,$opportunity_data)
    {

        $http_host = 'http://www.ebsig.com';
        $url = '/eoa/business/opportunity';
        $http_url = 'http://'.$http_host . '/eoa/dashboard/4?sidebar='.urlencode($url);

        $source = '个人';
        if ( $opportunity_data['source'] == 2 ) {
            $source = '公司';
        } else if ( $opportunity_data['source'] == 3 ) {
            $source = '渠道';
        } else if ( $opportunity_data['source'] == 4 ) {
            $source = '其他';
        }

        $feedback_time = '';
        if ( $opportunity_data['feedback_time'] == 1 ) {
            $feedback_time = '三天内';
        } else if ( $opportunity_data['feedback_time'] == 2 ) {
            $feedback_time = '一周内';
        } else if ( $opportunity_data['feedback_time'] == 3 ) {
            $feedback_time = '半个月内';
        } else if ( $opportunity_data['feedback_time'] == 4 ) {
            $feedback_time = '一个月内';
        } else if ( $opportunity_data['feedback_time'] == 5 ) {
            $feedback_time = '一个月以上';
        }

        $opportunity = [
            'email' => $data['email'],
            'name' => $data['user_name'],
            'operate' => $data['operate'],
            'operate_person' => $data['operate_person'],
            'source' => $source,
            'client_name' =>  $opportunity_data['client_name'],
            'linkman' => $opportunity_data['linkman'],
            'contact_number' => $opportunity_data['contact_number'],
            'appeal' => isset($opportunity_data['appeal']) ? nl2br($opportunity_data['appeal']) : '',
            'feedback_time' => $feedback_time,
            'url' => $http_url
        ];
        //保存日志
        Mail::send('emails.demand.opportunity', ['opportunity' => $opportunity], function ($m) use ($opportunity) {
            $m->to($opportunity['email'], $opportunity['name'])->subject($opportunity['operate']);
        });

    }



    /**
     * 售前记录发送邮件通知
     * @param $data
     * @param $sale_data
     */
    private function saleEmail($data,$sale_data )
    {

        $http_host = 'http://www.ebsig.com';
        $url = '/eoa/company/beforeSale';
        $http_url = 'http://'.$http_host . '/eoa/dashboard/4?sidebar='.urlencode($url);

        $problem_type = '无';
        if ( $sale_data['problem_type'] == 1 ) {
            $problem_type = '无法接触到关键决策人';
        } else if ( $sale_data['problem_type'] == 2 ) {
            $problem_type = '项目延期或有变化';
        } else if ( $sale_data['problem_type'] == 3 ) {
            $problem_type = '联系人变更或决策流程变更';
        } else if ( $sale_data['problem_type'] == 4 ) {
            $problem_type = '客户需求不明确';
        } else if ( $sale_data['problem_type'] == 5 ) {
            $problem_type = '竞争对手因素';
        } else if ( $sale_data['problem_type'] == 6 ) {
            $problem_type = '价格因素';
        } else if ( $sale_data['problem_type'] == 7 ) {
            $problem_type = '合同条款因素';
        } else if ( $sale_data['problem_type'] == 8 ) {
            $problem_type = $sale_data['custom_problem'];
        }

        $support = '无';
        if ( $sale_data['support'] == 1 ) {
            $support = '解决方案';
        } else if ( $sale_data['support'] == 2 ) {
            $support = '报价清单';
        } else if ( $sale_data['support'] == 3 ) {
            $support = '系统演示';
        } else if ( $sale_data['support'] == 4 ) {
            $support = '测试账号';
        } else if ( $sale_data['support'] == 5 ) {
            $support = '现场拜访';
        } else if ( $sale_data['support'] == 6 ) {
            $support = $sale_data['other_support'];
        }

        $feedback_time = '';
        if ( $sale_data['feedback_time'] == 1 ) {
            $feedback_time = '三天内';
        } else if ( $sale_data['feedback_time'] == 2 ) {
            $feedback_time = '一周内';
        } else if ( $sale_data['feedback_time'] == 3 ) {
            $feedback_time = '半个月内';
        } else if ( $sale_data['feedback_time'] == 4 ) {
            $feedback_time = '一个月内';
        } else if ( $sale_data['feedback_time'] == 5 ) {
            $feedback_time = '一个月以上';
        }

        $sale = [
            'email' => $data['email'],
            'name' => $data['user_name'],
            'operate' => $data['operate'],
            'operate_person' => $data['operate_person'],
            'company_name' =>$data['company_name'],
            'client_object' =>  $sale_data['client_object'],
            'participants' => $sale_data['participants'],
            'problem_type' => $problem_type,
            'support' => $support,
            'feedback_time' => $feedback_time,
            'url' => $http_url
        ];

        Mail::send('emails.demand.beforeSale', ['sale' => $sale], function ($m) use ($sale) {
            $m->to($sale['email'], $sale['name'])->subject($sale['operate']);
        });

    }


}
