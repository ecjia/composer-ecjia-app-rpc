<?php


namespace Ecjia\App\Rpc\Lists;


use ecjia;
use Ecjia\App\Rpc\Repositories\DefaultRpcAccountRepository;
use ecjia_page;
use RC_Time;

class RpcAccountList
{

    public function __invoke()
    {

//        $db_platform_account = RC_DB::table('rpc_account');

        $filter              = array();
//        $platform            = !empty($_GET['platform']) ? $_GET['platform'] : '';

        $keywords  = empty($_GET['keywords']) ? '' : trim($_GET['keywords']);
        if (!empty($keywords)) {
            $filter['keywords'] = function ($query) use ($keywords) {
                return $query->where('name', 'like', '%' . mysql_like_quote($keywords) . '%');
            };
//            $db_platform_account->where('name', 'like', '%' . mysql_like_quote($filter['keywords']) . '%');
        }
//        $db_platform_account->where('platform', '!=', 'weapp')->where('shop_id', 0);
//        if (!empty($platform)) {
//            $db_platform_account->where('platform', $platform);
//        }

        $repository = new DefaultRpcAccountRepository();

        foreach ($filter as $closure) {
            $repository->addScopeQuery($closure);
        }

        $count = $repository->count();

        $filter['record_count'] = $count;
        $page                   = new ecjia_page($count, $repository->getModel()->getPerPage(), 5);

        $arr  = array();
        $data = $repository->orderBy('sort', 'asc')
                ->orderBy('add_time', 'desc')
                ->paginate(10)->all();
//        dd($data->all());
        if (!empty($data)) {
            foreach ($data as $rows) {
                $rows['add_time'] = RC_Time::local_date(ecjia::config('time_format'), $rows['add_gmtime']);
                $arr[] = $rows;
            }
        }

        return array('item' => $arr, 'filter' => $filter, 'page' => $page->show(5), 'desc' => $page->page_desc());
    }

}