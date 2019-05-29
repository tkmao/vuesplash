<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\StoreComment;
use App\Http\Requests\StorePhoto;
use App\Http\Requests\StoreUser;
use App\Services\User\UserServiceInterface;
use App\Photo;
use App\Repositories\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Integer;

class PhotoController extends Controller
{
    /** @var UserServiceInterface */
    protected $userServiceInterface;

    public function __construct(
        UserServiceInterface $userServiceInterface
    ) {
        $this->userServiceInterface = $userServiceInterface;

        // 認証が必要
        $this->middleware('auth')->except(['index', 'download', 'show']);
    }

    /**
     * ユーザ一覧
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function indexUser()
    {
        $users = User::orderBy(User::CREATED_AT, 'desc')->get();

        return $users;
    }

    /**
     * 写真一覧
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        $photos = Photo::with(['owner', 'likes'])
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate();

        return $photos;
    }

    /**
     * ユーザ詳細
     * @param string $id
     * @return User
     */
    public function showUser(string $id)
    {
        dd('showUser', $id);
        $user = User::where('id', $id)->first();

        return $user ?? abort(404);
    }

    /**
     * ユーザ登録
     * @param string $id
     * @return User
     */
    public function storeUser(string $id, StoreUser $request)
    {

        //$result = $this->userServiceInterface->store($request);
        dd('storeUser', $id, $request->all(), gettype($request));

        return $user ?? abort(404);
    }

    /**
     * 写真詳細
     * @param string $id
     * @return Photo
     */
    public function show(string $id)
    {
        $photo = Photo::where('id', $id)
            ->with(['owner', 'comments.author', 'likes'])->first();

        return $photo ?? abort(404);
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Http\Response
     */
    public function create(StorePhoto $request)
    {
        $extension = $request->photo->extension();

        $photo = new Photo();
        $photo->filename = $photo->id . '.' . $extension;

        Storage::cloud()->putFileAs('', $request->photo, $photo->filename, 'public');

        DB::beginTransaction();

        try {
            Auth::user()->photos()->save($photo);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Storage::cloud()->delete($photo->filename);
            throw $exception;
        }

        return response($photo, 201);
    }

    /**
     * コメント投稿
     * @param Photo $photo
     * @param StoreComment $request
     * @return \Illuminate\Http\Response
     */
    public function addComment(Photo $photo, StoreComment $request)
    {
        $comment = new Comment();
        $comment->content = $request->get('content');
        $comment->user_id = Auth::user()->id;
        $photo->comments()->save($comment);

        $new_comment = Comment::where('id', $comment->id)->with('author')->first();

        return response($new_comment, 201);
    }

    /**
     * いいね
     * @param string $id
     * @return array
     */
    public function like(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);
        $photo->likes()->attach(Auth::user()->id);

        return ["photo_id" => $id];
    }

    /**
     * いいね解除
     * @param string $id
     * @return array
     */
    public function unlike(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);

        return ["photo_id" => $id];
    }

    /*
     * 写真ダウンロード
     * @param Photo $photo
     * @return \Illuminate\Http\Response
     */
    public function download(Photo $photo)
    {
        // 写真の存在チェック
        if (! Storage::cloud()->exists($photo->filename)) {
            abort(404);
        }

        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $photo->filename . '"',
        ];

        return response(Storage::cloud()->get($photo->filename), 200, $headers);
    }
}
