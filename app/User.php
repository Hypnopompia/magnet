<?php

namespace App;

use App\Board;
use App\Magnet\Workerjob;
use DirkGroenen\Pinterest\Endpoints\Boards;
use DirkGroenen\Pinterest\Endpoints\Pins;
use DirkGroenen\Pinterest\Pinterest;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Log;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function boards() {
        return $this->hasMany("App\Board");
    }

    public function pinterestLoggedIn() {
        if ($this->pinterestaccesstoken) {
            return true;
        }
        return false;
    }

    public function reset() {
        Board::where('user_id', $this->id)->delete();
        Pin::where('user_id', $this->id)->delete();

        $this->pinterestaccesstoken = null;
        $this->save();

        return $this;
    }

    public function printBoards() {
        $pinterest = new Pinterest(config("services.pinterest.appid"), config("services.pinterest.appsecret"));
        $pinterest->auth->setOAuthToken($this->pinterestaccesstoken);

        $page = [];
        $boards = $pinterest->users->getMeBoards( array_merge($page, ['fields' => 'id,name,url,description,creator,created_at,counts,image']) );
        dd($boards);
    }

    public static function importBoardsJob($jobData) {
        $user = User::find($jobData['user_id']);
        if (!$user) {
            Log::error("Invalid user for importBoardsJob: " . $jobData['user_id']);
        }
        $user->importBoards();
    }

    public function importBoards() {
        $workerjob = new Workerjob;

        $pinterest = new Pinterest(config("services.pinterest.appid"), config("services.pinterest.appsecret"));
        $pinterest->auth->setOAuthToken($this->pinterestaccesstoken);

        $user = $this;
        $page = [];

        do {
            $boards = $pinterest->users->getMeBoards( array_merge($page, ['fields' => 'id,name,url,description,creator,created_at,counts,image']) );

            foreach ($boards as $b) {
                $newBoard = [
                    'user_id' => $user->id,
                    'pinterestid' => $b->id,
                    'name' => $b->name,
                    'description' => $b->description,
                ];

                if (isset($b->image['60x60']['url'])) {
                    $newBoard['imageurl'] = $b->image['60x60']['url'];
                }

                if (isset($b->image['60x60']['width'])) {
                    $newBoard['imagewidth'] = $b->image['60x60']['width'];
                }

                if (isset($b->image['60x60']['height'])) {
                    $newBoard['imageheight'] = $b->image['60x60']['height'];
                }

                $board = Board::unguarded(function() use ($newBoard) {
                    return Board::firstOrCreate($newBoard);
                });

                $workerjob->addJob('ImportPins', ['user_id' => $user->id, 'board_id' => $board->id]);
            }

            $page = $boards->pagination;
        } while ($page);

        $workerjob->send();
    }

    public static function importPinsJob($jobData) {
        $user = User::find($jobData['user_id']);
        $board = Board::find($jobData['board_id']);
        $user->importPins($board);
    }

    public function importPins($board) {
        $pinterest = new Pinterest(config("services.pinterest.appid"), config("services.pinterest.appsecret"));
        $pinterest->auth->setOAuthToken($this->pinterestaccesstoken);

        $user = $this;
        $page = [];

        do {
            $pins = $pinterest->pins->fromBoard($board->pinterestid, array_merge($page, ['fields' => 'id,link,url,note,color,media,attribution,image,metadata']));
            foreach ($pins as $p) {

                $newPin = [
                    'user_id' => $user->id,
                    'board_id' => $board->id,
                    'pinterestid' => $p->id,
                    'link' => $p->link,
                    'note' => $p->note,
                    'color' => $p->color,
                ];

                if (isset($p->image['original']['url'])) {
                    $newPin['imageurl'] = $p->image['original']['url'];
                }

                if (isset($p->image['original']['width'])) {
                    $newPin['imagewidth'] = $p->image['original']['width'];
                }

                if (isset($p->image['original']['height'])) {
                    $newPin['imageheight'] = $p->image['original']['height'];
                }

                $pin = Pin::unguarded(function() use ($newPin) {
                    return Pin::firstOrCreate($newPin);
                });
            }

            $page = $pins->pagination;
        } while ($pins->pagination);
    }
}
