<?php

namespace App\Http\Controllers\App\Api;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Doctrine\ORM\EntityManager;

class NotificationsController extends Controller
{
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEmailList() 
    {
        $user = Auth::user();
        return Notifications::where('user_id', $user->id)->get();
    }
}