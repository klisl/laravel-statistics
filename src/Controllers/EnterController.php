<?php

namespace Klisl\Statistics\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


/**
 * Страница входа
 *
 * @package Klisl\Statistics\Controllers
 */
class EnterController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request){

        $password_config = config('statistics.password');
        $password_enter = $request->input('password');

        if($password_config == $password_enter){

            session(['ksl-statistics' => $password_config]);
            return redirect()->route('statistics');

        } else {
            if($password_enter){
                session()->flash('error', 'Неверный пароль');
            }

            return view('Views::enter');
        }

    }
}
