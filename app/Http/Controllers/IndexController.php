<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Page;
use App\Portfolio;
use App\Service;
use App\People;
use DB;
use Mail;

class IndexController extends Controller
{
    public function execute(Request $request){


        $p = Page::find(1);
        $p->addToIndex();
        Page::addAllToIndex();

        if($request->isMethod('post')){
            $messages = [
                'required' => 'This :attribute field is required',
                'email' => 'Wrong email format for field :attribute'
            ];
            $this->validate($request, [
                'name' => 'required|max:255',
                'email' => 'required|email',
                'text' => 'required'
            ], $messages);

            $data = $request->all();
            $result = Mail::send('site.email', ['data' => $data], function ($message) use ($data){
                $mail_admin = env('MAIL_ADMIN');
                $message->from($data['email'], $data['name']);
                $message->to($mail_admin)->subject('Question');
            });

            if($result){
                return redirect()->route('home')->with('status', 'Email is sent');
            }

        }
        $pages = Page::all();
        $portfolios = Portfolio::all();
        $services = Service::all();
        $peoples = People::all();

        $tags = DB::table('portfolios')->distinct()->pluck('filter');


        $menu = array();
        foreach ($pages as $page){
            $item = array('title' => $page->name, 'alias' => $page->alias);
            array_push($menu, $item);
        }

        $item = array('title' => 'Services', 'alias' => 'service');
        array_push($menu, $item);

        $item = array('title' => 'Portfolio', 'alias' => 'Portfolio');
        array_push($menu, $item);

        $item = array('title' => 'Clients', 'alias' => 'clients');
        array_push($menu, $item);

        $item = array('title' => 'Team', 'alias' => 'team');
        array_push($menu, $item);

        $item = array('title' => 'Contact', 'alias' => 'contact');
        array_push($menu, $item);

        return view('site.index', array(
            'menu' => $menu,
            'pages' => $pages,
            'portfolios' => $portfolios,
            'services' => $services,
            'peoples' => $peoples,
            'tags' => $tags
        ));
    }
}
