<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\User\CategoryServiceInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /** @var CategoryServiceInterface */
    protected $categoryServiceInterface;

    public function __construct(
        CategoryServiceInterface $categoryServiceInterface
    ) {
        $this->categoryServiceInterface = $categoryServiceInterface;
        // 認証が必要
        $this->middleware('auth');
    }

    /**
     * PJ区分一覧
     * @param string $id
     * @return Category
     */
    public function getAll(Request $request)
    {
        $categories = $this->categoryServiceInterface->all();

        return $categories ?? abort(404);
    }

    /**
     * PJ区分登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $requestArray = $request->all();
        $this->categoryServiceInterface->store($requestArray['category']);
    }

    /**
     * PJ区分修正
     *
     * @param Request $request
     * @return void
     */
    public function edit(Request $request)
    {
        $requestArray = $request->all();
        $this->categoryServiceInterface->edit($requestArray['category']);
    }

    /**
     * PJ区分削除
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $requestArray = $request->all();
        $this->categoryServiceInterface->delete($requestArray['categoryId']);
    }
}
