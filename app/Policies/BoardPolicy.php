<?php

namespace App\Policies;

use App\User;
use App\Board;
use Illuminate\Auth\Access\HandlesAuthorization;

class BoardPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the board.
     *
     * @param  App\User  $user
     * @param  App\Board  $board
     * @return mixed
     */
    public function view(User $user, Board $board)
    {
        return $user->id === $board->user_id;
    }

    /**
     * Determine whether the user can create boards.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the board.
     *
     * @param  App\User  $user
     * @param  App\Board  $board
     * @return mixed
     */
    public function update(User $user, Board $board)
    {
        return $user->id === $board->user_id;
    }

    /**
     * Determine whether the user can delete the board.
     *
     * @param  App\User  $user
     * @param  App\Board  $board
     * @return mixed
     */
    public function delete(User $user, Board $board)
    {
        return $user->id === $board->user_id;
    }
}
