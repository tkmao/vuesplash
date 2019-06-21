<?php

namespace App\Repositories;

use App\Repositories\Models\Company;

class CompanyRepository implements CompanyRepositoryInterface
{
    /** @var Company */
    protected $company;

    /**
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * 全企業データ取得
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $companies = $this->company->where('is_deleted', false)->get();

            return $companies;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 企業データ登録
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            $company = new Company;
            $company->name = $requestArray['name'];
            $company->zipcode = $requestArray['zipcode'];
            $company->address = $requestArray['address'];
            $company->phone = $requestArray['phone'];
            $company->fax = $requestArray['fax'];
            $company->is_deleted = $requestArray['is_deleted'];
            $company->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 企業データ編集
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $where = [ 'id' => $requestArray['id'] ];
            $update_values  = [ 'name' => $requestArray['name'],
                                'zipcode' => $requestArray['zipcode'],
                                'address' => $requestArray['address'],
                                'phone' => $requestArray['phone'],
                                'fax' => $requestArray['fax'],
                                'is_deleted' => $requestArray['is_deleted'],
                            ];

            $this->company->where($where)->update($update_values);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 企業データ削除
     *
     * @param int $companyId
     * @return void
     */
    public function delete(int $companyId): void
    {
        try {
            $where = [ 'id' => $companyId ];
            $update_values  = [ 'is_deleted' => true ];

            $this->company->where($where)->update($update_values);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
