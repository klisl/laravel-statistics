<?php

namespace Klisl\Statistics\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


/**
 * Class EnterController
 * Обрабатывает форму входа на страницу статистики
 *
 * @package Klisl\Statistics\Controllers
 */
class EnterController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request){

        $password_config = config('statistics.password');
        $password_enter = $request->input('password');

        if($password_config == $password_enter){

            session(['ksl-statistics' => true]);

            return redirect()->route('statistics');

        } else {
            session()->flash('error', 'Неверный пароль');
            return view('Views::enter');
        }

    }
}
