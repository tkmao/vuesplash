<?php

namespace App\Repositories;

use App\Repositories\Models\ProjectWork;
use Illuminate\Support\Facades\DB;

class ProjectWorkRepository implements ProjectWorkRepositoryInterface
{
    /** @var ProjectWork */
    protected $projectWork;

    /**
     * @param ProjectWork $projectWork
     */
    public function __construct(ProjectWork $projectWork)
    {
        $this->projectWork = $projectWork;
    }

    public function find($id)
    {
        try {
            $projectWork = $this->projectWork->find($id);
            if (!$projectWork) {
                $projectWork = new ProjectWork();
            }
            return $projectWork;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * プロジェクトリスト登録
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            DB::transaction(function () use ($requestArray) {
                // work_schedule_id リストの作成
                foreach ($requestArray['workschedules'] as $key => $value) {
                    $workschedulesids[] = $value['id'];
                }

                // プロジェクト時間を削除(ついでに削除件数も取得)
                $projectWorkDeleteCount = $this->projectWork->whereIn('work_schedule_id', $workschedulesids)->delete();

                // プロジェクト時間の追加
                foreach ($requestArray['workschedules'] as $workscheduleskey => $workschedule) {
                    foreach ($workschedule['project_work'] as $projectWorkkey => $projectWorkValue) {
                        $projectWork = new ProjectWork;
                        $projectWork->work_schedule_id = $workschedule['id'];
                        $projectWork->project_id = $projectWorkValue['project_id'];
                        $projectWork->worktime = $projectWorkValue['worktime'];
                        $projectWork->save();
                    }
                }
            });
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
