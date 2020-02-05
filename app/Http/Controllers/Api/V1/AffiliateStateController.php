<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AffiliateState;
class AffiliateStateController extends Controller
{
    public function index()
    {
        return AffiliateState::get();
    }
}
