<?php

namespace App\Http\Controllers;

use App\Models\rssfeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RssfeedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'News';
        $feeds = null;
        $invalidurl = false;

        try {
            $response = Http::timeout(8)
                ->retry(1, 250)
                ->withHeaders(['User-Agent' => config('app.name', 'Remington').' News Reader'])
                ->get('https://cointelegraph.com/feed');

            if ($response->successful() && function_exists('simplexml_load_string')) {
                $feeds = @simplexml_load_string(
                    $response->body(),
                    'SimpleXMLElement',
                    LIBXML_NOCDATA
                );
            }

            $invalidurl = $feeds === false || $feeds === null;
        } catch (\Throwable $exception) {
            report($exception);
            $invalidurl = true;
        }

        return view('user.news.news',compact('page_title','feeds','invalidurl'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\rssfeed  $rssfeed
     * @return \Illuminate\Http\Response
     */
    public function show(rssfeed $rssfeed)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\rssfeed  $rssfeed
     * @return \Illuminate\Http\Response
     */
    public function edit(rssfeed $rssfeed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\rssfeed  $rssfeed
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, rssfeed $rssfeed)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\rssfeed  $rssfeed
     * @return \Illuminate\Http\Response
     */
    public function destroy(rssfeed $rssfeed)
    {
        //
    }
}
